<?php

	class Airport
	{
		public $down = false;
		public $match;
		public $iata;
		public $icao;
		public $name;
		public $metar;
		public $taf;

		public function __construct($input)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://www.ourairports.com/search?q=' . $input);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$html = curl_exec($ch);
			
			if(curl_error($ch))
			{
				$this->down = true;
			}
			
			curl_close($ch);
			
			// Set some initial properties, in case we need to return, e.g.
			// http://chat.stackexchange.com/transcript/message/13128783#13128783
			
			if(strlen($input) === 3)
			{
				$this->iata = $input;
			}
			
			else if(strlen($input) === 4)
			{
				$this->icao = $input;
			}
			
			if($this->icao)
			{
				// Shorten the URL to reduce the chance of hitting the 500 char limit
				$this->metar = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://aviationweather.gov/adds/metars/?station_ids=' . $this->icao . '&std_trans=translated&chk_metars=on&chk_tafs=on'));
				$this->taf = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://aviationweather.gov/adds/tafs/?station_ids=' . $this->icao . '&std_trans=translated'));
			}
			
			if($this->down)
			{	
				$this->name = 'Unavailable';
				
				return;
			}
			
			$dom = new DOMDocument();
			@$dom->loadHTML($html);
			$path = new DOMXPath($dom);
			
			$h1 = trim($path->query("//h1")->item(0)->nodeValue);	// Also used to create $this->name
			
			$this->match = strpos($h1, "Search results for ") === false;
			
			if(!$this->match)
			{
				return;
			}
			
			$data = $path->query("//div[@class='column-in']/div")->item(0)->nodeValue;
			
			// LPL EGGP
			// KMMV MMV
			if(strpos($data, ' ') !== false)
			{
				$codes = explode(' ', $data);
				
				// Sometimes the codes are back-to-front - additional sanity check
				// http://chat.stackexchange.com/transcript/message/13080686#13080686
				for($i = 0; $i < 2; $i++)
				{
					if(strlen($codes[$i]) === 3)
					{
						$this->iata = $codes[$i];
					}
					
					else if(strlen($codes[$i]) === 4)
					{
						$this->icao = $codes[$i];
					}
				}
			}
			
			// FDKB
			else
			{
				$this->icao = $data;
			}
			
			$this->name = iconv('UTF-8', 'ISO-8859-1', $h1);
			
			// Shorten the URL to reduce the chance of hitting the 500 char limit
			$this->metar = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://aviationweather.gov/adds/metars/?station_ids=' . $this->icao . '&std_trans=translated&chk_metars=on&chk_tafs=on'));
			$this->taf = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://aviationweather.gov/adds/tafs/?station_ids=' . $this->icao . '&std_trans=translated'));
		}
		
		public function ToJSON()
		{
			// Remove ONLY null (keep false)
			// http://stackoverflow.com/questions/7741415/strip-null-values-of-json-object/
			
			function is_not_null($var)
			{
				return !is_null($var);
			}
			
			return json_encode(array_filter((array) $this, 'is_not_null'));
		}
	}
	
?>
