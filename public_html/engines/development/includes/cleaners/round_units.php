<?php
	function round_units()
	{
		$groups = array('army' => 0, 'navy' => 0);
		$planets = 0;
		
		foreach (array('army', 'navy') as $type)
		{
			$db_query = "
				SELECT 
					`" . $type . "group_id` AS 'group_id', 
					`units` 
				FROM `" . $type . "groups`";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['units'] = unserialize($db_row['units']);
				$units = round_units_up($db_row['units']);
				
				if ($units !== $db_row['units'])
				{
					$groups[$type]++;
					$db_query = "
						UPDATE `" . $type . "groups` 
						SET `units` = '" . mysql_real_escape_string(serialize($units)) . "' 
						WHERE `" . $type . "group_id` = " . $db_row['group_id'];
					mysql_query($db_query);
				}
			}
		}
		
		$db_query = "
			SELECT 
				`planet_id`, 
				`units` 
			FROM `planets`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['units'] = unserialize($db_row['units']);
			
			$units = array();
			foreach (array('army', 'navy') as $type)
			{
				$units[$type] = round_units_up($db_row['units'][$type]);
			}
			
			if ($units !== $db_row['units'])
			{
				$planets++;
				$db_query = "
					UPDATE `planets` 
					SET `units` = '" . mysql_real_escape_string(serialize($units)) . "' 
					WHERE `planet_id` = " . $db_row['planet_id'];
				mysql_query($db_query);
			}
		}
		
		echo 'Planets Updated: ' . $planets . "<br />\n";
		echo 'Army Groups Updated: ' . $groups['army'] . "<br />\n";
		echo 'Navy Groups Updated: ' . $groups['navy'] . "<br />\n";
	}
	
	function round_units_up($units)
	{
		foreach ($units as $unit_id => $unit_count)
		{
			if (ceil($unit_count) != $unit_count)
			{
				$units[$unit_id] = ceil($unit_count);
			}
		}
		
		return $units;
	}
?>