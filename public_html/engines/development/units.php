<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'units_overview', 
			'units_list', # deprecated, use overview instead.
			'units_info', 
			'units_commission', 
			'units_commissions', 
			'units_cancel');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 6);
		if ($fn == 'list') $fn = 'overview';
		
		$units = new Units($data, $smarty);
		$units->$fn();
	}
	
	
	class Units
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $planet_id;
		var $unit_type;
		var $unit_id;
		
		function Units(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$this->planet_id = current_planet();
			$this->unit_type = request_variable('unit_type', NULL, 'army');
			$this->unit_id = request_variable('unit_id');
			
			if (!in_array($this->unit_type, array('army', 'navy')))
				error(__FILE__, __LINE__, 'DATA', 'Invalid unit type.');
			
			if (!empty($this->unit_id))
				permissions_check(PERMISSION_PLANET, $this->planet_id, 'commission');
			
			$this->smarty->assign('unit_type', $this->unit_type);
			$this->smarty->assign('unit_id', $this->unit_id);
			
			$this->data->updater->update(0, $this->planet_id);
		}
		
		function overview()
		{
			$db_query = "
				SELECT 
					`unit_id`, 
					`attribute`, 
					`completion` 
				FROM `tasks` 
				WHERE 
					`planet_id` = '" . $this->planet_id . "' AND 
					`type` = '" . TASK_UNIT . "' 
				ORDER BY `completion` DESC";
			$db_result = mysql_query($db_query);
			if ($db_result)
			{
				$beingcommissioned = array();
				while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$beingcommissioned[$db_row['attribute']][$db_row['unit_id']] = format_time(timeparser($db_row['completion'] - microfloat()));
				}
				$this->smarty->assign('beingcommissioned', $beingcommissioned);
			}
			
			
			$planet = $this->data->planet($this->planet_id);
			
			$type_array = array('army', 'navy');
			
			$unitlist = array();
			foreach ($type_array as $type)
			{
				if (empty($planet['units'][$type]) || !is_array($planet['units'][$type]))
				{
					$planet['units'][$type] = array();
				}
				
				$this->sql->select(array(
					array($type . 'designs', $type . 'concept_id', 'concept_id'), 
					array($type . 'blueprints', $type . 'blueprint_id'), 
					array($type . 'blueprints', 'name'), 
					array($type . 'blueprints', 'time')));
				$this->sql->where(array(
					array($type . 'blueprints', 'round_id', $_SESSION['round_id']), 
					array($type . 'blueprints', 'kingdom_id', $_SESSION['kingdom_id']), 
					array($type . 'designs', $type . 'design_id', array($type . 'blueprints', $type . 'design_id')), 
					array($type . 'blueprints', 'active', '1')));
				$this->sql->orderby(array(
					array($type . 'blueprints', $type . 'concept_id', 'desc'), 
					array($type . 'blueprints', $type . 'blueprint_id', 'desc')));
				$db_result = $this->sql->execute();
				
				while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					if (empty($planet['units'][$type][$db_row[$type . 'blueprint_id']]))
					{
						$planet['units'][$type][$db_row[$type . 'blueprint_id']] = 0;
					}
					$unitlist[$type][$db_row[$type . 'blueprint_id']] = array(
						'name' => $db_row['name'], 
						'count' => $planet['units'][$type][$db_row[$type . 'blueprint_id']]
					);
					if (empty($planet['production'][$type][$db_row['concept_id']]))
					{
						if (empty($beingcommissioned[$type][$db_row[$type . 'blueprint_id']]))
						{
							unset($unitlist[$type][$db_row[$type . 'blueprint_id']]);
							if (empty($unitlist[$type]))
							{
								unset($unitlist[$type]);
							}
						}
						else
						{
							$unitlist[$type][$db_row[$type . 'blueprint_id']]['time'] = '-';
						}
					}
					else
					{
						$unitlist[$type][$db_row[$type . 'blueprint_id']]['time'] = format_time(timeparser($db_row['time'] * $_SESSION['round_speed'] / $planet['production'][$type][$db_row['concept_id']]));
					}
					
				}
			}
			
			research_planets();
			
			$this->smarty->assign('units', $unitlist);
			$this->smarty->display('units_list.tpl');
		}
	
		function commissions()
		{
			$this->sql->select(array(
				array('tasks', 'task_id'), 
				array('tasks', 'unit_id'), 
				array('tasks', 'completion'), 
				array('tasks', 'attribute'), 
				array('tasks', 'number'), 
				array('tasks', 'planning')
			));
			$this->sql->where(array(
				array('tasks', 'planet_id', $this->planet_id), 
				array('tasks', 'type', TASK_UNIT)
			));
			$this->sql->orderby(array('tasks', 'completion', 'asc'));
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			$now = microfloat();
			
			if (mysql_num_rows($db_result) > 0)
			{
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['completion'] = format_time($db_row['completion'] - $now);
					
					$blueprints[$db_row['attribute']][$db_row['unit_id']] = true;
					
					$commissions[] = $db_row;
				}
				
				$units = array();
				foreach ($blueprints as $type => $unit)
				{
					$this->sql->select(array(
						array($type . 'blueprints', $type . 'blueprint_id'), 
						array($type . 'blueprints', 'name')
					));
					$this->sql->where(array($type . 'blueprints', $type . 'blueprint_id', array_keys($unit), 'IN'));
					$db_result = $this->sql->execute();
					
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$units[$type][$db_row[$type . 'blueprint_id']] = $db_row['name'];
					}
				}
				
				$this->smarty->assign('units', $units);
				$this->smarty->assign('commissions', $commissions);
			}
			
			$this->smarty->display('units_commissions.tpl');
		}
		
		function info()
		{
			$planet = $this->data->planet($this->planet_id);
			
			//$designs['army'] = array('attack', 'defense', 'armor', 'hull', 'weapons', 'size');
			//$designs['navy'] = array('attack', 'defense', 'armor', 'hull', 'weapons', 'cargo', 'speed', 'size');
			$unit = $this->data->blueprint($this->unit_type, $this->unit_id);
			
			$unit['name'] = htmlentities($unit['name']);
			
			if (!empty($planet['production'][$this->unit_type][$unit[$this->unit_type . 'concept_id']]))
			{
				$unit['resources']['time'] = format_time(timeparser(($unit['time'] * $_SESSION['round_speed']) / $planet['production'][$this->unit_type][$unit[$this->unit_type . 'concept_id']]));
			}
			$unit['resources']['workers'] = format_number($unit['workers'], true);
			$unit['resources']['energy'] = format_number($unit['energy'], true);
			
			if (!empty($unit['mineralspread']))
			{
				$mineralnames = unserialize(MINERALS_ARRAY);
				
				foreach($unit['mineralspread'] as $key => $value)
				{
					$resources['minerals'][$mineralnames[$key]] = format_number($unit['minerals'] / $value, true);
				}
				
				$unit['resources']['minerals'] = $resources['minerals'];
			}
			
			if ($unit['kingdom_id'] == $_SESSION['kingdom_id'])
			{
				blueprints_stats($this->unit_type, $this->unit_id);
			}
			
			research_planets();
			
			$this->smarty->assign('unit', $unit);
			$this->smarty->assign('unit_type', $this->unit_type);
			$this->smarty->assign('unit_id', $this->unit_id);
			$this->smarty->display('units_info.tpl');
		}
		
		function cancel()
		{
			if (empty($_REQUEST['tasks']))
			{
				$_SESSION['status'][] = 'No unit task specified.';
				redirect('units.php?fn=units_commissions');
			}
			
			if (!is_array($_REQUEST['tasks']))
				$tasks = array((int)$_REQUEST['tasks'] => true);
			else
			{
				foreach ($_REQUEST['tasks'] as $task_id => $value)
					if ((int)$task_id > 0)
						$tasks[(int)$task_id] = true;
			}
			
			$planet = &$this->data->planet($this->planet_id);
			$now = microfloat();
			$affected_rows = 0;
			
			foreach (array_keys($tasks) as $task_id)
			{
				$this->sql->where(array(
					array('tasks', 'task_id', $task_id), 
					array('tasks', 'round_id', $_SESSION['round_id']), 
					array('tasks', 'type', TASK_UNIT), 
					array('tasks', 'planet_id', $this->planet_id)));
				$this->sql->limit(1);
				$db_result = $this->sql->execute();
				if (mysql_num_rows($db_result) == 0) continue;
				
				$task = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$unit_type = $task['attribute'];
				$unit_id = $task['unit_id'];
				//$percentage = sqrt(($task['completion'] - $now) / ($task['completion'] - $task['start']));
				$percentage = 1;
				
				$unit = $this->data->blueprint($unit_type, $unit_id);
				
				foreach (array('food', 'workers', 'energy') as $resource)
				{
					if (empty($units[$unit_type][$unit_id][$resource])) continue;
					
					$planet[$resource] += $task['number'] * $units[$unit_type][$unit_id][$resource] * $percentage;
				}
				
				if (!empty($units[$unit_type][$unit_id]['minerals']))
				{
					foreach ($units[$unit_type][$unit_id]['mineralspread'] as $key => $value)
					{
						if (empty($value)) continue;
						
						$mineral = ($value / 100) * $units[$unit_type][$unit_id]['minerals'] * $task['number'] * $percentage;
						$planet['minerals'][$key] += $mineral;
					}
				}
				
				$db_query = "
					SELECT `completion` 
					FROM `tasks` 
					WHERE 
						`planet_id` = '" . $this->planet_id . "' AND 
						`type` = '" . TASK_UNIT . "' AND 
						`attribute` = '" . $unit_type . "' AND 
						`planning` = '" . $unit[$unit_type . 'concept_id'] . "' AND 
						`completion` < '" . $task['completion'] . "' 
					ORDER BY `completion` DESC 
					LIMIT 1";
				$db_result = mysql_query($db_query);
				if (mysql_num_rows($db_result) > 0)
				{
					$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
					$skip_time = $db_row['completion'];
				}
				else
				{
					$skip_time = $now;
				}
				
				$db_query = "
					UPDATE `tasks` 
					SET `completion` = `completion` - '" . ($task['completion'] - $skip_time) . "' 
					WHERE 
						`completion` > '" . $task['completion'] . "' AND 
						`type` = '" . TASK_UNIT . "' AND 
						`attribute` = '" . $unit_type . "' AND 
						`planning` = '" . $unit[$unit_type . 'concept_id'] . "' AND 
						`planet_id` = '" . $this->planet_id . "'";
				$db_result = mysql_query($db_query);
				
				$db_query = "DELETE FROM `tasks` WHERE `task_id` = '" . $task_id . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				
				if (mysql_affected_rows() == 1) $affected_rows++;
			}
			
			if ($affected_rows == count($tasks))
				$status[] = 'All specified tasks stopped.';
			else
				$status[] = 'At least one specified task was not stopped.';
			
			$this->data->save();
			
			$_SESSION['status'][] = $status;
			redirect('units.php?fn=units_commissions');
		}
		
		function commission_errorcheck($status = array())
		{
			if (empty($status)) return false;
			
			if ($this->output_mode == 'javascript')
			{
				echo 'alert(\'' . implode('\n', $status) . '\'); varCommissioned = false; varError = true;';
			}
			else
			{
				$this->smarty->append('status', $status);
				$this->overview();
			}
			
			exit;
		}
		
		function commission()
		{
			if (isset($_POST['mode']) && $_POST['mode'] == 'js')
				$this->output_mode = 'javascript';
			else
				$this->output_mode = '';
			
			$units = abs((int)request_variable('units', 'post'));
			
			if (empty($units) || empty($this->unit_id) || empty($this->unit_type))
			{
				$status[] = 'Must commission at least one unit.';
			}
			
			if ($this->commission_errorcheck($status)) return;
			
			$planet = $this->data->planet($this->planet_id);
			$unit = $this->data->blueprint($this->unit_type, $this->unit_id);
			
			if ($unit['kingdom_id'] != $planet['kingdom_id'])
			{
				$status[] = 'You do not own the blueprint to this unit.';
			}
			
			if ($this->commission_errorcheck($status)) return;
			
			if (empty($planet['production'][$this->unit_type][$unit[$this->unit_type . 'concept_id']]))
			{
				$status[] = 'No buildings to produce this unit.';
			}
			
			$resources = array('workers', 'energy');
			foreach ($resources as $value)
			{
				if ($planet[$value] - ($unit[$value] * $units) < 0)
				{
					$status[] = 'Not enough ' . $value . '.';
				}
			}
			
			if (!empty($unit['mineralspread']))
			{
				$mineralnames = unserialize(MINERALS_ARRAY);
				
				foreach ($unit['mineralspread'] as $key => $value)
				{
					if ($planet['minerals'][$key] - (($value / 100) * $unit['minerals'] * $units) < 0)
					{
						$status[] = 'Not enough ' . $mineralnames[$key] . '.';
					}
				}
			}
			
			if ($this->commission_errorcheck($status)) return;
			
			$planet = &$this->data->planet($this->planet_id);
			
			$resources = array('workers', 'energy');
			foreach ($resources as $value)
			{
				$planet[$value] -= $unit[$value] * $units;
			}
			
			
			if (!empty($unit['mineralspread']))
			{
				foreach ($unit['mineralspread'] as $key => $value)
				{
					$planet['minerals'][$key] -= ($value / 100) * $unit['minerals'] * $units;
				}
			}
			
			$completion = ($units * ($unit['time'] * $_SESSION['round_speed'])) / $planet['production'][$this->unit_type][$unit[$this->unit_type . 'concept_id']];
			
			$warptime = request_variable('warptime');
			if (!is_null($warptime) && $planet['player_id'] == $_SESSION['player_id'])
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
			
			$now = microfloat();
			
			$db_query = "SELECT `completion` FROM `tasks` WHERE `planet_id` = '" . $this->planet_id . "' AND `type` = '" . TASK_UNIT . "' AND `attribute` = '" . $this->unit_type . "' AND `planning` = '" . $unit[$this->unit_type . 'concept_id'] . "' ORDER BY `completion` DESC LIMIT 1";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) > 0)
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$start = $db_row['completion'];
			}
			else
			{
				$start = $now;
			}
			
			// Add the task.
			$task_insert = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'planet_id' => $this->planet_id, 
				'type' => TASK_UNIT, 
				'unit_id' => $this->unit_id, 
				'attribute' => $this->unit_type, 
				'number' => $units, 
				'planning' => $unit[$this->unit_type . 'concept_id'], 
				'completion' => $start + $completion, 
				'start' => $start);
			$this->sql->execute('tasks', $task_insert);
			
			$this->data->save();
			
			if ($output_mode == 'javascript')
			{
				echo 'varCommissioned = true; varError = false;';
				exit;
			}
			
			$this->smarty->append('status', 'The unit is being commissioned.');
			$this->overview();
			exit;
		}
	}
?>