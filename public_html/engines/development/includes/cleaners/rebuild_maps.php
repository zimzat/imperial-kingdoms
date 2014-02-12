<?php
	function rebuild_maps()
	{
		global $sql;
		
		$sql->set(array('quadrants', 'active', 0));
		$sql->execute();
		
		$db_query = "DELETE FROM `starsystems`";
		$db_result = mysql_query($db_query);
		
		// Rebuild quadrant and starsystem tables.
		$db_query = "SELECT `round_id`, `quadrant_id`, `starsystem_id`, `status` FROM `planets`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			if (empty($db_row['round_id'])) continue;
			if (!isset($map_tables[$db_row ['round_id']] [$db_row['quadrant_id']] [$db_row['starsystem_id']]))
				$map_tables[$db_row['round_id']][$db_row['quadrant_id']][$db_row['starsystem_id']] = 0;
			if (empty($db_row['status']))
				$map_tables[$db_row['round_id']][$db_row['quadrant_id']][$db_row['starsystem_id']]++;
		}
		
		srand(microfloat());
		
		foreach ($map_tables as $round_id => $quadrants)
		{
			foreach ($quadrants as $quadrant_id => $starsystems)
			{
				$sql->set(array('quadrants', 'active', 1));
				$sql->where(array('quadrants', 'quadrant_id', $quadrant_id));
				$sql->execute();
				
				$defined_starsystems = array();
				foreach ($starsystems as $starsystem_id => $count)
				{
					$x = rand(0, 6);
					$y = rand(0, 6);
					while (isset($defined_starsystems[$x . $y]))
					{
						$x = rand(0, 6);
						$y = rand(0, 6);
					}
					$defined_starsystems[$x . $y] = true;
					
					$starsystem_insert = array(
						'starsystem_id' => $starsystem_id, 
						'quadrant_id' => $quadrant_id, 
						'round_id' => $round_id, 
						'available' => $count, 
						'total' => 7, 
						'x' => $x, 
						'y' => $y);
					$sql->execute('starsystems', $starsystem_insert);
				}
			}
		}
	}
?>