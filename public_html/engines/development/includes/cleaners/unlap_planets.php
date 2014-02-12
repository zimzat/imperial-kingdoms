<?php
	function unlap_planets()
	{
		$db_query = "
			SELECT 
				a.`planet_id` AS 'planet_a', 
				b.`planet_id` AS 'planet_b' 
			FROM `planets` a 
			LEFT JOIN `planets` b 
				USING ( `round_id` , `quadrant_id` , `starsystem_id` , `x` , `y` ) 
			WHERE a.`planet_id` != b.`planet_id`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			if ((!empty($updated[$db_row['planet_a']]) && $updated[$db_row['planet_a']] == $db_row['planet_b']) ||
				(!empty($updated[$db_row['planet_b']]) && $updated[$db_row['planet_b']] == $db_row['planet_a']))
			{
				continue;
			}
			
			$updated[$db_row['planet_a']] = $db_row['planet_b'];
			
			move_planet($db_row['planet_a']);
		}
	}
	
	function move_planet($planet_id)
	{
		$coord = unserialize('a:7:{i:0;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:1;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:2;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:3;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:4;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:5;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}i:6;a:7:{i:0;i:0;i:1;i:1;i:2;i:2;i:3;i:3;i:4;i:4;i:5;i:5;i:6;i:6;}}');
		
		$db_query = "
			SELECT 
				a.`x` AS 'x', 
				a.`y` AS 'y' 
			FROM `planets` a 
			LEFT JOIN `planets` b 
				USING ( `round_id` , `quadrant_id`, `starsystem_id` ) 
			WHERE
				a.`planet_id` != b.`planet_id` AND 
				b.`planet_id` = '" . $planet_id . "'";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			unset($coord[$db_row['x']][$db_row['y']]);
			if (empty($coord[$db_row['x']]))
			{
				unset($coord[$db_row['x']]);
			}
		}
		
		$x = array_rand($coord);
		$y = array_rand($coord[$x]);
		
		$db_query = "UPDATE `planets` SET `x` = '" . $x . "', `y` = '" . $y . "' WHERE `planet_id` = '" . $planet_id . "' LIMIT 1";
		$db_result = mysql_query($db_query);
	}
?>