<?php
	function setcurrentnavyspeeds()
	{
		header('Content-Type: text/plain');
		echo 'Working...' . "\n";
		
		$db_query = "
			SELECT 
				`navyblueprints`.`navyblueprint_id`, 
				`navydesigns`.`speed_base` 
			FROM 
				`navyblueprints`, 
				`navydesigns` 
			WHERE 
				`navyblueprints`.`navydesign_id` = `navydesigns`.`navydesign_id` AND 
				`navyblueprints`.`techlevel` = `navydesigns`.`techlevel_current` AND 
				`navyblueprints`.`speed` != `navydesigns`.`speed_base`";
		$db_result = mysql_query($db_query);
		
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			echo $db_row['navyblueprint_id'] . ', ' . $db_row['speed_base'] . "\n";
			
			$update_query = "
				UPDATE `navyblueprints` 
				SET `speed` = '" . $db_row['speed_base'] . "' 
				WHERE `navyblueprint_id` = '" . $db_row['navyblueprint_id'] . "' 
				LIMIT 1";
			$update_result = mysql_query($update_query);
		}
	}
?>