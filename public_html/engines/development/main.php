<?php
	define('IK_AUTHORIZED', true);
	include(dirname(__FILE__) . '/includes/init.php');
	$_REQUEST['fn'] = 'news';
	if (!empty($_REQUEST['fn']) && $_REQUEST['fn'] == 'news')
	{
		news();
	}
	else
	{
		$smarty->display('main.tpl');
	}
	
	function news()
	{
		global $smarty;
		
		// Get the seven latest news posts and show them on the main page.
		// Forgot about BBCode so this won't work without a lot of modifications.
		// Trimmed down to not get post text.
		mysql_select_db('zimzatik2');
		$db_query = "
			SELECT 
				t.`topic_id` as 'topic', 
				t.`topic_replies` as 'replies', 
				t.`topic_title` as 'title', 
				(p.`post_time` + (c.`config_value` * 3600)) as 'time', 
				u.`user_id`, 
				u.`username`, 
				p.`post_id` 
			FROM 
				`phpbb_topics` t, 
				`phpbb_users` u, 
				`phpbb_config` c, 
				`phpbb_posts` p 
			WHERE 
				c.`config_name` = 'board_timezone' AND 
				t.`forum_id` = 1 AND 
				p.`post_id` = t.`topic_last_post_id` AND 
				u.`user_id` = p.`poster_id`
			ORDER BY p.`post_time` DESC 
			LIMIT 7";
		$db_result = mysql_query($db_query);
		if ($db_result)
		{
			$news = array();
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$news[] = $db_row;
			}
			$smarty->assign('news', $news);		
		}
		mysql_select_db(DATABASE);
		
		$smarty->display('main_news.tpl');
	}
?>
