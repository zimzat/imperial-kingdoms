<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(dirname(__FILE__)) . '/includes/init.php');
	
	
	// ###############################################
	// Validate function
	$valid_functions = array(
		'default' => 'rounds_list', 
		'rounds_view', 
		'rounds_modify', 
		'rounds_delete', 
		'rounds_archive'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$fn();
	
	// ###############################################
	// List rounds in the database
	function rounds_list()
	{
		global $smarty;
		
		$db_query = "SELECT `round_id`, `name` FROM `rounds` ORDER BY 'stoptime' DESC";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$rounds[] = $db_row;
		}
		
		$smarty->assign('rounds', $rounds);
		
		// Display the page
		$smarty->display('rounds_list.tpl');
	}
	
	
	// ###############################################
	// Create a new round in the database
	function rounds_view()
	{
		global $smarty;
		
		if (isset($_GET['round_id']))
		{
			$round = (int)$_GET['round'];
			$db_query = "SELECT * FROM `rounds` WHERE `round_id` = '" . $round_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$db_row['quadrants'] = unserialize($db_row['quadrants']);
			$db_row['buildings'] = unserialize($db_row['buildings']);
			$db_row['units'] = unserialize($db_row['units']);
			$db_row['concepts'] = unserialize($db_row['concepts']);
			$db_row['researched'] = unserialize($db_row['researched']);
			$db_row['minerals'] = unserialize($db_row['minerals']);
			
			$smarty->assign('round', $db_row);
			
			$smarty->display('rounds_modify.tpl');
		}
		else
		{
			$smarty->display('rounds_modify.tpl');
		}
	}
	
	function rounds_modify()
	{
		global $smarty;
		
		if (isset($_POST['round_id']))
		{
			$status = "The round has been successfully updated.";
			$db_query = "
				UPDATE `rounds` 
				SET 
					`name` = '" . $_POST['name'] . "', 
					`description` = '" . $_POST['description'] . "', 
					`starttime` = '" . $_POST['starttime'] . "', 
					`stoptime` = '" . $_POST['stoptime'] . "' 
				WHERE 
					`round_id` = '" . $_POST['round_id'] . "' 
				LIMIT 1
			";
		}
		else
		{
			$status = "The round has been successfully added.";
			$db_query = "
				INSERT INTO `rounds` 
				(
					`name`, 
					`description`, 
					`starttime`, 
					`stoptime`
				) 
				VALUES 
				(
					'" . $_POST['name'] . "', 
					'" . $_POST['description'] . "', 
					'" . $_POST['starttime'] . "', 
					'" . $_POST['stoptime'] . "'
				)
			";
		}
		$db_result = mysql_query($db_query);
		
		$smarty->append('status', $status);
		rounds_list();
	}
?>