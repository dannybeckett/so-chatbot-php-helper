<?php
	
	header('Content-Type: text/plain; charset=utf8');
	
	$airport = $_GET['a'];
	$mode = $_GET['m'];
	$callback = $_GET['callback'];
	$isIata = strlen($airport) === 3;
	$isIcao = strlen($airport) === 4;
	
	if(!$airport || !$mode || !$callback || (!$isIata && !$isIcao) || ($mode !== 'metars' && $mode !== 'tafs'))
	{
		die($callback . '({"error":"BadParams"})');
	}
	
	require_once('Classes/XMLToJSON.php');
	require_once('Classes/Airport.php');
	
	$Airport = new Airport($airport, $mode);
	
	if($isIata && $Airport->down)
	{
		die($callback . '({"error":"NoAirport"})');
	}
	
	else if($isIata && ($Airport->match === false || is_null($Airport->icao)))
	{
		die($callback . '({"error":"NoICAO"})');
	}
	
	$url = 'http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=' . $mode . '&requestType=retrieve&format=xml&stationString=' . $Airport->icao . '&hoursBeforeNow=24&mostRecent=true';
	
	$weather = XMLToJSON::FromURL($url);
	
	if($weather === false)
	{
		die($callback . '({"error":"NoWeather"})');
	}
	
	die($callback . '(' . substr($weather, 0, -1) . ',airport:' . $Airport->ToJSON() . '})');
	
?>