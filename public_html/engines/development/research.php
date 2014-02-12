<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'research_overview', 
			'research_list', # deprecated, use overview instead.
			'research_info', 
			'research_research', 
			'research_tree', 
			'research_completedlist', 
			'research_planets');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 9);
		if ($fn == 'list') $fn = 'overview';
		
		$research = new Research_Interface($data, $smarty);
		$research->$fn();
	}
	
	class Research_Interface
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $building_id;
		var $planet_id;
		
		var $output_mode;
		
		function Research_Interface(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$this->planet_id = current_planet();
			$this->concept_id = (int)request_variable('concept_id');
			
			permissions_check(PERMISSION_PLANET, $this->planet_id, array('build', 'commission'));
			
			$this->data->updater->update($_SESSION['kingdom_id']);
		}
		
		function planets()
		{
			research_planets();
		}
		
		function overview()
		{
			$kingdom = $this->data->kingdom($_SESSION['kingdom_id']);
			
			$concepts = array();
			
			if (!empty($kingdom['concepts']))
			{
				$this->planets();
				
				$concepts = $this->data->concept(array_keys($kingdom['concepts']));
				
				$types = array(1 => 'Concepts', 2 => 'Buildings', 3 => 'Army Units', 4 => 'Navy Units', 5 => 'Weapons');
				$conceptlist = array('Concepts' => array(), 'Buildings' => array(), 'Army Units' => array(), 'Navy Units' => array(), 'Weapons' => array());
				
				foreach ($concepts as $concept_id => $concept)
				{
					$concepttypes[$concept_id] = $types[$concept['type']];
					$conceptlist[$types[$concept['type']]][$concept_id] = array(
						'name' => $concept['name'], 
						'time' => format_time(timeparser($concept['time'] * $_SESSION['round_speed'])));
				}
				
				$this->sql->select(array(
					array('tasks', 'concept_id'), 
					array('tasks', 'completion'), 
					array('planets', 'name'), 
					array('planets', 'planet_id')));
				$this->sql->where(array(
					array('planets', 'kingdom_id', $_SESSION['kingdom_id']), 
					array('planets', 'round_id', $_SESSION['round_id']), 
					array('tasks', 'round_id', array('planets', 'round_id')), 
					array('planets', 'planet_id', array('tasks', 'planet_id')), 
					array('tasks', 'type', TASK_RESEARCH)));
				$this->sql->orderby(array('tasks', 'completion', 'desc'));
				$db_result = $this->sql->execute();
				if ($db_result)
				{
					$beingresearched = array();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$conceptlist[$types[$concepts[$db_row['concept_id']]['type']]][$db_row['concept_id']]['time'] = format_time(timeparser($db_row['completion'] - microfloat()));
						$beingresearched[$db_row['concept_id']] = 'P#' . $db_row['planet_id'] . ' ' . $db_row['name'];
					}
					$this->smarty->assign('beingresearched', $beingresearched);
				}
				
				foreach ($types as $value)
				{
					if (empty($conceptlist[$value]))
					{
						unset($conceptlist[$value]);
					}
				}
			}
			
			research_planets();
			
			$this->smarty->assign('concepts', $conceptlist);
			$this->smarty->display('research_overview.tpl');
		}
		
		function info()
		{
			// FIXME
			$concept_id = $this->concept_id;
			
			$this->planets();
			
			$concept = $this->data->concept($this->concept_id);
			
			if (empty($concept))
				error(__FILE__, __LINE__, 'DATA', 'Invalid concept selected');
			
			$grant_types = array(
				array(0 => 'concepts', 1 => 'concept_id', 2 => 'Concept'), 
				array(0 => 'buildings', 1 => 'building_id', 2 => 'Building'), 
				array(0 => 'armyconcepts', 1 => 'armyconcept_id', 2 => 'Army Unit'), 
				array(0 => 'navyconcepts', 1 => 'navyconcept_id', 2 => 'Navy Unit'), 
				array(0 => 'weaponconcepts', 1 => 'weaponconcept_id', 2 => 'Weapon'));
			
			foreach ($grant_types as $value)
			{
				if (!empty($concept['grants'][$value[0]]))
				{
					$db_query = "SELECT `name` FROM `" . $value[0] . "` WHERE `" . $value[1] . "` IN ('" . implode("', '", array_keys($concept['grants'][$value[0]])) . "')";
					$db_result = mysql_query($db_query);
					while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$grants[] = array('type' => $value[2], 'name' => $db_row['name']);
					}
				}
			}
			$concept['grants'] = $grants;
			
			$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
			foreach ($concept['mineralspread'] as $key => $value)
			{
				$concept['resources']['minerals'][$mineralnames[$key]] = format_number($concept['minerals'] * ($value / 100), true);
			}
			
			$concept['resources']['time'] = format_time(timeparser($concept['time'] * $_SESSION['round_speed']));
			$concept['resources']['workers'] = format_number($concept['workers'], true);
			$concept['resources']['energy'] = format_number($concept['energy'], true);
			
			unset($concept['minerals'], $concept['mineralspread'], $concept['time'], $concept['workers'], $concept['energy']);
			
			research_planets();
			
			$this->smarty->assign('concept', $concept);
			$this->smarty->display('research_info.tpl');
		}
		
		function cancel()
		{
			$planet_id = $this->planet_id;
			if (isset($_POST['mode']) && $_POST['mode'] == 'js')
				$output_mode = 'javascript';
			else
				$output_mode = '';
			
			permissions_check(PERMISSION_PLANET, $planet_id, 'research');
			
			$planet = &$this->data->planet($planet_id);
			if (empty($planet))
				error(__FILE__, __LINE__, 'INVALID_ID', 'Invalid planet id');
			$kingdom = &$this->data->kingdom($planet['kingdom_id']);
			
			$this->sql->where(array(
				array('tasks', 'type', TASK_RESEARCH), 
				array('tasks', 'planet_id', $planet_id)));
			$db_result = $this->sql->execute();
			if (mysql_num_rows($db_result) == 0)
				error(__FILE__, __LINE__, 'INVALID_ID', 'No research to cancel.');
			$task = mysql_fetch_row($db_result, MYSQL_ASSOC);
			
			$now = microfloat();
			$percentage = ($task['completion'] - $now) / ($task['completion'] - $task['start']);
			
			$score = return_resources($planet, $concept, $percentage);
			
			$planet['score'] -= $score;
			$player['score'] -= $score;
			$kingdom['score'] -= $score;
			
			$this->data->save();
			
			$status[] = 'Research cancelled.';
			$_SESSION['status'][] = $status;
			redirect('research.php');
		}
		
		function research_checkerror($status = array())
		{
			if (empty($status)) return;
			
			if ($this->output_mode == 'javascript')
			{
				echo 'alert(\'' . implode('\n', $status) . '\'); varResearching = false; varError = true;';
				exit;
			}
			
			$this->smarty->append('status', $status);
			$this->overview();
			exit;
		}
		
		function research()
		{
			// FIXME
			$concept_id = $this->concept_id;
			$planet_id = $this->planet_id;
			if (isset($_POST['mode']) && $_POST['mode'] == 'js')
				$output_mode = 'javascript';
			else
				$output_mode = '';
			$this->output_mode = $output_mode;
			
			$status = array();
			
			permissions_check(PERMISSION_PLANET, $planet_id, 'research');
			
			$kingdom = &$this->data->kingdom($_SESSION['kingdom_id']);
			
			if (!isset($kingdom['concepts'][$concept_id]))
			{
				$status[] = 'The concept you selected does not exist, has already been researched, or is outside of your grasp.';
				
				$this->research_checkerror($status);
			}
			
			$db_query = "SELECT `planet_id` FROM `tasks` WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `type` IN ('" . TASK_RESEARCH . "', '" . TASK_UPGRADE . "') AND (`planet_id` = '" . $planet_id . "' OR `concept_id` = '" . $concept_id ."') LIMIT 2";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				if ($db_row['planet_id'] == $planet_id)
				{
					$status[] = 'That planet is already researching something.';
				}
				else
				{
					$status[] = 'That concept is already being researched elsewhere';
				}
			}
			
			$this->research_checkerror($status);
			
			$planet = &$this->data->planet($planet_id);
			$concept = $this->data->concept($concept_id);
			
			$score = 0;
			$resources = array('workers' => SCORE_WORKERS, 'energy' => SCORE_ENERGY);
			
			foreach ($resources as $key => $value)
			{
				$score += $concept[$key] * $value;
				$planet[$key] -= $concept[$key];
				$kingdom[$key] -= $concept[$key];
				
				if ($planet[$key] < 0)
					$status[] = 'Not enough ' . $key . '.';
			}
			
			if (!empty($concept['mineralspread']))
			{
				$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
				foreach ($concept['mineralspread'] as $key => $value)
				{
					$mineral = ($value / 100) * $concept['minerals'];
					
					$score += $mineral * SCORE_MINERALS;
					$planet['minerals'][$key] -= $mineral;
					
					if ($planet['minerals'][$key] < 0)
						$status[] = 'Not enough ' . $mineralnames[$key] . '.';
				}
				
				$kingdom['minerals'] -= $concept['minerals'];
			}
		
			$this->research_checkerror($status);
			
			$player = &$this->data->player($planet['player_id']);
			
			$research_bonus = ($planet['researchbonus'] < 85) ? $planet['researchbonus'] : 85;
			
			$completion = $concept['time'] * $_SESSION['round_speed'] * ((100 - $research_bonus) / 100);
			
			$planet['researching']++;
			$planet['score'] -= $score;
			$player['score'] -= $score;
			
			$warptime = request_variable('warptime');
			if (!is_null($warptime))
			{
				if ($planet['warptime_research'] > $completion)
				{
					$planet['warptime_research'] -= $completion;
					$completion = 0;
				}
				else
				{
					$completion -= $planet['warptime_research'];
					$planet['warptime_research'] = 0;
				}
			}
			
			$now = microfloat();
			
			$task_insert = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'player_id' => $planet['player_id'], 
				'planet_id' => $planet['planet_id'], 
				'type' => TASK_RESEARCH, 
				'concept_id' => $this->concept_id, 
				'completion' => $now + $completion, 
				'start' => $now);
			$this->sql->execute('tasks', $task_insert);
			
			$this->data->save();
			
			if ($output_mode == 'javascript')
			{
				echo 'varResearching = true; varError = false;';
				exit;
			}
			
			if ($completion == 0)
			{
				$_SESSION['status'][] = 'Research successfully completed.';
			}
			else
			{
				$_SESSION['status'][] = 'Research successfully started.';
			}
			
			redirect('research.php');
		}
	}
?>