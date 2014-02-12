<?php
	function soft_reset_round()
	{
		global $sql;
		
		$round_id = $_REQUEST['round_id'];
		
		$db_query = "SELECT * FROM `rounds` WHERE `round_id` = '" . $round_id . "'";
		$db_result = mysql_query($db_query);
		$round = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$tables = 
		
		$t = 'players';
		$warptime = (microfloat() - $round['starttime']) * 0.75;
		
		$sql->set(array(
			array($t, 'npc', 0), 
			array($t, 'warptime', $warptime), 
			array($t, 'score', 0), 
			array($t, 'score_peak', 0)));
		$sql->where(array($t, 'round_id', $round_id));
		$sql->execute();
		
		$sql->select(array(
			array($t, 'player_id'), 
			array($t, 'kingdom_id'), 
			array($t, 'rank')));
		$sql->where(array($t, 'round_id', $round_id));
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$kingdoms[$db_row['kingdom_id']]['members'][$db_row['player_id']] = $db_row['rank'];
		}
		
		function addbuilding($buildings, $building, $buildingcount)
		{
			if ($building == 9)
			{
				// Zero-G
				$buildings[9] = $buildingcount;
				$buildings[1] = $buildingcount * 15;
				$buildings[10] = $buildingcount * 20;
				$buildings[14] = $buildingcount * 10;
			}
			else
			{
				// HydroCrane
				$buildings[8] = $buildingcount;
				$buildings[10] = $buildingcount * 15;
				$buildings[14] = $buildingcount * 25;
			}
			
			return $buildings;
		}
		
		$db_query = "SELECT `player_id` FROM `players` WHERE `round_id` = '" . $round_id . "' GROUP BY `kingdom_id` HAVING COUNT(`kingdom_id`) = 1";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$player_id = $db_row['player_id'];
			
			$buildings = array(8 => 0, 9 => 0);
			
			$db_query = "SELECT `planet_id`, `buildings` FROM `planets` WHERE `player_id` = '" . $player_id . "'";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$players[$player_id][$db_row['planet_id']] = true;
				
				$db_row['buildings'] = unserialize($db_row['buildings']);
				
				if (!empty($db_row['buildings'][8]) && $db_row['buildings'][8] > $buildings[8])
					$buildings[8] = $db_row['buildings'][8];
				if (!empty($db_row['buildings'][9]) && $db_row['buildings'][9] > $buildings[9])
					$buildings[9] = $db_row['buildings'][9];
			}
			
			if ($buildings[8] != 0 || $buildings[9] != 0)
			{
				$bonus = ($building == 8) ? 'buildingbonus' : 'researchbonus';
				
				$building = ($buildings[8] > $buildings[9]) ? 8 : 9;
				$planetcount = count($players[$player_id]);
				$buildingcount = round(30 / $planetcount);
				
				$planetbuildings = addbuilding(unserialize($round['buildings']), $building, $buildingcount);
			}
			else
			{
				$bonus = 'researchbonus';
				
				$buildingcount = 0;
				
				$planetbuildings = unserialize($round['buildings']);
			}
			
			$sql->set(array(
				array('planets', 'buildings', serialize($planetbuildings)), 
				array('planets', $bonus, $buildingcount)));
			$sql->where(array('planets', 'player_id', $player_id));
			$sql->execute();
		}
		
		
		$t = 'planets';
		$mineralstock = array(0 => 20, 1 => 20, 2 => 15, 3 => 15, 4 => 10, 5 => 10, 6 => 5, 7 => 5);
		foreach ($mineralstock as $key => $value)
			$mineralsremaining[$key] = $round['minerals'] * ($value / 100);
		
		$sql->set(array(
			array($t, 'cranes', 1), 
			array($t, 'planning', 1), 
			array($t, 'researching', 0), 
			array($t, 'buildingbonus', 0), 
			array($t, 'researchbonus', 0), 
			array($t, 'production', 'a:0:{}'), 
			array($t, 'units', 'a:0:{}'), 
			array($t, 'food', $round['food']), 
			array($t, 'foodrate', 0), 
			array($t, 'workers', $round['workers']), 
			array($t, 'workersrate', 0), 
			array($t, 'energy', $round['energy']), 
			array($t, 'energyrate', 0), 
			array($t, 'minerals', serialize(array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0))), 
			array($t, 'mineralsrate', 0), 
			array($t, 'mineralsremaining', serialize($mineralsremaining)), 
			array($t, 'resistance', 0), 
			array($t, 'score', 0), 
			array($t, 'score_peak', 0)));
		$sql->where(array($t, 'round_id', $round_id));
		$sql->execute();
		
		$sql->select(array(
			array($t, 'planet_id'), 
			array($t, 'player_id'), 
			array($t, 'kingdom_id')));
		$sql->where(array($t, 'round_id', $round_id));
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$kingdoms[$db_row['kingdom_id']]['planets'][$db_row['planet_id']] = true;
		}
		
		
		$t = 'kingdoms';
		$sql->set(array(
			array($t, 'buildings', $round['buildings']), 
			array($t, 'units', 'a:0:{}'), 
			array($t, 'concepts', $round['concepts']), 
			array($t, 'researched', 'a:0:{}'), 
			array($t, 'foodrate', 0), 
			array($t, 'workersrate', 0), 
			array($t, 'energyrate', 0), 
			array($t, 'mineralsrate', 0), 
			array($t, 'score', 0), 
			array($t, 'score_peak', 0)));
		$sql->where(array($t, 'round_id', $round_id));
		$sql->execute();
			
		foreach ($kingdoms as $kingdom_id => $sections)
		{
			if (empty($sections['planets'])) $sections['planets'] = array();
			if (empty($sections['members'])) $sections['members'] = array();
			$planets = count($sections['planets']);
			
			$sql->set(array(
				array($t, 'planets', serialize($sections['planets'])), 
				array($t, 'members', serialize($sections['members'])), 
				array($t, 'food', $round['food'] * $planets), 
				array($t, 'workers', $round['workers'] * $planets), 
				array($t, 'energy', $round['energy'] * $planets), 
				array($t, 'minerals', 0)));
			$sql->where(array($t, 'kingdom_id', $kingdom_id));
			$sql->execute();
		}
		
		
		
		$names = file(dirname(__FILE__) . '/names_planets.txt');
		foreach (array('players' => 'player_id', 'kingdoms' => 'kingdom_id', 'planets' => 'planet_id') as $t => $id)
		{
			if ($t == 'planets')
			{
				$sql->select(array(
					array($t, 'player_id') ,
					array($t, 'kingdom_id')));
			}
			$sql->select(array($t, $id));
			$sql->where(array($t, 'name', '[Empty]'));
			$db_result = $sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				if ($t == 'planets' && empty($db_row['player_id']) && empty($db_row['kingdom_id']))
					$sql->set(array($t, 'name', ''));
				else
					$sql->set(array($t, 'name', trim($names[rand(0, count($names))])));
				$sql->where(array($t, $id, $db_row[$id]));
				$sql->execute();
			}
		}
		
		$tables = array(
			'combat', 
			'combatreports', 
			'tasks');
		foreach (array('army', 'navy', 'weapon') as $type)
		{
			$tables[] = $type . 'blueprints';
			$tables[] = $type . 'designs';
			if ($type != 'weapon')
				$tables[] = $type . 'groups';
		}
		
		foreach ($tables as $t)
		{
			$db_query = "DELETE FROM `" . $t . "` WHERE `round_id` = '" . $round_id . "'";
			$db_result = mysql_query($db_query);
		}
	}
?>