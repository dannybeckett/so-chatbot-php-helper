<?php
	
	ini_set('default_socket_timeout', 5);
	
	class XMLToJSON
	{
		public static function FromURL($url)
		{
			$xml = @file_get_contents($url);
			
			if($xml === false)
			{
				return false;
			}
			
			$xml = str_replace(array("\n", "\r", "\t"), '', $xml);
			$xml = trim(str_replace('"', "'", $xml));
			return json_encode(simplexml_load_string($xml));
		}
	}
	
?>