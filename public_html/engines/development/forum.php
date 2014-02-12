<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'forum_topics', 
			'forum_messages', 
			'forum_mark', 
			'forum_post', 
			'forum_post_process');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 6);
		
		$forum = new Forum($data, $smarty);
		$forum->$fn();
	}
	
	class Forum
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $mark_display = true;
		
		function Forum(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
		}
		
		function topics()
		{
			$skip = abs((int)request_variable('skip', NULL, 0));
			$this->smarty->assign('skip', $skip);
			
			$db_query = "SELECT COUNT(*) AS 'count' FROM `forum_topics` WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$this->smarty->assign('count', ceil($db_row['count'] / 15));
			
			$this->sql->select(array(
				array('forum_topics', 'forum_topic_id'), 
				array('forum_topics', 'replies'), 
				array('forum_topics', 'subject'), 
				array('forum_topics', 'time_lastpost', 'lastpost'), 
				array('players', 'player_id'), 
				array('players', 'name', 'name_lastposter')));
			$this->sql->where(array(
				array('forum_topics', 'kingdom_id', $_SESSION['kingdom_id']), 
				array('players', 'player_id', array('forum_topics', 'lastposter_id'))));
			$this->sql->orderby(array('forum_topics', 'time_lastpost', 'desc'));
			$this->sql->limit(array(15, $skip * 15));
			$db_result = $this->sql->execute();
			
			if (!$db_result)
				error(__FILE__, __LINE__, 'DB_DATA', 'Invalid database query.');
			
			if (mysql_num_rows($db_result) > 0)
			{
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['lastpost'] = format_timestamp($db_row['lastpost']);
					$db_row['subject'] = htmlentities($db_row['subject']);
					$db_row['pages'] = ceil(($db_row['replies'] + 1) / 15);
					
					$topics[] = $db_row;
				}
				$this->smarty->assign('topics', $topics);
			}
			elseif ($skip > 0)
			{
				$_REQUEST['skip'] = 0;
				$this->topics();
				exit;
			}
			
			$this->smarty->display('forum_topics.tpl');
		}
		
		function messages()
		{
			if (empty($_REQUEST['forum_topic_id']))
			{
				$this->topics();
				exit;
			}
			
			$forum_topic_id = abs((int)request_variable('forum_topic_id', NULL, 0));
			$skip = abs((int)request_variable('skip', NULL, 0));
			$this->smarty->assign('skip', $skip);
			
			$this->sql->select(array(
				array('forum_topics', 'forum_topic_id'), 
				array('forum_topics', 'replies'), 
				array('forum_topics', 'subject')));
			$this->sql->where(array(
				array('forum_topics', 'round_id', $_SESSION['round_id']), 
				array('forum_topics', 'kingdom_id', $_SESSION['kingdom_id']), 
				array('forum_topics', 'forum_topic_id', $forum_topic_id)));
			$this->sql->limit(1);
			$db_result = $this->sql->execute();
			if (!$db_result || mysql_num_rows($db_result) == 0)
			{
				$this->smarty->append('status', 'Topic does not exist or you do not have access to it');
				$this->topics();
				exit;
			}
			
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$this->smarty->assign('subject', htmlentities($db_row['subject']));
			$this->smarty->assign('forum_topic_id', $db_row['forum_topic_id']);
			$this->smarty->assign('count', ceil(($db_row['replies'] + 1) / 15));
			
			require_once(dirname(__FILE__) . '/includes/bbcode.php');
			$bbcode = new bbcode();
			
			$bbtags = array(
				'b' => array('Name' => 'b', 'HtmlBegin' => '<span style="font-weight: bold;">', 'HtmlEnd' => '</span>'), 
				'i' => array('Name' => 'i', 'HtmlBegin' => '<span style="font-style: italic;">', 'HtmlEnd' => '</span>'), 
				'u' => array('Name' => 'u', 'HtmlBegin' => '<span style="text-decoration: underline;">', 'HtmlEnd' => '</span>'), 
				's' => array('Name' => 's', 'HtmlBegin' => '<span style="text-decoration: line-through;">', 'HtmlEnd' => '</span>'), 
				'quote' => array('Name' => 'quote', 'HasParam' => true, 'HtmlBegin' => '<b>Quote %%P%%:</b><div class="mailquote">', 'HtmlEnd' => '</div>'), 
				'code' => array('Name' => 'code', 'HtmlBegin' => '<div class="bbcode_code">', 'HtmlEnd' => '</div>'));
			
			$bbcode->add_tag($bbtags['b']);
			$bbcode->add_tag($bbtags['i']);
			$bbcode->add_tag($bbtags['u']);
			$bbcode->add_tag($bbtags['s']);
			$bbcode->add_tag($bbtags['quote']);
			$bbcode->add_tag($bbtags['code']);
			
			$db_query = "SELECT COUNT(*) AS 'count' FROM `players` WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `rank` > '0'";
			$db_result = mysql_query($db_query);
			$player = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$this->smarty->assign('player_count', $player['count']);
			
			$this->sql->select(array(
				array('players', 'player_id'), 
				array('players', 'name', 'name_poster'), 
				array('forum_messages', 'forum_message_id'), 
				array('forum_messages', 'posttime'), 
				array('forum_messages', 'message'), 
				array('forum_messages', 'marked')));
			$this->sql->leftjoin(array('players', 'player_id', array('forum_messages', 'poster_id')));
			$this->sql->where(array(
				array('forum_messages', 'kingdom_id', $_SESSION['kingdom_id']), 
				array('forum_messages', 'forum_topic_id', $forum_topic_id)));
			$this->sql->orderby(array('forum_messages', 'posttime', 'asc'));
			$this->sql->limit(array(15, $skip * 15));
			$db_result = $this->sql->execute();
			if (mysql_num_rows($db_result) == 0 && $skip > 0)
			{
				$_REQUEST['skip'] = 0;
				$this->messages();
			}
			
			
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$db_row['posttime'] = format_timestamp($db_row['posttime']);
				$db_row['message'] = nl2br(htmlentities($db_row['message']));
				$db_row['message'] = $bbcode->parse_bbcode($db_row['message']);
				$db_row['marked'] = unserialize($db_row['marked']);
				
				if (!empty($db_row['marked'][$_SESSION['player_id']]))
				{
					$db_row['marked'] = count($db_row['marked']);
					
					if ($db_row['marked'] >= $player['count'] * (2 / 3))
					{
						$_REQUEST['forum_message_id'] = $db_row['forum_message_id'];
						$this->mark_display = true;
						$this->mark();
					}
				}
				else
				{
					$db_row['marked'] = '';
				}
				
				$messages[] = $db_row;
			}
			
			$this->smarty->assign('messages', $messages);
			$this->smarty->display('forum_messages.tpl');
			exit;
		}
		
		function mark()
		{
			$forum_message_id = abs((int)request_variable('forum_message_id', NULL, 0));
			
			$this->sql->where(array(
				array('forum_messages', 'forum_message_id', $forum_message_id), 
				array('forum_messages', 'kingdom_id' , $_SESSION['kingdom_id'])));
			$db_result = $this->sql->execute();
			$message = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$message['marked'] = unserialize($message['marked']);
			
			if ($this->mark_display)
			{
				if (!empty($message['marked'][$_SESSION['player_id']]))
				{
					$this->messages();
					exit;
				}
				
				$message['marked'][$_SESSION['player_id']] = true;
			}
			
			$db_query = "SELECT COUNT(*) AS 'count' FROM `players` WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `rank` > '0'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			if (count($message['marked']) >= $db_row['count'] * (2 / 3))
			{
				$db_query = "DELETE FROM `forum_messages` WHERE `forum_message_id` = '" . $forum_message_id . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				
				$db_query = "SELECT COUNT(*) AS 'count' FROM `forum_messages` WHERE `forum_topic_id` = '" . $message['forum_topic_id'] . "'";
				$db_result = mysql_query($db_query);
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				if ($db_row['count'] == 0)
				{
					$db_query = "DELETE FROM `forum_topics` WHERE `forum_topic_id` = '" . $message['forum_topic_id'] . "' LIMIT 1";
					$db_result = mysql_query($db_query);
					$message_deleted = true;
				}
				else
				{
					$db_query = "UPDATE `forum_topics` SET `replies` = `replies` - 1 WHERE `forum_topic_id` = '" . $message['forum_topic_id'] . "' LIMIT 1";
					$db_result = mysql_query($db_query);
				}
			}
			else
			{
				$db_query = "UPDATE `forum_messages` SET `marked` = '" . serialize($message['marked']) . "' WHERE `forum_message_id` = '" . $forum_message_id . "' LIMIT 1";
				$db_result = mysql_query($db_query);
			}
			
			if ($this->mark_display)
			{
				if (empty($message_deleted))
				{
					$_REQUEST['forum_topic_id'] = $message['forum_topic_id'];
					$this->messages();
				}
				else
				{
					$this->topics();
				}
			}
		}
		
		function post()
		{
			$this->smarty->assign('forum_topic_id', abs((int)request_variable('forum_topic_id')));
			$this->smarty->display('forum_post.tpl');
		}
		
		function post_process()
		{
			$subject = trim(request_variable('subject', NULL, ''));
			$message = trim(request_variable('message', NULL, ''));
			$forum_topic_id = abs((int)request_variable('forum_topic_id', NULL, 0));
			
			if (empty($_REQUEST['message']))
			{
				$this->smarty->append('status', 'No message entered.');
				if ($forum_topic_id == 0)
				{
					$this->smarty->assign('subject', htmlentities($subject));
					$this->topics();
				}
				else
				{
					$this->messages();
				}
				exit;
			}
			
			if ($forum_topic_id == 0)
			{
				if (strlen($subject) < 3 || strlen($subject) > 64)
				{
					$this->smarty->assign('message', htmlentities($message));
					$this->smarty->assign('subject', htmlentities($subject));
					$this->smarty->append('status', 'No subject entered.');
					$this->topics();
					exit;
				}
				else
				{
					$topicinsert = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'lastposter_id' => $_SESSION['player_id'], 
						'subject' => $subject, 
						'time_lastpost' => microfloat());
					$db_query = $this->sql->insert('forum_topics', $topicinsert);
					$db_result = mysql_query($db_query);
					$forum_topic_id = mysql_insert_id();
				}
			}
			else
			{
				$db_query = "SELECT * FROM `forum_topics` WHERE `forum_topic_id` = '" . $forum_topic_id . "' AND `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				if (!$db_result || mysql_num_rows($db_result) == 0)
				{
					error(__FILE__, __LINE__, 'DB_DATA', 'Could not select valid forum_topic_id');
				}
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				$db_query = "
					UPDATE `forum_topics` 
					SET 
						`lastposter_id` = '" . $_SESSION['player_id'] . "', 
						`replies` = `replies` + '1', 
						`time_lastpost` = '" . microfloat() . "' 
					WHERE `forum_topic_id` = '" . $forum_topic_id . "' 
					LIMIT 1";
				$db_result = mysql_query($db_query);
			}
			
			$messageinsert = array(
				'forum_topic_id' => $forum_topic_id, 
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'poster_id' => $_SESSION['player_id'], 
				'message' => $message, 
				'posttime' => microfloat(), 
				'marked' => array());
			$db_result = $this->sql->execute('forum_messages', $messageinsert);
			
			$db_query = "
				UPDATE `players` 
				SET `forum` = '1' 
				WHERE 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
					`player_id` <> '" . $_SESSION['player_id'] . "' AND 
					`rank` > '0'";
			$db_result = mysql_query($db_query);
			
			$this->smarty->append('status', 'Message posted.');
			$this->messages();
			exit;
		}
	}
?>