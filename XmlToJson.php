<?php
	
	// http://lostechies.com/seanbiefeld/2011/10/21/simple-xml-to-json-with-php/
	
	class XmlToJson
	{
		public function FromUrl($url)
		{
			$xml = file_get_contents($url);
			$xml = str_replace(array("\n", "\r", "\t"), '', $xml);
			$xml = trim(str_replace('"', "'", $xml));
			return json_encode(simplexml_load_string($xml));
		}
	}
	
?>
