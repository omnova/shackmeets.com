<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Ajax_User extends Controller_Ajax_Base
{
	function __construct($request, $response)
	{	
		parent::__construct($request, $response);	
	}
  
  // Login 
  
  public function action_login()
  {    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$form_inputs = array();
		$form_inputs['username'] = $this->request->post('login_modal_username');
		$form_inputs['password'] = $this->request->post('login_modal_password');
		
		if ($this->validate_login_form($form_inputs, $ajax_response))
		{
			// Attempt login			
      try
      {
        Site::login($form_inputs['username'], $form_inputs['password']);
        
        $ajax_response->success = true;
      }
      catch (Exception $e)
			{
        //$ajax_response->add_error(null, null, null, 'Bad username or password.');
        $ajax_response->add_error(null, null, null, $e->getMessage());
      }			
		}
		
		echo $ajax_response->get_json();
  }
	
	private function validate_login_form($form_inputs, $ajax_response)
	{
		if (strlen($form_inputs['username']) == 0)
			$ajax_response->add_error('login_username', null, null, 'Username is a required field.');		
		
		if (strlen($form_inputs['password']) == 0)
			$ajax_response->add_error('login_password', null, null, 'Password is a required field.');		
	
		if ($ajax_response->get_error_count() > 0)
			return false;
	
	  return true;
	}

	// Logout

  public function action_logout()
  {    
		Site::logout();
		
		$ajax_response = new Ajax_Response();
		$ajax_response->success = true;
		
		echo $ajax_response->get_json();
	}
  
	// Save Preferences
  
  public function action_savepreferences()
  { 
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$form_inputs = array();
    $form_inputs['time_zone'] = $this->request->post('preferences_time_zone');
		$form_inputs['latitude'] = $this->request->post('preferences_latitude');
		$form_inputs['longitude'] = $this->request->post('preferences_longitude');
		$form_inputs['notify_max_distance'] = round($this->request->post('preferences_notify_max_distance'));
		$form_inputs['notify_option'] = $this->request->post('preferences_notify_option');
		$form_inputs['notify_shackmessage'] = $this->request->post('preferences_notify_shackmessage');
		$form_inputs['notify_email'] = $this->request->post('preferences_notify_email');
		$form_inputs['email_address'] = $this->request->post('preferences_email_address');
		
		if ($this->validate_preferences_form($form_inputs, $ajax_response))
		{
      $user = $this->current_user;
      
      if ($user != null)      
      {     
        $user->time_zone = $form_inputs['time_zone'];
        $user->latitude = (strlen($form_inputs['latitude']) > 0) ? $form_inputs['latitude'] : null;
        $user->longitude = (strlen($form_inputs['longitude']) > 0) ? $form_inputs['longitude'] : null;
        $user->notify_max_distance = $form_inputs['notify_max_distance'];
        $user->notify_option = $form_inputs['notify_option'];
        $user->notify_shackmessage = $form_inputs['notify_shackmessage'];
        $user->notify_email = $form_inputs['notify_email'];
        $user->email_address = $form_inputs['email_address'];  
        $user->update();
      
        // if($user->saved())
        // {      
          $ajax_response->success = true;
        // }
        // else
        // {
          // $ajax_response->add_error(null, null, null, 'Unable to save preferences.');
        // }        
      }
      else
      {
        $ajax_response->add_error(null, null, null, 'User is not logged in.');
      }			
		}
		
		echo $ajax_response->get_json();
  }
	
	private function validate_preferences_form($form_inputs, $ajax_response)
	{
    if (strlen($form_inputs['latitude']) > 0)
    {
      if (strlen($form_inputs['longitude']) == 0)
        $ajax_response->add_error('preferences_latitude', null, null, 'Longitude is required if a latitude is specified.');
      else if (!is_numeric($form_inputs['latitude']))
        $ajax_response->add_error('preferences_latitude', null, null, 'Latitude must be numeric.');
      else if ($form_inputs['latitude'] < -90.0 || $form_inputs['latitude'] > 90.0)
        $ajax_response->add_error('preferences_latitude', null, null, 'Latitude must be between -90 and 90.');
    }
    
    if (strlen($form_inputs['longitude']) > 0)
    {
      if (strlen($form_inputs['latitude']) == 0)
        $ajax_response->add_error('preferences_longitude', null, null, 'Latitude is required if a longitude is specified.');
      else if (!is_numeric($form_inputs['longitude']))
        $ajax_response->add_error('preferences_longitude', null, null, 'Longitude must be numeric.');
      else if ($form_inputs['longitude'] < -180.0 || $form_inputs['longitude'] > 180.0)
        $ajax_response->add_error('preferences_longitude', null, null, 'Longitude must be between -180 and 180.');
    }
    
    if ($form_inputs['notify_option'] == 1)
    { 
      if (strlen($form_inputs['latitude']) == 0)
        $ajax_response->add_error(null, null, null, 'Geolocation required for notification by distance.');
      
      if (strlen($form_inputs['notify_max_distance']) == 0)
        $ajax_response->add_error(null, null, null, 'Max Distance is required for notification by distance.');
      else if (!is_numeric($form_inputs['notify_max_distance']) || $form_inputs['notify_max_distance'] < 1)
        $ajax_response->add_error('preferences_notify_max_distance', null, null, 'Max Distance must be a positive number.');     
    }

    // Validate email
    if ($form_inputs['notify_email'] == 1)
    {
      if (strlen($form_inputs['email_address']) == 0)
        $ajax_response->add_error('preferences_email_address', null, null, 'Email Address is required for notification by email.');        
      else if (Valid::email($form_inputs['email_address']) != 1)
        $ajax_response->add_error('preferences_email_address', null, null, 'Email Address is not valid.');
    }        

		if ($ajax_response->get_error_count() > 0)
			return false;
	
	  return true;
	}
}