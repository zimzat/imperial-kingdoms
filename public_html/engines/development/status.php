<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'status_kingdom', 
		'status_player', 
		'status_planet', 
		'status_search', 
		'status_search_submit', 
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$fn();
	
	
	// ###############################################
	// Show the list of upgradable designs
	function status_kingdom()
	{
		global $smarty, $sql, $data;
		
		if (!empty($_REQUEST['kingdom_id']))
		{
			$kingdom_id = (int)$_REQUEST['kingdom_id'];
		}
		else
		{
			$kingdom_id = $_SESSION['kingdom_id'];
		}
		
		$data->updater->update($kingdom_id);
		
		$kingdom = $data->kingdom($kingdom_id);
		if (empty($kingdom))
		{
			error(__FILE__, __LINE__, 'DATA', 'Invalid kingdom selected.');
		}
		
		if ($kingdom['kingdom_id'] == $_SESSION['kingdom_id'])
		{
			$display['member'] = true;
		}
		
		$display['kingdom_id'] = $kingdom['kingdom_id'];
		$display['name'] = $kingdom['name'];
		$display['image'] = $kingdom['image'];
		$display['planet_count'] = count($kingdom['planets']);
		$display['description'] = nl2br(htmlspecialchars($kingdom['description']));
		
		
		$display['score'] = format_number($kingdom['score']);
		if ($kingdom['score_peak'] > $kingdom['score'])
		{
			$display['score_peak'] = format_number($kingdom['score_peak']);
		}
		
		
		if ($kingdom['kingdom_id'] == $_SESSION['kingdom_id'])
		{
			$player = $data->player($_SESSION['player_id']);
			
			// News
			/*
				Mergers
				War / Peace / Alliance Declarations
			*/
			
			if ($player['rank'] >= 80)
			{
				// Propositions
				/*
					Show last six proposals by status and then time
					Show 'More' at bottom of list.
					
					Title	 Y  M  N 
					xxxxxxxx  1  0  2 
					xxxxxxxx  2  1  0 
					xxxxxxxx  0  3  1 
					xxxxxxxx  Rejected
					xxxxxxxx   Expired
					xxxxxxxx	Passed
						More	   
				*/
				
				$sql->select(array(
					array('propositions', 'proposition_id'), 
					array('propositions', 'title'), 
					array('propositions', 'status'), 
					array('propositions', 'for'), 
					array('propositions', 'against'), 
					array('propositions', 'neutral')
				));
				$sql->where(array(
					array('propositions', 'round_id', $_SESSION['round_id']), 
					array('propositions', 'kingdom_id', $_SESSION['kingdom_id']), 
					array('propositions', 'status', 0)
				));
				$sql->orderby(array(
					array('propositions', 'expires', 'DESC')
				));
				$sql->limit(6);
				
				$db_query = $sql->generate(false);
				$db_result = mysql_query($db_query);
				
				$count = 0;
				$propositions = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$count++;
					$propositions[] = $db_row;
				}
				
				$sql->where(array('propositions', 'status', 0), false);
				$sql->where(array('propositions', 'status', 0, '>'));
				$sql->limit(6 - $count);
				
				$db_query = $sql->generate();
				$db_result = mysql_query($db_query);
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$propositions[] = $db_row;
				}
				
				$display['propositions'] = $propositions;
			}
		}
		else
		{
			military_declarations($kingdom['kingdom_id']);
		}
		
		
		if (!empty($kingdom['members']))
		{
			$members = $data->player(array_keys($kingdom['members']));
			$member_ranks = array(
					RANK_PRISONER => 'Prisoner', 
					RANK_GOVERNOR => 'Governor', 
					RANK_STEWARD => 'Steward', 
					RANK_COMMANDER => 'Commander', 
					RANK_SENATOR => 'Senator', 
					RANK_EMPEROR => 'Emperor');
			
			foreach ($members as $player_id => $player)
			{
				$display['members'][$player_id] = array(
					'name' => $player['name'], 
					'player_id' => $player_id, 
					'rank' => $member_ranks[$player['rank']], 
					'score' => format_number($player['score'], true), 
					'on' => (microfloat() - $player['lastactive'] < 900) ? true : false, 
				);
			}
		}
		
		$smarty->assign('kingdom', $display);
		$smarty->display('status_kingdom.tpl');
	}
	
	function status_player()
	{
		global $smarty, $sql, $data;
		
		if (!empty($_REQUEST['player_id']))
		{
			$player_id = (int)$_REQUEST['player_id'];
		}
		else
		{
			$player_id = $_SESSION['player_id'];
		}
		
		$data->updater->update(0, 0, $player_id);
		
		$player = $data->player($player_id);
		if (empty($player))
		{
			error(__FILE__, __LINE__, 'DATA', 'Invalid kingdom selected.');
		}
		
		$display['player_id'] = $player['player_id'];
		$display['kingdom_id'] = $player['kingdom_id'];
		$display['name'] = $player['name'];
		$display['image'] = $player['image'];
		$display['description'] = nl2br(htmlspecialchars($player['description']));
		
		$display['planet_count'] = count($player['planets']);
		
		$display['score'] = format_number($player['score']);
		if ($player['score_peak'] > $player['score'])
		{
			$display['score_peak'] = format_number($player['score_peak']);
		}
		
		if (microfloat() - $player['lastactive'] < 900)
		{
			$display['on'] = true;
		}
		
		$kingdom = $data->kingdom($player['kingdom_id']);
		if (empty($kingdom))
		{
			error(__FILE__, __LINE__, 'DATA', 'Invalid kingdom selected.');
		}
		
		$display['kingdom'] = array(
			'score' => format_number($kingdom['score']), 
			'kingdom_id' => $kingdom['kingdom_id'], 
			'name' => $kingdom['name'], 
		);
		
		$smarty->assign('player', $display);
		$smarty->display('status_player.tpl');
	}
	
	function status_search()
	{
		global $smarty;
		
		$smarty->display('status_search.tpl');
	}
	
	function status_search_submit()
	{
		global $data, $smarty;
		
		if (!empty($_REQUEST['kingdom_id']))
		{
			$search = 'kingdom_id';
			$kingdom_id = abs((int)$_REQUEST['kingdom_id']);
		}
		elseif (!empty($_REQUEST['player_id']))
		{
			$search = 'player_id';
			$player_id = abs((int)$_REQUEST['player_id']);
		}
		elseif (!empty($_REQUEST['kingdom_name']))
		{
			$search = 'kingdom_name';
			$error = str_check($_REQUEST['kingdom_name'], array(3, 25, REGEXP_NAME));
			if ($error)
			{
				$smarty->append('status', 'Invalid characters in kingdom name');
				status_search();
				exit;
			}
			
			$kingdom_name = $_REQUEST['kingdom_name'];
		}
		elseif (!empty($_REQUEST['player_name']))
		{
			$search = 'player_name';
			$error = str_check($_REQUEST['player_name'], array(3, 25, REGEXP_NAME));
			if ($error)
			{
				$smarty->append('status', 'Invalid characters in player name');
				status_search();
				exit;
			}
			
			$player_name = $_REQUEST['player_name'];
		}
		else
		{
			status_search();
			exit;
		}
		
		switch ($search) {
		case 'kingdom_id':
			$results = $data->kingdom($kingdom_id);
			break;
		case 'player_id':
			$results = $data->player($player_id);
			break;
		case 'kingdom_name':
			$db_query = "
				SELECT `kingdom_id` 
				FROM `kingdoms` 
				WHERE 
					`round_id` = " . $_SESSION['round_id'] . " AND 
					`name` LIKE '%" . $kingdom_name . "%'
				ORDER BY `name` ASC";
			$db_results = mysql_query($db_query);
			$kingdom_ids = array();
			while ($db_row = mysql_fetch_array($db_results, MYSQL_ASSOC))
			{
				$kingdom_ids[] = $db_row['kingdom_id'];
			}
			
			$results = $data->kingdom($kingdom_ids);
			break;
		case 'player_name':
			$db_query = "
				SELECT `player_id` 
				FROM `players` 
				WHERE 
					`round_id` = " . $_SESSION['round_id'] . " AND 
					`name` LIKE '%" . $player_name . "%'
				ORDER BY `name` ASC";
			$db_results = mysql_query($db_query);
			$player_ids = array();
			while ($db_row = mysql_fetch_array($db_results, MYSQL_ASSOC))
			{
				$player_ids[] = $db_row['player_id'];
			}
			
			$results = $data->player($player_ids);
			break;
		}
		
		$smarty->assign('results', $results);
		$smarty->assign('search', $search);
		
		status_search();
		exit;
	}
?>