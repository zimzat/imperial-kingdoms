<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	// ###############################################
	// Load global functions
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/functions.php');
	
	function return_resources(&$planet, $from, $percentage)
	{
		global $data;
		
		$kingdom = $data->kingdom($to['kingdom_id']);
		
		$resources = array('food', 'workers', 'energy');
		foreach ($resources as $resource)
		{
			if (empty($from[$resource])) continue;
			
			$resource_value = $from[$resource] * $percentage;
			$score_value = constant('SCORE_' . strtoupper($resource));
			
			$planet[$resource] += $resource_value;
			$kingdom[$resource] += $resource_value;
			$score += ($resource_value * $score_value) + (($from[$resource] - $resource_value) * $score_value * 2);
		}
		
		if (!empty($from['minerals']))
		{
			$mineral_value = $from['minerals'] * $percentage;
			
			foreach ($from['mineralspread'] as $key => $value)
			{
				if (empty($value)) continue;
				
				$planet['minerals'][$key] += ($value / 100) * $mineral_value;
			}
			
			$kingdom['minerals'] += $mineral_value;
			$score += ($mineral_value * SCORE_MINERALS) + (($from['minerals'] - $mineral_value) * SCORE_MINERALS * 2);
		}
		
		return $score;
	}
	
	function military_declarations($target_kingdom_id = 0)
	{
		global $data, $smarty;
		
		$declarations = array();
		
		if (empty($target_kingdom_id))
			$target_kingdom_id = $_SESSION['kingdom_id'];
		
		$kingdom = &$data->kingdom($target_kingdom_id);
		if (empty($kingdom['enemies'])) $kingdom['enemies'] = array();
		if (empty($kingdom['allies'])) $kingdom['allies'] = array();
		
		$kingdoms = &$data->kingdom(array_keys($kingdom['enemies'] + $kingdom['allies']));
		if (empty($kingdoms)) $kingdoms = array();
		foreach (array_keys($kingdoms) as $kingdom_id)
		{
			if (isset($kingdom['allies'][$kingdom_id])) $status = 'ally';
			else $status = 'enemy';
			
			$declarations[$kingdom_id] = array(
				'kingdom_id' => $kingdom_id, 
				'name' => $kingdoms[$kingdom_id]['name'], 
				'status' => $status);
		}
		
		$smarty->assign('declarations', $declarations);
	}
	
	function add_news_entry($type, $information)
	{
		global $sql, $smarty;
		
		require_once(dirname(__FILE__) . '/news_resource.php');
		
		$sql->select(array('news_entries', 'news_entry_id'));
		$sql->where(array('news_entries', 'type', $type));
		$sql->raw(array('orderby', 'RAND(' . microfloat() . ')'));
		$sql->limit(1);
		$db_result = $sql->execute();
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$news_entry_id = $db_row['news_entry_id'];
		
		$posted = $information['posted'];
		$information['posted'] = format_timestamp($information['posted']);
		
		foreach ($information as $key => $value)
		{
			$smarty->assign($key, $value);
		}
		
		$smarty->fetch('news:' . $news_entry_id);
		$entry_subject = $smarty->get_template_vars('subject');
		$entry_body = $smarty->get_template_vars('body');
		
		$news_insert = array(
			'round_id' => $_SESSION['round_id'], 
			'type' => $type, 
			'subject' => $entry_subject, 
			'body' => $entry_body, 
			'posted' => $posted);
		
		$fields = array('kingdom_id', 'player_id', 'planet_id');
		foreach ($fields as $value)
		{
			if (!empty($information[$value]))
			{
				$news_insert[$value] = $information[$value];
			}
		}
		
		$sql->execute('news', $news_insert);
	}
	
	function &request_id($type, $id_name)
	{
		global $data;
		
		if (empty($type) || !method_exists($data, $type))
			error(__FILE__, __LINE__, 'INVALID_TYPE', 'Invalid id type.');
		if (empty($_REQUEST[$id_name]))
			error(__FILE__, __LINE__, 'INVALID_ID', 'No ' . $type . ' id specified');
		
		$id = abs((int)$_REQUEST[$id_name]);
		
		if (empty($id))
			error(__FILE__, __LINE__, 'INVALID_ID_TYPE', 'No ' . $type . ' id specified');
		
		$return = &$data->$type($id);
		
		if (empty($return))
			error(__FILE__, __LINE__, 'INVALID_ID_TYPE', 'Invalid ' . $type . ' id specified');
		
		return $return;
	}
	
	// ###############################################
	// Used in planet.php to find the next or previous planet in a player's list.
	function array_neighbor($arr, $key)
	{
		$keys = array_keys($arr);
		$keyIndexes = array_flip($keys);
		
		$return = array();
		if (isset($keys[$keyIndexes[$key] - 1]))
		{
			$return[] = $keys[$keyIndexes[$key] - 1];
		}
		else
		{
			$return[] = $keys[sizeof($keys) - 1];
		}
		
		if (isset($keys[$keyIndexes[$key] + 1]))
		{
			$return[] = $keys[$keyIndexes[$key] + 1];
		}
		else
		{
			$return[] = $keys[0];
		}
		
		return $return;
	}
	
	function map_coordinate($x, $y)
	{
		$values = array(
			'x' => $x, 
			'y' => $y
		);
		
		foreach ($values as $key => $value)
		{
			$coordinates['quadrant'][$key] = floor($value / 49);
			$value = $value % 49;
			$coordinates['starsystem'][$key] = floor($value / 7);
			$coordinates['planet'][$key] = $value % 7;
		}
		
		return $coordinates;
	}
	
	// ###############################################
	// Prisoner filter
	function prisoner_filter($player_id = '')
	{
		global $smarty;
		
		if (empty($player_id))
		{
			$player_id = $_SESSION['player_id'];
		}
		
		$db_query = "SELECT `rank` FROM `players` WHERE `player_id` = '" . $player_id . "' LIMIT 1";
		$db_result = mysql_query($db_query);
		if (!$db_result || mysql_num_rows($db_result) == 0)
		{
			error(__FILE__, __LINE__, 'DATA_INVALID', 'Invalid player number: ' . $player_id);
		}
		
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		if ($db_row['rank'] == 0)
		{
			if (basename($_SERVER['PHP_SELF']) == 'map.php')
			{
				exit;
			}
			
			$status[] = 'All you see from your prison cell is the glow of day and darkness of night.';
			$smarty->append('status', $status);
			$smarty->display('error.tpl');
			exit;
		}
	}
	
	function permissions_check($type, $id, $actions = array(), $handle = true)
	{
		$sql = new SQL_Generator;
		
		// players permissions: allow for everything
		// planets permissions disallow/allow for planet
		
		// owner = only if they own it
		// grant = sudo for everything
		// research, build, commission, military = specific grant
		
		$acceptable_actions = array('research', 'build', 'commission', 'military');
		
		if (empty($actions))
		{
			$actions = $acceptable_actions;
			$handle = false;
		}
		
		if ($type < 1 || $type > 3)
		{
			error(__FILE__, __LINE__, 'PERMISSIONS_INVALID', 'Invalid permissions check.');
		}
		
		if (!is_array($actions))
		{
			$actions = array($actions);
		}
		
		if (empty($id))
		{
			error(__FILE__, __LINE__, 'PERMISSIONS_INVALID_ID', 'Invalid permissions id specified.');
		}
		
		$tables = array(
			PERMISSION_PLANET => 'planet', 
			PERMISSION_ARMY => 'armygroup', 
			PERMISSION_NAVY => 'navygroup');
		$table = $tables[$type];
		
		$sql->select(array(
			array($table . 's', 'kingdom_id'), 
			array($table . 's', 'player_id')));
		$sql->where(array(
			array($table . 's', $table . '_id', $id), 
			array('players', 'player_id', $_SESSION['player_id'])));
		$sql->limit(1);
		$db_query = $sql->generate();
		
		$db_result = mysql_query($db_query);
		$check = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$grant = false;
		$level = 0;
		
		while (in_array('grant', $actions))
		{
			unset($actions[array_search('grant', $actions)]);
			$actions = $acceptable_actions;
		}
		
		// check if they're in the same kingdom.
		if ($_SESSION['kingdom_id'] == $check['kingdom_id'])
		{
			// check if they're the owner. Overrides all permissions.
			if ($_SESSION['player_id'] == $check['player_id'])
			{
				$grant = true;
				
				$return['owner'] = true;
				foreach ($actions as $action)
				{
					$return[$action] = true;
				}
			}
			else
			{
				$return['owner'] = false;
				
				while (in_array('owner', $actions))
				{
					unset($actions[array_search('owner', $actions)]);
				}
				
				if (count($actions) > 0)
				{
					$db_query = "SELECT * FROM `permissions` WHERE `player_id` = '" . $_SESSION['player_id'] . "' AND ((`type` = '" . $type . "' AND `id` = '" . $id . "') OR `type` = '0') ORDER BY `type` DESC LIMIT 1";
//					 $db_query = $sql->generate();
					$db_result = mysql_query($db_query);
					$check = mysql_fetch_array($db_result, MYSQL_ASSOC);
					
					foreach ($actions as $level => $action)
					{
						if (in_array($action, $acceptable_actions) && $check[$action] == 1)
						{
							$grant = true;
							$return[$action] = true;
						}
						else
						{
							$return[$action] = false;
						}
					}
				}
			}
		}
		else
		{
			$return['owner'] = false;
			
			foreach ($actions as $action)
			{
				$return[$action] = false;
			}
		}
		
		if ($handle)
		{
			if (!$grant)
			{
				global $smarty;
				
				$smarty->append('status', 'You do not have permission to access that.');
				$smarty->display('error.tpl');
				exit;
			}
			elseif ($grant && !$return[$actions[0]])
			{
				$pages = array(
					'research' => 'research.php', 
					'build' => 'buildings.php', 
					'commission' => 'units.php', 
					'military' => 'military.php');
				redirect($pages[$action]);
			}
		}
		
		$return['grant'] = $grant;
		
		return $return;
	}
	
	function permissions_planets($player_id, $actions = array())
	{
		global $sql;
		
		$acceptable_actions = array('research', 'build', 'commission', 'military');
		
		if (empty($player_id))
		{
			$player_id = $_SESSION['player_id'];
		}
		
		if (!is_array($actions))
		{
			$actions = array($actions);
		}
		
		if (empty($permissions))
		{
			$actions = $acceptable_actions;
		}
		
		$sql->select(array(
			array('permissions', 'owner_id'), 
			array('permissions', 'type'), 
			array('permissions', 'id')));
		$sql->where(array(
			array('permissions', 'round_id', $_SESSION['round_id']), 
			array('permissions', 'type', array(PERMISSION_PLAYER, PERMISSION_PLANET), 'IN'), 
			array('permissions', 'player_id', $player_id)));
		
		foreach ($acceptable_actions as $action)
		{
			if (in_array($action, $actions))
			{
				$sql->select(array('permissions', $action));
			}
		}
		
		$db_result = $sql->execute();
		if (mysql_num_rows($db_result) > 0)
		{
			$planets = array('exclude' => array(), 'include' => array());
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				if ($db_row['type'] == PERMISSION_PLAYER)
				{
					$players[] = $db_row['owner_id'];
				}
				else
				{
					$included = false;
					foreach ($acceptable_actions as $action)
					{
						if (!empty($db_row[$action]))
						{
							$planets['include'][$db_row['id']] = true;
							$included = true;
							break;
						}
					}
					
					if (!$included)
					{
						$planets['exclude'][$db_row['id']] = true;
					}
				}
			}
			
			if (!empty($players))
			{
				$sql->select(array(
					array('players', 'player_id'), 
					array('players', 'planets')
				));
				$sql->where(array('players', 'player_id', $players, 'IN'));
				$db_query = $sql->generate();
				$db_result = mysql_query($db_query);
				
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['planets'] = unserialize($db_row['planets']);
					$planets['include'] = $planets['include'] + array_diff($db_row['planets'], $planets['exclude']);
				}
			}
			
			return $planets['include'];
		}
	}
	
	function permissions_update_planets($player_id)
	{
		global $sql;
		
		$sql->select(array('players', 'planets_permissions'));
		$sql->where(array('players', 'player_id', $player_id));
		$sql->limit(1);
		$db_result = $sql->execute();
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$planets = unserialize($db_row['planets_permissions']);
		
		if (isset($planets['current']))
		{
			$current = $planets['current'];
		}
		else
		{
			$current = 0;
		}
		
		$planets = permissions_planets($player_id, array('build', 'commission'));
		if (!empty($planets))
		{
			ksort($planets);
			
			if (!isset($planets[$current]))
			{
				$planets['current'] = array_rand($planets);
			}
		}
		
		$sql->set(array('players', 'planets_permissions', serialize($planets)));
		$sql->where(array('players', 'player_id', $player_id));
		$sql->limit(1);
		$db_result = $sql->execute();
	}
	
	function current_planet()
	{
		global $data, $smarty, $sql;
		
		if (!empty($_REQUEST['planet_id']))
		{
			$planet_id = abs((int)$_REQUEST['planet_id']);
		}
		elseif (!empty($_SESSION['planet_id']))
		{
			$planet_id = $_SESSION['planet_id'];
		}
		else
		{
			$player = $data->player($_SESSION['player_id']);
			
			if (empty($player['planet_current']))
			{
				$player =& $data->player($_SESSION['player_id']);
				$player['planet_current'] = array_rand($player['planets']);
				$data->save();
			}
			
			$_SESSION['planet_id'] = $planet_id = $player['planet_current'];;
		}
		
		$planet = $data->planet($planet_id);
		
		if (empty($planet) || $planet['kingdom_id'] != $_SESSION['kingdom_id'])
		{
			if (!empty($_REQUEST['planet_id']))
			{
				unset($_REQUEST['planet_id']);
			}
			elseif (!empty($_SESSION['planet_id']))
			{
				unset($_SESSION['planet_id']);
			}
			else
			{
				error(__FILE__, __LINE__, 'INVALID_PLANET_ID', 'Invalid planet id specified or returned');
			}
			$planet_id = current_planet();
		}
		
		$smarty->assign('planet_id', $planet['planet_id']);
		$smarty->assign('planet_name', htmlentities($planet['name']));
		
		return $planet_id;
	}
	
	function research_planets()
	{
		global $smarty, $sql;
		
		if (isset($_POST['mode']) && $_POST['mode'] == 'js')
			$output_mode = 'javascript';
		else
			$output_mode = '';
		
		$planets_permissions = permissions_planets($_SESSION['player_id'], 'research');
		if (!empty($planets_permissions))
		{
			$sql->raw(array('where' => "OR `planets`.`planet_id` IN ('" . implode("', '", array_keys($planets_permissions)) . "')"));
		}
		
		// Player's planets
		$sql->select(array(
			array('planets', 'planet_id'), 
			array('planets', 'name'), 
			array('planets', 'researching'), 
			array('planets', 'researchbonus'), 
			array('planets', 'warptime_research')
		));
		$sql->where(array('planets', 'player_id', $_SESSION['player_id']));
		$sql->orderby(array(
			array('planets', 'researching', 'ASC'), 
			array('planets', 'researchbonus', 'DESC'), 
			array('planets', 'planet_id', 'ASC')
		));
		
		$db_query = $sql->generate();
		$db_result = mysql_query($db_query);
		
		if ($db_result && mysql_num_rows($db_result) > 0)
		{
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['warptime_research'] = format_time(timeparser($db_row['warptime_research']));
				$planets[$db_row['planet_id']] = $db_row;
			}
			
			$smarty->assign('planets', $planets);
			
			if ($output_mode == 'javascript')
			{
				$smarty->assign('output', 'javascript');
				$smarty->display('research_planets.tpl');
				exit;
			}
		}
	}
	
	function blueprints_stats($type = '', $blueprint_id = 0)
	{
		global $smarty, $sql;
		
		if (isset($_REQUEST['type'])) $type = $_REQUEST['type'];
		if (isset($_REQUEST['blueprint_id'])) $blueprint_id = $_REQUEST['blueprint_id'];
		
		if (!in_array($type, array('army', 'navy', 'weapon')) || empty($blueprint_id))
		{
			error(__FILE__, __LINE__, 'INVALID_ID/TYPE', 'Invalid blueprint type or id');
		}
		
		
		if ($type == 'weapon')
		{
			$sql->select(array(
				array('weaponblueprints', 'accuracy'), 
				array('weaponblueprints', 'areadamage'), 
				array('weaponblueprints', 'rateoffire'), 
				array('weaponblueprints', 'power'), 
				array('weaponblueprints', 'damage')));
		}
		else
		{
			$sql->select(array(
				array($type . 'blueprints', 'weapons'), 
				array($type . 'blueprints', 'attack'), 
				array($type . 'blueprints', 'defense'), 
				array($type . 'blueprints', 'armor'), 
				array($type . 'blueprints', 'hull')));
			if ($type == 'navy')
			{
				$sql->select(array(
					array('navyblueprints', 'speed'), 
					array('navyblueprints', 'cargo')));
			}
		}
		
		$sql->select(array(
			array($type . 'blueprints', 'size'), 
			array($type . 'blueprints', 'techlevel')));
		$sql->where(array($type . 'blueprints', $type . 'blueprint_id', $blueprint_id));
		$db_result = $sql->execute();
		$blueprint = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		if ($type != 'weapon')
		{
			$blueprint['weapons'] = unserialize($blueprint['weapons']);
			
			if (!empty($blueprint['weapons']))
			{
				$sql->select(array(
					array('weaponblueprints', 'weaponblueprint_id'), 
					array('weaponblueprints', 'name')));;
				$sql->where(array('weaponblueprints', 'weaponblueprint_id', array_keys($blueprint['weapons']), 'IN'));
				$db_result = $sql->execute();
				
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$blueprint['weapons'][$db_row['weaponblueprint_id']] = array(
						'name' => $db_row['name'], 
						'count' => $blueprint['weapons'][$db_row['weaponblueprint_id']]);
				}
			}
		}
		
		$smarty->assign('blueprint_stats', $blueprint);
	}
?>