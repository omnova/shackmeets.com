<?php defined('SYSPATH') OR die('No Direct Script Access');

class Model_Message_Queue extends Model_Base
{
  protected $table_name = 'message_queue';
  protected $primary_key_col = 'message_queue_id';
  protected $columns = array('message_queue_id', 'message_type_id', 'message_recipients', 'message_subject', 'message_body', 'is_processed', 'meet_id', 'notification_reason_id', 'created_timestamp');

  // Columns
  public $message_queue_id;
  public $message_type_id;
  public $message_recipients;
  public $message_subject;
  public $message_body;
  public $is_processed;
  public $meet_id;
  public $notification_reason_id;
  public $created_timestamp; 
}