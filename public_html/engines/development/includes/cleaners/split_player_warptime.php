<?php
	function split_player_warptime()
	{
		$db_query = "SELECT `player_id`, `warptime` FROM `players`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$update_query = "UPDATE `planets` SET `warptime_construction` = '" . $db_row['warptime'] . "', `warptime_research` = '" . $db_row['warptime'] . "' WHERE `player_id` = '" . $db_row['player_id'] . "'";
			$update_result = mysql_query($update_query);
		}
	}
?>