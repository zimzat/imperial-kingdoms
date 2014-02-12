<?php
/*
	The following functions are referenced from Updater_Round:
	$this->data->updater->task_navy($db_row);

	$this->data->updater->update_tasks($planet_id, $current['combat']['completion']);
	$this->data->updater->update_resources($planet_id, $current['combat']['completion']);

	$this->data->updater->update_score($planet_id, $info['planets'][$planet_id]['score'] * -1, SCORE_PLANET);

	$this->data->updater->update_score($planet_id, $info['planets'][$planet_id]['score'], SCORE_PLANET);
*/

class Updater_Combat
{
	var $data;
	var $sql;

	var $log;

	var $ad_fractions = array(0, 1, 1.5, 1.8333333333333, 2.0833333333333, 2.2833333333333, 2.45,
		2.5928571428571, 2.7178571428571, 2.8289682539683, 2.9289682539683, 3.0198773448773,
		3.1032106782107, 3.1801337551338, 3.2515623265623, 3.318228993229);

	var $combat = array();
	var $info = array(
		'round' => array(),
		'planets' => array(),
		'kingdoms' => array(),
		'players' => array(),
		'groups' => array(
			'army' => array(),
			'navy' => array()));
	var $blueprints = array(
		'army' => array(),
		'navy' => array(),
		'weapon' => array());

	var $id_array = array(
		'players' => array(),
		'kingdoms' => array(),
		'blueprints' => array(
			'army' => array(),
			'navy' => array(),
			'weapon' => array()));

	var $type_array = array('army', 'navy');

	function __construct()
	{
		global $data;

		$this->data = &$data;
		$this->sql = new SQL_Generator;

		$this->log = &$data->updater->log;
	}

	function calculate_ad_bonus($areadamage)
	{
		// just in case the areadamage is more than pre-calculated, calculate it on the fly.
		$precalc_ad = count($this->ad_fractions) - 1;
		if ($areadamage > $precalc_ad)
		{
			for ($ad = $precalc_ad + 1; $ad <= $areadamage; $ad++)
			{
				$this->ad_fractions[$ad] = $this->ad_fractions[$ad - 1] + (1 / $ad);
			}
		}

		return $this->ad_fractions[$areadamage];
	}

	function update_combat($planet_id = 0, $update_to = '')
	{
		$this->log[] = 'Update Combat';

		if (empty($update_to)) {
			$update_to = $this->data->update_to();
		}

		// ##################################################
		// Make sure any navy groups prior to now have landed {
		if (empty($planet_id))
		{
			$this->sql->where(array(
				array('tasks', 'round_id', $_SESSION['round_id']),
				array('tasks', 'type', TASK_NAVY),
				array('tasks', 'completion', $update_to, '<=')
			));
			$this->sql->orderby(array(
				array('tasks', 'planet_id', 'asc'),
				array('tasks', 'completion', 'asc')
			));
			$this->sql->limit(1);
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$this->data->updater->task_navy($db_row);
			}
		}
		// }

		list ($combat, $info, $blueprints) = $this->getAllInformation($planet_id, $update_to);
		if (empty($combat)) {
			return;
		}

		// ##################################################
		// Loop through all combat {
		foreach ($combat as $planet_id => $current)
		{
			$this->log[] = 'Combat: Updating ' . $planet_id;

			$reports = array();

			$last_update = $update_to - $current['combat']['completion'];
			$time_left = $last_update % $info['round']['combattick'];
			$combat_updates = floor($last_update / $info['round']['combattick']) + 1;

			// ##################################################
			// Update however many times combat happened in this time {
			for ($i = 0; $i < $combat_updates; $i++)
			{
// Start running combat
				$this->log[] = 'Combat: Update ' . $i;

				$combat_report = array();

				$current['combat']['rounds']++;

				// If no kingdoms are present no battle.
				$fighting = false;
				$kingdoms = array_keys($current['sudo']['army']['kingdoms'] + $current['sudo']['navy']['kingdoms'] + array($info['planets'][$planet_id]['kingdom_id'] => true));
				foreach ($kingdoms as $kingdom_id)
				{

					$variable = array_diff($kingdoms, array_keys($info['kingdoms'][$kingdom_id]['allies']) + array($kingdom_id));
					if (!empty($variable))
					{
						$fighting = true;
						break;
					}
				}

				if (!$fighting)
				{
					// nobody won
					// delete combat
					$db_query = "DELETE FROM `combat` WHERE `combat_id` = '" . $current['combat']['combat_id'] . "' LIMIT 1";
					$db_result = mysql_query($db_query);

					$this->log[] = 'Combat: No combat';

					// go to the next planet for combat
					continue 2;
				}


				$after_combat = $current;

				// Repeat for army and navy units
				foreach ($this->type_array as $type)
				{
					$this->log[] = 'Combat: Type: ' . $type;

					$storedCombatDamage = array();

					foreach ($current['sudo'][$type]['kingdoms'] as $kingdom_id => $groups)
					{
						$kingdom = $info['kingdoms'][$kingdom_id];
						$this->log[] = 'Combat: Kingdom: ' . $kingdom_id;

						$find = array('kingdom_id' => array_keys($kingdom['allies']));
						$find['kingdom_id'][] = $kingdom_id;

						foreach ($groups as $group_id => $units)
						{
							$group = $info['groups'][$type][$group_id];
							$this->log[] = 'Combat: Group: ' . $group_id;

							foreach ($units as $unit_id => $unit_count)
							{
								$unit = $blueprints[$type][$unit_id];
								$this->log[] = 'Combat: Unit: ' . $unit_id;

								foreach ($unit['weapons'] as $weapon_id => $weapon_count)
								{
									$weapon = $blueprints['weapon'][$weapon_id];
									$this->log[] = 'Combat: Weapon: ' . $weapon_id;

									// Filter remaining groups against kingdom allies
									$targets['groups'] = array_find($find, $after_combat['sudo'][$type]['groups'], true);

									if (empty($targets['groups']))
									{
										$this->log[] = 'Combat: Empty Targets';

										// This kingdom is out of targets
										// Continue with the next kingdom
										continue 4;
									}

									// Rate of Fire Spray: Weapons with a high rof should have decreased accuracy.
									$rof_spray = pow($weapon['rateoffire'], 0.9) / $weapon['rateoffire'];
									$ammo = $unit_count * $weapon_count * $weapon['rateoffire'] * $weapon['accuracy'] * $rof_spray / 100;

									$this->log[] = 'Combat: Ammo: ' . $ammo;

									$targets_left = true;

									if (isset($targeted)) unset($targeted);

									// Repeat combat until we're out of targets or ammo
									while ($ammo > 0 && $targets_left)
									{
										$targets['groups'] = array_find($find, $after_combat['sudo'][$type]['groups'], true);

										if (!empty($group['targets'][$weapon_id]) && empty($targeted['group']))
										{
											$target_type = $group['targets'][$weapon_id];
											$targeted['group'] = true;

											$this->log[] = 'Combat: Target: Group: ' . $target_type;
										}
										elseif (!empty($weapon['targets'][$weapon_id]) && empty($targeted['weapon']))
										{
											$target_type = $weapon['targets'][$weapon_id];
											$targeted['weapon'] = true;

											$this->log[] = 'Combat: Target: Weapon: ' . $target_type;
										}
										else
										{
											$targets['random'] = $targets['groups'];
											if (!empty($targets['random']))
											{
												$target_type = array_rand($targets['random']);
												unset($targets['random'][$target_type]['kingdom_id']);
												$target_type = array_rand($targets['random'][$target_type]);
											}
											else
											{
												$targets_left = false;
												$target_type = 0;
											}

											$this->log[] = 'Combat: Target: Random: ' . $target_type;

											$targeted['random'] = true;
										}

										// Filters units
										$targets['active'] = array_find(array('array_find:key' => $target_type), $targets['groups']);

										if (empty($targets['active']))
										{
											$this->log[] = 'Combat: No targets of that type.';
											if (!empty($targeted['random'])) continue 5;
											else continue;
										}

										// Associated Shuffle (maintain keys while shuffling values)
										$targets['active'] = ashuffle($targets['active']);

										foreach ($targets['active'] as $target_group_id => $unit_types)
										{
											$target_kingdom_id = $unit_types['kingdom_id'];
											$target_kingdom = $info['kingdoms'][$target_kingdom_id];

											unset($unit_types['kingdom_id']);

											$this->log[] = 'Combat: Target Kingdom: ' . $target_kingdom_id;
											$this->log[] = 'Combat: Target Group: ' . $target_group_id;

											foreach ($unit_types[$target_type] as $target_id => $target_count)
											{
												$target = $blueprints[$type][$target_id];

												$this->log[] = 'Combat: Target Unit: ' . $target_id;

// COMBAT CALCULATIONS START HERE!
												// combat calculations
												$rand = rand(15, 25) / 10;

												if (empty($info['round']['combat_engine'])) $info['round']['combat_engine'] = 'xenocide';

												switch ($info['round']['combat_engine'])
												{
													case 'x^2':
														// Random here, random there
														$random_factor = rand(80, 120) / 100;

														/*
															* Attack vs Defense balance
															* Half of the shots will hit by default.
															* 1% to 99% are valid. 0% and 100% are not.
															* 1/100 = 2%
															* 10/100 = 26%
															* 10/90 = 27.1%
															* 20/80 = 35%
															* 30/70 = 41.2%
															* 40/60 = 45.7%
															* 50/50 (1/1) = 50%
															* 100/1 = 98%
															*/
														$combat_balance = log($unit['attack'] / $target['defense']) * 24 + 50;
														$combat_balance /= 100 / $random_factor;

														$hits = $ammo * $combat_balance;

														if ($hits < 0) $hits = 0;

														if ($weapon['power'] < $target['armor'])
														{
															// You just targeted a unit that has impenitrable armor; shots wasted.
															$damage = 0;
															$kills = 0;
															$ammo = 0;
														}
														else
														{
															// Single Shot Damage
															$damage = $weapon['damage'] * $this->calculate_ad_bonus($weapon['areadamage']);

															// We can't kill any more than we hit. rof_spray is already taken into account.
															$maxkills = $ammo;

															// if we can kill a single unit with one shot then we can kill as many as in our area.
															if ($damage > $target['hull']) $maxkills *= $weapon['areadamage'];

															$kills = $damage * $hits / $target['hull'];

															// If there are actually less targets than "killed", refund some unusd ammo.
															if ($kills > $target_count)
															{
																// Reverse calculate how much wasn't really fired, but only give half of that back.
																// (expended in the heat of battle).
																$ammo = floor(($kills - $target_count) * ($target['hull'] / $damage));
																$hits -= $ammo;
																$ammo = floor($ammo / ($combat_balance / 0.5));

																$kills = $target_count;
															}
															else
															{
																$ammo = 0;
															}

															// If we can't actually kill this many units (due to rof or ad)...
															if ($kills > $maxkills) $kills = $maxkills;

															// Sometimes we won't fully kill a unit. Induce random chance of a critical hit.
															else if (floor($kills) != $kills)
															{
																if (!empty($storedCombatDamage[$target_kingdom_id][$target_id])) {
																	$kills += $storedCombatDamage[$target_kingdom_id][$target_id];
																}

																$leftover_kills = $kills - floor($kills);
																$chance_kill = rand(0, 10000);
																if (($leftover_kills > 0.5 && $chance_kill < $leftover_kills * 5000)
																		|| $chance_kill < pow($leftover_kills, 2) * 10000) {
																	unset($storedCombatDamage[$target_kingdom_id][$target_id]);
																	$kills = ceil($kills);
																} else {
																	$storedCombatDamage[$target_kingdom_id][$target_id] = $leftover_kills;
																	$kills = floor($kills);
																}
															}

															$damage = $hits * $damage;
														}
														break;
													case 'toel':
														$modifier = 0.01;

														// porting over Ferret's areadamage calculation since ToeL doesn't account for it
														$damage = $weapon['damage'] * $this->calculate_ad_bonus($weapon['areadamage']);

														// $kills = 1 / $target['hull'] * $damage * $hits * $modifier * $weapon['power'] * sqrt(1 / ($weapon['damage'] * $target['armor']) * $weapon['power'] * $target['hull']);

														$hits2kill = ceil($target['hull'] / ($damage * $modifier * $weapon['power'] * sqrt($weapon['power'] * $target['hull'] / ($damage * $target['armor']))));
														if($hits2kill < (1 / $weapon['areadamage'])) $hits2kill = (1 / $weapon['areadamage']);

														$kills = $ammo / $hits2kill;
														if ($kills > $target_count) $kills = $target_count;
														$hits = $kills * $hits2kill;
														$damage = $hits * $weapon['damage'];
														$ammo -= $hits;

														$remainder = $kills - floor($kills);
														if ($remainder > 0)
														{
															if (rand(0, 100) < $remainder)
															{
																$kills = ceil($kills);
															}
															else
															{
																$kills = floor($kills);
															}
														}
														break;
													case 'xenocide':
													default:
														if ($weapon['power'] < $target['armor'])
														{
															// You just tried to target a unit that has impenitrable armor; shots wasted.
															$hits = floor($ammo * ($unit['attack'] / $target['defense'] / $rand));
															if ($hits > $ammo) $hits = $ammo;

															$damage = 0;
															$kills = 0;
															$ammo = 0;
														}
														else
														{
															$modifier = $weapon['power'] / $target['armor'];
															if ($weapon['power'] > $target['armor']) {
																$modifier = sqrt($modifier);
															}

															// New Area Damage Code
															$damage = $weapon['damage'] * $this->calculate_ad_bonus($weapon['areadamage']);

															// $weapon['damage'] replaced by $damage here for area damage.


															$hits2kill = $target['hull'] / ($damage * $modifier);
															$hits2kill = $hits2kill / ($unit['attack'] / $target['defense'] / $rand);

															// Area Damage cap on hits2kill
															if ($hits2kill < (1 / $weapon['areadamage'])) {
																$hits2kill = (1 / $weapon['areadamage']);
															}

															$kills = $ammo / $hits2kill;
															if ($kills > $target_count) {
																$kills = $target_count;
															}
															$hits = $kills * $hits2kill;
															$damage = $hits * $weapon['damage'];
															$ammo -= $hits;

															$remainder = $kills - floor($kills);
															if ($remainder > 0) {
																if (rand(0, 100) < $remainder) {
																	$kills = ceil($kills);
																} else {
																	$kills = floor($kills);
																}
															}
														}
												}

												if (floor($kills) != $kills) $kills = floor($kills);

												$this->log[] = 'Combat: Hits: ' . $hits;
												$this->log[] = 'Combat: Damage: ' . $damage;
												$this->log[] = 'Combat: Killed: ' . $kills . ' / ' . $target_count;
												$this->log[] = 'Combat: Ammo Left: ' . $ammo;

												$score = $kills * $target['score'];
												if ($type == 'army') $score *= 300;

												$combat_report['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][] = array(
													'target_unit_id' => $target_id,
													'hits' => ceil($hits),
													'damage' => ceil($damage),
													'kills' => floor($kills));

												if (empty($after_combat['combat']['scores'][$kingdom_id][$info['groups'][$type][$group_id]['player_id']]))
													$after_combat['combat']['scores'][$kingdom_id][$info['groups'][$type][$group_id]['player_id']] = 0;
												$after_combat['combat']['scores'][$kingdom_id][$info['groups'][$type][$group_id]['player_id']] += $score;

												$after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id] -= $kills;
												if (ceil($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id]) !=
													$after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id])
													$after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id] = ceil($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id]);

												if (empty($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id]))
													unset($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type][$target_id]);
												if (empty($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type]))
													unset($after_combat['sudo'][$type]['groups'][$target_group_id][$target_type]);
												if (empty($after_combat['sudo'][$type]['groups'][$target_group_id]) || count($after_combat['sudo'][$type]['groups'][$target_group_id]) == 1)
													unset($after_combat['sudo'][$type]['groups'][$target_group_id]);

												$after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id][$target_group_id][$target_id] -= $kills;
												if (empty($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id][$target_group_id][$target_id]))
													unset($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id][$target_group_id][$target_id]);
												if (empty($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id][$target_group_id]))
													unset($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id][$target_group_id]);
												if (empty($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id]))
													unset($after_combat['sudo'][$type]['kingdoms'][$target_kingdom_id]);

												$info['groups'][$type][$target_group_id]['units'][$target_id] -= $kills;
												if (empty($info['groups'][$type][$target_group_id]['units'][$target_id]))
													unset($info['groups'][$type][$target_group_id]['units'][$target_id]);

												$info['groups'][$type][$target_group_id]['size'] -= $blueprints[$type][$target_id]['size'] * $kills;
												if ($type == 'navy')
													$info['groups'][$type][$target_group_id]['cargo_max'] -= $blueprints[$type][$target_id]['cargo'] * $kills;

												// continue with the next weapon
												if ($ammo <= 0) continue 4;
											}
										}
									}
								}
							}
						}
					}

					// }

					// Going through each group it needs a way to see how many units of that kingdom were killed.
					// That would update the unit totals of the group but not any details.
					//
					// go through each kingdom's group.
					// what percent of the total units are in that group?
					// take that percent and multiply it by what the group did.
					// hits, damage, kills, etc

					// ##################################################
					// Build combat report {

					$this->log[] = 'Combat: Results';

					foreach ($current['sudo'][$type]['kingdoms'] as $kingdom_id => $kingdom_groups)
					{
						$combat_report['names']['kingdoms'][$kingdom_id] = $info['kingdoms'][$kingdom_id]['name'];
						$combat_report['participants'][$kingdom_id][$type] = 0;

						foreach ($kingdom_groups as $group_id => $group_units)
						{
							$combat_report['names']['groups'][$type][$group_id] = $info['groups'][$type][$group_id]['name'];
							$combat_report['participants'][$kingdom_id][$type] += array_sum($group_units);

							foreach ($group_units as $unit_id => $unit_total)
							{
								$combat_report['names']['units'][$type][$unit_id] = $blueprints[$type][$unit_id]['name'];

								foreach ($blueprints[$type][$unit_id]['weapons'] as $weapon_id => $weapon_count)
								{
									$combat_report['names']['weapons'][$weapon_id] = $blueprints['weapon'][$weapon_id]['name'];
								}

								// How many were lost?
								$lost = $current['sudo'][$type]['kingdoms'][$kingdom_id][$group_id][$unit_id];
								if (!empty($after_combat['sudo'][$type]['kingdoms'][$kingdom_id][$group_id][$unit_id]))
									$lost -= $after_combat['sudo'][$type]['kingdoms'][$kingdom_id][$group_id][$unit_id];

								$remaining = 0;
								if (!empty($after_combat['sudo'][$type]['kingdoms'][$kingdom_id][$group_id][$unit_id]))
									$remaining = $after_combat['sudo'][$type]['kingdoms'][$kingdom_id][$group_id][$unit_id];

								$combat_report['casualties'][$kingdom_id][$type][$group_id][$unit_id] = array(
									'killed' => $lost,
									'remaining' => $remaining
								);
							}
						}
					}

					// }
				}

				foreach ($combat_report['names']['kingdoms'] as $kingdom_id => $kingdomname)
				{
					if (empty($after_combat['sudo']['army']['kingdoms'][$kingdom_id]) &&
						empty($after_combat['sudo']['navy']['kingdoms'][$kingdom_id]))
					{
						$combat_report['status'][$kingdom_id] = 2;
					}
					else
					{
						$combat_report['status'][$kingdom_id] = 0;
					}
				}

				$combat_report['header'] = array(
					'defender' => $info['players'][$info['planets'][$planet_id]['player_id']]['name'],
					'date' => $current['combat']['completion'],
					'location' => array('name' => $info['planets'][$planet_id]['name'], 'planet_id' => $planet_id)
				);

				$current = $after_combat;

				// ##################################################
				// Check Victory Conditions {
// victory conditions check
				$armies = count($current['sudo']['army']['kingdoms']);
				$navies = count($current['sudo']['navy']['kingdoms']);
				if ($armies == 0 && $navies == 0)
				{
					// nobody won
					// delete combat
					$db_query = "DELETE FROM `combat` WHERE `combat_id` = '" . $current['combat']['combat_id'] . "' LIMIT 1";
					$db_result = mysql_query($db_query);

					$this->log[] = 'Combat: No groups on the planet';

					// stop combat on this planet
					$reports[] = $combat_report;
					break;
				}
				else
				{
					// Check allies of all remaining kingdoms and calculate scores
					$fighting = false;
					$scores = array();
					$kingdoms = array_keys($current['sudo']['army']['kingdoms'] + $current['sudo']['navy']['kingdoms']);
					foreach ($kingdoms as $kingdom_id)
					{
						$variable = array_keys($info['kingdoms'][$kingdom_id]['allies']);
						$variable[] = $kingdom_id;
						$variable = array_diff($kingdoms, $variable);
						if (!empty($variable))
						{
							$fighting = true;
							break;
						}

						if (!empty($current['combat']['scores'][$kingdom_id]))
							$scores[$kingdom_id] = array_sum($current['combat']['scores'][$kingdom_id]);
					}

					if ($fighting)
					{
						$reports[] = $combat_report;
						$current['combat']['completion'] += $info['round']['combattick'];
						// still fighting. Next combat.
						continue;
					}

					if (!empty($scores))
					{
						asort($scores);
						while ($winning_kingdom = array_pop($scores))
						{
							if (empty($current['combat']['scores'][$winning_kingdom])) continue;

							asort($current['combat']['scores'][$winning_kingdom]);
							while ($winning_player = array_pop($current['combat']['scores'][$winning_kingdom]))
							{
								break;
							}
						}
					}

					if (empty($scores) || empty($winning_kingdom) || empty($winning_player))
					{
						$winning_kingdom = $kingdoms[array_rand($kingdoms)];
						if (empty($current['sudo']['navy']['kingdoms'][$winning_kingdom])) $type = 'army';
						else $type = 'navy';

						$winning_player = $info['groups'][$type][array_rand($current['sudo'][$type]['kingdoms'][$winning_kingdom])]['player_id'];
					}

					$combat_report['status'][$winning_kingdom] = 1;

					// Planet was lost
					if ($winning_kingdom != $info['planets'][$planet_id]['kingdom_id'] && !in_array($info['planets'][$planet_id]['kingdom_id'], $kingdoms))
					{
						// ##################################################
						// TODO
						/*
							X	Delete Combat
							X	Figure out winning player_id
							X	Set planet player_id, kingdom_id, & permissions = winner
							X	Remove planet from players & kingdoms
						*/



						$this->data->updater->update_tasks($planet_id, $current['combat']['completion']);
						$this->data->updater->update_resources($planet_id, $current['combat']['completion']);



						$losing_player = $info['planets'][$planet_id]['player_id'];
						$losing_kingdom = $info['planets'][$planet_id]['kingdom_id'];


						$news = array(
							'kingdom_id' => $winning_kingdom,
							'kingdom_name' => $info['kingdoms'][$winning_kingdom]['name'],

							'player_id' => $winning_player,
							'player_name' => $info['players'][$winning_player]['name'],

							'target_kingdom_id' => $losing_kingdom,
							'target_kingdom_name' => $info['kingdoms'][$losing_kingdom]['name'],

							'target_player_id' => $losing_player,
							'target_player_name' => $info['players'][$losing_player]['name'],

							'planet_id' => $planet_id,
							'planet_name' => $info['planets'][$planet_id]['name'],

							'posted' => $current['combat']['completion']);



						// ##################################################
						// Take score from losing player and kingdom. {
						$this->data->updater->update_score($planet_id, $info['planets'][$planet_id]['score'] * -1, SCORE_PLANET);

						// }


						// ##################################################
						// Delete combat now that it has been resolved. {
						$this->log[] = 'Combat: Combat winner: K#' . $winning_kingdom . ' P#' . $winning_player;
						$db_query = "DELETE FROM `combat` WHERE `combat_id` = '" . $current['combat']['combat_id'] . "' LIMIT 1";
						$db_result = mysql_query($db_query);

						$db_query = "DELETE FROM `permissions` WHERE `id` = '" . $planet_id . "' AND `type` = '" . PERMISSION_PLANET . "'";
						$db_result = mysql_query($db_query);

						//permissions_update_planets();

						// }


						// ##################################################
						// Take planet from losing player and kingdom
						unset($info['players'][$losing_player]['planets'][$planet_id]);
						unset($info['kingdoms'][$losing_kingdom]['planets'][$planet_id]);

						// Give planet to winning player and kingdom
						$info['players'][$winning_player]['planets'][$planet_id] = true;
						$info['kingdoms'][$winning_kingdom]['planets'][$planet_id] = true;

						$info['planets'][$planet_id]['kingdom_id'] = $winning_kingdom;
						$info['planets'][$planet_id]['player_id'] = $winning_player;

						// }


						// ##################################################
						// Delete certain tasks on said planet {
						$db_query = "
							DELETE FROM `tasks`
							WHERE
								`planet_id` = '" . $planet_id . "' AND
								`type` IN (" . TASK_RESEARCH . ", " . TASK_UPGRADE . ")";
						$db_result = mysql_query($db_query);

						$db_query = "UPDATE `planets` SET `researching` = '0' WHERE `planet_id` = '" . $planet_id . "' LIMIT 1";
						$db_result = mysql_query($db_query);

						// }

						// ##################################################
						// Remove warp time from planet {
						$info['planets'][$planet_id]['warptime_construction'] = 0;
						$info['planets'][$planet_id]['warptime_research'] = 0;
						// }


						// ##################################################
						// Take score from losing player and kingdom. {
						$this->data->updater->update_score($planet_id, $info['planets'][$planet_id]['score'], SCORE_PLANET);

						// }

						// ##################################################
						// If the player has no more planets left then
						// make them a prisoner of the winner {
						$loser_count = count($info['players'][$losing_player]['planets']);
						if ($loser_count == 0)
						{
							$db_query = "DELETE FROM `permissions` WHERE `id` = '" . $losing_player . "' AND `type` = '" . PERMISSION_PLAYER . "'";
							$db_result = mysql_query($db_query);

							$info['players'][$losing_player]['kingdom_id'] = $winning_kingdom;
							$info['players'][$losing_player]['planets'] = array();
							$info['players'][$losing_player]['rank'] = 0;
							unset($info['kingdoms'][$losing_kingdom]['members'][$losing_player]);
							$info['kingdoms'][$winning_kingdom]['members'][$losing_player] = true;

							$leftover_members = count($info['kingdoms'][$losing_kingdom]['members']);
							if ($leftover_members == 0)
							{
								$this->kingdom_defeated($losing_kingdom);
								add_news_entry(NEWS_KINGDOMDEFEATED, $news);
							}
							else
							{
								add_news_entry(NEWS_PLAYERCAPTURED, $news);
							}
						}
						else
						{
							add_news_entry(NEWS_PLANETCONQUERED, $news);
						}

						$reports[] = $combat_report;
						break;

						// }

					}
					elseif ($winning_kingdom == $info['planets'][$planet_id]['kingdom_id'] || in_array($info['planets'][$planet_id]['kingdom_id'], $kingdoms))
					{
						// defender won
						// delete combat
						$db_query = "DELETE FROM `combat` WHERE `combat_id` = '" . $current['combat']['combat_id'] . "' LIMIT 1";
						$db_result = mysql_query($db_query);

						$this->log[] = 'Combat: Winner is owner';

						$reports[] = $combat_report;
						break;
					}
				}

				// }

				$current['combat']['completion'] += $info['round']['combattick'];
				$reports[] = $combat_report;

// End running combat
			}

			// }

			// Store battle report(s)
			foreach ($reports as $value)
			{
				$insertreport = array();

				$insertreport['round_id'] = $_SESSION['round_id'];
				$insertreport['report'] = $value;
				$insertreport['date'] = $value['header']['date'];
				$insertreport['planet_id'] = $planet_id;
				foreach ($value['status'] as $kingdom_id => $status)
				{
					$insertreport['kingdom_id'] = $kingdom_id;
					$insertreport['status'] = $status;

					$db_query = $this->sql->insert('combatreports', $insertreport);
					$db_result = mysql_query($db_query);
				}
			}

			$this->sql->set(array(
				array('combat', 'rounds', $current['combat']['rounds']),
				array('combat', 'completion', $current['combat']['completion']),
				array('combat', 'scores', serialize($current['combat']['scores'])),
				array('combat', 'beingupdated', 0)));
			$this->sql->where(array('combat', 'combat_id', $current['combat']['combat_id']));
			$this->sql->limit(1);
			$db_result = $this->sql->execute();

			// $db_query = "UPDATE `combat` SET `rounds` = `rounds` + '" . $combat_updates . "', `completion` = '" . $current['combat']['completion'] . "', `beingupdated` = '0' WHERE `combat_id` = '" . $current['combat']['combat_id'] . "' LIMIT 1";
			// $db_result = mysql_query($db_query);

			// End updating planets
		}
		// }

		foreach ($this->type_array as $type)
		{
			foreach ($info['groups'][$type] as $group_id => $group)
			{
				if (empty($info['groups'][$type][$group_id]['units']))
				{
					$info['groups'][$type][$group_id] = null;
					unset($info['groups'][$type][$group_id]);
				}
			}
		}
	}

	/**
	* Get combat information and return $combat, $info, & $blueprints
	*
	* @param
	* @return array( $combat, $info, $blueprints )
	*/
	function getAllInformation($planet_id, $update_to) {
		$this->planetId = $planetId;
		$this->updateTo = $update_to;

		$this->info['round'] = &$this->data->round();

		// ##################################################
		// Get all combats up to now. (or on just one planet) {
		$this->getCombatInformation();
		// }

		// Nothing to update.
		if (empty($this->combat))
		{
			$this->log[] = 'No combat found';
			return array($this->combat, $this->info, $this->blueprints);
		}

		// Fill out planets array
		$this->getPlanetInformation();

		// ##################################################
		// Grab all groups & units and store weapon blueprints {
		$this->getGroupInformation();

		$this->id_array['players'] = array_keys($this->id_array['players']);
		$this->id_array['kingdoms'] = array_keys($this->id_array['kingdoms']);
		$this->id_array['blueprints']['weapon'] = array_keys($this->id_array['blueprints']['weapon']);

		// }

		// ##################################################
		// Grab all weapon blueprints {
		$this->blueprints['weapon'] = &$this->data->blueprint('weapon', $this->id_array['blueprints']['weapon']);
		$this->info['kingdoms'] = &$this->data->kingdom($this->id_array['kingdoms']);
		$this->info['players'] = &$this->data->player($this->id_array['players']);

		// Compatibility with current system.
		return array($this->combat, $this->info, $this->blueprints);
	}

	private function getCombatInformation() {
		$this->sql->where(array(
			array('combat', 'round_id', $_SESSION['round_id']),
			array('combat', 'completion', $this->updateTo, '<='),
			array('combat', 'beingupdated', 0)));
		if (!empty($planet_id)) {
			$this->sql->where(array('combat', 'planet_id', $this->planetId));
		}

		$db_result = $this->sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$this->sql->set(array('combat', 'beingupdated', time()));
			$this->sql->where(array(
				array('combat', 'combat_id', $db_row['combat_id']),
				array('combat', 'beingupdated', 0)));
			$this->sql->execute();

			// Another process grabbed this combat before we could get to it; skip it.
			if (mysql_affected_rows() == 0) {
				continue;
			}

			$db_row['scores'] = unserialize($db_row['scores']);

			// Initialize the combat information array for this planet.
			$this->combat[$db_row['planet_id']] = array(
				'combat' => $db_row,
				'sudo' => array(
					'army' => array(
						'kingdoms' => array(),
						'groups' => array()),
					'navy' => array(
						'kingdoms' => array(),
						'groups' => array())));

			$this->id_array['planets'][$db_row['planet_id']] = true;
		}
	}

	private function getPlanetInformation() {
		$this->id_array['planets'] = array_keys($this->id_array['planets']);
		$this->info['planets'] = &$this->data->planet();

		foreach ($this->info['planets'] as $planet)
		{
			$this->id_array['players'][$planet['player_id']] = true;
			$this->id_array['kingdoms'][$planet['kingdom_id']] = true;
		}
	}

	private function getGroupInformation() {
		foreach ($this->type_array as $type)
		{
			$this->id_array['blueprints'][$type] = array();

			$this->sql->select(array($type . 'groups', $type . 'group_id'));
			$this->sql->where(array($type . 'groups', 'planet_id', $this->id_array['planets'], 'IN'));

			if ($type == 'navy')
			{
				$this->sql->where(array(
					array($type . 'groups', 'x_current', array($type . 'groups', 'x_destination')),
					array($type . 'groups', 'y_current', array($type . 'groups', 'y_destination'))));
			}

			$db_result = $this->sql->execute();
			if (mysql_errno() == 0 && mysql_num_rows($db_result) > 0)
			{
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$this->id_array['groups'][$type][$db_row[$type . 'group_id']] = true;
				}
			}

			if (!empty($this->id_array['groups'][$type])) {
				$this->id_array['groups'][$type] = array_keys($this->id_array['groups'][$type]);
				$this->info['groups'][$type] = &$this->data->group($type, $this->id_array['groups'][$type]);
			}

			foreach ($this->info['groups'][$type] as $group_id => $group) {
				// Merge units into array for later use.
				if (!is_array($group['units'])) {
					$group['units'] = array();
				}

				// Merge units into blueprint array
				$this->id_array['blueprints'][$type] = $this->id_array['blueprints'][$type] + $group['units'];

				// Store units in combat array.
				$this->combat[$group['planet_id']]['sudo'][$type]['kingdoms'][$group['kingdom_id']][$group_id] = $group['units'];

				// Mark player and kingdom for later download
				$this->id_array['players'][$group['player_id']] = true;
				$this->id_array['kingdoms'][$group['kingdom_id']] = true;
			}

			if (!empty($this->id_array['blueprints'][$type]))
			{
				$this->id_array['blueprints'][$type] = array_keys($this->id_array['blueprints'][$type]);
				$this->blueprints[$type] = &$this->data->blueprint($type, $this->id_array['blueprints'][$type]);
			}

			foreach ($this->blueprints[$type] as $blueprint_id => $blueprint)
			{
				foreach ($blueprint['weapons'] as $weapon_id => $count)
				{
					$this->id_array['blueprints']['weapon'][$weapon_id] = true;
				}
			}

			$this->getSudoGroupInformation();
		}
	}

	private function getSudoGroupInformation() {
		foreach ($this->combat as $planet_id => $combat)
		{
			foreach ($combat['sudo'][$type]['kingdoms'] as $kingdom_id => $kingdoms)
			{
				foreach ($kingdoms as $group_id => $group)
				{
					$this->combat[$planet_id]['sudo'][$type]['groups'][$group_id]['kingdom_id'] = $kingdom_id;

					foreach ($group as $unit_id => $unit_count)
					{
						$concept_id = $this->blueprints[$type][$unit_id][$type . 'concept_id'];
						$this->combat[$planet_id]['sudo'][$type]['groups'][$group_id][$concept_id][$unit_id] = $unit_count;
					}
				}
			}
		}
	}

	function kingdom_defeated($kingdom_id)
	{
		$kingdom = &$this->data->kingdom($kingdom_id);
		if (!is_array($kingdom['allies'])) $kingdom['allies'] = array();
		if (!is_array($kingdom['enemies'])) $kingdom['enemies'] = array();

		$relations = &$this->data->kingdom(array_keys($kingdom['allies'] + $kingdom['enemies']));
		foreach (array_keys($relations) as $relation_id)
		{
			if (isset($kingdom['allies'][$relation_id]))
			{
				unset($kingdom['allies'][$relation_id], $relations[$relation_id]['allies'][$kingdom_id]);
			}
			else
			{
				unset($kingdom['enemies'][$relation_id], $relations[$relation_id]['enemies'][$kingdom_id]);
			}
		}
	}
}

?>
