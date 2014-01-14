<?php
	
	// Error reporting
	error_reporting(E_ALL ^ E_NOTICE);
	
	// file_get_contents timeout
	ini_set('default_socket_timeout', 5);
	
	// Content type & character set
	header('Content-Type: text/plain; charset=utf8');
	
	// Authorised IPs in SHA512
	$whitelist = array(
		'8763310358d0d03e28a91db563ec1d68ad2003be95a5b30c80ba5719273e7ea144175eace3112f58a26dea0613b455c65b78319ef4376df1ce51a83490cc8e9d',	// Bot
		'd00229fe19f985a56447137adfcb7d9921dbda52a398bc129ddef422cd4f0d386653a23d005efd188ad89c0bf328f4e385f7ab35cfbc82522c841262a3dd323e'	// Danny
	);
	
	//////////////////////////////
	// Do not modify below here //
	//////////////////////////////
	
	if(!in_array(hash('sha512', $_SERVER['REMOTE_ADDR']), $whitelist))
	{
		http_response_code(403);
		exit;
	}
	
?>