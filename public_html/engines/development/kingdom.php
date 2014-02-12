<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	
	// ###############################################
	// Prisoner filter
	prisoner_filter($_SESSION['player_id']);
	
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'info_kingdom', 
		'info_planet'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$updater->update($_SESSION['kingdom_id']);
	
	$fn();
	
	
	// ###############################################
	// Show the kingdom info for the mini pane
	function info_kingdom()
	{
		global $smarty, $update;
		
		// If requesting a specific
		if (!empty($_REQUEST['kingdom_id']))
		{
			$current = (int)$_REQUEST['kingdom'];
		}
		else
		{
			$db_query = "SELECT `kingdom_id` FROM `players` WHERE `player_id` = '" . $_SESSION['player_id'] . "' LIMIT 1";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$current = $db_row['kingdom_id'];
		}
		
		
		// Show status of kingdom
		$db_query = "
			SELECT 
				`kingdoms`.`kingdom_id`, 
				`kingdoms`.`name`, 
				`kingdoms`.`image`, 
				`kingdoms`.`food`, 
				`kingdoms`.`foodrate`, 
				`kingdoms`.`workers`, 
				`kingdoms`.`workersrate`, 
				`kingdoms`.`energy`, 
				`kingdoms`.`energyrate`, 
				`kingdoms`.`minerals`, 
				`kingdoms`.`mineralsrate`, 
				`kingdoms`.`members` 
			FROM 
				`kingdoms` 
			WHERE 
				`kingdoms`.`round_id` = '" . $_SESSION['round_id'] . "' AND 
				`kingdoms`.`kingdom_id` = '" . $current . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$kingdom = $db_row;
		
		$kingdom['minerals'] = unserialize($kingdom['minerals']);
		$kingdom['members'] = unserialize($kingdom['members']);
		
		$i = 0;
		$db_query = "SELECT COUNT(`planet_id`) as 'planets' FROM `planets` WHERE `player_id` IN ('" . implode("', '", array_keys($kingdom['members'])) . "')";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$kingdom['planets'] = $db_row['planets'];
		
		if (isset($kingdom['members'][$_SESSION['player_id']]))
		{
			$kingdom['member'] = true;
		}
		
		$kingdom['members'] = count($kingdom['members']);
		
		if (is_array($kingdom['minerals']) && count($kingdom['minerals']) > 0)
		{
			$kingdom['minerals'] = array_sum($kingdom['minerals']);
		}
		else
		{
			$kingdom['minerals'] = 0;
		}
		
		$smarty->assign('kingdom', $kingdom);
		$smarty->display('info_kingdom.tpl');
	}
?>