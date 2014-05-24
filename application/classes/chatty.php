<?php defined('SYSPATH') or die('No direct access allowed.');

class Chatty
{
  protected $username = Shackmeetsconfig::chattyUsername;
  protected $password = Shackmeetsconfig::chattyPassword;

  protected $is_prepared = false;
  protected $uid = null;
  protected $cached_curl = null;
  protected $cached_cookiejar = null;

  protected $cursors;
  protected $html;
  protected $len;

  function __destruct()
  {
    if ($this->is_prepared)
    {
      curl_close($this->cached_curl);
      unlink($this->cached_cookiejar);
    }
  }

  protected function init($html)
  {
    $this->cursors = array(null, 0, 0);
    $this->html    = $html;
    $this->len     = strlen($html);
  }

  protected function peek($which_cursor, $keyword)
  {
    return strpos($this->html, $keyword, $this->cursors[$which_cursor]);
  }

  protected function clip($before_keywords, $after_keyword)
  {
    $this->seek(1, $before_keywords);
    $this->incr(1);
    $this->seek(2, $after_keyword);

    return $this->read();
  }

  protected function incr($which_cursor)
  {
    $this->cursors[$which_cursor]++;

    if ($this->cursors[$which_cursor] >= $this->len)
       throw new Exception('Unexpected end of HTML data.');
  }

  protected function seek($which_cursor, $keywords)
  {
    # If $keyword is an array, then seek to each one in sequence.
    if (is_array($keywords))
    {
       foreach ($keywords as $keyword)
          $this->seek($which_cursor, $keyword);
    }
    else
    {
       $i = $this->cursors[1];
       $j = strpos($this->html, $keywords, $i);
       if ($j === false)
          throw new Exception("Did not find '$keywords' starting at index '$i'");
       else
          $this->cursors[$which_cursor] = $j;
    }
  }

  protected function read()
  {
    $c1 = $this->cursors[1];
    $c2 = $this->cursors[2];
    return substr($this->html, $c1, $c2 - $c1);
  }

  // General usage

  protected function getUserID($username, $password)
  {

  }

  protected function userDownload($url, $username, $password, $postArgs = null)
   {
      $cookiejar = tempnam(sys_get_temp_dir(), 'Shackmeets.CookieJar');
      chmod($cookiejar, 0666);

      $curl = curl_init();

      curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);
      curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
      curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiejar);
      curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiejar);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($curl, CURLOPT_USERAGENT, 'shackmeets.com');

      # Log in first.
      $fields = 'get_fields%5B%5D=result&user-identifier=' . urlencode($username) . '&supplied-pass=' . urlencode($password) . '&remember-login=0';

      curl_setopt($curl, CURLOPT_URL, 'https://www.shacknews.com/account/signin');
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));

      $response = curl_exec($curl);

      if (strpos($response, '{"result":{"valid":"true"') === false)
      {
         curl_close($curl);
         unlink($cookiejar);

         throw new Exception('Unable to log into user account.');
      }

      # Download the requested page.
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_POST, false);
      curl_setopt($curl, CURLOPT_POSTFIELDS, null);

      if ($postArgs != null)
      {
         curl_setopt($curl, CURLOPT_POST, 1);
         curl_setopt($curl, CURLOPT_POSTFIELDS, $postArgs);
      }

      $html = curl_exec($curl);

      curl_close($curl);
      unlink($cookiejar);

      return $html;
   }

  // Prepare method

  protected function prepare()
  {
    $cookiejar = tempnam(sys_get_temp_dir(), 'Shackmeets.CookieJar2');
    chmod($cookiejar, 0666);

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiejar);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiejar);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'shackmeets.com');

    # Log in first.
    $fields = 'get_fields%5B%5D=result&user-identifier=' . urlencode($this->username) . '&supplied-pass=' . urlencode($this->password) . '&remember-login=0';

    curl_setopt($curl, CURLOPT_URL, 'https://www.shacknews.com/account/signin');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));

    $response = curl_exec($curl);

    if (strpos($response, '{"result":{"valid":"true"') === false)
    {
      curl_close($curl);
      unlink($cookiejar);

      throw new Exception('Unable to log into user account.');
    }

    $this->cached_curl = $curl;
    $this->cached_cookiejar = $cookiejar;
    $this->is_prepared = true;
  }

  public function request($url, $post_args = null)
  {
    if (!$this->is_prepared)
      $this->prepare();

    curl_setopt($this->cached_curl, CURLOPT_URL, $url);
    curl_setopt($this->cached_curl, CURLOPT_POST, false);
    curl_setopt($this->cached_curl, CURLOPT_POSTFIELDS, null);

    if ($post_args != null)
    {
      curl_setopt($this->cached_curl, CURLOPT_POST, 1);
      curl_setopt($this->cached_curl, CURLOPT_POSTFIELDS, $post_args);
    }

    $html = curl_exec($this->cached_curl);

    return $html;
  }

  public function send_message($recipient, $subject, $body)
  {
    // Need the UID early, so prepare here
    if (!$this->is_prepared)
      $this->prepare();

    $url = 'https://www.shacknews.com/messages/send';
    $post_args =
       'uid='      . urlencode($this->uid) .
       '&to='      . urlencode($recipient) .
       '&subject=' . urlencode($subject) .
       '&message=' . urlencode($body);

    $this->request($url, $post_args);

    return true;
  }

  public function post($parent_id, $story_id, $body)
  {
    $url = 'https://www.shacknews.com/post_chatty.x';

    if ($parent_id == 0)
       $parent_id = '';

    $post_args = array(
       'parent_id' => $parent_id,
       'content_type_id' => '17',
       'content_id' => '17',
       'page' => '',
       'parent_url' => '/chatty',
       'body' => $body);

    return $this->request($url, $post_args);
  }

  public function authenticate($username, $password)
  {
    $cookiejar = tempnam(sys_get_temp_dir(), 'Shackmeets.CookieJar');
    chmod($cookiejar, 0666);

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiejar);
    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiejar);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_USERAGENT, 'shackmeets.com');

    $fields = 'get_fields%5B%5D=result&user-identifier=' . urlencode($username) . '&supplied-pass=' . urlencode($password) . '&remember-login=0';

    curl_setopt($curl, CURLOPT_URL, 'https://www.shacknews.com/account/signin');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Requested-With: XMLHttpRequest'));

    $response = curl_exec($curl);

    curl_close($curl);
    unlink($cookiejar);

    if (strpos($response, '{"result":{"valid":"true"') === false)
      throw new Exception('Bad username or password.');

    return true;
  }
}
