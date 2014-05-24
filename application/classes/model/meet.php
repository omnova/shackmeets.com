<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_Meet extends Model_Base
{
  // Columns
  public $meet_id;
  public $title;
  public $description;
  public $organizer;
  public $created_timestamp;
  public $changed_timestamp;
  public $start_date;
  public $post_announcement;
  public $last_announcement_date;
  public $status_id;
  
  protected $table_name = 'meet';
  protected $primary_key_col = 'meet_id';
  protected $columns = array('meet_id', 'title', 'description', 'organizer', 'created_timestamp', 'changed_timestamp', 'start_date', 'post_announcement', 'last_announcement_date', 'status_id'); 
}