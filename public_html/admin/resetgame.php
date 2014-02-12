<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(dirname(__FILE__)) . '/includes/init.php');
	
	if ($_REQUEST['fn'] == 'resetdevelopment')
	{
		$tables = array('kingdoms', 'mail', 'navygroups', 'planets', 'players', 'quadrants', 'scores', 'starsystems', 'tasks');
		foreach ($tables as $value)
		{
			$db_query = "DELETE FROM `" . $value . "` WHERE `game_id` = '1'";
			$db_result = mysql_query($db_query);
		}
		echo 'Development game reset';
	}
?>