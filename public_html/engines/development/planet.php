<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	
	// ###############################################
	// Prisoner filter
	prisoner_filter($_SESSION['player_id']);
	
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'planet_extraction', 
		'planet_extraction_set', 
		'planet_permissions', 
		'planet_permissions_set', 
		'planet_settings', 
		'planet_settings_set', 
		'planet_massmanage', 
		'planet_massmanage_set');
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	if (!in_array($fn, array('planet_massmanage', 'planet_massmanage_set')))
	{
		$planet_id = current_planet();
		permissions_check(PERMISSION_PLANET, $planet_id, 'owner');
	}
	
	$updater->update(0, 0, $_SESSION['player_id']);
	
	$fn();
	
	function planet_massmanage()
	{
		global $smarty, $sql, $data;
		
		// get all planets owned by this player
		// picture, name, food storage & rate, worker storage & rate, energy storage & rate, mineral storage & rate, 
		// [ ] The Past	  1.2K  +8   1.1K  +40   872  +48   0  0   Derrick   1h, 38m
		
		$available_planning = $available_cranes = 0;
		
		// Retrieve all of the player's planets
		$player = $data->player($_SESSION['player_id']);
		$planet_ids = $player['planets'];
		
		$planet_ids = array_keys($planet_ids);
		$planets = $data->planet($planet_ids);
		
		if (!empty($planets))
		{
			$db_query = "
				SELECT 
					t.`planet_id`, 
					b.`name`, 
					MIN(t.`completion`) as 'completion' 
				FROM 
					`tasks` t, 
					`buildings` b 
				WHERE 
					t.`planet_id` IN ('" . implode("', '", array_keys($planets)) . "') AND 
					b.`building_id` = t.`building_id` 
				GROUP BY t.`planet_id` 
				LIMIT " . count($planets);
			$db_result = $sql->query($db_query);
			
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$planets[$db_row['planet_id']]['construction'] = array(
					'name' => strshort($db_row['name'], 15), 
					'completion' => format_time(timeparser($db_row['completion'] - microfloat())));
			}
			
			$db_query = "
				SELECT 
					t.`planet_id`, 
					c.`name`, 
					MIN(t.`completion`) as 'completion' 
				FROM 
					`tasks` t, 
					`concepts` c 
				WHERE 
					t.`planet_id` IN ('" . implode("', '", array_keys($planets)) . "') AND 
					c.`concept_id` = t.`concept_id` 
				GROUP BY t.`planet_id` 
				LIMIT " . count($planets);
			$db_result = $sql->query($db_query);
			
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$planets[$db_row['planet_id']]['research'] = array(
					'name' => strshort($db_row['name'], 15), 
					'completion' => format_time(timeparser($db_row['completion'] - microfloat())));
			}
		}
		
		// Sort planets by cranes and then score, desc.
		$sort_key = array('cranes' => array('busy_planets' => array(), 'idle_planets' => array()), 'score' => array('busy_planets' => array(), 'idle_planets' => array()));
		$idle_planets = $busy_planets = array();
		foreach ($planets as $planet_id => $planet)
		{
			if (!empty($planet['minerals']))
			{
				$planet['minerals'] = array_sum($planet['minerals']);
			}
			else
			{
				$planet['minerals'] = 0;
			}
			
			$resource_deficiency = 0;
			
			foreach (array('food', 'workers', 'energy', 'minerals') as $resource)
			{
				if ($resource_deficiency != 0)
					$planet[$resource . 'deficiency'] = $planet[$resource . 'rate'];
				
				$planet[$resource . 'rate'] += $resource_deficiency;
				
				if ($planet[$resource . 'rate'] < 0 && $planet[$resource] < abs($planet[$resource . 'rate']))
					$resource_deficiency = $planet[$resource . 'rate'] + $planet[$resource];
				
				$planet[$resource] = format_number($planet[$resource], true);
				$planet[$resource . 'rate'] = format_number($planet[$resource . 'rate'], true, true);
			}
			
			if ($planet['planning'] > $available_planning && $planet['cranes'] > 0)
				$available_planning = $planet['planning'];
			if ($planet['cranes'] > $available_cranes)
				$available_cranes = $planet['cranes'];
			
			$planets[$planet['planet_id']] = $planet;
			
			if ($planet['cranes'] == 0)
			{
				$busy_planets[$planet['planet_id']] = &$planets[$planet['planet_id']];
				$sort_key['cranes']['busy_planets'][$planet_id]  = $planet['cranes'];
				$sort_key['score']['busy_planets'][$planet_id] = $planet['score'];
			}
			else
			{
				$idle_planets[$planet['planet_id']] = &$planets[$planet['planet_id']];
				$sort_key['cranes']['idle_planets'][$planet_id]  = $planet['cranes'];
				$sort_key['score']['idle_planets'][$planet_id] = $planet['score'];
			}
		}
		
		array_multisort($sort_key['cranes']['busy_planets'], SORT_DESC, $sort_key['score']['busy_planets'], SORT_DESC, $busy_planets);
		array_multisort($sort_key['cranes']['idle_planets'], SORT_DESC, $sort_key['score']['idle_planets'], SORT_DESC, $idle_planets);
		
		$kingdom = $data->kingdom($_SESSION['kingdom_id']);
		
		$db_query = "SELECT `building_id`, `name` FROM `buildings` WHERE `building_id` IN ('" . implode("', '", array_keys($kingdom['buildings'])) . "') ORDER BY `name` ASC";
		$db_result = $sql->query($db_query);
		
		$buildings = array();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$buildings[$db_row['building_id']] = $db_row['name'];
		}
		
		
		$smarty->assign('available_cranes', $available_cranes);
		$smarty->assign('available_planning', $available_planning);
		$smarty->assign('buildings', $buildings);
		$smarty->assign('planets', $planets);
		$smarty->assign('idle_planets', $idle_planets);
		$smarty->assign('idle_planet_count', count($idle_planets));
		$smarty->assign('busy_planets', $busy_planets);
		$smarty->display('planet_massmanage.tpl');
	}
	
	function planet_massmanage_set()
	{
		global $smarty, $sql, $data;
		
		if (!empty($_REQUEST['planet_ids']) && is_array($_REQUEST['planet_ids']))
		{
			$planets = $_REQUEST['planet_ids'];
			
			require_once(dirname(__FILE__) . '/buildings.php');
			
			foreach (array_keys($planets) as $planet_id)
			{
				$_REQUEST['planet_id'] = $planet_id;
				
				if (empty($buildings)) $buildings = new Buildings($data, $smarty);
				else $buildings->Buildings($data, $smarty);
				
				$buildings->quiet = true;
				$buildings->build();
			}
		}
		
		planet_massmanage();
	}
	
	function planet_extraction()
	{
		global $data, $smarty, $sql, $planet_id;
		
		$planet = &$data->planet($planet_id);
		
		$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
		$mineral_fix = false;
		
		foreach ($planet['minerals'] as $key => $value)
		{
			if ($value < 0)
			{
				$planet['minerals'][$key] = $value = 0;
				$mineral_fix = true;
			}
			
			if (floor($value) != $value)
			{
				$planet['minerals'][$key] = $value = floor($value);
				$mineral_fix = true;
			}
			
			$minerals[$mineralnames[$key]] = format_number($value, true);
		}
		
		foreach ($planet['mineralsremaining'] as $key => $value)
		{
			$mineralsremaining[$mineralnames[$key]] = format_number($value, true);
		}
		
		foreach ($planet['extractionrates'] as $key => $value)
		{
			$extraction[$mineralnames[$key]] = $value;
		}
		
		if ($mineral_fix) $data->save();
		
		$smarty->assign('minerals', $minerals);
		$smarty->assign('mineralsremaining', $mineralsremaining);
		$smarty->assign('extraction', $extraction);
		$smarty->display('planet_extraction.tpl');
		exit;
	}
	
	function planet_extraction_set()
	{
		global $smarty, $data, $planet_id;
		
		if (empty($_REQUEST['extraction']) || !is_array($_REQUEST['extraction']))
		{
			$smarty->append('status', 'Extraction error. Could not set rates.');
			planet_extraction();
			exit;
		}
		
		$mineralnames = unserialize(MINERALS_ARRAY);
		foreach ($mineralnames as $key => $name)
		{
			$extraction[$key] = floor(abs((int)$_REQUEST['extraction'][$name]));
		}
		
		if (array_sum($extraction) != 100)
		{
			$smarty->append('status', 'Extraction rate must total 100.');
			planet_extraction();
			exit;
		}
		
		$planet = &$data->planet($planet_id);
		
		foreach ($mineralnames as $key => $name)
		{
			$planet['extractionrates'][$key] = $extraction[$key];
		}
		
		$data->save();
		
		$smarty->append('status', 'Successfully set extraction rate.');
		planet_extraction();
		exit;
	}
	
	function planet_permissions()
	{
		global $smarty, $sql, $planet_id;
		
		$sql->select(array(
			array('players', 'player_id'), 
			array('players', 'name')));
		$sql->where(array(
			array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
			array('players', 'rank', 0, '>'), 
			array('players', 'player_id', $_SESSION['player_id'], '!=')));
		$db_result = $sql->execute();
		if (mysql_num_rows($db_result) > 0)
		{
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$players[$db_row['player_id']] = $db_row;
			}
			$smarty->assign('players', $players);
		}
		
		$sql->select(array(
			array('permissions', 'permission_id'), 
			array('permissions', 'player_id'), 
			array('players', 'name', 'player_name'), 
			array('permissions', 'research'), 
			array('permissions', 'build'), 
			array('permissions', 'commission'), 
			array('permissions', 'military')));
		$sql->where(array(
			array('permissions', 'type', PERMISSION_PLANET), 
			array('permissions', 'id', $planet_id), 
			array('players', 'player_id', array('permissions', 'player_id'))));
		$db_result = $sql->execute();
		
		if (mysql_num_rows($db_result) > 0)
		{	
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$permissions[$db_row['permission_id']] = $db_row;
			}
			$smarty->assign('permissions', $permissions);
		}
		
		$smarty->display('planet_permissions.tpl');
	}
	
	function planet_permissions_set()
	{
		global $smarty, $sql, $planet_id;
		
		$players = array();
		
		if (!empty($_REQUEST['permission_id']))
		{
			$db_query = "SELECT DISTINCT `player_id` FROM `permissions` WHERE `type` = '" . PERMISSION_PLANET . "' AND `permission_id` IN ('" . implode("', '", array_keys($_REQUEST['permission_id'])) . "') AND `id` = '" . $planet_id . "'";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$players[$db_row['player_id']];
			}
			
			$db_query = "DELETE FROM `permissions` WHERE `type` = '" . PERMISSION_PLANET . "' AND `permission_id` IN ('" . implode("', '", array_keys($_REQUEST['permission_id'])) . "') AND `id` = '" . $planet_id . "'";
			$db_result = mysql_query($db_query);
		}
		else
		{
			$_REQUEST['permission_id'] = array();
		}
		
		if (!empty($_REQUEST['player_id']))
		{
			$insert_permission = array(
				'round_id' => $_SESSION['round_id'], 
				'owner_id' => $_SESSION['player_id'], 
				'id' => $planet_id, 
				'type' => PERMISSION_PLANET);
			
			$permission_array = array('build', 'research' ,'commission', 'military');
			foreach ($permission_array as $value)
			{
				if (!empty($_REQUEST['permissions'][$value]))
					$insert_permission[$value] = 1;
			}
			
			
			foreach ($_REQUEST['player_id'] as $player_id)
			{
				if (!isset($_REQUEST['permission_id'][$player_id]))
				{
					$db_query = "DELETE FROM `permissions` WHERE `type` = '" . PERMISSION_PLANET . "' AND `player_id` = '" . abs((int)$player_id) . "' AND `id` = '" . $planet_id . "'";
					$db_result = mysql_query($db_query);
				}
				
				$insert_permission['player_id'] = abs((int)$player_id);
				$sql->execute('permissions', $insert_permission);
				
				permissions_update_planets($player_id);
				if (isset($players[$player_id])) unset($players[$player_id]);
			}
			
			if (!empty($players))
			{
				foreach ($players as $player_id => $empty)
				{
					permissions_update_planets($player_id);
				}
			}
			
			$status[] = 'Permissions Set';
			
			$smarty->assign('status', $status);
		}
		
		planet_permissions();
	}
	
	
	function planet_settings()
	{
		global $smarty, $sql, $planet_id;
		
		$sql->select(array(
			array('players', 'player_id'), 
			array('players', 'name')));
		$sql->where(array(
			array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
			array('players', 'rank', RANK_STEWARD, '>='), 
			array('players', 'player_id', $_SESSION['player_id'], '!=')));
		$db_result = $sql->execute();
		if (mysql_num_rows($db_result) > 0)
		{
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$players[$db_row['player_id']] = $db_row;
			}
			$smarty->assign('players', $players);
		}
		
		// Planet name is already passed to template by current_planet();
		
		$smarty->display('planet_settings.tpl');
	}
	
	function planet_settings_set()
	{
		global $smarty, $sql, $planet_id;
		
		if (isset($_REQUEST['transfer']))
		{
			global $data;
			
			$transfer_player_id = abs((int)$_REQUEST['transfer_id']);
			
			$to_player = &$data->player($transfer_player_id);
			$from_player = &$data->player($_SESSION['player_id']);
			
			if ($to_player['kingdom_id'] != $from_player['kingdom_id'])
			{
				error(__FILE__, __LINE__, 'INVALID_ID', 'Player is not in the same kingdom as you');
			}
			
			if ($to_player['rank'] < RANK_STEWARD)
			{
				$status[] = 'Player is not of sufficient rank to own a planet.';
				$status[] = $to_player['rank'] . ' < ' . RANK_STEWARD;
			}
			else
			{
				$count_from_player = $count_to_player = 0;
				
				$db_query = "
					SELECT COUNT(`player_id`) AS 'count', 
						`player_id` 
					FROM `planets` 
					WHERE `player_id` IN ('" . $_SESSION['player_id'] . "', '" . $transfer_player_id . "') 
					GROUP BY `player_id`";
				$db_result = mysql_query($db_query);
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					if ($db_row['player_id'] == $transfer_player_id)
					{
						$count_to_player = $db_row['count'];
					}
					elseif ($db_row['player_id'] == $_SESSION['player_id'])
					{
						$count_from_player = $db_row['count'];
					}
				}
				
				$count_to_player++;
				$count_from_player--;
				
				if ($count_to_player == 1) {
					if ($count_from_player == 0) {
						$status[] = 'You cannot transfer your last planet to someone that does not have any.';
					}
				} else if ($count_to_player / $count_from_player > (2 / 5)) {
					$status[] = 'Target player cannot have more than two for every five of your planets after the transfer.';
					$status[] = $count_to_player . '/' . $count_from_player . ' > 2/5';
				}
			}
			
			if (!empty($status))
			{
				$smarty->append('status', $status);
				planet_settings();
				exit;
			}
			
			$planet = &$data->planet($planet_id);
			$planet['player_id'] = $transfer_player_id;
			
			$from_player['score'] -= $planet['score'];
			$to_player['score'] += $planet['score'];
			if ($to_player['score'] > $to_player['score_peak'])
				$to_player['score_peak'] = $to_player['score'];
			
			$db_query = "DELETE FROM `permissions` WHERE `id` = '" . $planet_id . "' AND `type` = '" . PERMISSION_PLANET . "'";
			$db_result = mysql_query($db_query);
			
			// permissions_update_planets($player_id);
			
			unset($from_player['planets'][$planet_id]);
			$to_player['planets'][$planet_id] = true;
			
			// make sure transfer_id is in player's kingdom.
			// make sure this isn't the last planet of player.
			// make sure that player doesn't own any planets.
			// else status = error; planet_settings();
			
			// transfer score of planet
			// transfer planet
			
			$data->save();
			
			$smarty->append('status', 'Planet ownership transfered.');
			redirect('status.php');
		}
		elseif (isset($_REQUEST['cancel_research']))
		{
			if (empty($_REQUEST['confirm_research_cancel']))
			{
				$smarty->append('status', 'Confirmation Required.');
				planet_settings();
				exit;
			}
			
			global $data;
			$planet =& $data->planet($planet_id);
			$planet['researching'] = 0;
			$data->save();
			
			$db_query = "
				DELETE FROM `tasks` 
				WHERE 
					`planet_id` = " . $planet_id . " AND 
					`type` IN (" . TASK_RESEARCH . ", " . TASK_UPGRADE . ") AND 
					`round_id` = " . $_SESSION['round_id'] . "
				LIMIT 1";
			$db_result = mysql_query($db_query);
			
			$smarty->append('status', 'Research cancelled.');
			planet_settings();
		}
		else
		{
			$planet_name = (isset($_REQUEST['planet_name'])) ? $_REQUEST['planet_name'] : '';
			if (strlen($planet_name) < 3 || strlen($planet_name) > 25 || 
				preg_match(REGEXP_NAME_PLANET, $planet_name) > 0)
			{
				$status[] = 'Planet name error.<br />';
			}
			
			if (empty($status))
			{
				$sql->set(array('planets', 'name', $planet_name));
				$sql->where(array(
					array('planets', 'planet_id', $planet_id), 
					array('planets', 'player_id', $_SESSION['player_id'])));
				$sql->limit(1);
				$sql->execute();
			}
			
			if (empty($status) && mysql_affected_rows() > 0)
			{
				$smarty->assign('planet_name', $_REQUEST['planet_name']);
				$status[] = 'Planet name successfully changed.';
			}
			else
			{
				$status[] = 'Failed to change planet name.';
			}
			
			$smarty->append('status', $status);
			planet_settings();
		}
	}
?>