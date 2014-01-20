<?php
	
	// Database credentials
	define('DBHOST', 'localhost');
	define('DBUSER', '');
	define('DBPASS', '');
	define('DBNAME', '');
	
	// Error reporting
	error_reporting(E_ALL ^ E_NOTICE);
	
	// file_get_contents timeout
	ini_set('default_socket_timeout', 5);
	
	// Authorised IPs
	$whitelist = array(
		'127.0.0.1',	// Localhost
		'1.2.3.4.5',	// A user
		'6.7.8.9.10'	// Another
	);
	
	//////////////////////////////
	// Do not modify below here //
	//////////////////////////////
	
	header('Content-Type: text/plain; charset=utf8');
	
	if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist))
	{
		header('HTTP/1.1 403 Forbidden');
		die('You are not authorised to view this page!');
	}
	
?>