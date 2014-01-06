<?php
	
	class IataToIcao
	{
		public function FromString($iata)
		{
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, 'http://www.ourairports.com/search?q=' . $iata);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_exec($ch);
			$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			curl_close($ch);
			
			$icao = end(explode('/', rtrim($url, '/')));
			
			if(strlen($icao) !== 4)
			{
				return false;
			}
			
			return $icao;
		}
	}
	
?>
