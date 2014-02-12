<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	$updater->update(0, 0, $_SESSION['player_id']);
	
	help_status();
	
	function help_status()
	{
		global $smarty;
		
		$db_query = "
			SELECT 
				`round_id`, 
				`round_engine`, 
				`name`, 
				`description`, 
				`starttime`, 
				`stoptime`, 
				`starsystems`, 
				`planets`, 
				`resistance`, 
				`speed`, 
				`resourcetick`, 
				`combattick` 
			FROM `rounds` 
			WHERE `round_id` = '" . $_SESSION['round_id'] . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$round = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$round['speed'] /= 1000;
		
		// Dynamic attack limit based on elapsed time of current round
		$end_time = 3456000 * $_SESSION['round_speed'];
		$current_time = microfloat() - $round['starttime'];
		$attack_limit = 1 - ($current_time / $end_time);
		if ($attack_limit < 0) $attack_limit = 0;
		
		$round['attack_limit'] = round($attack_limit * 100, 2);
		
		$round['description'] = nl2br($round['description']);
		$round['starttime'] = format_timestamp($round['starttime']);
		$round['stoptime'] = format_timestamp($round['stoptime']);
		$round['resistance'] = format_number($round['resistance']);
		$round['resourcetick'] = format_time(timeparser($round['resourcetick'] / 1000));
		$round['combattick'] = format_time(timeparser($round['combattick'] / 1000));
		
		$smarty->assign('round', $round);
		$smarty->display('help_status.tpl');
		exit;
	}
?>