<?php
  $mode = $_GET['m'];
  $id = $_GET['a'];
  $jsonp = $_GET['callback'];

  header('Content-Type: text/plain');

  if (strlen($id) == 3) {
    require_once('Classes/Airport.php');
    $apt = new Airport($id);
    $id = $apt->icao;
    if ($Airport->down) {
      header('HTTP/1.1 403 Service Unavailable');
      die($jsonp.'({"error":"NoAirport"})');
    } else if ($Airport->match === false || is_null($Airport->icao)) {
      header('HTTP/1.1 404 Not Found');
      die($jsonp.'({"error":"NoICAO"})');
    }
  }

  $id = strtoupper($id);

  $url = null;

  if ($mode == 'metars') {
    $url = "http://weather.noaa.gov/pub/data/observations/metar/stations/$id.TXT";
  } else if ($mode == 'tafs') {
    $url = "http://weather.noaa.gov/pub/data/forecasts/taf/stations/$id.TXT";
  }
  
  if (! is_null($url)) {
    $data = file_get_contents ($url);
    if ($data === false) {
      header('HTTP/1.1 404 Not Found');
      die($jsonp.'({"error":"NoWeather"})');
    } else {
      header('HTTP/1.1 200 Ok');
      die($jsonp.'({"raw_text":'.json_encode(trim($data)).'})');
    }
  } else {
    header('HTTP/1.1 400 Bad Request');
    die($jsonp.('{"error":"BadParams"}'));
  }
?>
