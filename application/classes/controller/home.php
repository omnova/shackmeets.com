<?php defined('SYSPATH') or die('No direct script access.');

class Controller_home extends Controller_Base
{
	public function action_index()
	{  
    $view = View::factory('home');
  
    $upcoming_shackmeets = $this->load_upcoming_shackmeets($this->current_user);   
  
    $view->set('upcoming_shackmeets', $upcoming_shackmeets);
      
		$this->template->content = $view;
	}

  private function load_upcoming_shackmeets($current_user, $only_local = false)
  {
    $sql = 'SELECT
              m.meet_id AS "meet_id",
							m.title AS "title",
              m.organizer AS "organizer",
              l.name AS "location",
              l.address AS "address",
              l.state AS "state",
              l.country AS "country",
              DATE_FORMAT(l.start_date, \'%m/%d/%Y\') AS "start_date",
              ( SELECT COUNT(1) + SUM(a1.extra_attendees) FROM attendee a1 WHERE a1.meet_id = m.meet_id AND a1.attendance_option_id = 2) AS "attendee_count_definite",
              ( SELECT COUNT(1) + SUM(a2.extra_attendees) FROM attendee a2 WHERE a2.meet_id = m.meet_id AND a2.attendance_option_id = 1) AS "attendee_count_maybe",
              CASE
                WHEN l.start_date IS NULL THEN 0
                ELSE 1
              END AS "is_dated"
            FROM meet m
              INNER JOIN user u ON u.username = m.organizer
              INNER JOIN location l ON l.meet_id = m.meet_id AND l.order_id = 1
            WHERE m.status_id = 0
              AND l.start_date >= DATE(NOW())';
     
    if ($current_user != null && $only_local)
    {
      $sql .= ' AND u.country_id = m.country_id AND u.state_id = m.state_id';
    }
    
    $sql .= ' ORDER BY is_dated, l.start_date, l.start_time'; 
   
    $query = DB::query(Database::SELECT, $sql);
    
    return $query->execute();
  }  
}

?>
