<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api extends Controller_Ajax_Base
{
  public function action_shackmeetspost()
  {
    $upcoming_shackmeets = $this->load_upcoming_shackmeets();

    $post_text = '';

    foreach ($upcoming_shackmeets as $shackmeet)
    {
      $post_text .= $this->build_shackmeet_text($shackmeet) . "\n\n";
    }

    echo trim($post_text);
  }

  private function load_upcoming_shackmeets()
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
              AND l.start_date >= DATE(NOW())
            ORDER BY is_dated, l.start_date, l.start_time';

    $query = DB::query(Database::SELECT, $sql);

    return $query->execute();
  }

  private function build_shackmeet_text($shackmeet)
  {
    $body = '*[b{' . $shackmeet['title'] . '}b]*' . "\n";
    $body .= 'Suggested by y{' . $shackmeet['organizer'] . '}y for *[' . date('F j, Y', strtotime($shackmeet['start_date'])) . ']* in *[';

    if (strlen($shackmeet['state']) > 0)
      $body .= $shackmeet['state'] . ', ' . $shackmeet['country'];
    else
      $body .= $shackmeet['country'];

    $body .= "]*\n";
    $body .= 'Click here for all the details: ' . Url::site('shackmeet/view/' . $shackmeet['meet_id'], 'http');

    return $body;
  }
  
  public function action_php()
  {
    phpinfo();
  }
}

?>
