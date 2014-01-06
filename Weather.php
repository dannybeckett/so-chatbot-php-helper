<?php
	
	$airport = $_GET['a'];
	$callback = $_GET['callback'];
	
	if(!$airport || !$callback || strlen($airport) !== 4)
	{
		die();
	}
	
	include 'XmlToJson.php';
	echo $callback . '(' . XmlToJson::FromUrl('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=' . $airport . '&hoursBeforeNow=24&mostRecent=true') . ')';
	
?>
