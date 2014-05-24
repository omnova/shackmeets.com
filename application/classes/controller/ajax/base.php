<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Ajax_Base extends Controller
{
	function __construct($request, $response)
	{	
		parent::__construct($request, $response);	

		$this->current_user = Site::current_user();
	}
  
  public function before()
  {
    parent::before();   
  }
  
  public function after()
  {
    parent::after();
  }
	
	public function __call($method, $arguments)
	{
		// $content = new View('error');
		
		// $this->template->content .= $content;		
	} 
}