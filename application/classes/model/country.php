<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_Country extends Model_Base
{
  protected $table_name = 'country';
  protected $primary_key_col = 'country_id';
  protected $columns = array('country_id', 'name', 'has_states');

  // Columns
  public $country_id;
  public $name;
  public $has_states;
}