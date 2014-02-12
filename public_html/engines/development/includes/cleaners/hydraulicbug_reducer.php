<?php
	function hydraulicbug_reducer()
	{
		$max_buildings = array(
			5 => 1, 
			6 => 1, 
			7 => 14, 
			8 => 75, 
			9 => 85, 
		);
		
		$db_query = "SELECT `planet_id`, `buildings` FROM `planets`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['buildings'] = unserialize($db_row['buildings']);
			foreach ($max_buildings as $building_id => $max)
			{
				if (!empty($db_row['buildings'][$building_id]) && $db_row['buildings'][$building_id] > $max)
				{
					$db_row['buildings'][$building_id] = $max;
					$update = true;
				}
			}
			
			$db_query_update = "UPDATE `planets` SET `buildings` = '" . mysql_real_escape_string(serialize($db_row['buildings'])) . "' WHERE `planet_id` = '" . $db_row['planet_id'] . "' LIMIT 1";
			$db_result_update = mysql_query($db_query_update);
		}
	}
?>