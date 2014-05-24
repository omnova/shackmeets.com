<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Ajax_Shackmeet extends Controller_Ajax_Base
{
	function __construct($request, $response)
	{	
		parent::__construct($request, $response);	
	}
  
  // Create Shackmeet 
  
  public function action_create()
  {    
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$inputs = array();
		$inputs['title'] = $this->request->post('meet_title');
		$inputs['description'] = $this->request->post('meet_description');
		$inputs['post_announcement'] = $this->request->post('meet_post_announcement');

    if ($this->current_user->username == 'omnova')
      $inputs['send_notifications'] = $this->request->post('meet_send_notifications');
    else
      $inputs['send_notifications'] = 1;

		$inputs['rsvp_creator'] = $this->request->post('meet_rsvp_creator');
		$inputs['location_name'] = $this->request->post('meet_location_name');
		$inputs['location_address'] = $this->request->post('meet_location_address');
		$inputs['location_latitude'] = $this->request->post('meet_location_latitude');
		$inputs['location_longitude'] = $this->request->post('meet_location_longitude');
		$inputs['location_start_date'] = $this->request->post('meet_location_start_date');
		$inputs['location_start_time'] = $this->request->post('meet_location_start_time');
		
		if ($this->validate_create_meet_form($inputs, $ajax_response))
		{
      $meet = Model::factory('meet');      
      $meet->title = $inputs['title'];
      $meet->description = $inputs['description'];
      $meet->organizer = $this->current_user->username;
      $meet->post_announcement = $inputs['post_announcement'];

      if ($meet->post_announcement == 1)
        $meet->last_announcement_date = date("Y-m-d H:i:s");
      
      if (strlen($inputs['location_start_date']) > 0)
        $meet->start_date = $this->convert_date($inputs['location_start_date']);

      $meet->insert();

      if ($meet->saved())
      {
        $location = Model::factory('location');      
        $location->meet_id = $meet->meet_id;
        $location->name = $inputs['location_name'];
 
        $geocoding = Geocode::convert_address_to_geocode($inputs['location_address']);
       
        $location->address = $geocoding['formatted_address'];
        $location->state = $geocoding['state'];
        $location->country = $geocoding['country'];
        $location->latitude = $geocoding['latitude'];
        $location->longitude = $geocoding['longitude'];

        //
        
        if (strlen($inputs['location_start_date']) > 0)
          $location->start_date = $this->convert_date($inputs['location_start_date']);
                  
        // if (strlen($inputs['location_start_time']) > 0)
          // $location->start_time = $this->convert_time($inputs['location_start_time']);
          
        $location->order_id = 1;
        $location->insert();
        
        if ($location->saved())
        {
          if ($inputs['rsvp_creator'] == 1)
          {
            $attendee = Model::factory('attendee');
            $attendee->meet_id = $meet->meet_id;
            $attendee->username = $this->current_user->username;
            $attendee->attendance_option_id = 2;
            $attendee->extra_attendees = 0;
            $attendee->insert();
          }

          if ($inputs['send_notifications'] == 1 && Shackmeetsconfig::enableMessaging)
            $this->notify_on_add($meet, $location);
          
          if ($inputs['post_announcement'] == 1 && Shackmeetsconfig::enablePosting)
          {
            $this->post_announcement($meet, $location);
          }
        
          $ajax_response->data = $meet->meet_id;
          $ajax_response->success = true;
        }
        else
        {
          $ajax_response->add_error(null, null, null, 'Unable to create meet location.');
        }
      }
      else
      {
        $ajax_response->add_error(null, null, null, 'Unable to create meet.');
      }        
		}
		
		echo $ajax_response->get_json();
  }
  
  private function validate_create_meet_form($inputs, $ajax_response)
  {
    if (!$this->check_meet_creation_limit())
    {
      $ajax_response->add_error(null, null, null, 'You have hit the shackmeet rate limit!');	
      
      return false;
    }
    else
      return $this->validate_meet_form($inputs, $ajax_response);
  }
	
	private function validate_meet_form($inputs, $ajax_response)
	{  
		if (strlen($inputs['title']) == 0)
			$ajax_response->add_error('meet_title', null, null, 'Title is required.');		
		
		if (strlen($inputs['description']) == 0)
			$ajax_response->add_error('meet_description', null, null, 'Description is required.');		
	
		if (strlen($inputs['location_name']) == 0 )
			$ajax_response->add_error('meet_location_name', null, null, 'Venue Name is required.');	
      
    if (strlen($inputs['location_start_date']) == 0)
			$ajax_response->add_error('meet_location_start_date', null, null, 'Event Date is required.');	
    else if (!$this->validate_date($inputs['location_start_date']))
			$ajax_response->add_error('meet_location_start_date', null, null, 'Event Date must be in the format MM/DD/YYYY.');	
    else if ($this->is_past_date($inputs['location_start_date']))
			$ajax_response->add_error('meet_location_start_date', null, null, 'Event Date cannot be in the past.');	
       
     
    // if (strlen($inputs['location_start_time']) > 0 && !$this->validate_time($inputs['location_start_time']))
			// $ajax_response->add_error('meet_location_start_time', null, null, 'Start Time is not in the correct format.');	
      
    if (strlen($inputs['location_address']) == 0 )
			$ajax_response->add_error('location_address', null, null, 'Venue Address is required.');	
    else
    {      
      $geocoding = geocode::convert_address_to_geocode($inputs['location_address']);
      
      if ($geocoding == null)
        $ajax_response->add_error('meet_location_address', null, null, 'Venue Address must resolve to a geolocation.');	
      else if (strlen($geocoding['country']) == 0)
        $ajax_response->add_error('meet_location_address', null, null, 'Venue Address must be within a country.');	
    }

		return ($ajax_response->get_error_count() == 0);
	}
  
  // This will prevent users from creating a billion meets at once
  private function check_meet_creation_limit()
  {
    $recent_shackmeet_count = count($this->load_recent_shackmeet_count());
  
     return $recent_shackmeet_count < 2;
  }
  
  public function load_recent_shackmeet_count()
  {
    $sql = 'SELECT 
              m.meet_id AS "meet_id"
            FROM meet m
            WHERE m.organizer = :organizer
              AND (m.created_timestamp + INTERVAL 5 MINUTE) > NOW()';
     
    $query = DB::query(Database::SELECT, $sql);
    
    $query->parameters(array(
        ':organizer' => $this->current_user->username,
    ));
    
    return $query->execute();  
  }
  
  private function is_past_date($date)
  {
    $date_time = strtotime($date);
    $current_time = strtotime(date('Y-m-d'));
    
    return $current_time > $date_time;
  }
  
  // Edit Shackmeet
  
  public function action_edit()
  {    
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$inputs = array();
    $inputs['meet_id'] = $this->request->post('meet_id');
    $inputs['location_id'] = $this->request->post('location_id');
		$inputs['title'] = $this->request->post('meet_title');
		$inputs['description'] = $this->request->post('meet_description');
		$inputs['location_name'] = $this->request->post('meet_location_name');
		$inputs['location_address'] = $this->request->post('meet_location_address');
		$inputs['location_latitude'] = $this->request->post('meet_location_latitude');
		$inputs['location_longitude'] = $this->request->post('meet_location_longitude');
		$inputs['location_start_date'] = $this->request->post('meet_location_start_date');
		$inputs['location_start_time'] = $this->request->post('meet_location_start_time');
    
    $meet = Model::factory('meet')->load_by_id($inputs['meet_id']);
    
		if ($meet != null)
    {
      if ($this->validate_meet_form($inputs, $ajax_response))
      {
        $meet->title = $inputs['title'];
        $meet->description = $inputs['description']; 
      
        if (strlen($inputs['location_start_date']) > 0)
          $meet->start_date = $this->convert_date($inputs['location_start_date']);
        
        $meet->update();
        
        if ($meet->saved())
        {
          $location = Model::factory('location')->load_by_id($inputs['location_id']); 
          $location->name = $inputs['location_name'];

          $geocoding = Geocode::convert_address_to_geocode($inputs['location_address']);
         
          $location->address = $geocoding['formatted_address'];
          $location->state = $geocoding['state'];
          $location->country = $geocoding['country'];
          $location->latitude = $geocoding['latitude'];
          $location->longitude = $geocoding['longitude'];

          if (strlen($inputs['location_start_date']) > 0)
            $location->start_date = $this->convert_date($inputs['location_start_date']);
                    
          if (strlen($inputs['location_start_time']) > 0)
            $location->start_time = $this->convert_time($inputs['location_start_time']);
            
          $location->order_id = 1;
          $location->update();
          
          if (Shackmeetsconfig::enableMessaging)
            $this->notify_on_update($meet, $location);            
          
          $ajax_response->data = $meet->meet_id;
          $ajax_response->success = true;
        }
        else
        {
          $ajax_response->add_error(null, null, null, 'Unable to save meet.');
        }  
      }
		}
    else
    {
      $ajax_response->add_error(null, null, null, 'Bad meet ID.');
    }
		
		echo $ajax_response->get_json();
  }
  
  // Notifications
  
  // Notify users that a new meet has been created.
  private function notify_on_add($meet, $location)
  {    
    $subject = 'Shackmeet Announcement - ' . $meet->title;
    $body = $this->build_create_message($meet, $location);
  
    $notification_users = $this->load_users_to_notify();
    
    foreach ($notification_users as $user)
    {
      // Prevent shackmessages from being sent to yourself.  Unless you're me.  I get all the shackmessages.
      if ($user['username'] == $this->current_user->username && $user['username'] != 'omnova') 
        continue;
    
      if ($user['notify_option'] == 2 || ($user['notify_option'] == 1 && $this->eligible_for_notification($user['latitude'], $user['longitude'], $location->latitude, $location->longitude, $user['notify_max_distance'])))
      {  
        // Insert SM message into the queue
        if ($user['notify_shackmessage'] == 1)
        {
          $message = Model::factory('message_queue');
          $message->message_type_id = 1;
          $message->message_recipients = $user['username'];
          $message->message_subject = $subject;
          $message->message_body = $body;
          $message->meet_id = $meet->meet_id;
          $message->notification_reason_id = 1;
          $message->insert(); 
        }
          
        // Insert email message into the queue
        if ($user['notify_email'] == 1)
        {
          $message = Model::factory('message_queue');
          $message->message_type_id = 2;
          $message->message_recipients = $user['email_address'];
          $message->message_subject = $subject;
          $message->message_body = $body;
          $message->meet_id = $meet->meet_id;
          $message->notification_reason_id = 1;
          $message->insert();
        }
      }
    }
  }  
  
  // Notify all meet attendees that the meet has been updated
  private function notify_on_update($meet, $location)
  {
    $subject = 'Shackmeet Updated - ' . $meet->title;
    $body = $this->build_update_message($meet, $location);
    
    $this->notify_attendees($meet, 2, $subject, $body);
  }
  
  private function notify_attendees($meet, $notification_reason_id, $subject, $body)
  {
    $notification_users = $this->load_attendees_to_message($meet->meet_id);
    
    foreach ($notification_users as $user)
    {
      // Prevent shackmessages from being sent to yourself.  Unless you're me.  I get all the shackmessages.
      if ($user['username'] == $this->current_user->username && $user['username'] != 'omnova') 
        continue;
        
      // Insert SM message into the queue
      if ($user['notify_shackmessage'] == 1)
      {
        $message = Model::factory('message_queue');
        $message->message_type_id = 1;
        $message->message_recipients = $user['username'];
        $message->message_subject = $subject;
        $message->message_body = $body;
        $message->meet_id = $meet->meet_id;
        $message->notification_reason_id = $notification_reason_id;
        $message->insert(); 
      }
        
      // Insert email message into the queue
      if ($user['notify_email'] == 1)
      {
        $message = Model::factory('message_queue');
        $message->message_type_id = 2;
        $message->message_recipients = $user['email_address'];
        $message->message_subject = $subject;
        $message->message_body = $body;
        $message->meet_id = $meet->meet_id;
        $message->notification_reason_id = $notification_reason_id;
        $message->insert();
      }
    }
  }
  
  private function build_create_message($meet, $location)
  {
    $message = htmlentities($meet->organizer) . ' has suggested a shackmeet';
    
    if (strlen($location->start_date) > 0)
      $message .= ' occuring on ' . date('F j, Y', strtotime($location->start_date));
    
    if (strlen($location->state) > 0)
      $message .= ' in ' . $location->state . ', ' . $location->country . "!\n\n";
    else
      $message .= ' in ' . $location->country . "!\n\n";
    
    //$message .= "--------------------------------------------------------------------------------\n\n";
    $message .= Cleaner::strip_shacktags($meet->description) . "\n\n";
    //$message .= "--------------------------------------------------------------------------------\n\n";
    $message .= 'Go here for more information and to RSVP: ' . Url::site('shackmeet/view/' . $meet->meet_id, 'http');
    
    return $message;
  }
  
  private function build_update_message($meet, $location)
  {
    $message = htmlentities($meet->organizer) . ' has updated the details of the shackmeet "' . htmlentities($meet->title) . '" occurring on ' . date('F j, Y', strtotime($location->start_date)) . ".\n\n";
    //$message .= "--------------------------------------------------------------------------------\n\n";
    $message .= Cleaner::strip_shacktags($meet->description) . "\n\n";
    //$message .= "--------------------------------------------------------------------------------\n\n";
    $message .= 'Go here for more information: ' . Url::site('shackmeet/view/' . $meet->meet_id, 'http');
    
    return $message;
  }
  
  private function load_users_to_notify()
  {
    $sql = 'SELECT 
               u.username AS "username",
               u.latitude AS "latitude",
               u.longitude AS "longitude",
               u.notify_option AS "notify_option",
               u.notify_max_distance AS "notify_max_distance",
               u.notify_shackmessage AS "notify_shackmessage",
               u.notify_email AS "notify_email",
               u.email_address AS "email_address"
            FROM user u
            WHERE (u.notify_option = 2 OR (u.notify_option = 1 AND u.notify_max_distance IS NOT NULL))
              AND (u.notify_shackmessage = 1 OR u.notify_email = 1)
            ORDER BY u.username';
     
    $query = DB::query(Database::SELECT, $sql);
    
    return $query->execute();  
  }
    
  private function eligible_for_notification($user_latitude, $user_longitude, $meet_latitude, $meet_longitude, $max_distance)
  {
    if (strlen($user_latitude) == 0 || strlen($meet_latitude) == 0 || strlen($max_distance) == 0)
      return false;
      
    return (Geocode::haversine($user_latitude, $user_longitude, $meet_latitude, $meet_longitude) <= $max_distance);
  }
   
  private function load_attendees_to_message($meet_id)
  {
    $sql = 'SELECT 
              a.username AS "username",
              u.notify_shackmessage AS "notify_shackmessage",
              u.notify_email AS "notify_email",
              u.email_address AS "email_address"
            FROM attendee a
              INNER JOIN user u ON u.username = a.username
            WHERE a.meet_id = :meetid
              AND (u.notify_shackmessage = 1 OR u.notify_email = 1)
            ORDER BY u.username';
     
    $query = DB::query(Database::SELECT, $sql);

    $query->parameters(array(
        ':meetid' => $meet_id,
    ));
        
    return $query->execute();
  }
  
  // Post Announcement
  
  private function post_announcement($meet, $location, $mode = 'CREATE')
  {
    $parentID = null;
    $storyID = null;

    $body = '*[b{Shackmeet Announcement - ' . $meet->title . '}b]*' . "\n\n";
    $body .= 'y{' . $meet->organizer . '}y has suggested a shackmeet for *[' . date('F j, Y', strtotime($location->start_date)) . ']* in *[';

    if (strlen($location->state) > 0)
      $body .= $location->state . ', ' . $location->country;
    else
      $body .= $location->country;
    
    $body .= "]*!\n\n"; 
    $body .= htmlentities($meet->description) . "\n\n";
    $body .= 'Click here for all the details: ' . Url::site('shackmeet/view/' . $meet->meet_id, 'http');

    // Create a queue message for the announcement
    $message = Model::factory('message_queue');
    $message->message_type_id = 3;
    $message->message_body = $body;
    $message->meet_id = $meet->meet_id;
    $message->notification_reason_id = 1;
    $message->insert();
  }

  // Post Reminder

  public function action_post_reminder()
  {
    Site::require_login();

    $ajax_response = new Ajax_Response();
    $ajax_response->success = false;

    $inputs = array();
    $inputs['meet_id'] = $this->request->post('meet_id');
    $inputs['location_id'] = $this->request->post('location_id');

    $meet = Model::factory('meet')->load_by_id($inputs['meet_id']);
    $location = Model::factory('location')->load_by_id($inputs['location_id']);

    if ($meet->organizer != $this->current_user->username)
    {
      $ajax_response->add_error('', null, null, 'Cannot post a reminder to this shackmeet.');
    }
    else if ($meet->last_announcement_date != null && (strtotime($meet->last_announcement_date) > (time() - 18 * 60 * 60)))
    {
      $ajax_response->add_error('', null, null, 'A reminder can only be posted every 18 hours.');
    }
    else
    {
      $meet->last_announcement_date = date("Y-m-d H:i:s");
      $meet->update();

      if (Shackmeetsconfig::enablePosting)
        $this->post_reminder($meet, $location);

      $ajax_response->success = true;
    }

    echo $ajax_response->get_json();
  }

  private function post_reminder($meet, $location)
  {
    $parentID = null;
    $storyID = null;

    // Reminder
    $body = '*[g{Shackmeet Reminder - ' . $meet->title . '}g]*' . "\n\n";
    $body .= 'y{' . $meet->organizer . '}y has suggested a shackmeet for *[' . date('F j, Y', strtotime($location->start_date)) . ']* in *[';

    if (strlen($location->state) > 0)
      $body .= $location->state . ', ' . $location->country;
    else
      $body .= $location->country;

    $body .= "]*!\n\n";
    $body .= htmlentities($meet->description) . "\n\n";
    $body .= 'Click here for all the details: ' . Url::site('shackmeet/view/' . $meet->meet_id, 'http');

    // Create a queue message for the announcement
    $message = Model::factory('message_queue');
    $message->message_type_id = 3;
    $message->message_body = $body;
    $message->meet_id = $meet->meet_id;
    $message->notification_reason_id = 1;
    $message->insert();
  }
  
  // Attendance  
   
  public function action_set_attendance()
  {    
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

		$inputs = array();
		$inputs['meet_id'] = $this->request->post('attendance_meet_id');
		$inputs['attendance_option_id'] = $this->request->post('attendance_option');
		$inputs['extra_attendees'] = $this->request->post('attendance_extra_attendees');		
    
    if ($inputs['attendance_option_id'] == 0)
      $inputs['extra_attendees'] = 0;
		
		if ($this->validate_attendance_form($inputs, $ajax_response))
		{
      $meet = Model::factory('meet')->load_by_id($inputs['meet_id']);      
     
      if ($meet != null)
      {      
        $attendee = Model::factory('attendee')->load_by_meet_user($inputs['meet_id'], $this->current_user->username);
      
        if ($attendee == null)
        {
          $attendee = Model::factory('attendee');
          $attendee->meet_id = $inputs['meet_id'];
          $attendee->username = $this->current_user->username;
          $attendee->attendance_option_id = $inputs['attendance_option_id'];
          $attendee->extra_attendees = $inputs['extra_attendees'];
          $attendee->insert();
        }
        else
        {
          $attendee->meet_id = $inputs['meet_id'];
          $attendee->username = $this->current_user->username;
          $attendee->attendance_option_id = $inputs['attendance_option_id'];
          $attendee->extra_attendees = $inputs['extra_attendees'];
          $attendee->update();
        }
                
        if ($attendee->saved())
        {
          $ajax_response->success = true;
          $ajax_response->data = $attendee->meet_id;
        }
        else
        {
          $ajax_response->add_error(null, null, null, 'Unable to save attendance. Contact omnova.');
        }
      }
      else
      {
        $ajax_response->add_error(null, null, null, 'Meet does not exist!');
      }
		}
		
		echo $ajax_response->get_json();
  }
	
	private function validate_attendance_form($inputs, $ajax_response)
	{    
    // Hard coded
    $bullshit_fixed = array();
    $bullshit_fixed[99] = "Please, there aren't even 99 people who tolerate you, let alone like you.";
    $bullshit_fixed[98] = "Nice try, idiot.";
    $bullshit_fixed[97] = "Nope, try again.";
    $bullshit_fixed[96] = "Seriously, what did you think was going to happen?";    
    $bullshit_fixed[95] = "Fine, now you're just going to get random beratements.  Way to go, moron.";
    $bullshit_fixed[88] = "Why even bother, McFly?";
    $bullshit_fixed[69] = "You ain't ever gettin' that.";
    $bullshit_fixed[55] = "Halfway there! You got this you dolt.";
    $bullshit_fixed[30] = "Stay on target.";
    $bullshit_fixed[29] = "\"I can't shake him\"";
    $bullshit_fixed[28] = "Stay on target!";
    $bullshit_fixed[27] = "KABOOM";
    $bullshit_fixed[26] = "Use the force, Luke!";
    $bullshit_fixed[27] = "Let go, Luke!";
    $bullshit_fixed[26] = "PEW PEW";
    $bullshit_fixed[25] = "Explosions in space would actually be silent, but here they sound an awful lot like \"You're retarded.\"";
    $bullshit_fixed[23] = "Michael Jordan just slam dunked all over you ass.";
    $bullshit_fixed[11] = "Okay, you're getting warmer now";
    $bullshit_fixed[10] = "I can feel it.  You're almost there.";    
    
    
    // Random set
    $bullshit_random = array();
    $bullshit_random[0] = "Stop trying to pretend you have more than two friends.  That's being generous.";
    $bullshit_random[1] = "Your life is like a box of old crusty Hershey's chocolates. Ain't nobody want that shit.";
    $bullshit_random[2] = "Nope nope nope!";
    $bullshit_random[3] = "Okay fine you can bring that many wait nope. Idiot.";
    $bullshit_random[4] = "Mmmmmmmnope.";
    $bullshit_random[5] = "I don't know why you're even bothering with this.";
    $bullshit_random[6] = "Now you're just guessing.";
    $bullshit_random[7] = "I left this field two digits long for imbeciles like you.";
    $bullshit_random[8] = "You're either very persistent or very foolish.";
    $bullshit_random[9] = "I like you. Protesting against the system. Keep up the good work.";
    $bullshit_random[10] = "You should probably go see a doctor. This much stupid is probably contagious.";
    $bullshit_random[11] = "Good to see you haven't backed down. Keep up the good fight!";
    $bullshit_random[12] = "You're almost there.  I can feel it.";
    $bullshit_random[13] = "So close! This would have been allowed yesterday!";
    $bullshit_random[14] = "You really should take a break from this. It doesn't seem like you have many brain cells to spare.";
    $bullshit_random[15] = "Still going?";
    $bullshit_random[16] = "Come on, you can't even count that high.";
    $bullshit_random[17] = "Stop trying to pad the numbers. Everyone knows you're lame and not going.";
    $bullshit_random[18] = "I bet if you try one less than this it'll work.";
    $bullshit_random[19] = "Stop wasting my bandwidth, idiot.";
    $bullshit_random[20] = "If you've seen the same error twice now it's because you're a twat.";
    
		if (strlen($inputs['attendance_option_id']) == 0)
			$ajax_response->add_error('attendance_option_id', null, null, 'RSVP is a required field.');		
		
		if (strlen($inputs['extra_attendees']) > 0)
    {
      if (!is_numeric($inputs['extra_attendees']) || $inputs['extra_attendees'] < 0)      
        $ajax_response->add_error('extra_attendees', null, null, 'Additional Guests must be a non-negative number.');	
      else if ($inputs['extra_attendees'] > 9)
      {
        if (array_key_exists($inputs['extra_attendees'], $bullshit_fixed))
          $ajax_response->add_error('extra_attendees', null, null, $bullshit_fixed[$inputs['extra_attendees']]);	
        else 
          $ajax_response->add_error('extra_attendees', null, null, $bullshit_random[rand(0, 20)]);	
      }
    }

		if ($ajax_response->get_error_count() > 0)
			return false;
	
	  return true;
	}
  
  // Validation
  
  private function validate_date($date)
  {
    $pattern = '/^(([1-9])|(0[1-9])|(1[0-2]))\/(([0-9])|([0-2][0-9])|(3[0-1]))\/(([0-9][0-9])|([1-2][0,9][0-9][0-9]))$/';
    
    $count = preg_match($pattern, $date);
    
    return $count == 1;
  }
  
  private function convert_date($date)
  {
    return date('Y-m-d', strtotime($date));
  }
  
  private function validate_time($time)
  {
    $pattern = '^(([1-9])|(0[1-9])|(1[0-2]))\/(([0-9])|([0-2][0-9])|(3[0-1]))\/(([0-9][0-9])|([1-2][0,9][0-9][0-9]))$';
    
    $count = preg_match($pattern, $time);
    
    return true; //$count == 1;
  }
  
  private function convert_time($time)
  {
    return $time; //date('Y-m-d', strtotime($date));
  }
  
  // Cancel a Shackmeet
  public function action_cancel()
  {
    Site::require_login();
    
		$ajax_response = new Ajax_Response();
		$ajax_response->success = false;

    $inputs = array();
		$inputs['meet_id'] = $this->request->post('cancel_meet_id');
		$inputs['location_id'] = $this->request->post('cancel_location_id');
  
    $meet = Model::factory('meet')->load_by_id($inputs['meet_id']);    
    $location = Model::factory('location')->load_by_id($inputs['location_id']);
    
    if ($meet->organizer != $this->current_user->username)
    {
      $ajax_response->add_error('', null, null, 'Cannot cancel this shackmeet.');	
    }
    else
    {  
      $meet->status_id = 1;
      $meet->update();
      
      $subject = 'Shackmeet Canceled - ' . $meet->title;
      $body = $this->build_cancel_message($meet, $location);

      if (Shackmeetsconfig::enableMessaging)
        $this->notify_attendees($meet, 3, $subject, $body);
      
      $ajax_response->success = true;
    }
   
    echo $ajax_response->get_json();
  }
  
  private function build_cancel_message($meet, $location)
  {
    $message = 'This message is to inform you that ' . htmlentities($meet->organizer) . ' has canceled the shackmeet ' . $meet->title;     
    $message .= ', which was to take place on ' . date('F j, Y', strtotime($location->start_date)) . '.';
    
    return $message;
  }  
}