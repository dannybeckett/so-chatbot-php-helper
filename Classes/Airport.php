<?php
	
	class Airport
	{
		public $match;
		public $iata;
		public $icao;
		public $name;
		public $link;

		public function __construct($input)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://www.ourairports.com/search?q=' . $input);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$html = curl_exec($ch);
			curl_close($ch);
			
			$dom = new DOMDocument();
			$dom->loadHTML($html);
			$path = new DOMXPath($dom);
			
			$this->match = $path->query("//p[@class='info']")->length === 0;
			
			if($this->match)
			{
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
				
				$this->name = trim($path->query("//h1")->item(0)->nodeValue);
				
				// Shorten the URL to reduce the chance of hitting the 500 char limit
				$this->link = file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode('http://aviationweather.gov/adds/metars/?station_ids=' . $this->icao . '&std_trans=translated&chk_metars=on&chk_tafs=on'));
			}
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
