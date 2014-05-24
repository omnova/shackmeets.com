<?
class ProfileParser extends Parser
{
   public function getProfile($username)
   {
      throw new Exception('The new Shacknews does not have a profile feature.');
   
      $url = 'http://www.shacknews.com/profile/' . urlencode($username);
      $this->init($this->download($url, true));
   
      $info = array();

      $fields = array(
         array('Registered',  'registered'),
         array('Age',         'age'),
         array('Sex',         'sex'),
         array('Location',    'location'),
         array('Homepage',    'homepage'),
         array('AIM',         'aim'),
         array('Yahoo!',      'yahoo'),
         array('ICQ',         'icq'),
         array('MSN',         'msn'),
         array('GTalk',       'gtalk'),
         array('Steam',       'steam'),
         array('XBox Live',   'xboxlive'),
         array('PlayStation Network', 'psn'),
         array('Wii',         'wii'),
         array('XFire',       'xfire'));
      
      foreach ($fields as $field)
      {
         $label = $field[0];
         $key   = $field[1];
         
         $info[$key] = trim(strip_tags(html_entity_decode($this->clip(
            array("<th>$label</th>", '<td>', '>'),
            '</td>'))));
      }
      
      # Trim the timezone from the registration date.
      $info['registered'] = substr($info['registered'], 0, -4);
      
      $body = trim($this->clip(
         array('<!-- bio stuff goes here -->', '>'),
         '<!-- end user bio -->'));
      
      return array('info' => $info, 'body' => $body);
   }
}

function ProfileParser()
{
   return new ProfileParser();
}