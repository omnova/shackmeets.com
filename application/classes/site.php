<?php defined('SYSPATH') OR die('No direct access allowed.');

class Site
{
	public static function current_user()
	{
    $session = Session::instance();
  
    $user = $session->get('current_user');
		
		$status = is_object($user) ? true : false;
		
		// Get the user from the cookie
		if ($status == false)
		{
			$token = cookie::get("user_autologin");
           			
			if (is_string($token))
			{
        $user = Model::factory('user');
        $user->load_by_session_key($token);
				
				if ($user->loaded())
				{			
					$session->set('current_user', $user);
        
					Cookie::set('user_autologin', $token, 1209600);
					
					return $user;
				}
			}
		}
			
		if ($status == true)
		{
			return $user;
		}
		
		return null;
	}
	
	/*
	 *  Auth methods
	 */
 
	public static function require_admin()
	{

	}
 
	public static function require_login()
	{
		$current_user = Site::current_user();
	
		if ($current_user == null)
		{
			Request::current()->redirect('');
		}
	}
	
	public static function login($username, $password, $remember = true)
	{	
		$chatty = new Chatty();

    $current_user = null;
    
    if ($chatty->authenticate($username, $password))
    {
      // Check if the user already exists in the DB
      $user = Model::factory('user')->load_by_id($username);
    
      if ($user == null)
      {
        $user = Model::factory('user');
        $user->username = $username;
        $user->insert();
      }  
    
      $session = Session::instance();      
      
      if ($remember == true)
      {
        $token = $session->id();
                  
        $user->session_key = $token;                
        $user->update();
                
        Cookie::set('user_autologin', $token, 1209600);
      }
            
      $session->set('current_user', $user);
      $current_user = $user;
		}    
    
		return $current_user;
	}
 
	public static function logout()
	{		
    Cookie::delete('user_autologin');  
    Session::instance()->set('current_user', null);
	}	
  
  
  public function get_salt()
  {        
    return $this->sha1(time() . 'salt');
  }
}