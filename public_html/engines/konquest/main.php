<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		include(dirname(__FILE__) . '/includes/init.php');
		
		$valid_functions = array(
			'default' => 'display_map', 
			'display_fleets', 
			'display_scores', 
			'measure_distance', 
			'send_fleet', 
			'end_turn', 
			'create_game');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$interface = new Interface($data, $smarty);
		$interface->$fn();
	}
	
	
	/*
		28x28 cell
		1 border
		centered background image
		7 status rows
		
		Planet Name: G
		Owner: Comp2
		Ships: 31
		Production: 7
		Kill Percent: 0.795
		
		
		Turn #: 19 of 40
		
		
		Max players and npcs:
			16 (w/ 2 cell spacing)
		
		Max planets: 
			36 (w/ 1 cell spacing)
		
		Colors:
			Players:
				$colors = array('#000080', '#0000FF', '#00FF00', '#00FFFF', '#800000', '#8000FF', '#808000', '#909090', '#80FF00', '#FF0000', '#FF00FF', '#FF8000', '#FF8080', '#FF80FF', '#FFFF00', '#FFFFFF');
			Grid:
				$colors = array('#008000');
			Neutrals:
				$colors = array('#404040');
		
		if ($player['user_id'] == 0)
		{
			if ($player['name'] == '')
			{
				// Vacant
			}
			else
			{
				// NPC
			}
		}
		else
		{
			// Player
		}
	*/
	
	class Core
	{
		var $data;
		var $smarty;
		var $sql;
		
		function Core($data, $smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
		}
		
		function turn()
		{
			$this->fleets();
			$this->production();
			
			$round = &$this->data->round();
			
			if ($round['turn_current'] == $round['turn_total'])
			{
				// game over
			}
			
			$round['turn_current']++;
			
		}
		
		function fleets()
		{
			$round = &$this->data->round();
			
			$this->sql->select(array('konquest_fleets', 'fleet_id'));
			$this->sql->where(array(
				array('konquest_fleets', 'round_id', $_SESSION['round_id']), 
				array('konquest_fleets', 'arrival_turn', $round['turn_current'], '>'), 
				array('konquest_fleets', 'arrival_turn', $round['turn_current'] + 1, '<=')));
			$this->sql->orderby(array('konquest_fleets', 'arrival_turn', 'asc'));
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				$fleets[$db_row['fleet_id']] = true;
			
			$fleets = &$this->data->fleet(array_keys($fleets));
			
			foreach ($fleets as $fleet)
			{
				$planets[$fleet['destination_planet_id']] = true;
				$players[$fleet['player_id']] = true;
			}
			
			$planets = &$this->data->planet(array_keys($planets));
			
			foreach ($planets as $planet)
				$players[$planet['player_id']] = true;
			
			$players = &$this->data->player(array_keys($players));
			
			foreach (array_keys($fleets) as $fleet_id)
			{
				$fleet = &$fleets[$fleet_id];
				
				$planet_id = $fleet['destination_planet_id'];
				
				if ($planets[$planet_id]['player_id'] == $fleet['player_id'])
				{
					$players[$fleet['player_id']]['fleets']--;
					$planets[$planet_id]['ships'] += $fleet['ships'];
					continue;
				}
				
				$attacker_original = $fleet;
				$defender_original = $planets[$planet_id];
				
				$attacker = &$fleet;
				$defender = &$planets[$planet_id];
				
				if ($attacker['ships'] > $defender['ships'])
					$ships = $defender['ships'];
				else $ships = $attacker['ships'];
				
				for ($i = 1; $i < $ships; $i++)
				{
					if (rand(0, 100) / 100 > $defender['kill_percent']) $attacker['ships']--;
					if (rand(0, 100) / 100 > $attacker['kill_percent']) $defender['ships']--;
				}
				
				do
				{
					if (rand(0, 100) / 100 > $defender['kill_percent'])
					{
						$attacker['ships']--;
						if ($attacker['ships'] <= 0)
						{
							$winner = 'defender';
							break;
						}
					}
					
					if (rand(0, 100) / 100 > $attacker['kill_percent'])
					{
						$defender['ships']--;
						if ($defender['ships'] <= 0)
						{
							$winner = 'attacker';
							break;
						}
					}
							
				} while ($planets[$planet_id]['ships'] > 0 && $fleet['ships'] > 0);
				
				// Update player stats
				$ships_lost = $attacker_original['ships'] - $attacker['ships'];
				$players[$attacker['player_id']]['ships'] -= $ships_lost;
				$players[$attacker['player_id']]['ships_lost'] += $ships_lost;
				$players[$defender['player_id']]['ships_destroyed'] += $ships_lost;
				
				$ships_lost = $defender_original['ships'] - $defender['ships'];
				$players[$defender['player_id']]['ships'] -= $ships_lost;
				$players[$defender['player_id']]['ships_lost'] += $ships_lost;
				$players[$attacker['player_id']]['ships_destroyed'] += $ships_lost;
				
				if ($winner == 'defender')
				{
					$players[$defender['player_id']]['fleets_destroyed']++;
					$players[$attacker['player_id']]['fleets_lost']++;
					$players[$attacker['player_id']]['fleets']--;
				}
				else
				{
					$defender['ships'] = $attacker['ships'];
					
					$players[$attacker['player_id']]['fleets_destroyed']++;
					$players[$defender['player_id']]['fleets_lost']++;
					$players[$defender['player_id']]['fleets']--;
					
					$players[$attacker['player_id']]['planets']++;
					$players[$attacker['player_id']]['planets_won']++;
					
					$players[$defender['player_id']]['planets']--;
					$players[$defender['player_id']]['planets_lost']++;
				}
			}
		}
		
		function production()
		{
			$this->sql->select(array('konquest_planets', 'planet_id'));
			$this->sql->where(array('konquest_planets', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				$planets[$db_row['planet_id']] = true;
			
			$planets = &$this->data->planet(array_keys($planets));
			
			foreach ($planets as $planet)
				$players[$planet['player_id']] = true;
			
			$players = &$this->data->player(array_keys($players));
			
			foreach (array_keys($planets) as $planet_id)
			{
				$planet = &$planets[$planet_id];
				
				if ($planet['player_id'] == 0) $planet['ships']++;
				else $planet['ships'] += $planet['production'];
			}
		}
				
	}
	
	class Planet
	{
		var $data;
		var $smarty;
		var $sql;
		
		function Planet($data, $smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
		}
		
		function generatePlanet($count = 1)
		{
			$this->sql->select(array(
				array('konquest_planets', 'x'), 
				array('konquest_planets', 'y')));
			$this->sql->where(array('konquest_planets', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$planets[$db_row['x']][$db_row['y']] = true;
			}
			
			for ($i = 0; $i < $count; $i++)
			{
				$type = rand(0, 9);
				$kill_percent = $this->generateKillPercentage();
				$production = $this->generateProduction();
				
				if (count($planets) < 180)
				{
					// Trial and error
					do
					{
						$x = rand(0, 16);
						$y = rand(0, 16);
					} while (!empty($planets[$x][$y]));
				}
				else
				{
					if (!isset($freepositions))
					{
						// Create full array
						for ($x = 0; $x < 16; $x++)
						{
							for ($y = 0; $y < 16; $y++)
							{
								$freepositions[$x][$y] = true;
							}
						}
						
						// Empty positions already taken
						foreach ($planets as $x => $x_axis)
						{
							foreach ($x_axis as $y => $y_axis)
							{
								unset($freepositions[$x][$y]);
							}
						}
					}
					
					// Pick two available coordinates
					$x = array_rand($freepositions);
					$y = array_rand($freepositions[$x]);
				}
				
				$planets[$x][$y] = true;
				
				$planet_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'player_id' => 0, 
					'x' => $x, 
					'y' => $y, 
					'type' => $type, 
					'ships' => $production, 
					'kill_percent' => $kill_percent, 
					'production' => $production);
				$this->sql->execute('konquest_planets', $planet_insert);
			}
		}
		
		function generateKillPercentage()
		{
			$round = &$this->data->round();
			
			return rand($round['killpercent_min'] * 100, $round['killpercent_max'] * 100) / 100;
		}
		
		function generateProduction()
		{
			$round = &$this->data->round();
			
			return rand($round['production_min'], $round['production_max']);
		}
		
		function distance($source_planet, $destination_planet)
		{
			if (!is_array($source_planet))
				$source_planet = &$this->data->planet($source_planet);
			
			if (!is_array($destination_planet))
				$destination_planet = &$this->data->planet($destination_planet);
			
			$x = ($source_planet['x'] - $destination_planet['x']) / 2;
			$y = ($source_planet['y'] - $destination_planet['y']) / 2;
			
			return sqrt(($x * $x) + ($y * $y));
		}
	}
	
	class Interface
	{
		var $data;
		var $smarty;
		var $sql;
		
		function Interface($data, $smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$output = strtolower(request_variable('output', NULL, 'html'));
			
			if (in_array($output, array('html', 'javascript')))
				$this->smarty->assign('output', $output);
			else exit('Invalid output method.');
			
			$round = &$this->data->round();
			
			if ($round['turn_current'] == $round['turn_total'])
			{
				$this->display_score();
				exit;
			}
		}
		
		function create_game()
		{
			$round_insert = array(
				'turn' => array(), 
				'players' => 1);
			$this->sql->execute('konquest_rounds', $round_insert);
			$_SESSION['round_id'] = mysql_insert_id();
			
			$round = &$this->data->round();
			
			for ($x = 0; $x < 16; $x++)
			{
				for ($y = 0; $y < 16; $y++)
				{
					$freepositions[$x][$y] = true;
				}
			}
			
			$colors = array('#000080', '#0000FF', '#00FF00', '#00FFFF', '#800000', '#8000FF', '#808000', '#909090', '#80FF00', '#FF0000', '#FF00FF', '#FF8000', '#FF8080', '#FF80FF', '#FFFF00', '#FFFFFF');
			
			$ships = floor(($round['production_min'] + $round['production_max']) / 2);
			$kill_percent = floor(($round['killpercent_min'] + $round['killpercent_max']) / 2);
			
			$player_insert = array(
				'planets' => 1, 
				'fleets' => 1, 
				'ships' => $ships);
			
			$planets = $round['players'] + $round['npcs'];
			for ($i = 0; $i < $planets; $i++)
			{
				$x = rand($freepositions);
				$y = rand($freepositions[$x]);
				
				for ($clear_x = $x - 2; $clear_x <= $x + 2; $clear_x++)
				{
					for ($clear_y = $y - 2; $clear_y <= $y + 2; $clear_y++)
					{
						if (isset($freepositions[$clear_x][$clear_y]))
							unset($freepositions[$clear_x][$clear_y]);
					}
				}
				
				if ($i > $round['players'])
					$player_insert['name'] = 'Comp' . ($i - $round['players']);
				
				$player_insert['color'] = $colors[$i];
				$this->sql->execute('konquest_players', $player_insert);
				$player_id = mysql_insert_id();
				
				$type = rand(0, 9);
				
				$planet_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'player_id' => $player_id, 
					'x' => $x, 
					'y' => $y, 
					'type' => $type, 
					'ships' => $production, 
					'kill_percent' => $kill_percent, 
					'production' => $production);
				$this->sql->execute('konquest_planets', $planet_insert);
				
			}
			
			$neutral_planets = $round['planets'] - ($round['players'] + $round['npcs']);
			if ($neutral_planets > 0)
			{
				$planet = new Planet($this->data, $this->smarty);
				$planet->generatePlanet($neutral_planets);
			}
		}
		
		function display_map()
		{
			foreach (array('players', 'planets') as $attribute)
			{
				if ($attribute == 'players')
				{
					$this->sql->select(array(
						array('konquest_players', 'round_id'), 
						array('konquest_players', 'player_id'), 
						array('konquest_players', 'name'), 
						array('konquest_players', 'endturn'), 
						array('konquest_players', 'lastturn')));
				}
				
				$this->sql->where(array('konquest_' . $attribute, 'round_id', $_SESSION['round_id']));
				$db_result = $this->sql->execute();
				
				$$attribute = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					if ($attribute == 'planets' && $db_row['player_id'] == 0)
						unset($db_row['ships'], $db_row['kill_percent'], $db_row['production']);
					
					$$attribute[$db_row['planet_id']] = $db_row;
				}
				
				$this->smarty->assign($attribute, $$attribute);
			}
			
			$this->smarty->display('map.tpl');
		}
		
		function display_fleets()
		{
			$this->sql->where(array(
				array('konquest_fleets', 'round_id', $_SESSION['round_id']), 
				array('konquest_fleets', 'player_id', $_SESSION['player_id'])));
			$this->sql->orderby(array('konquest_fleets', 'arrival_turn', 'asc'));
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				$fleets[$db_row['fleet_id']] = $db_row;
			
			$this->smarty->assign('fleets', $fleets);
			$this->smarty->display('fleets.tpl');
		}
		
		function display_scores()
		{
			$this->sql->where(array('konquest_players', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				$players[$db_row['player_id']] = $db_row;
			
			$this->smarty->assign('players', $players);
			$this->smarty->display('scores.tpl');
		}
		
		function send_fleet()
		{
			$player = &$this->data->player($_SESSION['player_id']);
			if ($player['endturn']) exit;
			
			$round = &$this->data->round();
			$planets = &$this->data->planet(array($source_planet_id, $destination_planet_id));
			
			$source_planet_id = request_variable('source_planet_id');
			$destination_planet_id = request_variable('destination_planet_id');
			$ships = request_variable('ships');
			
			if (empty($ships))
				$status[] = 'Must send at least one ship.';
			
			if (empty($planets[$source_planet_id]) || $planets[$source_planet_id]['player_id'] != $_SESSION['player_id'])
				$status[] = 'Invalid source planet.';
			
			if (empty($planets[$destination_planet_id]))
				$status[] = 'Invalid destination planet.';
			
			if (($planets[$source_planet_id]['ships'] - $round['turn'][$source_planet_id]) < $ships)
				$status[] = 'Not enough ships to send.';
			
			if (!empty($status))
			{
				$this->smarty->append('status', $status);
				$this->smarty->display('status.tpl');
				exit;
			}
			
			$round['turn'][$source_planet_id] += $ships;
			
			$this->smarty->assign('planets', $planets);
			
			$distance = Planet::distance($planets[$source_planet_id], $planets[$destination_planet_id]);
			
			$fleet_insert = array(
				'round_id' => $_SESSION['round_id'], 
				'player_id' => $_SESSION['player_id'], 
				'source_planet_id' => $source_planet_id, 
				'destination_planet_id' => $destination_planet_id, 
				'ships' => $ships, 
				'kill_percent' => $planets[$source_planet_id]['kill_percent'], 
				'arrival_turn' => $round['turn_current'] + $distance);
			$this->sql->execute('konquest_fleets', $fleet_insert);
			
			$this->data->save();
		}
		
		function measure_distance()
		{
			$source_planet_id = request_variable('source_planet_id');
			$destination_planet_id = request_variable('destination_planet_id');
			
			$planets = &$this->data->planet(array($source_planet_id, $destination_planet_id));
			
			if (empty($planets[$source_planet_id]))
				$status[] = 'Invalid source planet.';
			
			if (empty($planets[$destination_planet_id]))
				$status[] = 'Invalid destination planet.';
			
			if (!empty($status))
			{
				$this->smarty->append('status', $status);
				$this->smarty->display('status.tpl');
				exit;
			}
			
			$distance = Planet::distance($planets[$source_planet_id], $planets[$destination_planet_id]);
			
			$this->smarty->assign('distance', $distance);
			$this->smarty->display('distance.tpl');
		}
		
		function end_turn()
		{
			$player = &$this->data->player($_SESSION['player_id']);
			
			$player['endturn'] = 1;
			$player['lastturn'] = microfloat();
			
			$this->data->save();
			
			// Now check to see if they were the last person to end turn
		}
	}
?>