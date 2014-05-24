<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_Attendee extends Model_Base
{
  // Columns
  public $attendee_id;
  public $username;
  public $meet_id;
  public $attendance_option_id;
  public $extra_attendees; 
  
  protected $table_name = 'attendee';
  protected $primary_key_col = 'attendee_id';
  protected $columns = array('attendee_id', 'username', 'meet_id', 'attendance_option_id', 'extra_attendees');

  public function load_by_meet_id($meet_id)
	{
    return $this->load(array('meet_id' => $meet_id));
	}
  
  public function load_by_meet_user($meet_id, $username)
	{
    $result = $this->load(array('username' => $username, 'meet_id' => $meet_id));
    
    if (count($result) == 1)
      return $result[0];
    else
      return null;
	}
}