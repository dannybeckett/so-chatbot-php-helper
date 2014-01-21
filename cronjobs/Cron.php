<?php
	
	require_once('Config.php');
	
	$db = @new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
	
	if($db->connect_error)
	{
		die('Failed to connect to the database! Error #' . $db->connect_errno . ' - ' . $db->connect_error);
	}
	
	$csv = tempnam('tmp', 'csv');
	$get = copy('http://www.ourairports.com/data/airports.csv', $csv);
	
	if(!$get)
	{
		$db->close();
		die('Failed to download airports.csv!');
	}
	
	$csv = str_replace('\\', '/', $csv);
	
	// Don't use `DELIMITER $$` and `DELIMITER ;` around functions - add a `;` after `END`
	$sql = <<<eoq
DROP FUNCTION IF EXISTS	`distance`;
DROP TABLE IF EXISTS	`airports`;

CREATE FUNCTION `distance` (`airport1` VARCHAR(4), `airport2` VARCHAR(4))
RETURNS DOUBLE
BEGIN
	SELECT	X(`location`),
			Y(`location`)
	INTO	@lat1,
			@lon1
	FROM	`airports`
	WHERE	`ident` = `airport1`
	OR		`iata` = `airport1`;
	
	SELECT	X(`location`),
			Y(`location`)
	INTO	@lat2,
			@lon2
	FROM	`airports`
	WHERE	`ident` = `airport2`
	OR		`iata` = `airport2`;
	
	RETURN (6371.009 * ACOS(COS(RADIANS(@lat1)) * COS(RADIANS(@lat2)) * COS(RADIANS(@lon1) - RADIANS(@lon2)) + SIN(RADIANS(@lat1)) * SIN(RADIANS(@lat2))));
END;

CREATE TABLE `airports` (
	`id`		MEDIUMINT UNSIGNED	NOT NULL,
	`ident`		VARCHAR(15)			NOT NULL,
	`iata`		VARCHAR(3)			NULL,
	`type`		ENUM('balloonport', 'closed', 'heliport', 'large_airport', 'medium_airport', 'seaplane_base', 'small_airport')
									NOT NULL,
	`name`		VARCHAR(100)		NULL,
	`location`	POINT				NOT NULL,
	`elevation`	SMALLINT			NULL,
	
	PRIMARY KEY					(`id`),
	UNIQUE INDEX `id_UNIQUE`	(`id` ASC),
	UNIQUE INDEX `ident_UNIQUE`	(`ident` ASC)
) ENGINE = InnoDB;

LOAD DATA LOCAL INFILE '$csv'
	INTO TABLE airports
	CHARACTER SET UTF8
	FIELDS
		TERMINATED BY ','
		OPTIONALLY ENCLOSED BY '"'
	LINES
		TERMINATED BY '\n'
	IGNORE 1 LINES
	(id, ident, type, @name, @lat, @lon, @elevation, @x, @x, @x, @x, @x, @x, @iata, @x, @x, @x, @x)
	SET
		elevation	= NULLIF(@elevation, ''),
		iata		= NULLIF(@iata, ''),
		name		= NULLIF(@name, ''),
		location	= POINT(@lat, @lon);
eoq;
	
	if($db->multi_query($sql))
	{
		do
		{
			if(!$db->next_result())
			{
				$errno = $db->errno;
				$error = $db->error;
				break;
			}
		}
		while($db->more_results());
	}
	
	$db->close();
	
	unlink($csv);
	
	if(isset($error))
	{
		die('Error #' . $errno . ' - ' . $error);
	}
	
	die('Updated!');
	
?>