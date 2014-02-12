<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	
	// ###############################################
	// Prisoner filter
	prisoner_filter($_SESSION['player_id']);
	
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'cluster', 
		'quadrant', 
		'starsystem'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	if (!empty($_REQUEST['coordinates']))
	{
		$smarty->assign('coordinates', true);
	}
	
	$updater->update($_SESSION['kingdom_id']);
	
	$fn();
	
	
	// ###############################################
	// Show the cluster page
	function cluster()
	{
		global $smarty;
		
		// $cluster[x][y]['active']
		// $cluster[x][y]['kingdom']
		// $cluster[x][y]['target']
		
		if (!empty($_REQUEST['target_id']))
		{
			$target_id = abs((int)$_REQUEST['target_id']);
			
			$smarty->assign('target_id', $target_id);
			
			$db_query = "SELECT DISTINCT `quadrant_id` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '" . $target_id . "'";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$target[$db_row['quadrant_id']] = true;
			}
		}
		
		$db_query = "SELECT DISTINCT `quadrant_id` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '" . $_SESSION['kingdom_id'] . "'";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$kingdom[$db_row['quadrant_id']] = true;
		}
		
		$db_query = "SELECT `quadrant_id`, `x`, `y` FROM `quadrants` WHERE `active` = '1' AND `round_id` = '" . $_SESSION['round_id'] . "'";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$cluster[$db_row['x']][$db_row['y']]['quadrant_id'] = $db_row['quadrant_id'];
			$cluster[$db_row['x']][$db_row['y']]['active'] = true;
			
			if (isset($kingdom[$db_row['quadrant_id']]))
			{
				$cluster[$db_row['x']][$db_row['y']]['kingdom'] = true;
			}
			
			if (isset($target[$db_row['quadrant_id']]))
			{
				$cluster[$db_row['x']][$db_row['y']]['target'] = true;
			}
		}
		$smarty->assign('cluster', $cluster);
		
		$smarty->display('map_cluster.tpl');
	}
	
	function quadrant()
	{
		global $smarty;
		
		// $quadrant[x][y]['kingdom']
		// $quadrant[x][y]['target']
		
		if (isset($_REQUEST['quadrant_id']))
		{
			$quadrant_id = (int)$_REQUEST['quadrant_id'];
			
			$db_query = "SELECT `quadrant_id` FROM `quadrants` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `quadrant_id` = '" . $quadrant_id . "'";
			$db_result = mysql_query($db_query);
			
			if (mysql_num_rows($db_result) == 0)
			{
				unset($quadrant_id);
			}
		}
		
		if (!isset($quadrant_id))
		{
			$planet_id = current_planet();
			
			$db_query = "SELECT `quadrant_id` FROM `planets` WHERE `planet_id` = '" . $planet_id . "'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$quadrant_id = $db_row['quadrant_id'];
		}
		
		if (!empty($_REQUEST['target_id']))
		{
			$target_id = abs((int)$_REQUEST['target_id']);
			
			$smarty->assign('target_id', $target_id);
			
			$db_query = "SELECT DISTINCT `starsystem_id` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `quadrant_id` = '" . $quadrant_id . "' AND `kingdom_id` = '" . $target_id . "'";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$target[$db_row['starsystem_id']] = true;
			}
		}
		
		$db_query = "SELECT DISTINCT `starsystem_id` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `quadrant_id` = '" . $quadrant_id . "' AND `kingdom_id` = '" . $_SESSION['kingdom_id'] . "'";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$kingdom[$db_row['starsystem_id']] = true;
		}
		
		$db_query = "SELECT `starsystem_id`, `x`, `y` FROM `starsystems` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `quadrant_id` = '" . $quadrant_id . "' AND `available` < `total`";
		$db_result = mysql_query($db_query);
		
		$quadrant = array();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$quadrant[$db_row['x']][$db_row['y']]['starsystem_id'] = $db_row['starsystem_id'];
			$quadrant[$db_row['x']][$db_row['y']]['exists'] = true;
			
			if (isset($kingdom[$db_row['starsystem_id']]))
			{
				$quadrant[$db_row['x']][$db_row['y']]['kingdom'] = true;
			}
			
			if (isset($target[$db_row['starsystem_id']]))
			{
				$quadrant[$db_row['x']][$db_row['y']]['target'] = true;
			}
		}
		$smarty->assign('quadrant', $quadrant);
		
		$smarty->display('map_quadrant.tpl');
	}
	
	function starsystem()
	{
		global $smarty;
		
		// $starsystem[x][y]['exists']
		// $starsystem[x][y]['kingdom']
		// $starsystem[x][y]['target']
		
		
		if (isset($_REQUEST['starsystem_id']))
		{
			$starsystem_id = (int)$_REQUEST['starsystem_id'];
			
			$db_query = "SELECT `starsystem_id` FROM `starsystems` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `starsystem_id` = '" . $starsystem_id . "'";
			$db_result = mysql_query($db_query);
			
			if (mysql_num_rows($db_result) == 0)
			{
				unset($starsystem_id);
			}
		}
		
		if (!isset($starsystem_id))
		{
			$planet_id = current_planet();
			
			$db_query = "SELECT `starsystem_id` FROM `planets` WHERE `planet_id` = '" . $planet_id . "'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$starsystem_id = $db_row['starsystem_id'];
		}
		
		if (!empty($_REQUEST['target_id']))
		{
			$target_id = abs((int)$_REQUEST['target_id']);
		}
		
		$db_query = "SELECT `planet_id`, `kingdom_id`, `type`, `x`, `y`, `name` FROM `planets` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `starsystem_id` = '" . $starsystem_id . "' AND `status` = '" . PLANETSTATUS_OCCUPIED . "'";
		$db_result = mysql_query($db_query);
		
		$starsystem = array();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			if (empty($db_row['type'])) $db_row['type'] = '';
			
			$starsystem[$db_row['x']][$db_row['y']]['type'] = $db_row['type'];
			$starsystem[$db_row['x']][$db_row['y']]['planet_id'] = $db_row['planet_id'];
			$starsystem[$db_row['x']][$db_row['y']]['planet_name'] = $db_row['name'];
			$starsystem[$db_row['x']][$db_row['y']]['exists'] = true;
			
			if ($db_row['kingdom_id'] == $_SESSION['kingdom_id'])
			{
				$starsystem[$db_row['x']][$db_row['y']]['kingdom'] = true;
			}
			elseif (isset($target_id) && $db_row['kingdom_id'] == $target_id)
			{
				$starsystem[$db_row['x']][$db_row['y']]['target'] = true;
			}
		}
		$smarty->assign('starsystem', $starsystem);
		
		$smarty->display('map_starsystem.tpl');
	}
?>