<?php
	
	$airport = $_GET['a'];
	$callback = $_GET['callback'];
	$isIata = strlen($airport) === 3;
	$isIcao = strlen($airport) === 4;
	
	if(!$airport || !$callback || (!$isIata && !$isIcao))
	{
		die($callback . '({"error":"BadParams"})');
	}
	
	require_once('XmlToJson.php');
	require_once('IataToIcao.php');
	
	if($isIata)
	{
		$airport = IataToIcao::FromString($airport);
		
		if($airport === false)
		{
			die($callback . '({"error":"NoICAO"})');
		}
	}
	
	echo $callback . '(' . XmlToJson::FromUrl('http://aviationweather.gov/adds/dataserver_current/httpparam?dataSource=metars&requestType=retrieve&format=xml&stationString=' . $airport . '&hoursBeforeNow=24&mostRecent=true') . ')';
	
?>
