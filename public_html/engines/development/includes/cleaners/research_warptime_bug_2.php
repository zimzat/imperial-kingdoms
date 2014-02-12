<?php
	function research_warptime_bug_2()
	{
		$round_id = $_REQUEST['round_id'];
		
		$db_query = "
			SELECT 
				`planets`.`player_id`, 
				`players`.`warptime`, 
				`planets`.`warptime_research`, 
				COUNT(`planets`) AS 'count' 
			FROM `planets` 
			LEFT JOIN `players` ON `players`.`player_id` = `planets`.`player_id` 
			WHERE 
				`planets`.`round_id` = '" . $round_id . "' AND 
				`planets`.`player_id` != '0' 
			GROUP BY `planets`.`player_id` 
			HAVING COUNT(`planets`) = 1";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			if ($db_row['warptime_research'] == $new_warptime) continue;
			
			$db_query_update = "
				UPDATE `planets` 
				SET `warptime_research` = '" . $db_row['warptime'] . "' 
				WHERE `player_id` = '" . $db_row['player_id'] . "' 
				LIMIT " . $db_row['count'];
			$db_result_update = mysql_query($db_query_update);
		}
	}
?>