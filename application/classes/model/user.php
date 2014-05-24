<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_User extends Model_Base
{
  // Columns
  public $username;
  public $session_key;
  public $latitude;
  public $longitude;
  public $time_zone;
  public $notify_max_distance;
  public $notify_option;
  public $notify_shackmessage;
  public $notify_email;
  public $email_address;
  public $is_banned;
  
  protected $table_name = 'user';
  protected $primary_key_col = 'username';
  protected $columns = array('username', 'session_key', 'latitude', 'longitude', 'time_zone', 'notify_max_distance', 'notify_option', 'notify_shackmessage', 'notify_email', 'email_address', 'is_banned');
  protected $auto_key = false;

  public function load_by_session_key($session_key) 
	{
    $result = $this->load(array('session_key' => $session_key));
    
    if (count($result) == 1)
      return $result[0];
    else
      return null;
	}

}