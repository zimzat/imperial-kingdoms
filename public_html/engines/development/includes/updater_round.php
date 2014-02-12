<?php
	/* Copyright (c) 2006 Zimzat */
	
	require_once(dirname(__FILE__) . '/updater_combat.php');
	
	class Updater_Round
	{
		var $combat;
		
		function Updater_Round()
		{
			data::initialize();
			$this->combat = &new Updater_Combat;
		}
		
//   ----------------------------------------------------------------------------- {
		
		function update($kingdom_ids = 0, $planet_ids = 0, $player_ids = 0)
		{
			if (empty($kingdom_ids) && empty($planet_ids) && empty($player_ids))
			{
				return;
			}
			
			// If round stop time is over, don't update anything.
			$round = $this->data->round();
			if ($round['stoptime'] < time())
			{
				return;
			}
			
			// Turn into an array if not already.
			if (!empty($kingdom_ids) && !is_array($kingdom_ids))
				$kingdom_ids = array($kingdom_ids);
			if (!empty($planet_ids) && !is_array($planet_ids))
				$planet_ids = array($planet_ids);
			if (!empty($player_ids) && !is_array($player_ids))
				$player_ids = array($player_ids);
			
			
			// Fill in missing values
			// Kingdom returns list of players
			// Players returns list of planets
			if (!empty($kingdom_ids))
			{
				$player_ids = $this->update_kingdoms($kingdom_ids);
				$planet_ids = $this->update_players($player_ids);
			}
			elseif (!empty($player_ids))
			{
				$planet_ids = $this->update_players($player_ids);
			}
			
			// Run update
			$this->log[] = 'Combat';
			$this->combat->update_combat();
			$this->log[] = 'Tasks';
			$this->update_tasks($planet_ids);
			$this->log[] = 'Resources';
			$this->update_resources($planet_ids);
			
			$this->log[] = 'Save';
			$this->data->save();
			
			if (!empty($_SESSION['admin']) && $_SESSION['admin'] == true)
			{
				$log_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'kingdom_id' => $_SESSION['kingdom_id'], 
					'player_id' => $_SESSION['player_id'], 
					'user_id' => $_SESSION['user_id'], 
					'type' => 'UPDATER', 
					'log' => serialize($this->log));
				$this->sql->execute('logs', $log_insert);
			}
			
			// Hack to keep cranes from getting out of sync
			// If anyone ever finds the cause of this please let me know.
			$this->update_cranes($planet_ids);
		}
		
		function update_kingdoms($kingdom_ids)
		{
			$kingdoms =& $this->data->kingdom($kingdom_ids);
			
			$player_ids = array();
			foreach ($kingdoms as $kingdom)
			{
				if (isset($kingdom['members'][0]))
				{
					unset($kingdom['members'][0]);
				}
				
				$player_ids = $player_ids + $kingdom['members'];
			}
			
			return array_keys($player_ids);
		}
		
		/**
		 * @param array player ids
		 * @return array planet ids
		 */
		function update_players($player_ids)
		{
			$players = $this->data->player($player_ids);
			
			$planet_ids = array();
			foreach ($players as $player_id => $player)
			{
				$planet_ids = $planet_ids + $player['planets'];
			}
			
			return array_keys($planet_ids);
		}
		
// } ----------------------------------------------------------------------------- {
		
		function update_resources($planet_ids, $update_to = '')
		{
			if (empty($update_to))
			{
				$update_to = $this->data->update_to();
			}
			
			if (!is_array($planet_ids)) $planet_ids = array($planet_ids);
			
			$resources = array(
				'food' => SCORE_FOOD, 
				'workers' => SCORE_WORKERS, 
				'energy' => SCORE_ENERGY);
			
			$round = &$this->data->round();
			$planets = &$this->data->planet($planet_ids);
			if (empty($planets))
			{
				$this->log[] = 'Error loading planets:';
				$this->log[] = $planet_ids;
				return;
			}
			
			foreach (array_keys($planets) as $planet_id)
			{
				$planet = &$planets[$planet_id];
				
				$this->log[] = 'Updating Resources for planet: ' . $planet_id;
				
				if (empty($planet['kingdom_id']) || empty($planet['player_id'])) continue;
				
				$kingdom = &$this->data->kingdom($planet['kingdom_id']);
				$score = 0;
				
				$last_update = $update_to - $planet['lastupdated'];
				
				if ($last_update < $round['resourcetick']) continue;
				
				$time_left = $last_update % $round['resourcetick'];
				$resource_updates = floor($last_update / $round['resourcetick']);
				
				$resource_deficiency = 0;
				
				foreach ($resources as $resource => $value)
				{
					$resource_change = $resource_updates * $planet[$resource . 'rate'];
					$resource_change += $resource_deficiency;
					
					if ($resource_change == 0) continue;
					
					// If negative income and less stock than change...
					if ($resource_change < 0 && (int)$planet[$resource] < abs($resource_change))
					{
						$resource_deficiency = $resource_change + $planet[$resource];
						$resource_change = $planet[$resource];
					}
					else
					{
						$resource_deficiency = 0;
					}
					
					$score += $resource_change * $value;
					$planet[$resource] += $resource_change;
					$kingdom[$resource] += $resource_change;
				}
				
				// The following code is bugged. Minerals already extracted are becoming negative on a planet.
				// This if statement was added, but does it do what it is intended to do?
				if ($planet['mineralsrate'] + $resource_deficiency > 0)
				{
					$leftover = $leftover_value = $extraction_total = 0;
					foreach ($planet['extractionrates'] as $key => $value)
					{
						// Figure out how much % is translated into minerals
						$extraction = ($value / 100) * ($planet['mineralsrate'] * $resource_updates) + $leftover;
						// Decrease by the deficiency of food/workers/energy
						$extraction += ($value / 100) * $resource_deficiency;
						
						// Less than zero? Nothing gets extracted.
						if ($extraction < 0) $extraction = 0;
						
						// If not enough of that mineral is available
						if ($planet['mineralsremaining'][$key] < $extraction)
						{
							// carry over to the next mineral and only extract as much is available.
							$leftover = $extraction - $planet['mineralsremaining'][$key];
							$extraction = $planet['mineralsremaining'][$key];
							$leftover_value += $value;
						}
						else
						{
							if ($leftover_value)
							{
								$planet['extractionrates'][$key] += $leftover_value;
								$leftover_value = 0;
							}
							$leftover = 0;
						}
						
						$extraction_total += $extraction;
						
						$score += $extraction * SCORE_MINERALS;
						$planet['minerals'][$key] += $extraction;
						$planet['mineralsremaining'][$key] -= $extraction;
					}
					
					$kingdom['minerals'] += $extraction_total;
				}
				
				$planet['lastupdated'] = $update_to - $time_left;
				
				$this->update_score($planet_id, $score);
			}
		}
		
// } ----------------------------------------------------------------------------- {
		
		function update_tasks($planet_ids, $update_to = '')
		{
			$tasks = $this->get_tasks($planet_ids, $update_to);
			
			$task_types = array(
				TASK_BUILD => 'build', 
				TASK_RESEARCH => 'research', 
				TASK_UPGRADE => 'upgradedesign', 
				TASK_UNIT => 'unit', 
				TASK_NAVY => 'navy');
			
			foreach (array_keys($tasks) as $task_id)
			{
				if (!isset($task_types[$tasks[$task_id]['type']]))
				{
					print_r($tasks[$task_id]);
					continue;
				}
				$task_fn = 'task_' . $task_types[$tasks[$task_id]['type']];
				$this->$task_fn($tasks[$task_id]);
				
				// This... may be causing a problem.
				// Is the data management system checking for null values or empty values?
				// It's not possible to check for null values as is_null() returns true for unset and null variables.
				$tasks[$task_id] = NULL;
			}
		}
		
		function get_tasks($planet_ids, $update_to = '')
		{
			if (empty($update_to))
			{
				$update_to = $this->data->update_to();
			}
			
			if (!is_array($planet_ids))
			{
				$planet_ids = array($planet_ids);
			}
			
			/*
				Get all tasks where ...
					type = research and kingdom_id = this one
					type = build and planet_id = this one
					type = navy and kingdom_id = this one OR
						target_kingdom_id = this one
			*/
			
			$db_query = "
				SELECT task_id 
				FROM `tasks` 
				WHERE 
					`completion` <= " . $update_to . " AND 
					(
						`planet_id` IN ('" . implode("', '", $planet_ids) . "') OR 
						(
							`type` = '" . TASK_NAVY . "' AND 
							(
								`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' OR 
								`target_kingdom_id` = '" . $_SESSION['kingdom_id'] . "'
							)
						)
					)
				ORDER BY `completion` ASC";
			$db_result = mysql_query($db_query);
			
			$tasks = array();
			if ($db_result && mysql_num_rows($db_result) > 0)
			{
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$tasks[] = $db_row['task_id'];
				}
				
				// Get all the tasks using the data abstraction / management system.
				$tasks = &$this->data->task($tasks);
			}
			
			return $tasks;
		}
		
		function task_build(&$task)
		{
			$this->log[] = 'Task Build: ' . $task['building_id'];
			// Retroactive resource upkeep
			$this->update_resources($task['planet_id'], $task['completion']);
			
			// Ask the DAL for the relevant information
			$planet = &$this->data->planet($task['planet_id']);
			$kingdom = &$this->data->kingdom($planet['kingdom_id']);
			$building = &$this->data->building($task['building_id']);
			
			// Calculate how many were built.
			$building_count = $task['number'] * $task['planning'];
			
			// Add up how much score was gained
			$score = 0;
			foreach (array('food', 'workers', 'energy', 'minerals') as $type)
			{
				if (!empty($building[$type]))
				{
					$score += $building[$type] * $building_count * constant('SCORE_' . strtoupper($type));
				}
				
				$kingdom[$type . 'rate'] += $building[$type . 'rate'] * $building_count;
				$planet[$type . 'rate'] += $building[$type . 'rate'] * $building_count;
			}
			
			// This stops uninitialized variable errors
			if (!isset($planet['buildings'][$task['building_id']]))
				$planet['buildings'][$task['building_id']] = 0;
			
			// Add the number of newly built buildings to the planet
			$planet['buildings'][$task['building_id']] += $building_count;
			
			// Return the used cranes to planet
			$planet['cranes'] += $task['number'];
			
			// Add whatever features the building grants
			foreach ($building['features'] as $key => $value)
			{
				switch ($key)
				{
					case 1:
					case 'cranes':
						$planet['cranes'] += $value * $building_count;
						break;
					case 2:
					case 'planning':
						$planet['planning'] += $value * $building_count;
						break;
					case 3:
					case 'researchbonus':
						$db_query = "SELECT `task_id` FROM `tasks` WHERE `type` IN ('" . TASK_RESEARCH . "', '" . TASK_UPGRADE . "') AND `planet_id` = '" . $task['planet_id'] . "' AND `completion` > '" . $task['completion'] . "'";
						$db_result = mysql_query($db_query);
						
						if ($db_result && mysql_num_rows($db_result) > 0)
						{
							$task_ids = array();
							while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
							{
								$task_ids[] = $db_row['task_id'];
							}
							
							$this->recalculate_task_time($task_ids, $task['completion'], $planet['researchbonus'], $planet['researchbonus'] + ($value * $building_count));
						}
						
						$planet['researchbonus'] += $value * $building_count;
						break;
					case 4:
					case 'buildingbonus':
						$db_query = "SELECT `task_id` FROM `tasks` WHERE `type` IN ('" . TASK_BUILD . "') AND `planet_id` = '" . $task['planet_id'] . "' AND `completion` > '" . $task['completion'] . "'";
						$db_result = mysql_query($db_query);
						
						if ($db_result && mysql_num_rows($db_result) > 0)
						{
							$task_ids = array();
							while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
							{
								$task_ids[] = $db_row['task_id'];
							}
							
							$this->recalculate_task_time($task_ids, $task['completion'], $planet['buildingbonus'], $planet['buildingbonus'] + ($value * $building_count));
						}
						
						$planet['buildingbonus'] += $value * $building_count;
						break;
					case 5:
					case 'production':
						if (empty($planet['production'][$value[0]][$value[1]]))
						{
							$planet['production'][$value[0]][$value[1]] = 0;
						}
						$planet['production'][$value[0]][$value[1]] += $building_count;
						break;
				}
			}
			
			$this->update_score($task['planet_id'], $score);
		}
		
		function recalculate_task_time($task_ids, $time_now, $current_bonus, $new_bonus)
		{
			if (empty($task_ids)) return;
			
			$tasks = &$this->data->task($task_ids);
			
			foreach (array_keys($tasks) as $task_id)
			{
				$task = &$tasks[$task_id];
				
				$difference_percentage = 1 - (($new_bonus - $current_bonus) / 100);
				$task['completion'] = $time_now + (($task['completion'] - $time_now) * $difference_percentage);
				
				unset($task);
			}
		}
		
		function task_research(&$task)
		{
			$this->log[] = 'Task Research: ' . $task['concept_id'];
			
			$round = &$this->data->round();
			$kingdom = &$this->data->kingdom($task['kingdom_id']);
			$player = &$this->data->player($task['player_id']);
			$planet = &$this->data->planet($task['planet_id']);
			
			$concept = &$this->data->concept($task['concept_id']);
			$grants = $concept['grants'];
			
			$planet['researching'] = 0;
			
			if (isset($kingdom['researched'][$task['concept_id']]))
				return;
			
			$news = array(
				'kingdom_id' => $task['kingdom_id'], 
				'kingdom_name' => $kingdom['name'], 
				
				'player_id' => $task['player_id'], 
				'player_name' => $player['name'], 
				
				'planet_id' => $task['planet_id'], 
				'planet_name' => $planet['name'], 
				
				'concept_id' => $task['concept_id'], 
				'concept_name' => $concept['name'], 
				'concept_description' => $concept['description'], 
				
				'posted' => $task['completion']);
			
			if (!isset($round['researched'][$task['concept_id']]))
			{
				$round['researched'][$task['concept_id']] = $task['kingdom_id'];
				
				add_news_entry(NEWS_FIRSTRESEARCH, $news);
			}
			else
			{
				add_news_entry(NEWS_RESEARCH, $news);
			}
			
			$score = 0;
			$score += $concept['workers'] * SCORE_WORKERS;
			$score += $concept['energy'] * SCORE_ENERGY;
			$score += $concept['minerals'] * SCORE_MINERALS;
			
			$kingdom['researched'][$task['concept_id']] = true;
			unset($kingdom['concepts'][$task['concept_id']]);
			
			$copyarray = array('concepts', 'buildings');
			foreach ($copyarray as $value)
			{
				if (empty($grants[$value])) continue;
				
				if (!is_array($kingdom[$value])) $kingdom[$value] = array();
				
				$kingdom[$value] = $kingdom[$value] + $grants[$value];
			}
			
			if (!empty($grants))
			{
				$this->copy_concepts($grants, $task['kingdom_id']);
			}
			
			$this->update_score($task['planet_id'], $score, SCORE_PLANET | SCORE_PLAYER);
		}
		
		function copy_concepts($grants, $kingdom_id)
		{
			$round = $this->data->round();
			
			$timeDividers = array(
				'army' => 5,
				'weapon' => 10,
				'navy' => 15);
			
			$copyrow = array('army', 'navy', 'weapon');
			foreach ($copyrow as $type)
			{
				if (empty($grants[$type . 'concepts'])) continue;
				
				foreach (array_keys($grants[$type . 'concepts']) as $id)
				{
					$this->sql->where(array($type . 'concepts', $type . 'concept_id', $id));
					$this->sql->limit(1);
					
					$db_result = $this->sql->execute();
					$concept = mysql_fetch_array($db_result, MYSQL_ASSOC);
					
					// initial Mk1 would be: resource / type * mk1, but * 1 is redundant.
					$resources = array('time', 'workers', 'energy', 'minerals');
					foreach ($resources as $resource) {
						$concept[$resource] /= $timeDividers[$type];
					}
					
					$concept['time'] *= $round['speed'];
					
					$concept['round_id'] = $_SESSION['round_id'];
					$concept['kingdom_id'] = $kingdom_id;
					
					$this->sql->execute($type . 'designs', $concept);
				}
			}
		}
		
		function task_upgradedesign(&$task)
		{
			$this->log[] = 'Task Upgradedesign: ' . $task['design_id'];
			
			$design = &$this->data->design($task['number'], $task['design_id']);
			$planet = &$this->data->planet($task['planet_id']);
			
			$planet['researching'] = 0;
			
			$score = 0;
			$score += $design['workers'] * SCORE_WORKERS;
			$score += $design['energy'] * SCORE_ENERGY;
			$score += $design['minerals'] * SCORE_MINERALS;
			
			$increment = floor((($design[$task['attribute'] . '_per'] / 100) * $design[$task['attribute'] . '_base']) + $design[$task['attribute'] . '_inc']);
			if ($design[$task['attribute'] . '_base'] + $increment > $design[$task['attribute'] . '_max'])
			{
				$increment = $design[$task['attribute'] . '_max'] - $design[$task['attribute'] . '_base'];
			}
			
			$sizeincrement = floor((($design[$task['attribute'] . '_size'] / 100) * $design['size_base']) + $design[$task['attribute'] . '_sizeinc']);
			if ($design['size_base'] + $sizeincrement > $design['size_max'])
			{
				$sizeincrement = $design['size_max'] - $design['size_base'];
			}
			
			$resources = array('time', 'workers', 'energy', 'minerals');
			foreach ($resources as $resource)
			{
				$design[$resource] += $design[$resource] / $design['techlevel_current'];
			}
			
			$design['size_base'] += $sizeincrement;
			$design[$task['attribute'] . '_base'] += $increment;
			$design['techlevel_current']++;
			
			$this->update_score($task['planet_id'], $score, SCORE_PLANET | SCORE_PLAYER);
		}
		
		function task_unit(&$task)
		{
			$this->log[] = 'Task Unit: ' . $task['attribute'] . ': ' . $task['unit_id'];
			
			$planet = &$this->data->planet($task['planet_id']);
			
			if (empty($planet['units'][$task['attribute']][$task['unit_id']]))
			{
				$planet['units'][$task['attribute']][$task['unit_id']] = 0;
			}
			
			$planet['units'][$task['attribute']][$task['unit_id']] += $task['number'];
			
			$blueprint = &$this->data->blueprint($task['attribute'], $task['unit_id']);
			
			$this->update_score($task['planet_id'], $blueprint['score'], SCORE_PLANET);
		}
		
		function task_navy(&$task)
		{
			$this->log[] = 'Task Navy: ' . $task['planet_id'];
			
			$this->combat->update_combat($task['planet_id'], $task['completion'] - 0.0001);
			
			$this->navy_arrive($task['group_id']);
			
			$this->unload_navy($task['planet_id'], $task['group_id']);
			
			if ($task['kingdom_id'] != $task['target_kingdom_id'])
			{
				$this->update_resistance($task['planet_id']);
				
				$this->create_combat($task['planet_id'], $task['completion']);
			}
		}
		
		function navy_arrive($navygroup_id)
		{
			$this->sql->set(array(
				array('navygroups', 'x_current', array('navygroups', 'x_destination')), 
				array('navygroups', 'y_current', array('navygroups', 'y_destination'))));
			$this->sql->where(array('navygroups', 'navygroup_id', $navygroup_id));
			$this->sql->execute();
		}
		
		function unload_navy($planet_id, $navygroup_id)
		{
			// Unload all army groups
			$db_query = "SELECT SUM(`size`) as 'size' FROM `armygroups` WHERE `navygroup_id` = '" . $navygroup_id . "'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$this->sql->set(array('navygroups', 'cargo_current', "raw:`navygroups`.`cargo_current` - '" . $db_row['size'] . "'"));
			$this->sql->where(array('navygroups', 'navygroup_id', $navygroup_id));
			$this->sql->execute();
			
			$this->sql->set(array(
				array('armygroups', 'planet_id', $planet_id), 
				array('armygroups', 'navygroup_id', 0)));
			$this->sql->where(array('armygroups', 'navygroup_id', $navygroup_id));
			$this->sql->execute();
		}
		
		function update_resistance($planet_id)
		{
			// Create workers resistance on planet if none has already been created
			$planet = &$this->data->planet($planet_id);
			$round = $this->data->round();
			if ($planet['resistance'] < $round['resistance'] && $planet['workers'] > 0)
			{
				$db_result = $this->sql->query("SELECT `armyblueprint_id` FROM `armyblueprints` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '0' AND `name` = 'Worker' LIMIT 1");
				if (mysql_num_rows($db_result) == 0)
				{
					$db_result = $this->sql->query("SELECT `weaponblueprint_id` FROM `weaponblueprints` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '0' AND `name` = 'Laser Cutters' LIMIT 1");;
					if (mysql_num_rows($db_result) == 0)
					{
						$insert_weapon = array(
							'round_id' => $_SESSION['round_id'], 
							'name' => 'Laser Cutters', 
							'techlevel' => 1, 
							'accuracy' => 40, 
							'areadamage' => 1, 
							'rateoffire' => 1, 
							'power' => 3, 
							'damage' => 2, 
							'size' => 10);
						$db_result = $this->sql->execute('weaponblueprints', $insert_weapon);
						$weaponblueprint_id = mysql_insert_id();
					}
					else
					{
						$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
						$weaponblueprint_id = $db_row['weapon_id'];
					}
					
					$insert_armyunit = array(
						'round_id' => $_SESSION['round_id'], 
						'armyconcept_id' => 1, 
						'name' => 'Worker', 
						'workers' => 1, 
						'techlevel' => 0, 
						'attack' => 40, 
						'defense' => 40, 
						'armor' => 2, 
						'hull' => 15, 
						'size' => 572662, 
						'weapons' => array($weaponblueprint_id => 1));
					$db_result = $this->sql->execute('armyblueprints', $insert_armyunit);
					$armyblueprint_id = mysql_insert_id();
				}
				else
				{
					$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
					$armyblueprint_id = $db_row['armyblueprint_id'];
				}
				
				$remainingresistance = $round['resistance'] - $planet['resistance'];
				if ($remainingresistance > $planet['workers'])
				{
					$workers = $planet['workers'];
				}
				else
				{
					$workers = $remainingresistance;
				}
				
				$planet['resistance'] += $workers;
				$planet['workers'] -= $workers;
				
				$resistance = array($armyblueprint_id => $workers);
				
				$groupinsert = array(
					'round_id' => $planet['round_id'], 
					'kingdom_id' => $planet['kingdom_id'], 
					'player_id' => $planet['player_id'], 
					'planet_id' => $planet['planet_id'], 
					'name' => 'Worker Resistance', 
					'units' => $resistance, 
					'size' => $workers * 572662);
				$db_query = $this->sql->execute('armygroups', $groupinsert);
			}
		}
		
		function create_combat($planet_id, $completion = '')
		{
			if (empty($completion))
			{
				$completion = $this->data->update_to();
			}
			
			// Create combat if it doesn't already exist on planet
			$this->sql->select(array('combat', 'combat_id'));
			$this->sql->where(array(
				array('combat', 'round_id', $_SESSION['round_id']), 
				array('combat', 'planet_id', $planet_id)));
			$this->sql->limit(1);
			
			$db_result = $this->sql->execute();
			if (mysql_num_rows($db_result) == 0)
			{
				$insertcombat['round_id'] = $_SESSION['round_id'];
				$insertcombat['planet_id'] = $planet_id;
				$insertcombat['completion'] = $completion;
				
				$this->sql->execute('combat', $insertcombat);
			}
		}
		
// } ----------------------------------------------------------------------------- {
		
		function update_score($planet_id, $score, $update_flag = 0)
		{
			$this->log[] = 'Update Score: Planet: ' . $planet_id;
			// Function to take a given score and add it to kingdom and optionally planet & player.
			
			$planet = &$this->data->planet($planet_id);
			
			if (($update_flag & SCORE_PLANET) != SCORE_PLANET)
			{
				$scoreupdates[] = 'planet';
			}
			
			if (($update_flag & SCORE_PLAYER) != SCORE_PLAYER)
			{
				$this->log[] = 'Update Score: Player: ' . $planet['player_id'];
				
				$player = &$this->data->player($planet['player_id']);
				$scoreupdates[] = 'player';
			}
			
			if (($update_flag & SCORE_KINGDOM) != SCORE_KINGDOM)
			{
				$this->log[] = 'Update Score: Kingdom: ' . $planet['kingdom_id'];
				
				$kingdom = &$this->data->kingdom($planet['kingdom_id']);
				$scoreupdates[] = 'kingdom';
			}
			
			foreach ($scoreupdates as $value)
			{
				${$value}['score'] += $score;
				if (${$value}['score'] > ${$value}['score_peak'])
				{
					${$value}['score_peak'] = ${$value}['score'];
				}
			}
		}
		
// } ----------------------------------------------------------------------------- {
		
		function update_group($group_id, $type)
		{
			/*
				Things this command needs to do:
					Recalculate army size and navy size & cargo
					
					Pick off army units if navy group cargo space gets smaller
					
					Delete navy group and any army groups on it
					
					Calculate score lost from units killed
						(Food*0.005*2) + (Workers*0.01*2) + (Energy*0.02) + (Minerals*0.1)
						
					Calculate score gained from killing units
						((Food0*.005*2) + (Workers**2) + (Energy*0.02) + (Minerals*0.1)) * 0.2
						
					Put minerals back in planet for units killed
						(Minerals*0.3)
			*/
			
			if ($type == 'army')
			{
				$db_query = "SELECT `units` FROM `armygroups` WHERE `armygroup_id` = '" . $group_id . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$units = unserialize($db_row['units']);
				
				foreach ($units as $unit_id => $value)
				{
					if (empty($value))
					{
						unset($units[$unit_id]);
					}
				}
				
				if (!empty($units))
				{
					$this->sql->select(array(
						array('armyblueprints', 'armyblueprint_id'), 
						array('armyblueprints', 'size')
					));
					$this->sql->where(array('armyblueprints', 'armyblueprint_id', array_keys($units), 'IN'));
					$db_query = $this->sql->generate();
					$db_result = mysql_query($db_query);
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						@$size += $units[$db_row['armyblueprint_id']] * $db_row['size'];
					}
				}
				else
				{
					// set size to 0;
					$size = 0;
				}
				
				$db_query = "
					UPDATE `armygroups` 
					SET 
						`size` = '" . $size . "', 
						`units` = '" . mysql_real_escape_string(serialize($units)) . "' 
					WHERE `armygroup_id` = '" . $group_id . "' 
					LIMIT 1";
				$db_result = mysql_query($db_query);
			}
			elseif ($type == 'navy')
			{
				$db_query = "SELECT `units`, `cargo` FROM `navygroups` WHERE `navygroup_id` = '" . $group_id . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$units = unserialize($db_row['units']);
				$groupcargo = unserialize($db_row['cargo']);
				
				foreach ($units as $unit_id => $value)
				{
					if (empty($value))
					{
						unset($units[$unit_id]);
					}
				}
				
				if (!empty($units))
				{
					$this->sql->select(array(
						array('navyblueprints', 'navyblueprint_id'), 
						array('navyblueprints', 'size'), 
						array('navyblueprints', 'cargo')
					));
					$this->sql->where(array('navyblueprints', 'navyblueprint_id', array_keys($units), 'IN'));
					$db_query = $this->sql->generate();
					$db_result = mysql_query($db_query);
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						@$size += $units[$db_row['navyblueprint_id']] * $db_row['size'];
						@$cargo['max'] += $units[$db_row['navyblueprint_id']] * $db_row['cargo'];
					}
				}
				else
				{
					// set size and cargo to 0;
					$size = 0;
					$cargo['max'] = 0;
					$cargo['current'] = 0;
				}
				
				
				
	//			 $db_query = "SELECT `armygroup_id` FROM `armygroups` WHERE `navygroup_id` = '" . $group_id . "'";
	//			 $db_result = mysql_query($db_query);
	//			 while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
	//			 {
	//				 update_group($db_row['armygroup_id'], 'army');
	//			 }
				
				$db_query = "SELECT SUM(`size`) as 'size' FROM `armygroups` WHERE `navygroup_id` = '" . $group_id . "'";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$cargo['current'] += $db_row['size'];
				
				$resourcearray = array('food', 'workers', 'energy');
				foreach ($resourcearray as $resource)
				{
					if ($resource == 'workers') $cargo['current'] += $groupcargo[$resource] * 70;
					else $cargo['current'] += $groupcargo[$resource];
				}
				
				if (!empty($groupcargo['minerals']))
				{
					$cargo['current'] += array_sum($groupcargo['minerals']);
				}
				
				$db_query = "
					UPDATE `navygroups` 
					SET 
						`units` = '" . mysql_real_escape_string(serialize($units)) . "', 
						`cargo_current` = '" . $cargo['current'] . "', 
						`cargo_max` = '" . $cargo['max'] . "', 
						`size` = '" . $size . "' 
					WHERE `navygroup_id` = '" . $group_id . "' 
					LIMIT 1";
				$db_result = mysql_query($db_query);
			}
		}
		
// } ----------------------------------------------------------------------------- {
		
		function update_cranes($planets)
		{
			if (is_array($planets))
			{
				foreach ($planets as $value)
				{
					$this->update_cranes($value);
				}
				return;
			}
			
			$db_query = "SELECT `buildings`, `cranes` FROM `planets` WHERE `planet_id` = '" . $planets . "'";
			$db_result = mysql_query($db_query);
			$planet = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$planet['buildings'] = unserialize($planet['buildings']);
			
			if (empty($planet['buildings'][7]))
			{
				$planet['buildings'][7] = 0;
			}
			
			$db_query = "SELECT SUM(`number`) AS 'cranes' FROM `tasks` WHERE `planet_id` = '" . $planets . "' AND `type` = '1'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			if (empty($db_row['cranes'])) $db_row['cranes'] = 0;
			
			if ($planet['cranes'] != ($planet['buildings'][7] + 1) - $db_row['cranes'])
			{
				$db_query = "
					UPDATE `planets` 
					SET `cranes` = '" . (($planet['buildings'][7] + 1) - $db_row['cranes']) . "' 
					WHERE `planet_id` = '" . $planets . "' 
					LIMIT 1";
				$db_result = mysql_query($db_query);
			}
		}
	
// } -----------------------------------------------------------------------------
	}
?>