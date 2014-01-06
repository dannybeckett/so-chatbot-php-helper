<?php
	
	$airport = $_GET['a'];
	
	if(!$airport || strlen($airport) !== 4)
	{
		die();
	}
	
	include 'XmlToJson.php';
	print $_GET['callback'] . '(' . XmlToJson::FromUrl('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=' . $airport . '&hoursBeforeNow=24&mostRecent=true') . ')';
	
?>
