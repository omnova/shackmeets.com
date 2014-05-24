<?php defined('SYSPATH') OR die('No direct access allowed.');

// SVN: svn checkout http://shacktourney.omnova.net/development .

class Controller_Base extends Controller_Template 
{
	public $template = 'site';
  public $current_user = null;
	
	function __construct($request, $response)
	{	
		parent::__construct($request, $response);	
	}
  
  public function before()
  {
    parent::before();
  
    $this->current_user = Site::current_user();
  
    View::bind_global('current_user', $this->current_user);    
    
		$this->template->content = '';	      
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