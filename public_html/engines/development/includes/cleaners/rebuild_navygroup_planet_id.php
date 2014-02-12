<?php
	function rebuild_navygroup_planet_id()
	{
		global $sql;
		
		$round_id = $_REQUEST['round_id'];
		
		$sql->select(array(
			array('navygroups', 'navygroup_id'), 
			array('planets', 'planet_id')));
		$sql->where(array(
			array('navygroups', 'planet_id', 0), 
			array('quadrants', 'round_id', $round_id), 
			array('quadrants', 'x', 'raw:FLOOR(`navygroups`.`x_destination` / 49)'), 
			array('quadrants', 'y', 'raw:FLOOR(`navygroups`.`y_destination` / 49)'), 
			array('starsystems', 'round_id', $round_id), 
			array('starsystems', 'quadrant_id', array('quadrants', 'quadrant_id')), 
			array('starsystems', 'x', 'raw:FLOOR(MOD(`navygroups`.`x_destination`, 49) / 7)'), 
			array('starsystems', 'y', 'raw:FLOOR(MOD(`navygroups`.`y_destination`, 49) / 7)'), 
			array('planets', 'round_id', $round_id), 
			array('planets', 'x', 'raw:MOD(MOD(`navygroups`.`x_current`, 49), 7)'), 
			array('planets', 'y', 'raw:MOD(MOD(`navygroups`.`y_current`, 49), 7)'), 
			array('planets', 'starsystem_id', array('starsystems', 'starsystem_id')), 
			array('planets', 'quadrant_id', array('quadrants', 'quadrant_id'))));
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$sql->set(array('navygroups', 'planet_id', $db_row['planet_id']));
			$sql->where(array('navygroups', 'navygroup_id', $db_row['navygroup_id']));
			$sql->execute();
		}
	}
?>