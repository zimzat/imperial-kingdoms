<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'buildings_overview', 
			'buildings_list', # deprecated, use overview instead.
			'buildings_info', 
			'buildings_constructions', 
			'buildings_demolish', 
			'buildings_build', 
			'buildings_cancel', 
			'buildings_available');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 10);
		if ($fn == 'list') $fn = 'overview';
		
		$buildings = new Buildings;
		$buildings->$fn();
	}
	
	class Buildings
	{
		var $building_id;
		var $planet_id;
		
		var $output_mode;
		var $quiet;
		
		function Buildings()
		{
			data::initialize();
			
			$this->planet_id = current_planet();
			$this->building_id = (isset($_REQUEST['building_id'])) ? (int)$_REQUEST['building_id'] : NULL;
			
			permissions_check(PERMISSION_PLANET, $this->planet_id, array('build', 'commission'));
			
			$this->data->updater->update(0, $this->planet_id);
			
			$this->quiet = false;
		}
		
//		 $building->getResourceRequirements()
//		 $building->getRates()
//		 
//		 $building->checkRequirements()
		
		function available()
		{
			$planet = $this->data->planet($this->planet_id);
			
			echo 'varAvailableCranes = ' . $planet['cranes'] . '; varAvailablePlanning = ' . $planet['planning'] . ';';
			exit;
		}
		
		function overview()
		{
			$planet = &$this->data->planet($this->planet_id);
			$kingdom = $this->data->kingdom($planet['kingdom_id']);
			
			$buildings = $planet['buildings'] + $kingdom['buildings'];
			
			$building = $this->data->building(array_keys($buildings));
			
			if (!empty($building))
			{
				$total = array(
					'built' => 0, 
					'foodrate' => 0, 
					'workersrate' => 0, 
					'energyrate' => 0, 
					'mineralsrate' => 0);
				
				foreach ($building as $db_row)
				{
					if ($buildings[$db_row['building_id']] > 0)
					{
						$buildingslist[$db_row['building_id']] = array(
							'building_id' => $db_row['building_id'], 
							'name' => $db_row['name'], 
							'built' => $buildings[$db_row['building_id']], 
							'foodrate' => $db_row['foodrate'] * $buildings[$db_row['building_id']], 
							'workersrate' => $db_row['workersrate'] * $buildings[$db_row['building_id']], 
							'energyrate' => $db_row['energyrate'] * $buildings[$db_row['building_id']], 
							'mineralsrate' => $db_row['mineralsrate'] * $buildings[$db_row['building_id']]);
						
						$total['foodrate'] += $buildingslist[$db_row['building_id']]['foodrate'];
						$total['workersrate'] += $buildingslist[$db_row['building_id']]['workersrate'];
						$total['energyrate'] += $buildingslist[$db_row['building_id']]['energyrate'];
						$total['mineralsrate'] += $buildingslist[$db_row['building_id']]['mineralsrate'];
						$total['built'] += $buildingslist[$db_row['building_id']]['built'];
					}
					else
					{
						$buildingslist[$db_row['building_id']] = array(
							'building_id' => $db_row['building_id'], 
							'name' => $db_row['name'], 
							'built' => 0, 
							'foodrate' => 0, 
							'workersrate' => 0, 
							'energyrate' => 0, 
							'mineralsrate' => 0);
					}
					
					$name[$db_row['building_id']] = $db_row['name'];
				}
				
				array_multisort($name, SORT_ASC, $buildingslist);
				
				$this->smarty->assign('total', $total);
				$this->smarty->assign('buildings', $buildingslist);
			}
			
			$rate_correction = false;
			foreach (array('foodrate', 'workersrate', 'energyrate', 'mineralsrate') as $rate)
			{
				if ($planet[$rate] != $total[$rate])
				{
					$planet[$rate] = $total[$rate];
					$rate_correction = true;
				}
			}
			
			if ($rate_correction)
			{
				$this->data->save();
			}
			
			$this->building();
			
			$this->smarty->assign('warptime', format_time(timeparser($planet['warptime_construction'])));
			$this->smarty->assign('available_cranes', $planet['cranes']);
			$this->smarty->assign('available_planning', $planet['planning']);
			
			$this->smarty->display('buildings_list.tpl');
		}
		
		function building()
		{
			$db_query = "SELECT DISTINCT `building_id`, `completion` FROM `tasks` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `planet_id` = '" . $this->planet_id . "' AND `type` = '" . TASK_BUILD . "' ORDER BY `completion` ASC";
			$db_result = mysql_query($db_query);
			if ($db_result)
			{
				$beingbuilt = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$beingbuilt[$db_row['building_id']] = format_time(timeparser($db_row['completion'] - microfloat()));
				}
				
				$this->smarty->assign('beingbuilt', $beingbuilt);
			}
		}
		
		
		function info()
		{
			$building = $this->data->building($this->building_id);
			$planet = $this->data->planet($this->planet_id);
			
			if (empty($building) || empty($planet))
			{
				error(__FILE__, __LINE__, 'DATA', 'Invalid building or planet selection');
			}
			
			$mineralnames = unserialize(MINERALS_ARRAY);
			foreach ($building['mineralspread'] as $key => $value)
			{
				if ($value > 0)
				{
					$building['resources']['minerals'][$mineralnames[$key]] = format_number($building['minerals'] * ($value / 100), true);
				}
				else
				{
					$building['resources']['minerals'][$mineralnames[$key]] = 0;
				}
			}
			
			$building['resources']['time'] = format_time(timeparser(($building['time'] * $_SESSION['round_speed']) * ((100 - $planet['buildingbonus']) / 100)));
			$building['resources']['workers'] = format_number($building['workers'], true);
			$building['resources']['energy'] = format_number($building['energy'], true);
			
			$construction_warptime = format_time(timeparser($planet['warptime_construction']));
			
			$this->smarty->assign('warptime', $construction_warptime);
			$this->smarty->assign('building', $building);
			$this->smarty->assign('available_cranes', $planet['cranes']);
			$this->smarty->assign('available_planning', $planet['planning']);
			$this->smarty->display('buildings_info.tpl');
		}
		
		function constructions()
		{
			$this->sql->select(array(
				array('tasks', 'task_id'), 
				array('tasks', 'building_id'), 
				array('tasks', 'completion'), 
				array('tasks', 'number'), 
				array('tasks', 'planning')
			));
			$this->sql->where(array(
				array('tasks', 'planet_id', $this->planet_id), 
				array('tasks', 'type', TASK_BUILD)
			));
			$this->sql->orderby(array('tasks', 'completion', 'asc'));
			$db_result = $this->sql->execute();
			
			$now = microfloat();
			
			if (mysql_num_rows($db_result) > 0)
			{
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['cranes'] = $db_row['number'];
					unset($db_row['number']);
					
					$db_row['completion'] = format_time($db_row['completion'] - $now);
					
					$buildings[$db_row['building_id']] = true;
					
					$construction[] = $db_row;
				}
				
				$buildings = $this->data->building(array_keys($buildings));
				
				$this->smarty->assign('buildings', $buildings);
				$this->smarty->assign('construction', $construction);
			}
			
			$this->smarty->display('buildings_constructions.tpl');
		}
		
		function demolish()
		{
			$cranes = abs((int)$_POST['cranes']);
			$planning = abs((int)$_POST['planning']);
			
			$planet = &$this->data->planet($this->planet_id);
			$kingdom = &$this->data->kingdom($planet['kingdom_id']);
			
			if (empty($planet))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input specified for this function.');
			
			if (empty($planet['buildings'][$this->building_id]))
			{
				$_SESSION['status'][] = 'No buildings to demolish.';
				redirect('buildings.php?planet_id=' . $this->planet_id);
			}
			
			$building = $this->data->building($this->building_id);
			
			if (!empty($building['demolishable']))
			{
				$_SESSION['status'][] = 'Building cannot be demolished.';
				redirect('buildings.php?fn=buildings_infoplanet_id=' . $this->planet_id . '&building_id=' . $this->building_id);
			}
			
			if (empty($building))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input specified for this function.');
			
			$demolish = $cranes * $planning;
			
			if ($planet['buildings'][$this->building_id] < $demolish)
			{
				$demolish = $planet['buildings'][$this->building_id];
			}
			
			$planet['buildings'][$this->building_id] -= $demolish;
			
			$score = 0;
			$score += $building['workers'] * $demolish * SCORE_WORKERS * 2;
			$score += $building['energy'] * $demolish * SCORE_ENERGY * 2;
			$score += $building['minerals'] * $demolish * SCORE_MINERALS * 1.9;
			
			foreach (array('food', 'workers', 'energy', 'minerals') as $resource)
			{
				$rate_value = $demolish * $building[$resource . 'rate'];
				$planet[$resource . 'rate'] -= $rate_value;
				$kingdom[$resource . 'rate'] -= $rate_value;
			}
			
			if ($building['minerals'] > 0)
			{
				foreach ($building['mineralspread'] as $key => $value)
				{
					$planet['minerals'][$key] += floor($demolish * ($building['minerals'] * ($value / 100)) * 0.20);
				}
				
				$kingdom['minerals'] += $building['minerals'] * 0.20;
			}
			
			if (!empty($building['features']))
			{
				foreach ($building['features'] as $key => $value)
				{
					switch ($key)
					{
						case 1:
						case 'cranes':
							$planet['cranes'] -= $value * $demolish;
							break;
						case 2:
						case 'planning':
							$planet['planning'] -= $value * $demolish;
							break;
						case 3:
						case 'researchbonus':
							$planet['researchbonus'] -= $value * $demolish;
							break;
						case 4:
						case 'buildingbonus':
							$planet['buildingbonus'] -= $value * $demolish;
							break;
						case 5:
						case 'production':
							// Check to see if we're trying to produce some of that unit before demolishing it.
							$db_query = "
								SELECT COUNT(*) AS 'count' 
								FROM `tasks` 
								WHERE 
									`planet_id` = '" . $this->planet_id . "' AND 
									`type` = '" . TASK_UNIT . "' AND 
									`planning` = '" . $value[1] . "' AND 
									`attribute` = '" . $value[0] . "'";
							$db_result = $this->sql->query($db_query);
							$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
							
							if ($db_row['count'] > 0)
							{
								$status[] = 'That building is currently producing units. It cannot be demolished until they are completed.';
							}
							
							$planet['production'][$value[0]][$value[1]] -= $demolish;
							if (empty($planet['production'][$value[0]][$value[1]]))
							{
								unset($planet['production'][$value[0]][$value[1]]);
							}
							break;
					}
				}
			}
			
			if (!empty($status))
			{
				$_SESSION['status'][] = $status;
			}
			else
			{
				$this->data->save();
				$_SESSION['status'][] = 'Demolished ' . $demolish . ' buildings.';
			}
			
			redirect('buildings.php?fn=buildings_info&planet_id=' . $this->planet_id . '&building_id=' . $this->building_id);
		}
		
		function build_checkerror($status = array())
		{
			if (empty($status)) return false;
			
			if ($this->output_mode == 'javascript')
			{
				$planet = $this->data->planet($this->planet_id);
				echo 'alert(\'' . implode('\n', $status) . '\'); varAvailableCranes = ' . $planet['cranes'] . '; varAvailablePlanning = ' . $planet['planning'] . '; varBuilt = false; varError = true;';
			}
			else
			{
				$this->smarty->append('status', $status);
				if ($this->quiet) return true;
				$this->info();
			}
			
			exit;
		}
		
		function build()
		{
			$status = array();
			
			$cranes = abs((int)$_POST['cranes']);
			$planning = abs((int)$_POST['planning']);
			if (isset($_POST['mode']) && $_POST['mode'] == 'js')
				$output_mode = 'javascript';
			else $output_mode = '';
			$this->output_mode = $output_mode;
			
			$planet = $this->data->planet($this->planet_id);
			$kingdom = $this->data->kingdom($planet['kingdom_id']);
			$building = $this->data->building($this->building_id);
			
			// check construction requirements {
			if ($cranes == 0 || $planning == 0)
				$status[] = 'Must use at least one crane and planning facility.';
			elseif ($planet['cranes'] < $cranes || $planet['planning'] < $planning)
				$status[] = 'Not enough available cranes or planning buildings.';
			
			if ($planning > 3) $planning = 3;
			if ($cranes > 15) $cranes = 15;
			
			if (!isset($kingdom['buildings'][$this->building_id]))
				$status[] = 'You do not have the ability to create that building.';
			
			if ($this->build_checkerror($status)) return;
			
			if ($building['maxbuildable'] > 0)
			{
				$db_query = "
					SELECT SUM(`number` * `planning`) as 'currentlybuilding' 
					FROM `tasks` 
					WHERE 
						`type` = '" . TASK_BUILD . "' AND 
						`building_id` = '" . $this->building_id . "' AND 
						`planet_id` = '" . $this->planet_id . "'";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				if (!isset($planet['buildings'][$this->building_id]))
					$planet['buildings'][$this->building_id] = 0;
				
				if ($db_row['currentlybuilding'] + $planet['buildings'][$this->building_id] + ($cranes * $planning) > $building['maxbuildable'])
					$status[] = 'Cannot build more than ' . $building['maxbuildable'] . ' of this building.';
			}
			
			if ($this->build_checkerror($status)) return;
			// }
			
			// calculate and check resource requirements {
			$resources = array('workers', 'energy');
			$resource_values = array();
			foreach ($resources as $value)
			{
				$resource_values[$value] = $building[$value] * $cranes * $planning;
				
				if ($planet[$value] - $resource_values[$value] < 0)
					$status[] = 'Not enough ' . $value . '.';
			}
			
			if (!empty($building['mineralspread']))
			{
				$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
				$mineral_values = array();
				
				foreach ($building['mineralspread'] as $key => $value)
				{
					if ($value <= 0) continue;
					
					$mineral_values[$key] = ($value / 100) * $building['minerals'] * $cranes * $planning;
					
					if ($planet['minerals'][$key] - $mineral_values[$key] < 0)
						$status[] = 'Not enough ' . $mineralnames[$key] . '.';
				}
				
				$kingdom['minerals'] -= $building['minerals'];
			}
			
			if ($this->build_checkerror($status)) return;
			// }
			
			// deduct required resources {
			// Make data writable before making changes
			$planet = &$this->data->planet($this->planet_id);
			$kingdom = &$this->data->kingdom($planet['kingdom_id']);
			
			foreach ($resources as $value)
			{
				$planet[$value] -= $resource_values[$value];
				$kingdom[$value] -= $resource_values[$value];
			}
			
			if (!empty($building['mineralspread']))
			{
				foreach ($building['mineralspread'] as $key => $value)
				{
					if ($value <= 0) continue;
					
					$planet['minerals'][$key] -= $mineral_values[$key];
				}
			}
			
			$planet['cranes'] -= $cranes;
			// }
			
			// calculate start and completion time {
			// Someone has found a way to force the building bonus to go higher than it should be. Stop-gap it here. Luser.
			$building_bonus = ($planet['buildingbonus'] < 75) ? $planet['buildingbonus'] : 75;
			
			$completion = $planning * ($building['time'] * $_SESSION['round_speed']) * ((100 - $building_bonus) / 100);
			
			$now = microfloat();
			
			$warptime = request_variable('warptime');
			if (!is_null($warptime))
			{
				if ($planet['warptime_construction'] > $completion)
				{
					$planet['warptime_construction'] -= $completion;
					$completion = 0;
				}
				else
				{
					$completion -= $planet['warptime_construction'];
					$planet['warptime_construction'] = 0;
				}
			}
			// }
			
			// add task to system {
			$taskinsert = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'player_id' => $planet['player_id'], 
				'planet_id' => $this->planet_id, 
				'type' => TASK_BUILD, 
				'building_id' => $this->building_id, 
				'number' => $cranes, 
				'planning' => $planning, 
				'completion' => $now + $completion, 
				'start' => $now);
			$this->sql->execute('tasks', $taskinsert);
			
			$this->data->save();
			// }
			
			if ($this->quiet)
				return;
			
			if ($output_mode == 'javascript')
			{
				echo 'varAvailableCranes = ' . $planet['cranes'] . '; varAvailablePlanning = ' . $planet['planning'] . '; varBuilt = true; varError = false;';
				exit;
			}
			else
			{
				if ($completion == 0)
				{
					$_SESSION['status'][] = 'The building has been constructed.';
				}
				else
				{
					$_SESSION['status'][] = 'The building is now under construction.';
				}
				redirect('buildings.php');
			}
		}
		
		function cancel()
		{
			if (empty($_REQUEST['tasks']))
			{
				$_SESSION['status'][] = 'No building task specified.';
				redirect('buildings.php?fn=buildings_constructions');
			}
			
			if (!is_array($_REQUEST['tasks']))
				$tasks = array((int)$_REQUEST['tasks'] => true);
			else
			{
				foreach ($_REQUEST['tasks'] as $task_id => $value)
					if ((int)$task_id > 0)
						$tasks[(int)$task_id] = true;
			}
			
			$buildings_array = array();
			$this->sql->where(array(
				array('tasks', 'task_id', array_keys($tasks), 'IN'), 
				array('tasks', 'round_id', $_SESSION['round_id']), 
				array('tasks', 'type', TASK_BUILD), 
				array('tasks', 'planet_id', $this->planet_id)));
			$this->sql->limit(count($tasks));
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$tasks[$db_row['task_id']] = $db_row;
				$buildings_array[$db_row['building_id']] = true;
			}
			
			$planet = &$this->data->planet($this->planet_id);
			$buildings = $this->data->building(array_keys($buildings_array));
			$now = microfloat();
			
			foreach ($tasks as $task_id => $task)
			{
				$building_id = $task['building_id'];
				$percentage = sqrt(($task['completion'] - $now) / ($task['completion'] - $task['start']));
				
				foreach (array('food', 'workers', 'energy') as $resource)
				{
					if (!empty($buildings[$building_id][$resource]))
					{
						$planet[$resource] += $task['number'] * $task['planning'] * $buildings[$building_id][$resource] * $percentage;
					}
				}
				
				if ($buildings[$building_id]['minerals'] == 0) continue;
				
				foreach ($buildings[$building_id]['mineralspread'] as $key => $value)
				{
					if ($value == 0) continue;
					
					$mineral = ($value / 100) * $buildings[$building_id]['minerals'] * $task['number'] * $task['planning'] * $percentage;
					$planet['minerals'][$key] += $mineral;
				}
			}
			
			$db_query = "
				DELETE FROM `tasks` 
				WHERE 
					`task_id` IN ('" . implode("', '", array_keys($tasks)) . "') AND 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`type` = '" . TASK_BUILD . "' AND 
					`planet_id` = '" . $this->planet_id . "' 
				LIMIT " . count($tasks);
			$db_result = mysql_query($db_query);
			
			if (mysql_affected_rows() == count($tasks))
				$status[] = 'All specified tasks stopped.';
			else
				$status[] = 'Some specified tasks were not stopped.';
			
			$this->data->save();
			
			$_SESSION['status'][] = $status;
			redirect('buildings.php?fn=buildings_constructions');
		}
	}
?>