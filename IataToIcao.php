<?php
	
	$airport = $_GET['a'];
	$callback = $_GET['callback'];
	
	if(!$airport || !$callback || strlen($airport) !== 3)
	{
		die();
	}
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, 'http://www.ourairports.com/search?q=' . $airport);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	curl_exec($ch);
	$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);
	
	$iata = end(explode('/', rtrim($url, '/')));
	
	if(strlen($iata) !== 4)
	{
		die('-1');
	}
	
	echo $callback . '({"icao":"' . $iata . '"})';
	
?>
