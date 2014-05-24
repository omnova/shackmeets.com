<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_Location extends Model_Base
{
  // Columns
  public $location_id;
  public $meet_id;
  public $name;
  public $address;
  public $state;
  public $country;
  public $latitude;
  public $longitude;
  public $start_date;
  public $start_time;
  public $order_id;
  
  protected $table_name = 'location';
  protected $primary_key_col = 'location_id';
  protected $columns = array('location_id', 'meet_id', 'name', 'address', 'state', 'country', 'latitude', 'longitude', 'start_date', 'start_time', 'order_id');
}