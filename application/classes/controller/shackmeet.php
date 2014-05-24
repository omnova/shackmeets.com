<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Shackmeet extends Controller_Base
{
  public function action_view()
  {
    $view = View::factory('shackmeetview');
  
    $meet_id = $this->request->param('id');    
    
    if ($meet_id == null)
      throw new Exception('Bad meet ID');
    
    $meet = Model::factory('meet')->load_by_id($meet_id);
    
    if ($meet == null || ($meet->status_id == 2 && ($this->current_user == null || $meet->organizer != $this->current_user->username)))
      $this->request->redirect('');

    $locations = $this->load_locations($meet_id);
    $attendees_definite = $this->load_definite_attendees($meet_id);
    $attendees_maybe = $this->load_maybe_attendees($meet_id);
    $definite_count = $this->get_attendee_count($attendees_definite);
    $maybe_count = $this->get_attendee_count($attendees_maybe); 
    
    if ($this->current_user != null)
    {
      $current_attendee = Model::factory('attendee')->load_by_meet_user($meet_id, $this->current_user->username); 
      
      if ($current_attendee == null)
        $current_attendee = Model::factory('attendee');
    }      
    else
    {
      $current_attendee = Model::factory('attendee');
      $current_attendee->attendance_option_id = 0;
      $current_attendee->extra_attendees = 0;
    }
      
    $attendance_options = $this->get_attendance_options($current_attendee->attendance_option_id);
    
    $view->set('shackmeet', $meet);
    $view->set('locations', $locations);
    $view->set('attendees_definite', $attendees_definite);
    $view->set('attendees_maybe', $attendees_maybe);
    $view->set('definite_count', $definite_count);
    $view->set('maybe_count', $maybe_count);
    $view->set('current_attendee', $current_attendee);
    $view->set('attendance_options', $attendance_options);
  
    $this->template->content = $view;
  }
  
  public function action_edit()
  {
    Site::require_login();
  
    $view = View::factory('shackmeetedit');
  
    $meet_id = $this->request->param('id');    
    
    if ($meet_id == null)
      throw new Exception('Bad meet ID');
    
    $meet = Model::factory('meet')->load_by_id($meet_id);
    
    if ($meet == null)
      throw new Exception('Meet does not exist');
    
    $locations = $this->load_locations($meet_id);
       
    $view->set('meet', $meet);
    $view->set('locations', $locations);
    
    $this->template->content = $view;  
  }
  
  public function action_create()
  {
    Site::require_login();
    
    $user = Model::factory('user')->load_by_id($this->current_user->username);
    
    if ($user->is_banned == 1)
      $this->request->redirect('');
  
    $view = View::factory('shackmeetcreate');
  
    $this->template->content = $view;
  }
  
  private function load_locations($meet_id)
  {
    $sql = 'SELECT 
              l.location_id AS "location_id",
              l.meet_id AS "meet_id",
              l.name AS "name",
              l.address AS "address",
              l.latitude AS "latitude",
              l.longitude AS "longitude",
              l.start_date AS "start_date",
              l.order_id AS "order_id"
            FROM location l
            WHERE l.meet_id = :meetid
            ORDER BY l.order_id';
     
    $query = DB::query(Database::SELECT, $sql);

    $query->parameters(array(
        ':meetid' => $meet_id,
    ));
    
    return $query->execute();
  }
  
  private function load_definite_attendees($meet_id)
  {
    $sql = 'SELECT 
               a.username AS "username",
               a.extra_attendees AS "extra_attendees"
            FROM attendee a
            WHERE a.attendance_option_id = 2
              AND a.meet_id = :meetid
            ORDER BY a.username';
     
    $query = DB::query(Database::SELECT, $sql);

    $query->parameters(array(
        ':meetid' => $meet_id,
    ));
    
    return $query->execute();
  }
    
  private function load_maybe_attendees($meet_id)
  {
    $sql = 'SELECT 
               a.username AS "username",
               a.extra_attendees AS "extra_attendees"
            FROM attendee a
            WHERE a.attendance_option_id = 1
              AND a.meet_id = :meetid
            ORDER BY a.username';
     
    $query = DB::query(Database::SELECT, $sql);

    $query->parameters(array(
        ':meetid' => $meet_id,
    ));
    
    return $query->execute();
  }  
  
  private function get_attendee_count($attendees)
  {
    $total = 0;
    
    foreach ($attendees as $attendee)
    {
      $total += 1 + $attendee['extra_attendees'];
    }
  
    return $total;
  }  
  
  private function get_attendance_options($attendance_option_id)
  {
    $attendance_options = array(0 => 'Not attending', 1 => 'Possibly attending', 2 => 'Definitely attending');
  
    $options = "";
    
    foreach ($attendance_options as $key => $value)
    {    
      $options .= '<option value="' . $key . '" ';
      
      if ($attendance_option_id == $key)
        $options .= 'selected="selected"';
        
      $options .= '>' . $value . '</option>' . "\n";
    }
        
    return $options;
  }
}

?>