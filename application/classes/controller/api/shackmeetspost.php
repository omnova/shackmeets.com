<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Api_Shackmeetspost extends Controller_Ajax_Base
{
  function __construct($request, $response)
  {
    parent::__construct($request, $response);
  }

  // Create Shackmeet

  public function action_index()
  {
    $ch = curl_init();
    $useragent="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20080704/3.0.0.1";

    // Set some standard cURL options
    curl_setopt($ch, CURLOPT_HEADER, 0); //important, turn off header
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_URL, 'http://www.shackmeets.com/api/shackmeetspost');
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result;
  }
}

?>