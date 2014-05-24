<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Ajax_Geocode extends Controller_Ajax_Base
{
	function __construct($request, $response)
	{	
		parent::__construct($request, $response);	
	}
  
  // Convert address to geocode
  
  public function action_convert_address_to_geocode()
  { 
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$form_inputs = array();
		$form_inputs['address'] = $this->request->query('address');

		if ($this->validate_convert_address_to_geocode($form_inputs, $ajax_response))
		{
			$result = Geocode::convert_address_to_geocode($form_inputs['address']);
      $ajax_response->data = json_encode($result);      
      $ajax_response->success = true;     
		}
		
		echo $ajax_response->get_json();
  }
	
	private function validate_convert_address_to_geocode($form_inputs, $ajax_response)
	{
		if (strlen($form_inputs['address']) == 0)
			$ajax_response->add_error(null, null, null, 'Address is a required field.');		
		
		if ($ajax_response->get_error_count() > 0)
			return false;
	
	  return true;
	}  
  
  // Convert geocode to address
  
  public function action_convert_geocode_to_address()
  {    
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$form_inputs = array();
		$form_inputs['latitude'] = $this->request->query('latitude');
    $form_inputs['longitude'] = $this->request->query('longitude');

		if ($this->validate_convert_geocode_to_address($form_inputs, $ajax_response))
		{
			$result = Geocode::convert_geocode_to_address($form_inputs['latitude'], $form_inputs['longitude']);
      $ajax_response->data = json_encode($result);      
      $ajax_response->success = true;     
		}
		
		echo $ajax_response->get_json();
  }
	
	private function validate_convert_geocode_to_address($form_inputs, $ajax_response)
	{
		if (strlen($form_inputs['latitude']) == 0)
			$ajax_response->add_error(null, null, null, 'Latitude is a required field.');	
      
		if (strlen($form_inputs['longitude']) == 0)
			$ajax_response->add_error(null, null, null, 'Longitude is a required field.');			
		
		if ($ajax_response->get_error_count() > 0)
			return false;
	
	  return true;
	}  
}