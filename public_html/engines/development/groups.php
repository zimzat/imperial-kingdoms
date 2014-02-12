<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'groups_overview', 
			'groups_list', 
			'groups_view', 
			'groups_modify_units', 
			'groups_modify_resources', 
			'groups_modify_groups', 
			'groups_modify_destination', 
			'groups_modify_targets', 
			'groups_create', 
			'groups_process_create', 
			'groups_abandon');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		
		$fn = substr($fn, 7);
		if ($fn == 'list') $fn = 'overview';
		
		$groups = new Groups($data, $smarty);
		$groups->$fn();
	}
	
	class Groups
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $group_id;
		var $group_type;
		var $group_view;
		
		function Groups(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$this->group_id = request_variable('group_id');
			$this->group_type = request_variable('group_type', NULL, 'army');
			$this->group_view = request_variable('group_view', NULL, 'units');
			
			if (!in_array($this->group_type, array('army', 'navy')))
				error(__FILE__, __LINE__, 'DATA', 'Invalid group type.');
			if (!in_array($this->group_view, array('units', 'targets', 'cargo', 'destination')))
				error(__FILE__, __LINE__, 'DATA', 'Invalid group view.');
			
			if (!empty($this->group_id))
				permissions_check(constant('PERMISSION_' . strtoupper($this->group_type)), $this->group_id, 'military');
			
			$this->smarty->assign('type', $this->group_type);
			$this->smarty->assign('group_type', $this->group_type);
			$this->smarty->assign('group_id', $this->group_id);
			$this->smarty->assign('viewing', $this->group_view);
			$this->smarty->assign('group_view', $this->group_view);
			
			$this->data->updater->update($_SESSION['kingdom_id']);
		}
		
		function overview()
		{
			$grouplist = array();
			
			$this->sql->select(array(
				array($this->group_type . 'groups', $this->group_type . 'group_id', 'group_id'), 
				array($this->group_type . 'groups', 'name'), 
				array($this->group_type . 'groups', 'planet_id'), 
				array($this->group_type . 'groups', 'units')));
			$this->sql->where(array(
				array($this->group_type . 'groups', 'round_id', $_SESSION['round_id']), 
				array($this->group_type . 'groups', 'kingdom_id', $_SESSION['kingdom_id']), 
				array($this->group_type . 'groups', 'player_id', $_SESSION['player_id'])));
			$this->sql->orderby(array($this->group_type . 'groups', 'planet_id', 'ASC'));
			
			if ($this->group_type == 'army')
			{
				$this->sql->select(array(
					array($this->group_type . 'groups', 'navygroup_id'), 
					array($this->group_type . 'groups', 'size')));
				$this->sql->orderby(array($this->group_type . 'groups', 'size', 'DESC'));
			}
			else
			{
				$this->sql->select(array(
					array($this->group_type . 'groups', 'x_current'), 
					array($this->group_type . 'groups', 'y_current'), 
					array($this->group_type . 'groups', 'x_destination'), 
					array($this->group_type . 'groups', 'y_destination'), 
					array($this->group_type . 'groups', 'cargo_current'), 
					array($this->group_type . 'groups', 'cargo_max')));
				$this->sql->orderby(array(
					array($this->group_type . 'groups', 'cargo_current', 'DESC'), 
					array($this->group_type . 'groups', 'cargo_max', 'DESC')));
			}
			
			$this->sql->orderby(array($this->group_type . 'groups', $this->group_type . 'group_id', 'ASC'));
			
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['units'] = unserialize($db_row['units']);
				$grouplist[$db_row['group_id']] = array(
					'name' => htmlentities($db_row['name']), 
					'units' => array_sum($db_row['units']));
				
				if ($this->group_type == 'army')
				{
					$locations = array('planet', 'navygroup');
					foreach ($locations as $value)
					{
						if (empty($db_row[$value . '_id'])) continue;
						
						$this->sql->select(array($value . 's', 'name'));
						$this->sql->where(array($value . 's', $value . '_id', $db_row[$value . '_id']));
						$location_result = $this->sql->execute();
						$location_row = mysql_fetch_array($location_result, MYSQL_ASSOC);
						
						$location = strtoupper($value{0}) . '#' . $db_row[$value . '_id'] . ' ' . strshort(htmlentities($location_row['name']), 10);
						break;
					}
					
					$grouplist[$db_row['group_id']]['location'] = $location;
					$grouplist[$db_row['group_id']]['size'] = format_number($db_row['size'], true);
				}
				else
				{
					$coordinates = map_coordinate($db_row['x_destination'], $db_row['y_destination']);
					
					$this->sql->select(array(
						array('planets', 'planet_id'), 
						array('planets', 'name')));
					$this->sql->where(array(
						array('quadrants', 'round_id', $_SESSION['round_id']), 
						array('starsystems', 'round_id', $_SESSION['round_id']), 
						array('planets', 'round_id', $_SESSION['round_id']), 
						array('quadrants', 'x', $coordinates['quadrant']['x']), 
						array('quadrants', 'y', $coordinates['quadrant']['y']), 
						array('starsystems', 'x', $coordinates['starsystem']['x']), 
						array('starsystems', 'y', $coordinates['starsystem']['y']), 
						array('planets', 'x', $coordinates['planet']['x']), 
						array('planets', 'y', $coordinates['planet']['y']), 
						array('planets', 'starsystem_id', array('starsystems', 'starsystem_id')), 
						array('planets', 'quadrant_id', array('quadrants', 'quadrant_id')),
						array('planets', 'planet_id', $db_row['planet_id'])));
					$this->sql->limit(1);
					$location_result = $this->sql->execute();
					if (mysql_num_rows($location_result) > 0)
					{
						$location_row = mysql_fetch_array($location_result, MYSQL_ASSOC);
						$grouplist[$db_row['group_id']]['location'] = 'P#' . $location_row['planet_id'] . ' ' . strshort($location_row['name'], 15);
					}
					else
					{
						$grouplist[$db_row['group_id']]['location'] = '(' . $coordinates['quadrant']['x'] . ', ' . $coordinates['quadrant']['y'] . '), ' . 
							'(' . $coordinates['starsystem']['x'] . ', ' . $coordinates['starsystem']['y'] . '), ' . 
							'(' . $coordinates['planet']['x'] . ', ' . $coordinates['planet']['y'] . ')';
					}
					
					
					if ($db_row['x_current'] != $db_row['x_destination'] || 
						$db_row['y_current'] != $db_row['y_destination'])
					{
						$this->sql->select(array('tasks', 'completion'));
						$this->sql->where(array(
							array('tasks', 'type', 5), 
							array('tasks', 'group_id', $db_row['group_id'])));
						$this->sql->limit(1);
						$location_result = $this->sql->execute();
						$location_row = mysql_fetch_array($location_result, MYSQL_ASSOC);
						
						$grouplist[$db_row['group_id']]['location'] .= ' ' . format_time(timeparser($location_row['completion'] - microfloat()));
						
						$grouplist[$db_row['group_id']]['transit'] = true;
					}
					
					$grouplist[$db_row['group_id']]['size'] = format_number($db_row['cargo_current'], true) . '/' . format_number($db_row['cargo_max'], true);
				}
			}
			
			$this->smarty->assign('type', $this->group_type);
			$this->smarty->assign('groups', $grouplist);
			$this->smarty->display('groups_overview.tpl');
		}
		
		
		
		function create()
		{
			$player = $this->data->player($_SESSION['player_id']);
			
			if ($player['rank'] < RANK_COMMANDER)
			{
				$this->smarty->append('status', 'Insufficient rank to create military groups.');
				$planetlist = array();
			}
			else
			{
				$db_query = "SELECT `planet_id`, `name` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `player_id` = '" . $_SESSION['player_id'] . "' ORDER BY `planet_id`";
				$db_result = mysql_query($db_query);
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$planetlist[$db_row['planet_id']] = 'P#' . $db_row['planet_id'] . ' ' . htmlentities(strshort($db_row['name'], 20));
				}
			}
			
			$this->smarty->assign('planets', $planetlist);
			$this->smarty->display('groups_create.tpl');
		}
		
		function process_create()
		{
			$player = $this->data->player($_SESSION['player_id']);
			
			if ($player['rank'] < RANK_COMMANDER)
			{
				$this->smarty->append('status', 'Insufficient rank to create military groups.');
				$this->create();
				exit;
			}
			
			$planet_id = abs((int)request_variable('planet_id', 'post', 0));
			$name = request_variable('name', 'post', '');
			
			if (empty($planet_id))
				$status[] = 'Must select a planet for group.';
			else
			{
				$permission = permissions_check(PERMISSION_PLANET, $planet_id, 'military', false);
				if ($permission['military'] == false)
					$status[] = 'You do not have permission to create groups on that planet';
			}
			
			if ($error = str_check($name, array(3, 20, REGEXP_NAME_PLANET)))
				$status[] = 'Group name error: ' . implode(' ', $error) . '<br />';
			
			if (!empty($status))
			{
				$this->smarty->append('status', $status);
				$this->create();
				exit;
			}
			
			$planet = $this->data->planet($planet_id);
			
			$insert_group = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'player_id' => $planet['player_id'], 
				'planet_id' => $planet['planet_id'], 
				'name' => $name, 
				'units' => array());
			
			if ($this->group_type == 'navy')
			{
				$this->sql->select(array(
					array('quadrants', 'x', 'x_quadrant'), 
					array('quadrants', 'y', 'y_quadrant'), 
					array('starsystems', 'x', 'x_starsystem'), 
					array('starsystems', 'y', 'y_starsystem')));
				$this->sql->where(array(
					array('starsystems', 'starsystem_id', $planet['starsystem_id']), 
					array('quadrants', 'quadrant_id', $planet['quadrant_id'])));
				$db_result = $this->sql->execute();
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$x = ($db_row['x_quadrant'] * 49) + ($db_row['x_starsystem'] * 7) + $planet['x'];
				$y = ($db_row['y_quadrant'] * 49) + ($db_row['y_starsystem'] * 7) + $planet['y'];
				
				$insert_group = $insert_group + array(
					'x_current' => $x, 
					'y_current' => $y, 
					'x_destination' => $x, 
					'y_destination' => $y, 
					'cargo' => array());
			}
			
			$this->sql->execute($this->group_type . 'groups', $insert_group);
			$this->group_id = mysql_insert_id();
			
			redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type);
		}
		
		function isGroupEmpty($group)
		{
			$empty = true;
			
			if (!empty($group['units']))
			{
				foreach ($group['units'] as $value)
				{
					if (empty($value)) continue;
					$empty = false;
					break;
				}
			}
			
			return $empty;
		}
		
		function abandon()
		{
			$group = &$this->data->group($this->group_type, $this->group_id);
			
			if ($this->isGroupEmpty($group))
			{
				$group = NULL;
				$this->data->save();
				redirect('groups.php?fn=groups_overview&group_type=' . $this->group_type);
			}
			else
			{
				$_SESSION['status'][] = 'Group is not empty; Cannot abandon group.';
				redirect('groups.php?fn=groups_view&group_view=units&group_id=' . $this->group_id . '&group_type=' . $this->group_type);
			}
		}
		
		function view()
		{
			$group = $this->data->group($this->group_type, $this->group_id);
			$group_permissions = permissions_check(constant('PERMISSION_' . strtoupper($this->group_type)), $this->group_id, 'military');
			
			// Viewing->Units
				// Permission: Group & Planet & Group Owner = Planet Owner
			// Viewing->Cargo
				// Permission: Group & Planet & Group Owner = Planet Owner
			// Viewing->Destination
				// Permission: Group
			// Viewing->Targets
				// Permission: Group
			
			$onplanet = false;
			if (in_array($this->group_view, array('units', 'cargo')) && 
				(($this->group_type == 'army' && !empty($group['planet_id'])) || 
				($this->group_type == 'navy' && $group['x_current'] == $group['x_destination'] && $group['y_current'] == $group['y_destination'])))
			{
				$planet = $this->data->planet($group['planet_id']);
				
				// Do they have permission to touch anything on this planet?
				$planet_permissions = permissions_check(PERMISSION_PLANET, $group['planet_id'], 'military', false);
				
				if (!empty($planet))
				{
					$onplanet = true;
					$this->smarty->assign('onplanet', true);
				}
			}
			
			if (empty($planet['planet_id'])) $planet['planet_id'] = '';
			
			// ##################################################
			// Viewing->Units
			if ($this->group_view == 'units')
			{
				$group_empty = $this->isGroupEmpty($group);
				
				$units = $group['units'];
				if ($onplanet && $planet_permissions['owner'] && !empty($planet['units'][$this->group_type]))
					$units = $units + $planet['units'][$this->group_type];
				
				$units = $this->data->blueprint($this->group_type, array_keys($units));
				
				$unitlist = array();
				foreach ($units as $unit_id => $unit)
				{
					$unitlist[$unit_id]['name'] = htmlentities($unit['name']);
					
					// Units on planet
					if ($planet_permissions['owner'] && !empty($planet['units'][$this->group_type][$unit_id]))
						$unitlist[$unit_id]['planet'] = format_number($planet['units'][$this->group_type][$unit_id]);
					
					// Units in group
					if (!empty($group['units'][$unit_id]))
						$unitlist[$unit_id]['group'] = format_number($group['units'][$unit_id]);
					
					// Unit size/cargo capacity
					if ($this->group_type == 'army')
						$unitlist[$unit_id]['size'] = format_number($unit['size'], true);
					else
						$unitlist[$unit_id]['cargo'] = format_number($unit['cargo'], true);
				}
				
				$this->smarty->assign('group_empty', $group_empty);
				$this->smarty->assign('units', $unitlist);
			}
			
			// ##################################################
			// Viewing->Cargo
			elseif ($this->group_view == 'cargo' && $this->group_type == 'navy')
			{
				if (empty($group['cargo']['food'])) $group['cargo']['food'] = 0;
				if (empty($group['cargo']['workers'])) $group['cargo']['workers'] = 0;
				if (empty($group['cargo']['energy'])) $group['cargo']['energy'] = 0;
				if (empty($group['cargo']['minerals'])) $group['cargo']['minerals'] = array();
				
				$resources['group'] = array(
					'food' => $group['cargo']['food'], 
					'workers' => $group['cargo']['workers'], 
					'energy' => $group['cargo']['energy']);
				
				$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
				foreach ($group['cargo']['minerals'] as $key => $value)
				{
					$resources['group']['minerals'][$mineralnames[$key]] = $value;
				}
				
				// If on planet group owns
				if ($onplanet && $planet_permissions['grant'] && $planet['player_id'] == $group['player_id'])
				{
					$resources['planet'] = array(
						'food' => format_number($planet['food'], true), 
						'workers' => format_number($planet['workers'], true), 
						'energy' => format_number($planet['energy'], true));
					
					foreach ($planet['minerals'] as $key => $value)
					{
						$resources['planet']['minerals'][$mineralnames[$key]] = format_number($value, true);
					}
				}
				
				$this->sql->select(array(
					array('armygroups', 'armygroup_id'), 
					array('armygroups', 'name'), 
					array('armygroups', 'size'), 
					array('armygroups', 'navygroup_id')));
				$this->sql->leftjoin(array('navygroups', 'navygroup_id', array('armygroups', 'navygroup_id')));
				$this->sql->where(array(
					array('armygroups', 'round_id', $_SESSION['round_id']), 
					array('armygroups', 'kingdom_id', $_SESSION['kingdom_id'])));
				if ($onplanet)
				{
					$this->sql->leftjoin(array('planets', 'planet_id', array('armygroups', 'planet_id')));
					$this->sql->select(array('planets', 'planet_id'));
				}
				
				$db_query = $this->sql->generate();
				
				if ($onplanet) $db_query .= " AND (`armygroups`.`planet_id` = '" . $planet['planet_id'] . "' OR `armygroups`.`navygroup_id` = '" . $group['navygroup_id'] . "')";
				else $db_query .= " AND `armygroups`.`navygroup_id` = '" . $group['navygroup_id'] . "'";
				$db_result = mysql_query($db_query);
				
				$grouplist = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$grouplist[$db_row['armygroup_id']] = array(
						'group_id' => $db_row['armygroup_id'], 
						'name' => strshort($db_row['name'], 20), 
						'size' => format_number($db_row['size'], true)
					);
					if (!empty($db_row['navygroup_id']))
					{
						$grouplist[$db_row['armygroup_id']]['location'] = 'group';
					}
					else
					{
						$grouplist[$db_row['armygroup_id']]['location'] = 'planet';
					}
				}
				
				$this->smarty->assign('groups', $grouplist);
				$this->smarty->assign('resources', $resources);
			}
			
			
			
			// ##################################################
			// Viewing->Destination
			elseif ($this->group_view == 'destination' && $this->group_type == 'navy')
			{
				
			}
			
			
			
			// ##################################################
			// Viewing->Targets
			elseif ($this->group_view == 'targets')
			{
				$this->sql->select(array($this->group_type . 'blueprints', 'weapons'));
				$this->sql->where(array($this->group_type . 'blueprints', $this->group_type . 'blueprint_id', array_keys($group['units']), 'IN'));
				$db_result = $this->sql->execute();
				
				$weapons = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['weapons'] = unserialize($db_row['weapons']);
					
					if (is_array($db_row['weapons']))
						$weapons = $weapons + $db_row['weapons'];
				}
				
				if (!empty($weapons))
				{
					$this->sql->select(array(
						array('weaponblueprints', 'weaponblueprint_id', 'weapon_id'), 
						array('weaponblueprints', 'name')));
					$this->sql->where(array('weaponblueprints', 'weaponblueprint_id', array_keys($weapons), 'IN'));
					$db_result = $this->sql->execute();
					
					$weapons = array();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$weapons[$db_row['weapon_id']] = array(
							'weapon_id' => $db_row['weapon_id'], 
							'name' => $db_row['name']);
						if (isset($group['targets'][$db_row['weapon_id']]))
							$weapons[$db_row['weapon_id']]['target_id'] = $group['targets'][$db_row['weapon_id']];
					}
					
					if (!empty($weapons))
					{
						$this->sql->select(array(
							array($this->group_type . 'concepts', $this->group_type . 'concept_id', 'target_id'), 
							array($this->group_type . 'concepts', 'name')));
						$this->sql->orderby(array($this->group_type . 'concepts', $this->group_type . 'concept_id', 'asc'));
						$db_result = $this->sql->execute();
						
						$targets = array();
						while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
						{
							$targets[$db_row['target_id']] = array(
								'target_id' => $db_row['target_id'], 
								'name' => $db_row['name']);
						}
						
						$this->smarty->assign('targets', $targets);
						$this->smarty->assign('weapons', $weapons);
					}
				}
			}
			
			$this->smarty->display('groups_view.tpl');
		}
		
		
		
		function modify_units()
		{
			
			if (empty($_POST['units']))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input specified for this function.');
			
			
			foreach ($_POST['units'] as $key => $value)
			{
				$value = str_replace(array($_SESSION['preferences']['thousands_seperator'], $_SESSION['preferences']['decimal_symbol']), array('', '.'), $value);
				$key = (int)$key;
				$value = abs((int)$value);
				if (!empty($key) && !empty($value))
				{
					$units[$key] = $value;
				}
			}
			
			$group = &$this->data->group($this->group_type, $this->group_id);
			if (empty($group['planet_id']) || 
				($this->group_type == 'navy' && 
					($group['x_current'] != $group['x_destination'] || $group['y_current'] != $group['y_destination'])))
			{
				$status[] = 'Cannot move units from/to group.';
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			$planet = &$this->data->planet($group['planet_id']);
			
			if ($planet['player_id'] != $_SESSION['player_id'])
			{
				$status[] = 'You must own the planet to load or unload units.';
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			if ($this->group_type == 'navy')
			{
				$loadtype = 'cargo';
				$loadextratype = '_max';
				$this->sql->select(array($this->group_type . 'blueprints', 'cargo'));
			}
			else
			{
				$loadtype = 'size';
				$loadextratype = '';
				$this->sql->select(array($this->group_type . 'blueprints', 'size'));
			}
			
			if (empty($planet['units'][$this->group_type]))
				$planet['units'][$this->group_type] = array();
			if (empty($group['units']))
				$group['units'] = array();
			
			$allunits = $group['units'] + $planet['units'][$this->group_type];
			$allunits = array_keys($allunits);
			
			$blueprints = &$this->data->blueprint($this->group_type, $allunits);
			
			foreach ($blueprints as $blueprint_id => $blueprint)
			{
				if (empty($units[$blueprint_id])) continue;
				
				if (isset($_POST['load']))
				{
					if (empty($planet['units'][$this->group_type][$blueprint_id])) continue;
					
					if ($planet['units'][$this->group_type][$blueprint_id] <= $units[$blueprint_id])
						$units[$blueprint_id] = $planet['units'][$this->group_type][$blueprint_id];
					
					@$group['units'][$blueprint_id] += $units[$blueprint_id];
					$group[$loadtype . $loadextratype] += $units[$blueprint_id] * $blueprint[$loadtype];
					
					$planet['units'][$this->group_type][$blueprint_id] -= $units[$blueprint_id];
					if (empty($planet['units'][$this->group_type][$blueprint_id]))
						unset($planet['units'][$this->group_type][$blueprint_id]);
				}
				else
				{
					if (empty($group['units'][$blueprint_id])) continue;
					
					if ($group['units'][$blueprint_id] <= $units[$blueprint_id])
						$units[$blueprint_id] = $group['units'][$blueprint_id];
					
					@$planet['units'][$this->group_type][$blueprint_id] += $units[$blueprint_id];
					
					$group[$loadtype . $loadextratype] -= $units[$blueprint_id] * $blueprint[$loadtype];
					$group['units'][$blueprint_id] -= $units[$blueprint_id];
					if (empty($group['units'][$blueprint_id]))
						unset($group['units'][$blueprint_id]);
				}
				
				if ($this->group_type == 'navy' && $group['cargo_current'] > $group['cargo_max'])
				{
					$status[] = 'Unload some cargo before removing units.';
					$this->smarty->append('status', $status);
					$this->overview();
					exit;
				}
			}
			
			$this->data->save();
			$status[] = 'Successfully moved units.';
			
			$_SESSION['status'][] = $status;
			redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
		}
		
		
		
		function modify_resources()
		{
			if ($this->group_type != 'navy')
				error(__FILE__, __LINE__, 'INVALID_GROUP_TYPE', 'Invalid group type specified.');
			
			if (empty($_POST['resources']))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input specified.');
			
			foreach ($_POST['resources'] as $key => $value)
			{
				if ($key == 'minerals')
				{
					foreach ($value as $mineralkey => $mineralvalue)
					{
						if (!empty($mineralvalue))
						{
							$mineralvalue = str_replace(array($_SESSION['preferences']['thousands_seperator'], $_SESSION['preferences']['decimal_symbol']), array('', '.'), $mineralvalue);
							$minerals[$mineralkey] = abs((int)$mineralvalue);
						}
					}
					
					$value = $minerals;
				}
				else
				{
					$value = str_replace(array($_SESSION['preferences']['thousands_seperator'], $_SESSION['preferences']['decimal_symbol']), array('', '.'), $value);
					$value = abs((int)$value);
				}
				
				if (!empty($key) && !empty($value))
				{
					$resources[$key] = $value;
				}
			}
			
			$group = &$this->data->group('navy', $this->group_id);
			if (empty($group['planet_id']))
			{
				die('refine error: ' . __LINE__);
			}
			
			$planet = &$this->data->planet($group['planet_id']);
			
			$permissions = permissions_check(PERMISSION_PLANET, $planet['planet_id'], 'military', false);
			if (!$permissions['grant'])
			{
				$_SESSION['status'][] = 'You do not have permission to that planet.';
				redirect('groups.php?group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
			}
			
			$resourcearray = array('food', 'workers', 'energy');
			foreach ($resourcearray as $value)
			{
				if (empty($resources[$value])) continue;
				
				if (isset($_POST['load']))
				{
					if ($planet[$value] < $resources[$value])
						$resources[$value] = $planet[$value];
					
					// Handle size change for resources
					if ($value == 'workers') $size = $resources[$value] * 70;
					else $size = $resources[$value];
					
					$planet[$value] -= $resources[$value];
					$group['cargo'][$value] += $resources[$value];
					$group['cargo_current'] += $size;
				}
				else
				{
					if ($group['cargo'][$value] < $resources[$value])
						$resources[$value] = $group['cargo'][$value];
					
					if ($value == 'workers') $size = $resources[$value] * 70;
					else $size = $resources[$value];
					
					$planet[$value] += $resources[$value];
					$group['cargo'][$value] -= $resources[$value];
					$group['cargo_current'] -= $size;
				}
			}
			
			$mineralkeys = array('fe' => 0, 'o' => 1, 'si' => 2, 'mg' => 3, 'ni' => 4, 's' => 5, 'he' => 6, 'h' => 7);
			foreach ($resources['minerals'] as $key => $value)
			{
				$key = $mineralkeys[$key];
				$value = (int)$value;
				if (isset($_POST['load']))
				{
					if ($planet['minerals'][$key] < $value)
						$value = $planet['minerals'][$key];
					
					$group['cargo']['minerals'][$key] += $value;
					
					$group['cargo_current'] += $value;
					$planet['minerals'][$key] -= $value;
				}
				else
				{
					if ($group['cargo']['minerals'][$key] < $value)
						$value = $group['cargo']['minerals'][$key];
					
					$planet['minerals'][$key] += $value;
					
					$group['cargo_current'] -= $value;
					$group['cargo']['minerals'][$key] -= $value;
				}
			}
			
			if ($group['cargo_current'] > $group['cargo_max'])
			{
				$status[] = 'Too many resources to fit in this group.';
				$this->smarty->append('status', $status);
				$this->view();
				exit;
			}
			else
			{
				$this->data->save();
			}
			
			redirect('groups.php?group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
		}
		
		
		
		function modify_targets()
		{
			if (empty($_POST['weapons']))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input.');
			
			permissions_check(constant('PERMISSION_' . strtoupper($this->group_type)), $this->group_id, 'military');
			
			$group = &$this->data->group($this->group_type, $this->group_id);
			
			foreach ($_POST['weapons'] as $key => $value)
			{
				$key = (int)$key;
				if (!empty($key))
				{
					if (empty($value))
					{
						unset($group['targets'][$key]);
					}
					else
					{
						$group['targets'][$key] = $value;
					}
				}
			}
			
			$this->data->save();
			
			redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
		}
		
		function modify_groups()
		{
			if (empty($_POST['groups']) || $this->group_type != 'navy')
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input given.');
			
			$groups = array();
			foreach ($_POST['groups'] as $key => $value)
			{
				$key = (int)$key;
				if (!empty($key) && !empty($value))
				{
					$groups[$key] = $value;
				}
			}
			
			$group = &$this->data->group('navy', $this->group_id);
			if (empty($group['planet_id']))
			{
				exit('refine error: ' . __LINE__);
			}
			
			$planet = &$this->data->planet($group['planet_id']);
			
			$armygroups = &$this->data->group('army', array_keys($groups));
			foreach ($armygroups as $armygroup_id => $armygroup)
			{
				if ($groups[$armygroup_id] == 'planet')
				{
					$armygroups[$armygroup_id]['planet_id'] = $planet['planet_id'];
					$armygroups[$armygroup_id]['navygroup_id'] = 0;
					$group['cargo_current'] -= $armygroup['size'];
				}
				else
				{
					$armygroups[$armygroup_id]['planet_id'] = 0;
					$armygroups[$armygroup_id]['navygroup_id'] = $this->group_id;
					$group['cargo_current'] += $armygroup['size'];
				}
			}
			
			if ($group['cargo_current'] > $group['cargo_max'])
			{
				$status[] = 'Army groups too large to fit in this navy group';
			}
			else
			{
				$this->data->save();
			}
			
			if (!empty($status)) $this->smarty->append('status', $status);
			$this->view();
			exit;
		}
		
		
		
		function modify_destination()
		{
			if (empty($this->group_type) || empty($this->group_id))
				error(__FILE__, __LINE__, 'DATA', 'Null type or group id.');
			
			if ($this->group_type != 'navy')
				error(__FILE__, __LINE__, 'INVALID_GROUP_TYPE', 'Invalid group type for this function.');
			
			if (empty($_POST['destination']))
				error(__FILE__, __LINE__, 'INVALID_INPUT', 'Invalid input specified for this function');
			
			$destination = $_POST['destination'];
			
			if (!empty($destination['planet_id']))
			{
				$planet_id = abs((int)$destination['planet_id']);
				
				$planet = &$this->data->planet($planet_id);
				$player = &$this->data->player($planet['player_id']);
				$group = &$this->data->group('navy', $this->group_id);
				
				$this->sql->select(array(
					array('quadrants', 'x', 'x_quadrant'), 
					array('quadrants', 'y', 'y_quadrant'), 
					array('starsystems', 'x', 'x_starsystem'), 
					array('starsystems', 'y', 'y_starsystem')));
				$this->sql->where(array(
					array('quadrants', 'quadrant_id', $planet['quadrant_id']), 
					array('starsystems', 'starsystem_id', $planet['starsystem_id'])));
				$this->sql->limit(1);
				
				$db_result = $this->sql->execute();
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$starsystem = array('x' => $db_row['x_starsystem'], 'y' => $db_row['y_starsystem']);
				$quadrant = array('x' => $db_row['x_quadrant'], 'y' => $db_row['y_quadrant']);
				
				if (empty($group['units']))
				{
					$_SESSION['status'][] = 'That group isn\'t going anywhere. You need at least one unit in a group to send it somewhere.';
					redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
				}
				
				if ($planet['kingdom_id'] != $_SESSION['kingdom_id'])
				{
					$kingdom = &$this->data->kingdom($_SESSION['kingdom_id']);
					
//					 print_r($kingdom);
					
					if (!isset($kingdom['enemies'][$planet['kingdom_id']]) && 
						!isset($kingdom['allies'][$planet['kingdom_id']]))
					{
						$status[] = 'You must declare war before sending fleets to attack, or an alliance to assist.';
						
						$_SESSION['status'][] = $status;
						redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
					}
				}
				
				$this->sql->select(array('navyblueprints', 'speed'));
				$this->sql->where(array('navyblueprints', 'navyblueprint_id', array_keys($group['units']), 'IN'));
				$this->sql->orderby(array('navyblueprints', 'speed', 'ASC'));
				$this->sql->limit(1);
				$db_result_speed = $this->sql->execute();
				$db_row_speed = mysql_fetch_array($db_result_speed, MYSQL_ASSOC);
				if ($db_row_speed['speed'] == 0)
				{
					$_SESSION['status'][] = 'That group isn\'t going anywhere. Your group can\'t go anywhere if there are stationary units in it.';
					redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
				}
				elseif ($db_row_speed['speed'] > 1)
				{
					$group['speed'] = (400 - $db_row_speed['speed']) / 400;
				}
				else
				{
					$group['speed'] = 1;
				}
				
				if ($group['x_current'] != $group['x_destination'] || $group['y_current'] != $group['y_destination'])
				{
					$this->sql->select(array('tasks', 'completion'));
					$this->sql->where(array('tasks', 'group_id', $this->group_id));
					$this->sql->limit(1);
					$existing_result = $this->sql->execute();
					if (mysql_num_rows($existing_result) > 0)
					{
						$existingtask = mysql_fetch_array($existing_result, MYSQL_ASSOC);
						
//						 $temp['x'] = $group['x_destination'] - $group['x_current'];
//						 $temp['y'] = $group['y_destination'] - $group['y_current'];
//						 $temp['distance'] = sqrt(($temp['x'] * $temp['x']) + ($temp['y'] * $temp['y']));
						
						$temp['distance'] = plot_distance(array($group['x_current'], $group['y_current']), array($group['x_destination'], $group['y_destination']));
						$temp['time'] = $temp['distance'] * (1200 * $_SESSION['round_speed']) * $group['speed'];
						
						$temp['now'] = microfloat();
						
						$temp['completed'] = ($temp['time'] - ($existingtask['completion'] - $temp['now'])) / $temp['time'];
						
						$group['x_current'] += $temp['completed'] * ($group['x_destination'] - $group['x_current']);
						$group['y_current'] += $temp['completed'] * ($group['y_destination'] - $group['y_current']);
						
						$db_query = "DELETE FROM `tasks` WHERE `group_id` = '" . $this->group_id . "'";
						$db_result = mysql_query($db_query);
					}
				}
				
				$x[0] = $group['x_current'];
				$y[0] = $group['y_current'];
				
				$x[1] = ($quadrant['x'] * 49) + ($starsystem['x'] * 7) + $planet['x'];
				$y[1] = ($quadrant['y'] * 49) + ($starsystem['y'] * 7) + $planet['y'];
				
				$group['planet_id'] = $planet['planet_id'];
			}
			else
			{
				$_SESSION['status'][] = 'Invalid input specified for this function.';
				redirect('groups.php?fn=groups_view&group_view=' . $this->group_view . '&group_id=' . $this->group_id . '&group_type=' . $this->group_type);
			}
			
			$distance = plot_distance(array($x[0], $y[0]), array($x[1], $y[1]));
			$time = $distance * (1200 * $_SESSION['round_speed']) * $group['speed'];
			
			$minimum_time = 7200 * $_SESSION['round_speed'];
			if ($time < $minimum_time)
				$time = $minimum_time;
			
			$group['x_destination'] = $x[1];
			$group['y_destination'] = $y[1];
			
			if ($planet['kingdom_id'] != $_SESSION['kingdom_id'])
			{ // Set combat alert
				$this->sql->set(array('players', 'combat', 1));
				$this->sql->where(array('players', 'kingdom_id', $planet['kingdom_id']));
				$this->sql->execute();
			}
			
			$now = microfloat();
			
			$insert_task = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'player_id' => $_SESSION['player_id'], 
				'group_id' => $this->group_id, 
				'type' => TASK_NAVY, 
				'start' => $now, 
				'completion' => $now + $time);
			
			if (!empty($group['planet_id']))
			{
				$insert_task['planet_id'] = $group['planet_id'];
				$insert_task['target_kingdom_id'] = $planet['kingdom_id'];
				
				$round = $this->data->round($_SESSION['round_id']);
				
				$db_query = "SELECT `completion` FROM `combat` WHERE `planet_id` = '" . $group['planet_id'] . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				if ($db_result && $db_result > 0)
				{
					$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
					$next_combat = $db_row['completion'];
				}
				else
				{
					$this->sql->select(array('tasks', 'completion'));
					$this->sql->where(array('tasks', 'planet_id', $db_row['planet_id']));
					$this->sql->orderby(array('tasks', 'completion', 'asc'));
					$this->sql->limit(1);
					$db_result = $this->sql->execute();
					
					if (mysql_num_rows($db_result) > 0)
					{
						$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
						$next_combat = $db_row['completion'];
					}
					else
					{
						$next_combat = $insert_task['completion'] + $round['combattick'] - 0.01;
					}
				}
				
				$next_update = ($next_combat - $insert_task['completion']) / $round['combattick'];
				$insert_task['completion'] += (ceil($next_update) - $next_update) * $round['combattick'];
			}
			
			$this->sql->execute('tasks', $insert_task);
			
			$this->data->save();
			
			$status[] = 'Successfully set destination.';
			
			if (!empty($status)) $_SESSION['status'][] = $status;
			redirect('groups.php?fn=groups_view&group_id=' . $this->group_id . '&group_type=' . $this->group_type . '&group_view=' . $this->group_view);
		}
	}
	
	function plot_distance($start_point, $end_point)
	{
		$start_point = array_altkey($start_point, array('x', 'y'));
		$end_point = array_altkey($end_point, array('x', 'y'));
		
		return sqrt(pow($start_point[0] - $end_point[0], 2) + pow($start_point[1] - $end_point[1], 2));
	}
	
	function plot_center($first_point, $second_point)
	{
		$first_point = array_altkey($first_point, array('x', 'y'));
		$second_point = array_altkey($second_point, array('x', 'y'));
		
		return array(($first_point[0] + $second_point[0]) / 2, ($first_point[1] + $second_point[1]) / 2);
	}
	
	function circle_radius($center_point, $edge_point)
	{
		$center_point = array_altkey($center_point, array('x', 'y'));
		$edge_point = array_altkey($edge_point, array('x', 'y'));
		
		return sqrt(pow($edge_point[0] - $center_point[0], 2) + pow($edge_point[1] - $center_point[1], 2));
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
?>