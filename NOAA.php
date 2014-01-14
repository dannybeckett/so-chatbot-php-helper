<?php
  $mode = $_GET['m'];
  $id = $_GET['a'];

  if (strlen($id) == 3) {
    require_once('Classes/Airport.php');
    $apt = new Airport($id);
    $id = $apt->icao;
  }

  $id = strtoupper($id);

  $url = null;

  if ($mode == 'metar') {
    $url = "http://weather.noaa.gov/pub/data/observations/metar/stations/$id.TXT";
  } else if ($mode == 'taf') {
    $url = "http://weather.noaa.gov/pub/data/forecasts/taf/stations/$id.TXT";
  }
  
  if (! is_null($url)) {
    $fn = fopen ($url, 'r');
    if ($fn === false) {
      header('HTTP/1.1 404 Not Found');
      header('Content-Type: text/plain');
      echo "No data found for $id";
    } else {
      header('HTTP/1.1 200 Ok');
      header('Content-Type: text/plain');
      fpassthru($fn);
      fclose($fn);
      exit;
    }
  } else {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: text/plain');
    echo 'No data';
  }
?>
