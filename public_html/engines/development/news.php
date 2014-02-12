<?php
	define('IK_AUTHORIZED', true);
	include(dirname(__FILE__) . '/includes/init.php');
	
	// ###############################################
	// Validate Function
	$updater->update($_SESSION['kingdom_id'], 0, 0);
	
	news();
	
	// ###############################################
	// Show the news page
	function news()
	{
		global $smarty, $sql, $data;
		
		$scores = news_scores();
		$smarty->assign('scores', $scores);
		
		$players = news_players();
		$smarty->assign('players', $players);
		$smarty->assign('kingdom_id', $_SESSION['kingdom_id']);
		
		$playerstats = news_playerstats();
		$smarty->assign('playerstats', $playerstats);
		
//		 $type_news = array(
//			 'military' => array(NEWS_WAR, NEWS_PEACE, NEWS_ALLY, NEWS_PLANETCONQUERED, NEWS_PLAYERCAPTURED, NEWS_KINGDOMDEFEATED), 
//			 'infrastructure' => array(NEWS_FIRSTRESEARCH, NEWS_RESEARCH, NEWS_BUILDING, NEWS_COMMISSION));
		$global_news = array(
			NEWS_WAR, 
			NEWS_PEACE, 
			NEWS_ALLY, 
			NEWS_FIRSTRESEARCH, 
			NEWS_PLANETCONQUERED, 
			NEWS_PLAYERCAPTURED, 
			NEWS_KINGDOMDEFEATED, 
			NEWS_EXECUTION, 
			NEWS_GAMEANNOUNCEMENT);
		$kingdom_news = array(
			NEWS_RESEARCH);
		$player_news = array(
			NEWS_BUILDING, 
			NEWS_COMMISSION);
		
		$news = array();
		
		if (!empty($_REQUEST['news_id']))
		{
			$news_id = abs((int)$_REQUEST['news_id']);
			$sql->where(array('news', 'news_id', $news_id));
			$db_result = $sql->execute();
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if (in_array($db_row['type'], $global_news) || 
				(in_array($db_row['type'], $kingdom_news) && $db_row['kingdom_id'] == $_SESSION['kingdom_id']) ||
				(in_array($db_row['type'], $player_news) && $db_row['player_id'] == $_SESSION['player_id']))
			{
				$news[] = $db_row;
			}
		}
		
		if (empty($news))
		{
			$db_query = "
				SELECT * 
				FROM `news` 
				WHERE 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					(
						`type` IN ('" . implode("', '", $global_news) . "')";
			$player = $data->player($_SESSION['player_id']);
			if ($player['rank'] > 0)
			{
				$db_query .= " OR 
						(
							`type` IN ('" . implode("', '", $kingdom_news) . "') AND 
							`kingdom_id` = '" . $_SESSION['kingdom_id'] . "'
						) OR
						(
							`type` IN ('" . implode("', '", $player_news) . "') AND 
							`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
							`player_id` = '" . $_SESSION['player_id'] . "'
						)";
			}
			$db_query .= "
					)
				ORDER BY `posted` DESC 
				LIMIT 20";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['posted'] = format_timestamp($db_row['posted']);
				$news[] = $db_row;
			}
		}
		
		$smarty->assign('news', $news);
		
		$smarty->display('news.tpl');
	}
	
	function news_scores()
	{
		global $sql, $data;
		
		$scores = array();
		
		$kingdom = $data->kingdom($_SESSION['kingdom_id']);
		
		$select = array(
			array('kingdoms', 'kingdom_id'), 
			array('kingdoms', 'name'), 
			array('kingdoms', 'score'), 
		);
		$where = array('kingdoms', 'round_id', $_SESSION['round_id']);
		$order = array('kingdoms', 'score', 'desc');
		
		// Find kingdom's position:
		$db_query = "SELECT COUNT(*) AS 'position' FROM `kingdoms` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `score` > '" . $kingdom['score'] . "'";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$kingdom_position = $db_row['position'] + 1;
		
		if ($kingdom_position < 15)
		{
			$top_limit = 21;
		}
		else
		{
			$top_limit = 10;
		}
		
		// Get the top ten scores.
		$sql->select($select);
		$sql->where($where);
		$sql->orderby($order);
		$sql->limit($top_limit);
		
		$i = 0;
		$db_query = $sql->generate();
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$i++;
			
			$db_row['score'] = format_number($db_row['score'], true);
			$db_row['position'] = $i;
			$scores[] = $db_row;
		}
		
		if ($top_limit < 21)
		{
			// Seperate the top ten scores.
			$scores[] = array('kingdom_id' => '0');
			
			// Five above
			
			$sql->select($select);
			$sql->where(array(
				array('kingdoms', 'score', $kingdom['score'], '>'), 
				$where));
			$sql->orderby(array('kingdoms', 'score', 'asc'));
			$sql->limit(5);
			
			$i = 0;
			$reverse_scores = array();
			$db_query = $sql->generate();
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$i++;
				
				$db_row['score'] = format_number($db_row['score'], true);
				$db_row['position'] = $kingdom_position - $i;
				$reverse_scores[] = $db_row;
			}
			
			$scores = array_merge($scores, array_reverse($reverse_scores));
			
			// Insert their kingdom
			$scores[] = array(
				'kingdom_id' => $kingdom['kingdom_id'], 
				'name' => $kingdom['name'], 
				'score' => format_number($kingdom['score'], true), 
				'position' => $kingdom_position, 
			);
			
			// Five below
			
			$sql->select($select);
			$sql->where(array(
				array('kingdoms', 'score', $kingdom['score'], '<'), 
				$where));
			$sql->orderby($order);
			$sql->limit(5);
			
			$i = 0;
			$db_query = $sql->generate();
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$i++;
				
				$db_row['score'] = format_number($db_row['score'], true);
				$db_row['position'] = $kingdom_position + $i;
				$scores[] = $db_row;
			}
		}
		
		return $scores;
	}
	
	function news_players()
	{
		global $sql;
		
		$now = microfloat();
		
		$players = array();
		$sql->select(array(
			array('players', 'player_id'), 
			array('players', 'name'), 
			array('players', 'lastactive')
		));
		$sql->where(array(
			array('players', 'round_id', $_SESSION['round_id']), 
			array('players', 'player_id', $_SESSION['player_id'], '!='), 
			array('players', 'lastactive', $now - 1200, '>=')
		));
		$sql->orderby(array('players', 'lastactive', 'desc'));
		$sql->limit(20);
		
		$db_query = $sql->generate();
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['lastactive'] = format_time(timeparser($now - $db_row['lastactive']));
			$players[] = $db_row;
		}
		
		return $players;
	}
	
	function news_playerstats()
	{
		$playerstats = array();
		
		// Recent Players in the last 48 hours
		$playerstats['active_time'] = 172800 * $_SESSION['round_speed'];
		$db_query = "
			SELECT COUNT(*) AS 'recentplayers' 
			FROM `players` 
			WHERE 
				`round_id` = '" . $_SESSION['round_id'] . "' AND 
				`lastactive` > '" . (microfloat() - $playerstats['active_time']) . "' AND 
				`npc` = 0 AND 
				`user_id` > 0";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$playerstats['recentplayers'] = $db_row['recentplayers'];
		
		// Total Players in round
		$db_query = "
			SELECT COUNT(*) AS 'totalplayers' 
			FROM `players` 
			WHERE 
				`round_id` = '" . $_SESSION['round_id'] . "' AND 
				`npc` = 0 AND 
				`user_id` > 0";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$playerstats['totalplayers'] = $db_row['totalplayers'];
		
		$playerstats['active_time'] = format_time(timeparser($playerstats['active_time']));
		
		return $playerstats;
	}
?>