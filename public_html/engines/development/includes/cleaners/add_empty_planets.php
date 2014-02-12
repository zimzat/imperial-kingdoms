<?php
	function add_empty_planets()
	{
		global $sql;
		
		$round_id = $_REQUEST['round_id'];
		
		require_once(dirname(dirname(__FILE__)) . '/join.php');
		
		$db_query = "SELECT COUNT(*) AS 'count' FROM `players` WHERE `round_id` = '" . $round_id . "'";
		$db_result = mysql_query($db_query);
		$players = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		for ($i = 0; $i < $players['count']; $i++)
		{
			createemptyplanet();
		}
	}
?>