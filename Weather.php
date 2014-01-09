<?php
	
	$airport = $_GET['a'];
	$callback = $_GET['callback'];
	$isIata = strlen($airport) === 3;
	$isIcao = strlen($airport) === 4;
	
	if(!$airport || !$callback || (!$isIata && !$isIcao))
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
	
	echo	$callback . '(' .
			substr(XMLToJSON::FromURL('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=' . $Airport->icao . '&hoursBeforeNow=24&mostRecent=true'), 0, -1) .
			',our_airport:' . $Airport->ToJSON() . '})';
	
?>
