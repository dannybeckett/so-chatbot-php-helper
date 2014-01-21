<?php
	
	class TinyURL
	{
		public static function Get($url)
		{
			return file_get_contents('http://tinyurl.com/api-create.php?url=' . urlencode($url));
		}
	}
	
?>