<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'mail_list', 
		'mail_read', 
		'mail_compose', 
		'mail_send', 
		'mail_delete'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$fn();
	
	/*
		mailbox_id
		user_id
		total
		unread
		name
	*/
	
	function mail_list()
	{
		global $smarty;
		
		$mail = array();
		
		$db_query = "
			SELECT 
				`mail`.`mail_id`, 
				`mail`.`from_player_id`, 
				`players`.`name` as 'from_player_name', 
				`mail`.`subject`, 
				`mail`.`time`, 
				`mail`.`status` 
			FROM `mail` 
			LEFT JOIN `players` ON `players`.`player_id` = `mail`.`from_player_id`
			WHERE 
				`mail`.`round_id` = '" . $_SESSION['round_id'] . "' AND 
				`mail`.`to_player_id` = '" . $_SESSION['player_id'] . "' AND 
				`mail`.`status` != '" . MAILSTATUS_DELETED . "' 
			ORDER BY `time` DESC
			LIMIT 30";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['time'] = format_timestamp($db_row['time'] + (3600 * $_SESSION['preferences']['timezone']));
			$db_row['subject'] = htmlentities($db_row['subject']);
			
			if ($db_row['from_player_id'] == 0) $db_row['from_player_name'] = 'Administration';
			
			$mail[$db_row['mail_id']] = $db_row;
		}
		
		$smarty->assign('mail', $mail);
		$smarty->display('mail_list.tpl');
		exit;
	}
	
	function mail_read()
	{
		global $smarty;
		
		$mail_id = (int)$_REQUEST['mail_id'];
		
		$db_query = "
			SELECT 
				`players`.`name` as 'from', 
				`mail`.`mail_id`, 
				`mail`.`from_player_id`, 
				`mail`.`body`, 
				`mail`.`subject`, 
				`mail`.`time`, 
				`mail`.`status` 
			FROM `mail` 
			LEFT JOIN `players` ON `players`.`player_id` = `mail`.`from_player_id` 
			WHERE 
				`mail`.`round_id` = '" . $_SESSION['round_id'] . "' AND 
				`mail`.`mail_id` = '" . $mail_id . "' AND 
				`mail`.`to_player_id` = '" . $_SESSION['player_id'] . "' AND 
				`mail`.`status` != '" . MAILSTATUS_DELETED . "'
			ORDER BY `time` ASC 
			LIMIT 30";
		$db_result = mysql_query($db_query);
		if (mysql_num_rows($db_result) == 0)
		{
			$status[] = 'That mail does not exist or you do not have permission to view it.';
			$smarty->append('status', $status);
			mail_list();
			exit;
		}
		$mail = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$mail['time'] = format_timestamp($mail['time'] + (3600 * $_SESSION['preferences']['timezone']));
		$mail['subject'] = htmlentities($mail['subject']);
		$mail['body'] = nl2br(htmlentities($mail['body']));
		
		if ($mail['from_player_id'] == 0) $mail['from'] = 'Administration';
		
		$bbtags = array(
			'b' => array('Name'=>'b','HtmlBegin'=>'<span style="font-weight: bold;">','HtmlEnd'=>'</span>'), 
			'i' => array('Name'=>'i','HtmlBegin'=>'<span style="font-style: italic;">','HtmlEnd'=>'</span>'), 
			'u' => array('Name'=>'u','HtmlBegin'=>'<span style="text-decoration: underline;">','HtmlEnd'=>'</span>'), 
			's' => array('Name'=>'s','HtmlBegin'=>'<span style="text-decoration: line-through;">','HtmlEnd'=>'</span>'), 
			'quote' => array('Name'=>'quote','HasParam'=>true,'HtmlBegin'=>'<b>Quote %%P%%:</b><div class="mailquote">','HtmlEnd'=>'</div>')
		);
		
		require_once(dirname(__FILE__) . '/includes/bbcode.php');
		$bbcode = new bbcode();
		$bbcode->add_tag($bbtags['b']);
		$bbcode->add_tag($bbtags['i']);
		$bbcode->add_tag($bbtags['u']);
		$bbcode->add_tag($bbtags['s']);
		$bbcode->add_tag($bbtags['quote']);
		
		$mail['body'] = $bbcode->parse_bbcode($mail['body']);
		
		if ($mail['status'] == 1)
		{
			$db_query = "UPDATE `mail` SET `status` = '2' WHERE `mail_id` = '" . $mail_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
		}
		
		$smarty->assign('mail', $mail);
		$smarty->display('mail_read.tpl');
	}
	
	function mail_compose()
	{
		global $smarty;
		
		$mail = array();
		
		if (!empty($_REQUEST['mail_id']))
		{
			$mail_id = (int)$_REQUEST['mail_id'];
			$db_query = "
				SELECT 
					`players`.`name` as 'to', 
					`mail`.`subject`, 
					`mail`.`body` 
				FROM `mail` 
				LEFT JOIN `players` ON `players`.`player_id` = `mail`.`from_player_id` 
				WHERE 
					`mail`.`mail_id` = '" . $mail_id . "' AND 
					`mail`.`to_player_id` = '" . $_SESSION['player_id'] . "' AND 
					`mail`.`status` != '" . MAILSTATUS_DELETED . "'";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) == 0)
			{
				$status[] = 'That mail does not exist or you do not have permission to view it.';
				$smarty->append('status', $status);
				mail_list();
				exit;
			}
			
			$mail = mysql_fetch_array($db_result, MYSQL_ASSOC);
			if (substr($mail['subject'], 0, 3) != 'Re:') $mail['subject'] = 'Re: ' . $mail['subject'];
			$mail['body'] = "\n\n\n" . '[quote=' . $mail['to'] . ']' . $mail['body'] . '[/quote]';
		}
		
		$parts = array('to', 'subject', 'body');
		foreach ($parts as $part)
		{
			if (!empty($_REQUEST[$part]))
			{
				$mail[$part] = $_REQUEST[$part];
			}
		}
		
		$smarty->assign('mail', $mail);
		$smarty->display('mail_compose.tpl');
	}
	
	function mail_send()
	{
		global $smarty;
		
		$mail['to'] = $_POST['to'];
		$mail['subject'] = $_POST['subject'];
		$mail['body'] = $_POST['body'];
		
		if ($mail['to'] == 'Administration')
		{
			$db_query = "
				SELECT `player_id` 
				FROM `players` 
				WHERE 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`user_id` = '1'";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) == 0)
			{
				$to = 0;
			}
			else
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$to = $db_row['player_id'];
			}
		}
		else
		{
			$db_query = "
				SELECT `player_id` 
				FROM `players` 
				WHERE 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`name` = '" . mysql_real_escape_string($mail['to']) . "'";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) == 0)
			{
				$status[] = 'Incorrect player name. Please check it and try again.';
			}
			else
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$to = $db_row['player_id'];
			}
		}
		
		if (empty($mail['subject']))
		{
			$status[] = 'Enter a subject and try again.';
		}
		
		if (empty($mail['body']))
		{
			$status[] = 'Cannot send a blank message.';
		}
		
		if (!empty($status))
		{
			$smarty->append('status', $status);
			$smarty->assign('mail', $mail);
			$smarty->display('mail_compose.tpl');
			exit;
		}
		
		$db_query = "INSERT INTO `mail` (`round_id`, `to_player_id`, `from_player_id`, `time`, `subject`, `body`) VALUES (
			'" . $_SESSION['round_id'] . "', 
			'" . $to . "', 
			'" . $_SESSION['player_id'] . "', 
			'" . microfloat() . "', 
			'" . mysql_real_escape_string($mail['subject']) . "', 
			'" . mysql_real_escape_string($mail['body']) . "'
		)";
		$db_result = mysql_query($db_query);
		
		$db_query = "UPDATE `players` SET `mail` = '1' WHERE `player_id` = '" . $to . "'";
		$db_result = mysql_query($db_query);
		
		$status[] = 'Message sent successfully.';
		$smarty->append('status', $status);
		mail_list();
		exit;
	}
	
	function mail_delete()
	{
		global $smarty;
		
		if (!empty($_REQUEST['mail_id']))
		{
			if (is_array($_REQUEST['mail_id']))
			{
				foreach ($_REQUEST['mail_id'] as $mail_id)
				{
					$mail[] = abs((int)$mail_id);
				}
			}
			else
			{
				$mail[] = abs((int)$_REQUEST['mail_id']);
			}
		}
		else
		{
			$status[] = 'No mail selected for deletion.';
			$smarty->append('status', $status);
			mail_list();
			exit;
		}
		
		$db_query = "
			UPDATE `mail`
			SET `status` = '" . MAILSTATUS_DELETED . "' 
			WHERE 
				`mail_id` IN ('" . implode("', '", $mail) . "') AND 
				`to_player_id` = '" . $_SESSION['player_id'] . "'";
		$db_result = mysql_query($db_query);
		
		$status[] = 'Successfully deleted selected message(s).';
		$smarty->append('status', $status);
		mail_list();
		exit;
	}
?>