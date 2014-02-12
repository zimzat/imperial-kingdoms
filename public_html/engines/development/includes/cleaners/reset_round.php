<?php
	function reset_round()
	{
		$round_id = $_REQUEST['round_id'];
		
		$tables = array(
			'combat', 
			'combatreports', 
			'forum_messages', 
			'forum_topics', 
			'kingdoms', 
			'mail', 
			'news', 
			'permissions', 
			'planets', 
			'players', 
			'propositions', 
			'quadrants', 
			'starsystems', 
			'tasks');
		
		foreach (array('army', 'navy', 'weapon') as $type)
		{
			$tables[] = $type . 'blueprints';
			$tables[] = $type . 'designs';
			if ($type != 'weapon')
				$tables[] = $type . 'groups';
		}
		
		foreach ($tables as $table)
		{
			$db_query = "DELETE FROM `" . $table . "` WHERE `round_id` = '" . $round_id . "'";
			$db_result = mysql_query($db_query);
		}
		
		$db_query = "UPDATE `rounds` SET `researched` = 'a:0:{}' WHERE `round_id` = '" . $round_id . "'";
		$db_result = mysql_query($db_query);
	}
?>