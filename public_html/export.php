<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	// ###############################################
	// Validate Function
	if (isset($_REQUEST['password']) && $_REQUEST['password'] == 'testing-admin-password-here') export();
	
	// ###############################################
	// Get any information needed for the front page
	function export()
	{
		header('Content-Type: text/plain');
		
		if (empty($_REQUEST['request']) || ($_REQUEST['request'] != 'round' && empty($_REQUEST['round_id'])))
			exit('Empty query');
		
		$request = request_variable('request');
		$round_id = abs((int)request_variable('round_id'));
		
		if (!in_array($request, array('kingdom', 'player', 'round')))
			exit('Invalid request: round, kingdom, or player');
		if ($request != 'round' && empty($round_id))
			exit('Invalid round id');
		
		$sql = new SQL_Generator;
		
		if ($request == 'round')
		{
			$sql->select(array(
				array('rounds', 'round_id'), 
				array('rounds', 'name'), 
				array('rounds', 'starttime'), 
				array('rounds', 'stoptime')));
			$sql->where(array('rounds', 'public', 1));
		}
		else
		{
			$sql->select(array($request . 's', $request . '_id'));
			
			if ($request == 'player')
				$sql->select(array($request . 's', 'kingdom_id'));
			
			$sql->select(array(
				array($request . 's', 'name'), 
				array($request . 's', 'score'), 
				array($request . 's', 'score_peak')));
			$sql->where(array($request . 's', 'round_id', $round_id));
		}
		
		$sql->orderby(array($request . 's', $request . '_id', 'asc'));
		
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$output = implode(',', $db_row) . ",\n";
			if (substr_count($output, ',') > count($db_row))
			{
				$output = ''; $multiple = false;
				foreach ($db_row as $value)
				{
					if ($multiple) $output .= ',';
					else $multiple = true;
					$output .= str_replace(',', '', $value);
				}
				$output .= ",\n";
			}
			echo $output;
		}
		exit;
	}
?>
