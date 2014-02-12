<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'info_kingdom', 
			'info_planet'
		);
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 5);
		
		$info = new Info($data, $smarty);
		$info->$fn();
	}
	
	class Info
	{
		var $data;
		var $smarty;
		var $sql;
		
		function Info(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
		}
		
		
		function get_adjacent_planet($planets, $planet_current, $action) {
			ksort($planets);
			
			if (empty($planets)) {
				return 0;
			}
			
			if (empty($planet_current)) {
				$planet_curent = array_rand($planets);
			}
			
			$neighbors = array_neighbor($planets, $planet_current);
			
			if ($action == 'next') {
				$planet_id = $neighbors['1'];
			} else {
				$planet_id = $neighbors['0'];
			}
			
			return $planet_id;
		}
		
		function set_current_planet(&$player, $planet_id, $planetList) {
			if ($planetList == 'own') {
				$player['planet_current'] = $planet_id;
			} else {
				$player['planet_permission_current'] = $planet_id;
			}
			
			$this->data->save();
		}

		
		
		// ###############################################
		// Show the planet info for the mini pane
		function planet()
		{
			// If requesting a specific / relative planet
			if (isset($_REQUEST['planet_id']))
			{
				$planet_id = $_REQUEST['planet_id'];
				
				// Let's duplicate our effort for sake of simplicity. We can always refactor later.
				if (in_array($planet_id, array('previous', 'next'))) {
					$planetList = 'own';
					$action = $planet_id;
					
					$player =& $this->data->player($_SESSION['player_id']);
					$planets = $player['planets'];
					$planet_current = $player['planet_current'];
					
					$planet_id = $this->get_adjacent_planet($planets, $planet_current, $action);
					$this->set_current_planet($player, $planet_id, $planetList);
				} else if (in_array($planet_id, array('previous_permissions', 'next_permissions'))) {
					$planetList = 'permission';
					if ($planet_id == 'previous_permissions') {
						$action = 'previous';
					} else {
						$action = 'next';
					}
					
					permissions_update_planets($_SESSION['player_id']);
					
					$player =& $this->data->player($_SESSION['player_id']);
					$planets = $player['planets_permissions'];
					$planet_current = $player['planet_permission_current']; 
					
					if (empty($planets)) {
						$this->smarty->append('status', 'You do not have permission to access any other planets');
						$this->smarty->display('error.tpl');
						exit;
					}
					
					$planet_id = $this->get_adjacent_planet($planets, $planet_current, $action);
					$this->set_current_planet($player, $planet_id, $planetList);
				} else {
					$planet_id = abs((int)$planet_id);
				}
				
				$_SESSION['planet_id'] = $planet_id;
			} else {
				$player =& $this->data->player($_SESSION['player_id']);
				
				$planet_id = $player['planet_current'];
			}
			
			$this->data->updater->update(0, $planet_id);
			
			$permissions = permissions_check(PERMISSION_PLANET, $planet_id);
			
			$round = $this->data->round();
			$planet = $this->data->planet($planet_id);
			
			if (!empty($planet['player_id'])) {
				$player = $this->data->player($planet['player_id']);
				$planet['player_name'] = strshort($player['name'], 15);
				if ($player['npc'] == 1) $planet['npc_player'] = true;
			}
			
			if (!empty($planet['kingdom_id'])) {
				$kingdom = $this->data->kingdom($planet['kingdom_id']);
				$planet['kingdom_name'] = strshort($kingdom['name'], 15);
			}
			
			$planet['permissions'] = $permissions;
			
			if ($planet['player_id'] == $_SESSION['player_id']) {
				$_SESSION['planet_id'] = $planet['planet_id'];
			}
			
			$planet['score'] = format_number($planet['score']);
			$planet['score_peak'] = format_number($planet['score_peak']);
			
			if ($permissions['grant'])
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
				if ($permissions['build'])
				{
					$types[] = 1;
				}
				
				if ($permissions['research'])
				{
					$types[] = 2;
					$types[] = 3;
				}
				
				if ($permissions['commission'])
				{
					$types[] = 4;
				}
				
				unset($planet['researching'], $planet['building'], $planet['army'], $planet['navy']);
				
				// Get tasks currently running on the planet
				$db_query = "SELECT DISTINCT `task_id`, `attribute`, `number`, `type` FROM `tasks` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `planet_id` = '" . $planet_id . "' AND `type` IN ('" . implode("', '", $types) . "') ORDER BY `completion` DESC";
				$db_result_tasks = mysql_query($db_query);
				while ($tasks = mysql_fetch_array($db_result_tasks, MYSQL_ASSOC))
				{
					$this->sql->select(array('tasks', 'completion'));
					$this->sql->where(array(
						array('tasks', 'round_id', $_SESSION['round_id']), 
						array('tasks', 'task_id', $tasks['task_id'])
					));
					$this->sql->limit(1);
					switch ($tasks['type'])
					{
						case 1:
							$this->sql->select(array('buildings', 'name'));
							$this->sql->where(array('buildings', 'building_id', array('tasks', 'building_id')));
							
							$db_result = $this->sql->execute();
							$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
							
							$planet['building']['name'] = strshort($db_row['name'], 15);
							$planet['building']['time'] = format_time(timeparser($db_row['completion'] - microfloat()));
							break;
						case 2:
							$this->sql->select(array('concepts', 'name'));
							$this->sql->where(array('concepts', 'concept_id', array('tasks', 'concept_id')));
							
							$db_result = $this->sql->execute();
							$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
							
							$planet['researching']['name'] = strshort($db_row['name'], 15);
							$planet['researching']['time'] = format_time(timeparser($db_row['completion'] - microfloat()));
							break;
						case 3:
							$designs = array('army', 'navy', 'weapon');
							$this->sql->select(array($designs[$tasks['number']] . 'designs', 'name'));
							$this->sql->where(array($designs[$tasks['number']] . 'designs', $designs[$tasks['number']] . 'design_id', array('tasks', 'design_id')));
							
							$db_result = $this->sql->execute();
							$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
							
							$planet['researching']['name'] = strshort($db_row['name'], 15);
							$planet['researching']['time'] = format_time(timeparser($db_row['completion'] - microfloat()));
							break;
						case 4:
							$this->sql->select(array($tasks['attribute'] . 'blueprints', 'name'));
							$this->sql->where(array($tasks['attribute'] . 'blueprints', $tasks['attribute'] . 'blueprint_id', array('tasks', 'unit_id')));
							
							$db_result = $this->sql->execute();
							$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
							
							$planet[$tasks['attribute']]['name'] = strshort($db_row['name'], 15);
							$planet[$tasks['attribute']]['time'] = format_time(timeparser($db_row['completion'] - microfloat()));
							break;
					}
				}
				
				$planet['nextupdate'] = format_time($round['resourcetick'] - (microfloat() - $planet['lastupdated']));
			}
			
			$this->smarty->assign('planet', $planet);
			$this->smarty->display('info_planet.tpl');
		}
		
		
		
		// ###############################################
		// Show the kingdom info for the mini pane
		function kingdom()
		{
			// If requesting a specific
			if (!empty($_REQUEST['kingdom_id']))
				$current = (int)$_REQUEST['kingdom'];
			else
				$current = $_SESSION['kingdom_id'];
			
			$this->data->updater->update($current);
			
			$kingdom = $this->data->kingdom($current);
			
			$kingdom['food'] = format_number($kingdom['food'], true);
			$kingdom['workers'] = format_number($kingdom['workers'], true);
			$kingdom['energy'] = format_number($kingdom['energy'], true);
			$kingdom['minerals'] = format_number($kingdom['minerals'], true);
			
			$kingdom['foodrate'] = format_number($kingdom['foodrate'], true, true);
			$kingdom['workersrate'] = format_number($kingdom['workersrate'], true, true);
			$kingdom['energyrate'] = format_number($kingdom['energyrate'], true, true);
			$kingdom['mineralsrate'] = format_number($kingdom['mineralsrate'], true, true);
			
			if (!empty($kingdom['members']))
			{
				$i = 0;
				$db_query = "SELECT COUNT(`planet_id`) AS 'planets' FROM `planets` WHERE `player_id` IN ('" . implode("', '", array_keys($kingdom['members'])) . "')";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$kingdom['planets'] = $db_row['planets'];
				
				if (isset($kingdom['members'][$_SESSION['player_id']]))
				{
					$kingdom['member'] = true;
				}
			}
			else
			{
				$kingdom['planets'] = count($kingdom['planets']);
			}
			
			$kingdom['members'] = count($kingdom['members']);
			
			if (empty($kingdom['minerals']))
			{
				$kingdom['minerals'] = 0;
			}
			
			$this->smarty->assign('kingdom', $kingdom);
			$this->smarty->display('info_kingdom.tpl');
		}
	}
?>