<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		// ###############################################
		// Validate function
		$valid_functions = array(
			'default' => 'prepround', 
			'joinround', 
			'selectround');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$join = new Join($data, $smarty);
		$join->$fn();
	}
	
	
	
	class Join
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $round;
		
		var $kingdom;
		var $player;
		var $planet;
		var $planets;
		
		var $starsystem_id;
		var $npc;
		
		function Join(&$data, &$smarty, $npc = false)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$this->round = $this->data->round($_SESSION['round_id']);
			
			if ($npc == false)
			{
//				 $this->player['name'] = request_variable('player_name', 'request', '');
				$this->player['name'] = (isset($_REQUEST['player_name'])) ? $_REQUEST['player_name'] : '';
				
				$this->planet['name'] = (isset($_REQUEST['planet_name'])) ? $_REQUEST['planet_name'] : '';
				$this->planet['bonus'] = (isset($_REQUEST['bonus'])) ? (int)$_REQUEST['bonus'] : 0;
				$this->planet['code'] = (isset($_REQUEST['planet_code'])) ? $_REQUEST['planet_code'] : '';
				$this->planets = (isset($_REQUEST['planets'])) ? (int)$_REQUEST['planets'] : $this->round['min_planets'];
				
				$this->kingdom['name'] = (isset($_REQUEST['kingdom_name'])) ? $_REQUEST['kingdom_name'] : '';
				$this->kingdom['mode'] = (isset($_REQUEST['kingdom_mode'])) ? $_REQUEST['kingdom_mode'] : '';
				
				$this->npc = false;
			}
			else
			{
				$names['planets'] = file(dirname(__FILE__) . '/includes/names_planets.txt');
				$names['players'] = file(dirname(__FILE__) . '/includes/names_players.txt');
				
				$this->player['name'] = trim($names['players'][rand(0, count($names['players']))]);
				
				$this->planet['name'] = trim($names['planets'][rand(0, count($names['planets']))]);
				$this->planet['bonus'] = rand(0, 1);
				$this->planets = $npc[0];
				
				$this->kingdom['name'] = trim($names['planets'][rand(0, count($names['planets']))]);
				$this->kingdom['mode'] = 'independant';
				
				$this->npc = true;
			}
		}
		
		// ###############################################
		// Prep information before joining the round
		function prepround()
		{
			if (!empty($this->round))
			{
				$this->smarty->assign('min_planets', $this->round['min_planets']);
				$this->smarty->assign('max_planets', $this->round['max_planets']);
				$this->smarty->assign('description', $this->round['description']);
				$this->smarty->assign('teams', $this->round['teams']);
				if ($this->round['teams'] == TEAMS_SOLO || $this->round['bonus'] == 0)
					$this->smarty->assign('bonus', 0);
				$this->smarty->display('join.tpl');
			}
			else
			{
				$_SESSION['status'][] = 'Invalid round.';
				redirect('login.php');
			}
			
			exit;
		}
		
		// ###############################################
		// Find out which round the user would like to join
		// The round number was recieved during login
		function joinround()
		{
			// Find out if the user is already in the round
			$this->sql->select(array(
				array('users', 'style'), 
				array('players', 'player_id'), 
				array('players', 'kingdom_id')));
			$this->sql->where(array(
				array('users', 'user_id', $_SESSION['user_id']), 
				array('players', 'user_id', $_SESSION['user_id']), 
				array('players', 'round_id', $_SESSION['round_id'])));
			$db_result = $this->sql->execute();
			
			// If so...
			if (mysql_num_rows($db_result) > 0)
			{
				// Get their details and store them in the current session
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$_SESSION['player_id'] = $db_row['player_id'];
				$_SESSION['kingdom_id'] = $db_row['kingdom_id'];
				$_SESSION['style'] = $db_row['style'];
				
				// All lights are a go. Load the round now.
				redirect('main.php');
			}
			
			$this->check_input();
		}
		
		function returnform()
		{
			if ($this->npc) return false;
			
			$this->smarty->assign('kingdom_name', $this->kingdom['name']);
			$this->smarty->assign('kingdom_mode', $this->kingdom['mode']);
			$this->smarty->assign('player_name', $this->player['name']);
			$this->smarty->assign('planet_name', $this->planet['name']);
			$this->smarty->assign('planet_code', $this->planet['code']);
			$this->smarty->assign('planet_bonus', $this->planet['bonus']);
			$this->smarty->assign('planets', $this->planets);
			
			$this->prepround();
		}
		
		function check_input()
		{
			$modes = array();
			// Check kingdom mode to see what we're doing.
			if (in_array($this->round['teams'], array(TEAMS_SOLO, TEAMS_BOTH)))
				$modes[] = 'independant';
			
			if (in_array($this->round['teams'], array(TEAMS_BOTH, TEAMS_TEAMS)))
			{
				$modes[] = 'createteam';
				$modes[] = 'jointeam';
			}
			
			if (!in_array($this->kingdom['mode'], $modes))
			{
				$status[] = 'Invalid kingdom mode.';
				
				$this->smarty->append('status', $status);
				return $this->returnform();
			}
			
			
			$kingdom_strlen = strlen($this->kingdom['name']);
			$player_strlen = strlen($this->player['name']);
			$planet_strlen = strlen($this->planet['name']);
			
			// Check for valid bonus if independant and allowed
			if ($this->round['bonus'] == 0 && (
				$this->kingdom['mode'] == 'independant' && $this->round['teams'] != TEAMS_SOLO && 
				($this->planet['bonus'] < 0 || $this->planet['bonus'] > 1)))
			{
				$status[] = 'Invalid planet bonus selected.<br />';
			}
			
			if ($this->kingdom['mode'] == 'independant')
			{
				if ($this->round['min_planets'] == $this->round['max_planets'])
				{
					$this->planets = $this->round['min_planets'];
				}
				elseif ($this->planets < $this->round['min_planets'] || $this->planets > $this->round['max_planets'])
				{
					$status[] = 'Invalid number of planets selected.<br />';
				}
			}
			
			// Check kingdom, player, and planet names as needed.
			if (($this->kingdom['mode'] == 'independant' || $this->kingdom['mode'] == 'createteam') && 
				$error = str_check($this->kingdom['name'], array(3, 25, REGEXP_NAME)))
				$status[] = 'Kingdom name error: ' . implode(' ', $error) . '<br />';
			
			if ($error = str_check($this->player['name'], array(3, 25, REGEXP_NAME)))
				$status[] = 'Player name error: ' . implode(' ', $error) . '<br />';
			
			if ($error = str_check($this->planet['name'], array(3, 25, REGEXP_NAME_PLANET)))
				$status[] = 'Planet name error: ' . implode(' ', $error) . '<br />';
			
			// Check planet code length (if length != 32 then invalid)
			if ($this->kingdom['mode'] == 'jointeam' && 
				$error = str_check($this->planet['code'], array(32, 32)))
			{
				$status[] = 'Planet code length incorrect.<br />';
			}
			
			// Report errors before going on any further.
			if (!empty($status))
			{
				$this->smarty->append('status', $status);
				return $this->returnform();
			}
			
			// Check the database to see if their name(s) are already in use
			$this->sql->select(array('players', 'player_id'));
			$this->sql->where(array(
				array('players', 'round_id', $_SESSION['round_id']), 
				array('players', 'name', $this->player['name'])));
			$db_result = $this->sql->execute();
			if (mysql_num_rows($db_result) > 0)
				$status[] = 'Player name "' . $this->player['name'] . '" already in use in this round.<br />';
			
			if ($this->kingdom['mode'] == 'independant' || $this->kingdom['mode'] == 'createteam')
			{
				$this->sql->select(array('kingdoms', 'kingdom_id'));
				$this->sql->where(array(
					array('kingdoms', 'round_id', $_SESSION['round_id']), 
					array('kingdoms', 'name', $this->kingdom['name'])));
				$db_result = $this->sql->execute();
				if (mysql_num_rows($db_result) > 0)
					$status[] = 'Kingdom name "' . $this->kingdom['name'] . '" already in use in this round.<br />';
			}
			
			if (!empty($status))
			{
				$this->smarty->append('status', $status);
				return $this->returnform();
			}
			
			// All of the user input checked out; proceed to getting them into the round.
			$this->selectplanets();
		}
		
		function selectplanets()
		{
			/*
				What do we want to do about teams vs solo players?
				What about teams with less than 'planet' players
				
				How about give all players all five planets then give 'planet' planet codes.
				New players could take over planets with a code.
				That would require allowing planets with owners to be given away using the planet code system.
				
				Another idea is to give solo and team creators one planet and 'planet - 1 codes.
				New or existing players can then redeem any number of codes, with each only being used once.
				I like this idea. It uses the existing system and the only modification needed will be to allow code redemption in-game.
				
				What about giving out a code that can used to join a team and be given a planet from existing players?
				There would need to be some way to make sure it was only used for the original 'planet' planets.
				No, it would still be possible to abuse it. Go with the second idea.
			*/
			// If they are creating a team give them all of the planets?
			if ($this->kingdom['mode'] == 'createteam' || $this->kingdom['mode'] == 'jointeam')
				$this->planets = 1;
			
			// Check for available planets and create kingdom
			if ($this->kingdom['mode'] == 'independant' || $this->kingdom['mode'] == 'createteam')
			{
				// Grab a starsystem that's available.
				if ($this->kingdom['mode'] == 'createteam')
				{
					$this->starsystem_id = $this->availablestarsystem('createteam');
				}
				else
				{
					$this->starsystem_id = $this->availablestarsystem($this->planets);
				}
				
				// If zero was returned nothing is available.
				if ($this->starsystem_id == 0)
				{
					if ($this->kingdom['mode'] == 'createteam')
					{
						$status[] = 'There is no more room in this round for new teams.<br />If you really want in this round, try creating an independant kingdom or finding a team with an available planet.';
					}
					elseif ($this->kingdom['mode'] == 'independant' && $this->planets > $this->round['min_planets'])
					{
						$status[] = 'There is no more room in this round for an independant kingdom with ' . $this->planets . ' planets. Try asking for less planets, or find a team with an available planet.';
					}
					else
					{
						$status[] = 'There is no more room in this round for independant kingdoms.<br />If you really want in this round, try finding a team with an available planet.';
					}
					
					$this->smarty->append('status', $status);
					return $this->returnform();
				}
				else
				{
					if ($this->kingdom['mode'] == 'createteam')
					{
						$db_query = "UPDATE `starsystems` SET `available` = '0' WHERE `starsystem_id` = '" . $this->starsystem_id . "' LIMIT 1";
						$db_result = mysql_query($db_query);
					}
					else
					{
						$db_query = "UPDATE `starsystems` SET `available` = `available` - '" . $this->planets . "' WHERE `starsystem_id` = '" . $this->starsystem_id . "' LIMIT 1";
						$db_result = mysql_query($db_query);
					}
				}
				
				$this->new_kingdom();
			}
			elseif ($this->kingdom['mode'] == 'jointeam')
			{
				$this->sql->select(array(
					array('planets', 'planet_id'), 
					array('kingdoms', 'kingdom_id'), 
					array('kingdoms', 'members'), 
					array('kingdoms', 'planets')));
				$this->sql->where(array(
					array('planets', 'player_id', 0), 
					array('planets', 'code', $this->planet['code']), 
					array('kingdoms', 'kingdom_id', array('planets', 'kingdom_id')), 
					array('kingdoms', 'round_id', $_SESSION['round_id']), 
					array('planets', 'round_id', $_SESSION['round_id'])));
				$this->sql->limit(1);
				
				$db_query = $this->sql->generate();
				$db_result = mysql_query($db_query);
				
				if (mysql_num_rows($db_result) == 0)
				{
					$status[] = 'The planet code does not match any on file, or the planet has already been claimed.';
					$this->smarty->append('status', $status);
					return $this->returnform();
				}
				
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$this->kingdom['kingdom_id'] = $db_row['kingdom_id'];
				$this->kingdom['members'] = unserialize($db_row['members']);
				$this->kingdom['planets'] = unserialize($db_row['planets']);
				$this->player['planets'][$db_row['planet_id']] = true;
			}
			
			$warptime = (microfloat() - $this->round['starttime']) * $this->round['warptime'];
			
			$player_insert = array(
				'round_id' => $_SESSION['round_id'], 
				'user_id' => $_SESSION['user_id'], 
				'name' => $this->player['name'], 
				'kingdom_id' => $this->kingdom['kingdom_id']);
			if ($this->npc)
			{
				$player_insert['npc'] = 1;
				$player_insert['user_id'] = 0;
			}
			$db_query = $this->sql->insert('players', $player_insert);
			$db_result = mysql_query($db_query);
			$this->player['player_id'] = mysql_insert_id();
			
			$this->sql->set(array(
				array('planets', 'name', $this->planet['name']), 
				array('planets', 'kingdom_id', $this->kingdom['kingdom_id']), 
				array('planets', 'player_id', $this->player['player_id']), 
				array('planets', 'status', PLANETSTATUS_OCCUPIED), 
				array('planets', 'lastupdated', microfloat()), 
				array('planets', 'warptime_construction', $warptime), 
				array('planets', 'warptime_research', $warptime)));
			$this->sql->where(array('planets', 'planet_id', array_keys($this->player['planets']), 'IN'));
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			
			$db_query = "
				SELECT 
					SUM(`food`) AS 'food', 
					SUM(`foodrate`) AS 'foodrate', 
					SUM(`workers`) AS 'workers', 
					SUM(`workersrate`) AS 'workersrate', 
					SUM(`energy`) AS 'energy', 
					SUM(`energyrate`) AS 'energyrate' 
				FROM `planets` 
				WHERE `planet_id` IN ('" . implode("', '", array_keys($this->player['planets'])) . "') 
				LIMIT " . count($this->player['planets']);
			$db_result = $this->sql->query($db_query);
			$resource_sums = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$db_query = "
				UPDATE `kingdoms` 
				SET 
					`food` = `food` + '" . $resource_sums['food'] . "', 
					`foodrate` = `foodrate` + '" . $resource_sums['foodrate'] . "', 
					`workers` = `workers` + '" . $resource_sums['workers'] . "', 
					`workersrate` = `workersrate` + '" . $resource_sums['workersrate'] . "', 
					`energy` = `energy` + '" . $resource_sums['energy'] . "', 
					`energyrate` = `energyrate` + '" . $resource_sums['energyrate'] . "' 
				WHERE `kingdom_id` = '" . $this->kingdom['kingdom_id'] . "' 
				LIMIT 1";
			$db_result = $this->sql->query($db_query);
			
			
			$this->kingdom['planets'] = $this->kingdom['planets'] + $this->player['planets'];
			$this->planet['planet_id'] = array_rand($this->player['planets']);
			$this->player['planet_current'] = $this->planet['planet_id'];
			$this->kingdom['members'][$this->player['player_id']] = true;
			
			
			$this->sql->set(array(
				array('kingdoms', 'members', serialize($this->kingdom['members'])), 
				array('kingdoms', 'planets', serialize($this->kingdom['planets']))));
			$this->sql->where(array('kingdoms', 'kingdom_id', $this->kingdom['kingdom_id']));
			$this->sql->limit(1);
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			
			$this->sql->set(array('players', 'planets', serialize($this->player['planets'])));
			$this->sql->where(array('players', 'player_id', $this->player['player_id']));
			$this->sql->limit(1);
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			if ($this->kingdom['mode'] == 'createteam')
			{
				// planet['planet_id'] is a randomly selected planet id that the player owns.
				// Since this is a team creator there was only one planet to select.
				unset($this->planet['planets'][$this->planet['planet_id']]);
				
				$mail_body = 'Below are your planet codes. You can give these to other players to join your kingdom or players already in your kingdom, including yourself, to claim more than one planet. To claim another planet visit the Options - Planet page.' . "\n\n";
				foreach ($this->planet['planets'] as $key => $value)
				{
					$this->sql->set(array(
						array('planets', 'code', $value), 
						array('planets', 'kingdom_id', $this->kingdom['kingdom_id']), 
						array('planets', 'status', PLANETSTATUS_RESERVED)));
					$this->sql->where(array('planets', 'planet_id', $key));
					$this->sql->execute();
					
					$mail_body .= '[b]P#' . $key . ':[/b] ' . $value . "\n";
				}
				
				$mail_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'to_player_id' => $this->player['player_id'], 
					'from_player_id' => $this->player['player_id'], 
					'time' => microfloat(), 
					'subject' => 'Planet Codes', 
					'body' => $mail_body);
				$this->sql->execute('mail', $mail_insert);
				
				$this->sql->set(array('players', 'mail', 1));
				$this->sql->where(array('players', 'player_id', $this->player['player_id']));
				$this->sql->execute();
			}
			elseif ($this->kingdom['mode'] == 'jointeam')
			{
				// Set new member's rank to senator.
				$this->sql->set(array('players', 'rank', RANK_SENATOR));
				$this->sql->where(array('players', 'player_id', $this->player['player_id']));
				$this->sql->limit(1);
				$db_query = $this->sql->generate();
				$db_result = mysql_query($db_query);
			}
			
			if ($this->kingdom['mode'] == 'independant' && $this->round['bonus'] != 0)
			{
				$buildingcount = round(30 / $this->planets);
				
				if ($this->planet['bonus'] == 0)
				{
					// Zero-G
					$buildings[9] = $buildingcount;
					$buildings[1] = $buildingcount * 15;
					$buildings[10] = $buildingcount * 20;
					$buildings[14] = $buildingcount * 10;
					$researchbonus = $buildingcount;
					$buildingbonus = 0;
				}
				else
				{
					// HydroCrane
					$buildings[8] = $buildingcount;
					$buildings[10] = $buildingcount * 15;
					$buildings[14] = $buildingcount * 25;
					$buildingbonus = $buildingcount;
					$researchbonus = 0;
				}
				
				$this->sql->set(array(
					array('planets', 'buildings', serialize($buildings)), 
					array('planets', 'researchbonus', $researchbonus), 
					array('planets', 'buildingbonus', $buildingbonus)));
				$this->sql->where(array('planets', 'player_id', $this->player['player_id']));
				$this->sql->execute();
			}
			
			if (!$this->npc) {
				$this->sql->select(array('users', 'style'));
				$this->sql->where(array('users', 'user_id', $_SESSION['user_id']));
				$db_result = $this->sql->execute();
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$_SESSION['style'] = $db_row['style'];
				
				$_SESSION['player_id'] = $this->player['player_id'];
				$_SESSION['kingdom_id'] = $this->kingdom['kingdom_id'];
				$_SESSION['planet_id'] = $this->planet['planet_id'];
				
				$this->createnpc();
			}
			
			redirect('main.php');
		}
		
		function new_kingdom()
		{
			static $kingdom_insert;
			
			if (empty($kingdom_insert))
			{
				if (!empty($this->round['buildings']))
				{
					foreach ($this->round['buildings'] as $key => $value)
					{
						$kingdombuildings[$key] = 0;
					}
				}
				else
				{
					$kingdombuildings = array();
				}
				
				$kingdom_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'buildings' => serialize($kingdombuildings), 
					'concepts' => $this->round['concepts']);
			}
			
			$kingdom_insert['name'] = $this->kingdom['name'];
			$kingdom_insert['starting_starsystem_id'] = $this->starsystem_id;
			
			$db_result = $this->sql->execute('kingdoms', $kingdom_insert);
			$this->kingdom['kingdom_id'] = mysql_insert_id();
			
			$this->kingdom['planets'] = array();
			
			if ($this->kingdom['mode'] == 'createteam')
			{
				$this->planets = 1;
				
				// Because we haven't actually set the creator's planet in the database we return all of them here
				// and unset the creator's planet before we send the codes later.
				$db_query = "SELECT `planet_id` FROM `planets` WHERE `starsystem_id` = '" . $this->starsystem_id . "'";
				$db_result = mysql_query($db_query);
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$this->planet['planets'][$db_row['planet_id']] = md5(rand(0, 32768));
				}
			}
			
			// Select all of the planets that will be given to the creator / this player
			$db_query = "
				SELECT `planet_id` 
				FROM `planets` 
				WHERE 
					`starsystem_id` = '" . $this->starsystem_id . "' AND 
					`player_id` = '0' AND 
					`round_id` = '" . $_SESSION['round_id'] . "' 
				ORDER BY RAND() 
				LIMIT " . $this->planets;
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$this->player['planets'][$db_row['planet_id']] = true;
			}
		}
		
		function createnpc()
		{
			// Chance of creating NPC player
			if ($this->npc || rand(0, 100) < 25) return;
			
			$free = rand($this->round['min_planets'], $this->round['max_planets']);
			
			$starsystem_id = $this->availablestarsystem($free);
			if (empty($starsystem_id)) return;
			
			$npc = new Join($this->data, $this->smarty, array($free));
			$npc->selectplanets();
		}
		
		function availablestarsystem($planets)
		{
			$round = $this->data->round($_SESSION['round_id']);
			
			// Get the ID of a starsystem with enough available planets in it.
			$this->sql->where(array('starsystems', 'round_id', $_SESSION['round_id']));
			if ($planets == 'createteam')
			{
				$this->sql->where(array('starsystems', 'available', array('starsystems', 'total')));
			}
			else
			{
				if ($planets <= 0) return 0;
				$this->sql->where(array('starsystems', 'available', $planets, '>='));
			}
			$db_query = $this->sql->generate();
			$db_query .= " ORDER BY RAND() LIMIT 1";
			$db_result = mysql_query($db_query);
			
			if (mysql_num_rows($db_result) > 0)
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				if ($db_row['total'] == $db_row['available'])
				{
					$this->create_planets($db_row['quadrant_id'], $db_row['starsystem_id'], $db_row['total']);
				}
				
				// Return the starsystem_id of an available starsystem.
				return $db_row['starsystem_id'];
			}
			else
			{
				// No star systems met our requirements.
				// Run the INAQ query to find an available neighboring quadrant.
				$db_query = "SELECT DISTINCT a.`quadrant_id` FROM `quadrants` a JOIN `quadrants` b WHERE a.`round_id` = '" . mysql_real_escape_string($_SESSION['round_id']) . "' AND b.`round_id` = '" . mysql_real_escape_string($_SESSION['round_id']) . "' AND a.`active` = '0' AND b.`active` = '1' AND (a.`x` = b.`x` OR a.`x` = b.`x`-1 OR a.`x` = b.`x`+1) AND (a.`y` = b.`y` OR a.`y` = b.`y`-1 OR a.`y` = b.`y`+1) ORDER BY RAND(" . time() . ") LIMIT 1";
				$db_result = mysql_query($db_query);
				
				// If we found one...
				if (mysql_num_rows($db_result) > 0)
				{
					$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
					$this->populatequadrant($db_row['quadrant_id']);
				}
				else
				{
					// Nothing was returned by our INAQ query.
					// Make sure there are quadrants for this round registered.
					$db_query = "SELECT COUNT(*) as 'count' FROM `quadrants` WHERE `round_id` = '" . mysql_real_escape_string($_SESSION['round_id']) . "'";
					$db_result = mysql_query($db_query);
					$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
					
					if ($db_row['count'] > 0)
					{
						// There are quadrants in the database, so the round is full.
						return 0;
					}
					else
					{
						// There were no quadrants in the database.
						// Find out how many active quadrants to start off with and add them.
						$db_query = "SELECT `quadrants` FROM `rounds` WHERE `round_id` = '" . mysql_real_escape_string($_SESSION['round_id']) . "'";
						$db_result = mysql_query($db_query);
						$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
						
						$quadrants = unserialize($db_row['quadrants']);
						
						for ($x = 0; $x < 7; $x++)
						{
							for ($y = 0; $y < 7; $y++)
							{
								if (!empty($quadrants[$x][$y]['active']))
								{
									$active = 1;
								}
								else
								{
									$active = 0;
								}
								
								$db_query = "INSERT INTO `quadrants` (`round_id`, `x`, `y`, `active`) VALUES ('" . mysql_real_escape_string($_SESSION['round_id']) . "', '" . mysql_real_escape_string($x) . "', '" . mysql_real_escape_string($y) . "', '" . mysql_real_escape_string($active) ."')";
								$db_result = mysql_query($db_query);
								$quadrant_id = mysql_insert_id();
								
								if ($active == 1)
								{
									$this->populatequadrant($quadrant_id);
								}
							}
						}
					}
				}
				
				// After having found no starsystems available, we've made some available.
				// Return a call to ourselves to find a new starsystem.
				return $this->availablestarsystem($planets);
			}
		}
		
		function create_planets($quadrant_id, $starsystem_id, $total = 1)
		{
			$round = $this->data->round();
			
			$mineralstock = array(0 => 17, 1 => 15, 2 => 17, 3 => 12, 4 =>  9, 5 => 12, 6 => 10, 7 => 8);
			
			// old		500,000,000  $mineralstock = array(0 => 20, 1 => 20, 2 => 15, 3 => 15, 4 => 10, 5 => 10, 6 => 5, 7 => 5);
			// buildings	6,739,850  $mineralstock = array(0 => 19, 1 => 17, 2 => 13, 3 => 13, 4 => 10, 5 => 11, 6 =>  8, 7 =>  9);
			// armies	   1,200,000  $mineralstock = array(0 => 19, 1 => 17, 2 => 17, 3 => 10, 4 => 10, 5 => 10, 6 =>  9, 7 =>  9);
			// navies	  88,550,000  $mineralstock = array(0 => 12, 1 => 12, 2 => 21, 3 => 14, 4 =>  8, 5 => 14, 6 => 12, 7 =>  7);
			
			foreach ($mineralstock as $key => $value)
			{
				$round['mineralsremaining'][$key] = $round['minerals'] * ($value / 100);
			}
			
			$round['minerals'] = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
			$round['foodrate'] = 0;
			$round['workersrate'] = 0;
			$round['energyrate'] = 0;
			$round['mineralsrate'] = 0;
			$round['cranes'] = 1;
			$round['planning'] = 1;
			$round['buildingbonus'] = 0;
			$round['researchbonus'] = 0;
			
			if (!empty($round['buildings']))
			{
				// Fill out the buildings
				$i = 0;
				$db_query = "
					SELECT * 
					FROM `buildings` 
					WHERE `building_id` IN ('" . implode(array_keys($round['buildings']), "', '") . "')";
				$db_result = mysql_query($db_query);
				while ($building = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$round['foodrate'] += $building['foodrate'] * $round['buildings'][$building['building_id']];
					$round['workersrate'] += $building['workersrate'] * $round['buildings'][$building['building_id']];
					$round['energyrate'] += $building['energyrate'] * $round['buildings'][$building['building_id']];
					$round['mineralsrate'] += $building['mineralsrate'] * $round['buildings'][$building['building_id']];
					
					// Hydraulic Cranes
					if ($building['building_id'] == 8) {
						$round['buildingbonus'] = $round['buildings'][$building['building_id']];
					}
					// Zero-G Research Laboratories
					if ($building['building_id'] == 9) {
						$round['researchbonus'] = $round['buildings'][$building['building_id']];
					}
					if ($building['building_id'] == 7) {
						$round['cranes'] = 1 + $round['buildings'][$building['building_id']];
					}
					if ($building['building_id'] == 5 || $building['building_id'] == 6) {
						$round['planning']++;
					}
				}
			}
			
			$planet_insert = array(
				'round_id' => $_SESSION['round_id'], 
				'quadrant_id' => $quadrant_id, 
				'starsystem_id' => $starsystem_id, 
				'type' => rand(1, 9), 
				'buildings' => $round['buildings'], 
				'production' => 'a:0:{}', 
				'cranes' => $round['cranes'], 
				'planning' => $round['planning'], 
				'buildingbonus' => $round['buildingbonus'], 
				'researchbonus' => $round['researchbonus'], 
				'units' => 'a:0:{}', 
				'food' => $round['food'], 
				'foodrate' => $round['foodrate'], 
				'workers' => $round['workers'], 
				'workersrate' => $round['workersrate'], 
				'energy' => $round['energy'], 
				'energyrate' => $round['energyrate'], 
				'minerals' => $round['minerals'], 
				'mineralsremaining' => $round['mineralsremaining'], 
				'extractionrates' => $mineralstock, 
				'mineralsrate' => $round['mineralsrate']);
			for ($i = 0; $i < $total; $i++)
			{
				$x = rand(0, 6);
				$y = rand(0, 6);
				
				while (isset($planets_xy[$x . $y]))
				{
					$x = rand(0, 6);
					$y = rand(0, 6);
				}
				
				$planets_xy[$x . $y] = true;
				
				$planet_insert['x'] = $x;
				$planet_insert['y'] = $y;
				
				$planet_insert['type'] = rand(1, 9);
				$db_result = $this->sql->execute('planets', $planet_insert);
			}
		}
		
		function populatequadrant($quadrant_id)
		{
			// Populate the starsystems table with information about a new available quadrant.
			$this->sql->select(array(
				array('rounds', 'starsystems'), 
				array('rounds', 'planets')));
			$this->sql->where(array('rounds', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$totalplanets = $db_row['planets'];
			$totalstarsystems = $db_row['starsystems'];
			
			// Randomly place starsystems
			for ($n = 0; $n < $totalstarsystems; $n++)
			{
				$x = rand(0, 6);
				$y = rand(0, 6);
				while (isset($starsystems[$x . $y]))
				{
					$x = rand(0, 6);
					$y = rand(0, 6);
				}
				$starsystems[$x . $y]['x'] = $x;
				$starsystems[$x . $y]['y'] = $y;
			}
			
			$starsystem_insert = array(
					'round_id' => $_SESSION['round_id'], 
					'quadrant_id' => $quadrant_id, 
					'total' => $totalplanets, 
					'available' => $totalplanets);
			foreach ($starsystems as $value)
			{
				$starsystem_insert['x'] = $value['x'];
				$starsystem_insert['y'] = $value['y'];
				$this->sql->execute('starsystems', $starsystem_insert);
			}
			
			$this->sql->set(array('quadrants', 'active', 1));
			$this->sql->where(array('quadrants', 'quadrant_id', $quadrant_id));
			$this->sql->execute();
		}
	}
?>