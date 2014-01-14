<?php
	
	// Error reporting
	error_reporting(E_ALL ^ E_NOTICE);
	
	// file_get_contents timeout
	ini_set('default_socket_timeout', 5);
	
	// Content type & character set
	header('Content-Type: text/plain; charset=utf8');
	
?>