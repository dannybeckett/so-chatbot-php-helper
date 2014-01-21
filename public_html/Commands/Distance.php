<?php
	
	require_once('../Config.php');
	require_once('../Classes/Airport.php');
	
	$airport1 = $_GET['a1'];
	$airport2 = $_GET['a2'];
	$callback = $_GET['callback'];
	
	if(
		!$airport1 || !$airport2 || !$callback ||
		(strlen($airport1) !== 3 && strlen($airport1) !== 4) ||
		(strlen($airport2) !== 3 && strlen($airport2) !== 4)
	)
	{
		die($callback . '({"error":"BadParams"})');
	}
	
	$Airport1 = new Airport($airport1);
	$Airport2 = new Airport($airport2);
	
	if($Airport1->down || $Airport2->down)
	{
		die($callback . '({"error":"NoSQL"})');
	}
	
	if(!$Airport1->match)
	{
		die($callback . '({"error":"NoMatch1"})');
	}
	
	if(!$Airport2->match)
	{
		die($callback . '({"error":"NoMatch2"})');
	}
	
	$db = @new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	
	if($db->connect_error)
	{
		die($callback . '({"error":"NoSQL"})');
	}
	
	if($sql = $db->prepare('SELECT DISTANCE(?, ?)'))
	{
		$sql->bind_param('ss', $airport1, $airport2);
		$sql->execute();
		$sql->bind_result($distance);
		$sql->fetch();
		$sql->close();
	}
	
	$db->close();
	
	die($callback . '({"airport1":"' . $Airport1->name . '","airport2":"' . $Airport2->name . '","distance_km":' . $distance . '})');
	
?>