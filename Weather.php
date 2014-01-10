<?php
	
	header('Content-Type: application/json; charset=utf-8');
	
	$airport = $_GET['a'];
	$mode = $_GET['m'];
	$callback = $_GET['callback'];
	$isIata = strlen($airport) === 3;
	$isIcao = strlen($airport) === 4;
	
	if(!$airport || !$mode || !$callback || (!$isIata && !$isIcao) || ($mode != 'metars' && $mode != 'tafs'))
	{
		die($callback . '({"error":"BadParams"})');
	}
	
	require_once('Classes/XMLToJSON.php');
	require_once('Classes/Airport.php');
	
	$Airport = new Airport($airport);
	
	if($isIata && is_null($Airport->icao))
	{
		die($callback . '({"error":"NoICAO"})');
	}
	
	$url = 'http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=' . $mode . '&requestType=retrieve&format=xml&stationString=' . $Airport->icao . '&hoursBeforeNow=24&mostRecent=true';
	
	echo $callback . '(' . substr(XMLToJSON::FromURL($url), 0, -1) . ',airport:' . $Airport->ToJSON() . '})';
	
?>
