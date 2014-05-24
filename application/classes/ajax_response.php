<?php defined('SYSPATH') OR die('No direct access allowed.');

// SVN: svn checkout http://www.omnova.net/shackbattles .

class Ajax_Response
{
	public $success;
	public $data;
	private $errors;	
	
	function __construct()
	{
		$success = null;
		$data = null;
		$errors = array();
	}
	
	public function add_error($control_id = null, $original_value = null, $new_value = null, $message = null)
	{
		$this->errors[] = array('control_id' => $control_id, 'original_value' => $original_value, 'new_value' => $new_value, 'message' => $message);
	}
	
	public function get_error_count()
	{
		return count($this->errors);
	}
	
	public function get_json()
	{
		$json = array('success' => $this->success, 'data' => $this->data, 'errors' => $this->errors);
	
		return json_encode($json);
	}
}