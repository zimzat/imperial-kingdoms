<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'news', 
		'info', 
		'scores');
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	if ($fn == 'news')
	{
		chdir('/home/.matylda/zimzat/ik-gh/shared_public_html/forum/');
		require_once('./global.php');
	}

	$fn();
	index();

	chdir('/home/zimzatik/shared_public_html/forum/');
	require_once('./global.php');

	// ###############################################
	// Get any information needed for the front page
	function index()
	{
		global $smarty;
		
		$rounds = array();
		
		// Get the last three previous rounds from the database
		$db_query = "SELECT `round_id`, `name` FROM `rounds` WHERE `stoptime` <= '" . microfloat() . "' AND `public` >= '1' ORDER BY `stoptime` DESC LIMIT 5";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$rounds['previous'][] = $db_row;
		}
		
		// Get all current and future rounds from the database
		$db_query = "
			SELECT 
				`round_id`, 
				`name`, 
				`starttime`, 
				`stoptime`, 
				`pause_time`, 
				`pause_message` 
			FROM `rounds` 
			WHERE 
				`stoptime` > '" . microfloat() . "' AND 
				`public` >= '1' 
			ORDER BY `starttime` ASC";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$temp = array();
			// If the starttime has already passed...
			if ($db_row['starttime'] < time())
			{
				if ($db_row['pause_time'] > 0)
				{
					$db_row['stoptime'] += time() - $db_row['pause_time'];
					$db_row['starttime'] += time() - $db_row['pause_time'];
				}
				
				// Count the seconds until stop
				$seconds = $db_row['stoptime'] - time();
				$started = true;
			}
			else
			{
				// Otherwise count the time until start
				$seconds = $db_row['starttime'] - time();
				$started = false;
			}
			
			$stopseconds = $db_row['stoptime'] - $db_row['starttime'];
			
			$db_row['starttime'] = format_time($seconds, 3);
			$db_row['stoptime'] = format_time($stopseconds, 3);
			
			if ($started)
			{
				$rounds['current'][] = $db_row;
			}
			else
			{
				$rounds['future'][] = $db_row;
			}
		}
		$smarty->assign('rounds', $rounds);
		
		// Display the page
		$smarty->display('index.tpl');
	}
	
	
	function news()
	{
		global $smarty, $vbulletin;
		
		$smarty->assign('content', 'news');

		$query = "
			SELECT
				t.threadid AS topic,
				t.replycount AS replies,
				t.title AS title,
				t.dateline AS time,
				t.postusername AS username,
				p.pagetext AS post_text
			FROM imperialkingdoms_misc.vb_thread AS t
				JOIN imperialkingdoms_misc.vb_post AS p ON (t.firstpostid = p.postid)
			WHERE t.forumid = 9
			ORDER BY t.dateline DESC
			LIMIT 3
		";

		$news = array();
		$result = $vbulletin->db->query_read($query);
		while ($db_row = $vbulletin->db->fetch_array($result))
		{
			$message = $db_row['post_text'];

			// Remove HTML
			$message = preg_replace('#(<)([\/]?.*?)(>)#is', "&lt;\\2&gt;", $message);
			$message = nl2br($message);

			$db_row['post_text'] = $message;

			$news[] = $db_row;
		}
		$smarty->assign('news', $news);

		mysql_select_db('imperialkingdoms');
	}
	
	
	function info()
	{
		global $smarty;
		
		if (empty($_REQUEST['round_id']))
		{
			error(__FILE__, __LINE__, 'INVALID_ID', 'Invalid Round ID');
		}
		
		$smarty->assign('content', 'info');
		
		$round_id = abs((int)$_REQUEST['round_id']);
		
		$db_query = "
			SELECT 
				`round_id`, 
				`round_engine`, 
				`name`, 
				`description`, 
				`starttime`, 
				`stoptime`, 
				`starsystems`, 
				`planets`, 
				`resistance`, 
				`speed`, 
				`resourcetick`, 
				`combattick` 
			FROM `rounds` 
			WHERE `round_id` = '" . $round_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$round = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
				$round['speed'] /= 1000;
		
		// Dynamic attack limit based on elapsed time of current round
		$end_time = 3456000 * $round['speed'];
		$current_time = microfloat() - $round['starttime'];
		$attack_limit = 1 - ($current_time / $end_time);
		if ($attack_limit < 0) $attack_limit = 0;
		
		$round['attack_limit'] = round($attack_limit * 100, 2);
		
		$round['description'] = nl2br($round['description']);
		$round['starttime'] = format_timestamp($round['starttime']);
		$round['stoptime'] = format_timestamp($round['stoptime']);
		$round['resistance'] = format_number($round['resistance']);
		$round['resourcetick'] = format_time(timeparser($round['resourcetick'] / 1000));
		$round['combattick'] = format_time(timeparser($round['combattick'] / 1000));
		
		$smarty->assign('round', $round);
	}
	
	
	function scores()
	{
		global $smarty;
		
		if (empty($_REQUEST['round_id']))
		{
			error(__FILE__, __LINE__, 'INVALID_ID', 'Invalid Round ID');
		}
		
		$smarty->assign('content', 'scores');
		
		$round_id = abs((int)$_REQUEST['round_id']);
		
		if (isset($_GET['sort_by']) && (
			$_GET['sort_by'] == 'players' || 
			$_GET['sort_by'] == 'kingdoms' || 
			$_GET['sort_by'] == 'resource' || 
			$_GET['sort_by'] == 'resource_peak' || 
			$_GET['sort_by'] == 'military' || 
			$_GET['sort_by'] == 'military_peak' || 
			$_GET['sort_by'] == 'total' || 
			$_GET['sort_by'] == 'total_peak'))
		{
			$sort_by = $_GET['sort_by'];
		}
		else
		{
			$sort_by = 'total';
		}
		
		if (isset($_GET['sort_order']) && (
			$_GET['sort_order'] == 'ASC' || 
			$_GET['sort_order'] == 'DESC'))
		{
			$sort_order = $_GET['sort_order'];
		}
		else
		{
			$sort_order = 'DESC';
		}
		
		if (isset($_GET['sort_show']) && (
			$_GET['sort_show'] == 'military' || 
			$_GET['sort_show'] == 'resource'))
		{
			$sort_show = $_GET['sort_show'];
		}
		else
		{
			$sort_show = 'military';
		}
		
		$db_query = "SELECT * FROM `scores` WHERE `round_id` = '" . $round_id . "' ORDER BY '" . $sort_by . "' " . $sort_order . " LIMIT 100";
		$db_result = mysql_query($db_query);
		if ($db_result)
		{
			$scores = array();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['total'] = $db_row['resource'] + $db_row['military'];
				$scores[] = $db_row;
			}
			$smarty->assign('scores', $scores);
		}
		
		$smarty->assign('sort_by', $sort_by);
		$smarty->assign('sort_order', $sort_order);
		$smarty->assign('sort_show', $sort_show);
	}
?>
