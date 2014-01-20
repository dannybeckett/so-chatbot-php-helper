<?php
	
	require_once('../Config.php');
	require_once('../Classes/XMLToJSON.php');
	require_once('../Classes/Airport.php');
	
	$airport = $_GET['a'];
	$mode = $_GET['m'];
	$source = $_GET['s'];
	$callback = $_GET['callback'];
	$isIata = strlen($airport) === 3;
	$isIcao = strlen($airport) === 4;
	
	if(
		!$airport || !$mode || !$source || !$callback ||
		
		(!$isIata && !$isIcao) ||
		($mode !== 'metars' && $mode !== 'tafs') ||
		($source !== 'adds' && $source !== 'noaa')
	)
	{
		die($callback . '({"error":"BadParams"})');
	}
	
	$Airport = new Airport($airport, $mode);
	
	if($isIata)
	{
		if($Airport->down)
		{
			die($callback . '({"error":"NoSQL"})');
		}
	}
	
	if(is_null($Airport->icao))
	{
		die($callback . '({"error":"NoICAO"})');
	}
	
	$noaa = array(
		'metars'	=>	'observations/metar',
		'tafs'		=>	'forecasts/taf'
	);
	
	$get = array(
		'adds'	=>	XMLToJSON::FromURL('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=' . $mode .
						'&requestType=retrieve&format=xml&stationString=' . $Airport->icao . '&hoursBeforeNow=24&mostRecent=true'),
		
		'noaa'	=>	@file_get_contents('http://weather.noaa.gov/pub/data/' . $noaa[$mode] . '/stations/'. $Airport->icao . '.TXT')
	);
	
	$err = array(
		'adds'	=>	'NoWeather',
		'noaa'	=>	'NoFile'
	);
	
	$weather = $get[$source];
	
	if($weather === false)
	{
		die($callback . '({"error":"' . $err[$source] . '"})');
	}
	
	switch($source)
	{
		case 'adds':	die($callback . '(' . substr($weather, 0, -1) . ',"airport":' . $Airport->ToJSON() . '})');
		case 'noaa':	die($callback . '({"raw_text":' . json_encode('    ' . trim(substr($weather, strpos($weather, "\n") + 1))) . '})');
	}
	
?>