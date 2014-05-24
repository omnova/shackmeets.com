<?php defined('SYSPATH') or die('No direct script access.');

// */1 * * * * wget -q --delete-after http://www.shackmeets.com/service

// Shackmeets Daemon
class Controller_Service extends Controller_Base
{
	public function action_index()
	{
    $messages = $this->load_queue_messages();

    $post_made = false;
    
    foreach ($messages as $message)
    {
      switch ($message['message_type_id'])
      {
        case 1:
          $this->send_shackmessage($message['message_recipients'], $message['message_subject'], $message['message_body']);
          break;
          
        case 2:
          $this->send_email($message['message_recipients'], $message['message_subject'], $message['message_body']);
          break;

        case 3:
          if (!$post_made)
          {
            $this->post_announcement($message['message_body']);
            $post_made = true;
          }

          break;
      }      
      
      $this->mark_processed($message['message_queue_id']);
    }
  }
  
  private function load_queue_messages()
  {
    $sql = 'SELECT 
               m.message_queue_id AS "message_queue_id",
               m.message_type_id AS "message_type_id",
               m.message_recipients AS "message_recipients",
               m.message_subject AS "message_subject",
               m.message_body AS "message_body"         
            FROM message_queue m
            WHERE m.is_processed = 0
              AND (m.message_type_id = 1 OR m.message_type_id = 2 OR m.message_type_id = 3)
            ORDER BY m.message_queue_id';
     
    $query = DB::query(Database::SELECT, $sql);
    
    return $query->execute();  
  }
  
  private function send_email($recipient, $subject, $body)
  {
    $headers = 'From: noreply@shackmeets.com' . "\r\n" .
        'Reply-To: noreply@shackmeets.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($recipient, $subject, $body, $headers);
  }
  
  private function send_shackmessage($recipient, $subject, $body)
  {
    $chatty = new Chatty();
    $chatty->send_message($recipient, $subject, $body);  
  }

  private function post_announcement($body)
  {
    $chatty = new Chatty();
    $chatty->post(null, null, $body);
  }
  
  private function mark_processed($message_id)
  {
    $message = Model::factory('message_queue')->load_by_id($message_id);
    
    if ($message != null)
    {
      $message->is_processed = 1;
      $message->update();
    }
  }
}