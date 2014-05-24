<?php //defined('SYSPATH') OR die('No direct access allowed.');
//
//// SVN: svn checkout http://www.omnova.net/shackbattles .
//
//class Shack
//{
//	private static $_send_message_url = 'http://www.shacknews.com/msgcenter/send_message.x';
//	private static $_cookie_path = '../shackmeets_cookie.txt';
//
//	public static function login($username, $password)
//	{
//    if ($username == null || strlen($username) == 0 || $password == null || strlen($password) == 0)
//      return false;
//
//		$fields = array();
//		$fields['username'] = $username;
//		$fields['password'] = '234234';
//			//http://www.shacknews.com/account/signin
//      //http://shackchatty.com/auth.fmt
//		$ch = curl_init('http://www.shacknews.com/account/signin');
//
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
//    curl_setopt($ch, CURLOPT_HEADER, 1);
//    //curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
//		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
//
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
//		curl_setopt($ch, CURLOPT_COOKIEJAR, '../shackmeets_cookie.txt');
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($ch, CURLOPT_POST, count($fields));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, Shack::build_post_field_string($fields));
//
//		$result = curl_exec($ch);
//		curl_close ($ch);
//
//		//return !strstr($result, "We're sorry, but something went wrong.");
//    return $result;
//	}
//
//	public static function post($username, $password)
//	{
//    if ($username == null || strlen($username) == 0 || $password == null || strlen($password) == 0)
//      return false;
//
//		$fields = array();
//		$fields['content_type_id'] = 17;
//		$fields['content_id'] = 17;
//		$fields['body'] = 'API test';
//		$fields['parent_id'] = 21474281;
//			//http://www.shacknews.com/account/signin
//      //http://shackchatty.com/auth.fmt
//		$ch = curl_init('http://www.shacknews.com/api/chat/create/17.json');
//
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
//    curl_setopt($ch, CURLOPT_HEADER, 1);
//    curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
//		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
//
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
//		curl_setopt($ch, CURLOPT_COOKIEJAR, '../shackmeets_cookie.txt');
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($ch, CURLOPT_POST, count($fields));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, Shack::build_post_field_string($fields));
//
//		$result = curl_exec($ch);
//		curl_close ($ch);
//
//		//return !strstr($result, "We're sorry, but something went wrong.");
//    return $result;
//	}
//	// Separate recipients with a comma
//	public static function send_message($recipient, $subject, $body)
//	{
//		$fields = array();
//		$fields['bodytext'] = $body;
//		$fields['id']       = '';
//		$fields['mode']     = "new";
//		$fields['subject']  = $subject;
//		$fields['to']       = $recipient;
//
//		$result = Shack::send_post_request(Shack::_send_message_url, $fields);
//
//		return !strstr($result, 'Save a copy in my outbox');
//	}
//
//
//	private static function send_post_request($url, $fields)
//	{
//		$ch = curl_init($url);
//
//		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
//		curl_setopt($ch, CURLOPT_HEADER, 1);
//		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//		curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
//		curl_setopt($ch, CURLOPT_COOKIEFILE, Shack::_cookie_path);
//		curl_setopt($ch, CURLOPT_POST, count($fields));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, Shack::build_post_field_string($fields));
//
//		$result = curl_exec($ch);
//		curl_close ($ch);
//
//		return $result;
//	}
//
//	private static function build_post_field_string($fields)
//	{
//		$post_string = '';
//
//		foreach ($fields as $key => $value)
//		{
//			$post_string .= $key . '=' . urlencode($value) . '&';
//		}
//
//		return trim($post_string, '&');;
//	}
//}
