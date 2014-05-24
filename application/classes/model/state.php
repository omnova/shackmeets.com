<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_State extends Model_Base
{
  protected $table_name = 'state';
  protected $primary_key_col = 'state_id';
  protected $columns = array('state_id', 'country_id', 'name');

  // Columns
  public $state_id;
  public $country_id;
  public $name;
}