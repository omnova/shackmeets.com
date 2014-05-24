<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Preferences extends Controller_Base
{
	public function action_index()
	{    
    Site::require_login();
    
    $view = View::factory('preferences');   

		$this->template->content = $view;
	}  
}

?>