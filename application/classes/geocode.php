<?php defined('SYSPATH') OR die('No direct access allowed.');

class Geocode
{
  public static function convert_address_to_geocode($address)
	{
    $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($address) . "&sensor=false";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $json = json_decode(curl_exec($ch));    
		curl_close ($ch);
		
    if (count($json->results) > 0)
    {
      return Geocode::build_result($json);
    }    
    
		return null;
	}
  
  public static function convert_geocode_to_address($latitude, $longitude)
	{
    $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . urlencode($latitude . ',' . $longitude) . "&sensor=false";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $json = json_decode(curl_exec($ch));    
		curl_close ($ch);
		
    if (count($json->results) > 0)
    {
      return Geocode::build_result($json);
    }    
    
		return null;
	}
  
  public static function build_result($json)
  {
    $formatted_address = $json->results[0]->formatted_address;
    $latitude = $json->results[0]->geometry->location->lat;
    $longitude = $json->results[0]->geometry->location->lng;
    $state = '';
    $country = '';
    
    foreach ($json->results[0]->address_components as $component)
    {
      if (in_array('country', $component->types))
      {
        $country = $component->long_name;
      }
      
      if (in_array('administrative_area_level_1', $component->types))
      {
        $state = $component->long_name;
      }
    }    

    return array('formatted_address' => $formatted_address,
                 'latitude' => $latitude,
                 'longitude' => $longitude,
                 'state' => $state,
                 'country' => $country);
  }
  
  public static function haversine($l1, $o1, $l2, $o2) 
  { 
    $l1 = deg2rad ($l1); 
    $sinl1 = sin ($l1); 
    $l2 = deg2rad ($l2); 
    $o1 = deg2rad ($o1); 
    $o2 = deg2rad ($o2); 
                 
    return (7926 - 26 * $sinl1) * asin (min (1, 0.707106781186548 * sqrt ((1 - (sin ($l2) * $sinl1) - cos ($l1) * cos ($l2) * cos ($o2 - $o1))))); 
  }

  public static function build_maps_url($address)
  {
    return 'http://maps.google.com/maps?q=' . urlencode($address);
  }
}


?>