<?php
	$password = 'testing-admin-password-here';
	if (empty($_REQUEST['password']) || $_REQUEST['password'] !== $password)
	{
		echo '<form method="post" action="' . basename(__FILE__) . '">' . "\n";
		echo '<input type="password" name="password" /><br />' . "\n";
		echo '<input type="submit" />' . "\n";
		echo '</form>' . "\n";
		exit;
	}
	
	define('IK_AUTHORIZED', true);
	
	require_once(dirname(__FILE__) . '/constants.php');
	require_once(dirname(__FILE__) . '/functions.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/sql_generator.php');
	$sql = new SQL_Generator;
	
	@$db_link = @mysql_connect('localhost', 'zimzatik', 'your-database-password-here') or die('Could not connect to database server.');
	@mysql_select_db(DATABASE) or die('Could not select database.');
	
	$dir = dir(dirname(__FILE__) . '/cleaners/');
	while (($entry = $dir->read()) !== false)
	{
		if ($entry === '.' || $entry === '..' || 
			$entry{0} === '.' || substr($entry, -4) !== '.php')
			continue;
		
		$functions[] = substr($entry, 0, -4);
	}
	
	$round_select = '';
	$db_query = "
		SELECT 
			`round_id`, 
			`name` 
		FROM `rounds` 
		ORDER BY `round_id` DESC";
	$db_result = mysql_query($db_query);
	while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
	{
		$rounds[$db_row['round_id']] = $db_row['name'];
		$round_select .= '<option value="' . $db_row['round_id'] . '">' . $db_row['name'] . '</option>' . "\n";
	}
	
	echo '<form method="post" action="' . basename(__FILE__) . '">' . "\n";
	echo '<input type="hidden" name="password" value="' . $password . '" />' . "\n";
	echo 'Function: <select name="fn">' . "\n" . '<option>' . implode('</option>' . "\n" . '<option>', $functions) . '</option>' . "\n" . '</select><br />' . "\n";
	echo 'Round: <select name="round_id">' . $round_select . '</select><br />' . "\n";
	echo 'Quadrant ID: <input type="text" name="quadrant_id" /><br />' . "\n";
	echo '<input type="submit" />' . "\n";
	echo '</form>' . "\n";
	
	if (empty($_REQUEST['fn']))
	{
		exit;
	}
	
	$fn = $_REQUEST['fn'];
	$file = dirname(__FILE__) . '/cleaners/' . $fn . '.php';
	
	if (!file_exists($file))
	{
		exit('function not found');
	}
	
	require_once($file);
	
	if (!function_exists($fn))
	{
		exit('function not found');
	}
	
	$fn();
	exit($fn);
	

	// The rest of this file is remenants of the previous cleaner mess.
	exit('Done.');
	
	
	
	
	// TODO: Add blueprint to concept_id link
	foreach (array('army', 'navy', 'weapon') as $type)
	{
		$unit_types = array();
		$sql->select(array(
			array($type . 'blueprints', $type . 'blueprint_id'), 
			array($type . 'designs', $type . 'concept_id')));
		$sql->where(array(
			array($type . 'designs', $type . 'design_id', array($type . 'blueprints', $type . 'design_id'))));
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$unit_types[$db_row[$type . 'concept_id']][] = $db_row[$type . 'blueprint_id'];
		}
		
		foreach ($unit_types as $unit_type => $units)
		{
			$sql->set(array($type . 'blueprints', $type . 'concept_id', $unit_type));
			$sql->where(array($type . 'blueprints', $type . 'blueprint_id', $units, 'IN'));
			$db_result = $sql->execute();
		}
	}
	
//	 $sql->select(array('tasks', 'task_id'));
//	 $sql->where(array(
//		 array('tasks', 'type', array(TASK_BUILD, TASK_RESEARCH, TASK_UPGRADE, TASK_UNIT), 'IN'), 
//		 array('planets', 'planet_id', array('tasks', 'planet_id')), 
//		 array('tasks', 'kingdom_id', array('planets', 'kingdom_id'), '!='), 
//		 array('tasks', 'kingdom_id', 0, '!='), 
//		 array('tasks', 'planet_id', 0, '!=')));
//	 $db_result = $sql->execute();
//	 if ($db_result && mysql_num_rows($db_result) > 0)
//	 {
//		 while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
//		 {
//			 $task_ids[] = $db_row['task_id'];
//		 }
//		 
//		 $db_query = "
//			 DELETE FROM `tasks` 
//			 WHERE `task_id` IN ('" . implode("', '", $task_ids) . "') 
//			 LIMIT " . count($task_ids);
//		 $db_result = mysql_query($db_query);
//	 }
	
	
	
	
	// ##################################################
	// Clean up kingdom members
	$db_query = "SELECT `round_id`, `kingdom_id`, `player_id` FROM `players` ORDER BY `round_id` ASC, `kingdom_id` ASC, `player_id` ASC";
	$db_result = mysql_query($db_query);
	while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
	{
		$members[$db_row['round_id']][$db_row['kingdom_id']][$db_row['player_id']] = true;
	}
	
	foreach ($members as $round_id => $kingdoms)
	{
		foreach ($kingdoms as $kingdom_id => $players)
		{
			$db_query = "UPDATE `kingdoms` SET `members` = '" . mysql_real_escape_string(serialize($players)) . "' WHERE `kingdom_id` = '" . $kingdom_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
		}
	}
	unset($members);
	
	
	
	
	// ##################################################
	// Clean up kingdom and player planets
	$db_query = "SELECT `round_id`, `kingdom_id`, `player_id`, `planet_id` FROM `planets` ORDER BY `round_id` ASC, `kingdom_id` ASC, `player_id` ASC";
	$db_result = mysql_query($db_query);
	while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
	{
		$planets[$db_row['round_id']]['kingdoms'][$db_row['kingdom_id']][$db_row['planet_id']] = true;
		$planets[$db_row['round_id']]['players'][$db_row['player_id']][$db_row['planet_id']] = true;
	}
	
	foreach ($planets as $round_id => $temp)
	{
		foreach ($temp['kingdoms'] as $kingdom_id => $kingdom_planets)
		{
			$db_query = "UPDATE `kingdoms` SET `planets` = '" . mysql_real_escape_string(serialize($kingdom_planets)) . "' WHERE `kingdom_id` = '" . $kingdom_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
		}
		
		foreach ($temp['players'] as $player_id => $player_planets)
		{
			$player_planets['current'] = array_rand($player_planets);
			$db_query = "UPDATE `players` SET `planets` = '" . mysql_real_escape_string(serialize($player_planets)) . "' WHERE `player_id` = '" . $player_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
		}
	}
?>
