<?php
	
	// This breaks Weather.php - not really sure why :S
	//require_once('../Config.php');
	require_once('TinyURL.php');
	
	class Airport
	{
		public $down = false;
		public $match;
		public $iata;
		public $icao;
		public $name = 'Missing';
		public $metar;
		public $taf;

		public function __construct($input, $weathermode = null)
		{
			if(strlen($input) === 3)
			{
				$this->iata = $input;
			}
			
			elseif(strlen($input) === 4)
			{
				$this->icao = $input;
			}
			
			$db = @new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
			
			if($db->connect_error)
			{
				$this->down = true;
				return;
			}
			
			if($result = $db->query("SELECT * FROM iata_icao WHERE iata = '$input' OR icao = '$input'"))
			{
				$row = $result->fetch_assoc();
				
				if($row === null)
				{
					$this->match = false;
				}
				
				else
				{
					$this->match = true;
					
					$this->iata = $row['iata'];
					$this->icao = $row['icao'];
					$this->name = $row['name']; //iconv('UTF-8', 'ISO-8859-1', $row['name'])
					
					$result->free();
				}
			}
			
			else
			{
				die('Error #' . $db->errno . ' - ' . $db->error);
			}
			
			// Shorten the URL to reduce the chance of hitting the 500 char limit
			if($weathermode !== null)
			{
				$this->{rtrim($weathermode, 's')} = TinyURL::Get('http://aviationweather.gov/adds/' . $weathermode . '/?station_ids=' . $this->icao . '&std_trans=translated' . ($weathermode === 'metars' ? '&chk_metars=on&chk_tafs=on' : ''));
			}
			
			$db->close();
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