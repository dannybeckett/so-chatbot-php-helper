<?php
	
	$airport = $_GET['a'];
	
	if(!$airport)
	{
		die();
	}
	
	include 'XmlToJson.php';
	print $_GET['callback'] . '(' . XmlToJson::Parse('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=' . $airport . '&hoursBeforeNow=24&mostRecent=true') . ')';
	
?>
