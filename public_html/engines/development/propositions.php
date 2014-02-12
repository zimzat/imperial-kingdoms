<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'propositions_types', 
			'propositions_info', 
			'propositions_vote', 
			'propositions_description', 
			'propositions_avatar', 
			'propositions_promote', 
			'propositions_demote', 
			'propositions_execute', 
//			 'propositions_ally', 
//			 'propositions_merge', 
			'propositions_war', 
			'propositions_peace'
		);
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 13);
		
		$propositions = new Propositions;
		$propositions->$fn();
	}
	
	class Propositions
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $proposition_id;
		var $proposition;
		var $statement;
		
		function Propositions()
		{
			global $data, $smarty;
			
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$player = $this->data->player($_SESSION['player_id']);
			if ($player['rank'] < RANK_SENATOR)
			{
				$this->access_error();
			}
			
			if (!empty($_REQUEST['proposition_id']))
			{
				$this->proposition_id = abs((int)$_REQUEST['proposition_id']);
				$this->proposition = &$this->data->proposition($this->proposition_id);
				
				if (empty($this->proposition) || 
					$this->proposition['kingdom_id'] != $_SESSION['kingdom_id'])
				{
					$this->access_error();
				}
			}
			else
			{
				$this->statement = request_variable('statement', NULL, '');
				$this->proposition_id = 0;
			}
		}
		
		function access_error()
		{
			$this->smarty->assign('status', 'You do not have permission to access that.');
			$this->smarty->display('error.tpl');
			exit;
		}
		
		function types()
		{
			$types = array(
				'propositions_description' => 'Change Kingdom Description', 
				'propositions_avatar' => 'Change Kingdom Avatar', 
				'propositions_promote' => 'Promote Prisoner', 
				'propositions_demote' => 'Demote Member', 
				'propositions_execute' => 'Execute Prisoner', 
//				 'propositions_ally' => 'Ally with Kingdom', 
//				 'propositions_merge' => 'Merge with Kingdom', 
				'propositions_war' => 'Declare War on Kingdom', 
				'propositions_peace' => 'Declare Peace with Kingdom'
			);
			
			$this->smarty->assign('types', $types);
			$this->smarty->display('propositions_types.tpl');
		}
		
		function flagcheck($valid_flags = array(), $flag = '')
		{
			if (!empty($flag))
			{
				return $flag;
			}
			
			if (!empty($valid_flags) && !empty($_REQUEST['action']) && in_array($_REQUEST['action'], $valid_flags))
			{
				return $_REQUEST['action'];
			}
			
			error(__FILE__, __LINE__, 'INVALID_ACTION', 'Invalid or unspecified action for proposition');
		}
		
		function description($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$kingdom = $this->data->kingdom($_SESSION['kingdom_id']);
					
					$description = nl2br(htmlspecialchars($kingdom['description']));
					
					$this->smarty->assign('description', $description);
					$this->smarty->display('propositions_description.tpl');
					break;
	// details_submit
				case 'details_submit':
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					$description = trim(substr(trim($_REQUEST['description']), 0, 1024));
					
					$proposition_insert = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Change Kingdom Description', 
						'statement' => $statement, 
						'storage' => $description, 
						'type' => PROPOSITION_DESCRIPTION, 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_query = $this->sql->execute('propositions', $proposition_insert);
					$this->proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $this->proposition_id);
					break;
	// for
				case 'for':
					$kingdom = &$this->data->kingdom($this->proposition['kingdom_id']);
					
					$kingdom['description'] = $this->proposition['storage'];
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function avatar($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit', 'avatar');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->smarty->display('propositions_avatar.tpl');
					break;
	// details_submit
				case 'details_submit':
					if (empty($_FILES['avatar']) || 
						$_FILES['avatar']['size'] == 0 || 
						$_FILES['avatar']['size'] > 40960 || 
						file_exists($_FILES['avatar']['tmp_name']) == false || 
						filesize($_FILES['avatar']['tmp_name']) == 0)
					{
						error(__FILE__, __LINE__, 'INVALID_FILE', 'Invalid file uploaded.');
					}
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$valid_extensions = array('.jpg', '.gif', '.png', '.jpeg');
					$extension = strtolower(substr(trim($_FILES['avatar']['name']), strrpos(trim($_FILES['avatar']['name']), '.')));
					if (!in_array($extension, $valid_extensions))
					{
						error(__FILE__, __LINE__, 'INVALID_EXTENSION', 'Invalid file extension for avatar');
					}
					
					$file = array(
						'size' => $_FILES['avatar']['size'], 
						'type' => $_FILES['avatar']['type'], 
						'extension' => $extension, 
						'file' => file_get_contents($_FILES['avatar']['tmp_name']));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Change Kingdom Avatar', 
						'statement' => $statement, 
						'storage' => serialize($file), 
						'type' => PROPOSITION_AVATAR, 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// avatar
				case 'avatar':
					$proposition = $this->data->proposition($proposition_id);
					
					$proposition['storage'] = unserialize($proposition['storage']);
					
					header('Content-length: ' . $db_row['storage']['size']);
					header('Content-type: ' . $db_row['storage']['type']);
					echo $proposition['storage']['file'];
					exit;
					break;
	// for
				case 'for':
					$proposition = $this->data->proposition($proposition_id);
					$kingdom = &$this->data->kingdom($proposition['kingdom_id']);
					
					$proposition['storage'] = unserialize($proposition['storage']);
					
					$images = array('0-0.gif', '0-0.png', '0-0.jpg');
					if (!in_array($kingdom['image'], $images))
						unlink($_SERVER['DOCUMENT_ROOT'] . '/images/avatars/kingdoms/' . $kingdom['image']);
					
					$kingdom['image'] = $proposition['round_id'] . '-' . $proposition['kingdom_id'] . $proposition['storage']['extension'];
					
					$handle = fopen($_SERVER['DOCUMENT_ROOT'] . '/images/avatars/kingdoms/' . $kingdom['image'], 'w');
					fwrite($handle, $proposition['storage']['file']);
					fclose($handle);
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function promote($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->sql->select(array(
						array('players', 'player_id'), 
						array('players', 'name')));
					$this->sql->where(array(
						array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
						array('players', 'rank', RANK_EMPEROR, '<')));
					$this->sql->orderby(array('players', 'player_id', 'ASC'));
					
					$db_query = $this->sql->generate();
					$db_result = mysql_query($db_query);
					
					$players = array();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$players[$db_row['player_id']] = $db_row['name'];
					}
					
					$this->smarty->assign('players', $players);
					$this->smarty->display('propositions_promote.tpl');
					break;
	// details_submit
				case 'details_submit':
					$player = &request_id('player', 'player_id');
					
					if ($player['rank'] >= RANK_EMPEROR || $player['kingdom_id'] != $_SESSION['kingdom_id'])
					{
						error(__FILE__, __LINE__, 'INVALID_PLAYER', 'Invalid player specified');
					}
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Promote ' . $player['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_PROMOTE, 
						'target_id' => $player['player_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$player = &$this->data->player($proposition['target_id']);
					
					if ($player['rank'] >= RANK_SENATOR)
					{
						$db_result = $this->sql->execute("SELECT COUNT(*) as 'count' FROM `players` WHERE `rank` = " . RANK_EMPEROR . " AND `kingdom_id` = '" . $player['kingdom_id'] . "'");
						$db_row = mysql_fetch_array($db_result);
						if ($db_row['count'] > 0) break;
					}
					
					if ($player['rank'] > RANK_EMPEROR)
					{
						$player['rank'] = RANK_EMPEROR;
					}
					else
					{
						$player['rank'] += RANK_DIFFERENCE;
					}
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function demote($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->sql->select(array(
						array('players', 'player_id'), 
						array('players', 'name')));
					$this->sql->where(array(
						array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
						array('players', 'rank', RANK_PRISONER, '>')));
					$this->sql->orderby(array('players', 'player_id', 'ASC'));
					
					$db_query = $this->sql->generate();
					$db_result = mysql_query($db_query);
					
					$players = array();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$players[$db_row['player_id']] = $db_row['name'];
					}
					
					$this->smarty->assign('players', $players);
					$this->smarty->display('propositions_demote.tpl');
					break;
	// details_submit
				case 'details_submit':
					$player = &request_id('player', 'player_id');
					
					if ($player['rank'] <= RANK_PRISONER || $player['kingdom_id'] != $_SESSION['kingdom_id'])
					{
						error(__FILE__, __LINE__, 'INVALID_PLAYER', 'Invalid player specified');
					}
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Demote ' . $player['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_DEMOTE, 
						'target_id' => $player['player_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$player = &$this->data->player($proposition['target_id']);
					
					if ($player['rank'] < RANK_GOVERNOR)
					{
						$player['rank'] = RANK_PRISONER;
					}
					else
					{
						$player['rank'] -= RANK_DIFFERENCE;
					}
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function execute($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->sql->select(array(
						array('players', 'player_id'), 
						array('players', 'name')));
					$this->sql->where(array(
						array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
						array('players', 'rank', 0)));
					$this->sql->orderby(array('players', 'player_id', 'ASC'));
					
					$db_query = $this->sql->generate();
					$db_result = mysql_query($db_query);
					
					$players = array();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$players[$db_row['player_id']] = $db_row['name'];
					}
					
					$this->smarty->assign('players', $players);
					$this->smarty->display('propositions_execute.tpl');
					break;
	// details_submit
				case 'details_submit':
					$player = &request_id('player', 'player_id');
					
					if ($player['rank'] != 0 || $player['kingdom_id'] != $_SESSION['kingdom_id'])
					{
						error(__FILE__, __LINE__, 'INVALID_PLAYER', 'Invalid player specified');
					}
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Execute ' . $player['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_EXECUTE, 
						'target_id' => $player['player_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$kingdom = &$this->data->kingdom($proposition['kingdom_id']);
					$player = &$this->data->player($proposition['player_id']);
					$target = &$this->data->player($proposition['target_id']);
					
					$target['kingdom_id'] = 0;
					$target['user_id'] = 0;
					$target['score'] = 0;
					unset($kingdom['members'][$target['player_id']]);
					
					$news = array(
						'kingdom_id' => $proposition['kingdom_id'], 
						'kingdom_name' => $kingdom['name'], 
						
						'player_id' => $proposition['player_id'], 
						'player_name' => $player['name'], 
						
						'target_id' => $proposition['target_id'], 
						'target_name' => $target['name'], 
						
						'statement' => $proposition['statement'],
						'posted' => microfloat());
					add_news_entry(NEWS_EXECUTION, $news);
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function war($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->smarty->display('propositions_war.tpl');
					break;
	// details_submit
				case 'details_submit':
					$target = &request_id('kingdom', 'target_kingdom_id');
					$kingdom = &$this->data->kingdom($_SESSION['kingdom_id']);
					
					if (isset($kingdom['enemies'][$target['kingdom_id']]))
						error(__FILE__, __LINE__, 'INVALID_ID', 'Already at war with specified kingdom.');
					
					// Dynamic attack limit based on elapsed time of current round
					$round = &$this->data->round();
					$end_time = 5184000 * $_SESSION['round_speed'] * (2/3);
					$current_time = microfloat() - $round['starttime'];
					$attack_limit = 1 - ($current_time / $end_time);
					if ($attack_limit < 0) $attack_limit = 0;
					
					if ($attack_limit > 0 && 
						($kingdom['score'] * $attack_limit > $target['score'] || 
						$kingdom['score'] / $attack_limit < $target['score']) && 
						microfloat() - $target['last_active'] < 432000 * $_SESSION['round_speed'])
					{
						$players = $this->data->player(array_keys($target['members']));
						if (!empty($players))
						{
							$npc = true;
							foreach ($players as $player_id => $player)
							{
								if ($player['npc'] == 0)
								{
									$npc = false;
								}
							}
						}
						else
						{
							$npc = false;
						}
						
						if (!$npc)
						{
							error(__FILE__, __LINE__, 'ATTACK_LIMIT', 'The current attack limit is approximately ' . round($attack_limit * 100, 2) . '%. Target kingdom outside of current attack limit.');
						}
					}
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'War with ' . $target['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_WAR, 
						'target_id' => $target['kingdom_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$kingdom = &$this->data->kingdom($proposition['kingdom_id']);
					$target = &$this->data->kingdom($proposition['target_id']);
					$player = &$this->data->player($proposition['player_id']);
					
					$kingdom['enemies'][$proposition['target_id']] = true;
					if (isset($kingdom['allies'][$proposition['target_id']]))
						unset($kingdom['allies'][$proposition['target_id']]);
					
					$target['enemies'][$proposition['kingdom_id']] = true;
					if (isset($target['allies'][$proposition['kingdom_id']]))
						unset($target['allies'][$proposition['kingdom_id']]);
					
					$news = array(
						'kingdom_id' => $proposition['kingdom_id'], 
						'kingdom_name' => $kingdom['name'], 
						
						'player_id' => $proposition['player_id'], 
						'player_name' => $player['name'], 
						
						'target_id' => $target['kingdom_id'], 
						'target_name' => $target['name'], 
						
						'statement' => $proposition['statement'], 
						
						'posted' => microfloat());
					
					add_news_entry(NEWS_WAR, $news);
					
					$this->data->save();
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function ally($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->smarty->display('propositions_ally.tpl');
					break;
	// details_submit
				case 'details_submit':
					$target = &request_id('kingdom', 'target_kingdom_id');
					$kingdom = &$this->data->kingdom($_SESSION['kingdom_id']);
					
					if (isset($kingdom['allies'][$target['kingdom_id']]))
						error(__FILE__, __LINE__, 'INVALID_ID', 'Already allied with specified kingdom.');
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Ally with ' . $target['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_ALLY, 
						'target_id' => $target['kingdom_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$kingdom = &$this->data->kingdom($proposition['kingdom_id']);
					
					if (empty($proposition['storage']))
					{
						$proposition = array(
							'round_id' => $_SESSION['round_id'], 
							'kingdom_id' => $proposition['target_id'], 
							'player_id' => $_SESSION['player_id'], 
							'title' => 'Ally with ' . $kingdom['name'], 
							'statement' => $proposition['statement'], 
							'type' => PROPOSITION_ALLY, 
							'storage' => $proposition['kingdom_id'], 
							'target_id' => $proposition['kingdom_id'], 
							'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
						$db_result = $this->sql->execute('propositions', $proposition);
					}
					else
					{
						$target = &$this->data->kingdom($proposition['target_id']);
						$player = &$this->data->player($proposition['player_id']);
						
						$kingdom['allies'][$proposition['target_id']] = true;
						if (isset($kingdom['enemies'][$proposition['target_id']]))
							unset($kingdom['enemies'][$proposition['target_id']]);
						
						$target['allies'][$proposition['kingdom_id']] = true;
						if (isset($target['enemies'][$proposition['kingdom_id']]))
							unset($target['enemies'][$proposition['kingdom_id']]);
						
						$news = array(
							'kingdom_id' => $proposition['kingdom_id'], 
							'kingdom_name' => $kingdom['name'], 
							
							'player_id' => $proposition['player_id'], 
							'player_name' => $player['name'], 
							
							'target_id' => $target['kingdom_id'], 
							'target_name' => $target['name'], 
							
							'statement' => $proposition['statement'], 
							
							'posted' => microfloat());
						
						add_news_entry(NEWS_ALLY, $news);
					}
					
					$this->data->save();
					break;
	// against
				case 'against':
					// diplomatic_message(
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
		function peace($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					$this->smarty->display('propositions_peace.tpl');
					break;
	// details_submit
				case 'details_submit':
					$target = &request_id('kingdom', 'target_kingdom_id');
					$kingdom = &$this->data->kingdom($_SESSION['kingdom_id']);
					
					if (!isset($kingdom['allies'][$target['kingdom_id']]) && !isset($kingdom['enemies'][$target['kingdom_id']]))
						error(__FILE__, __LINE__, 'INVALID_ID', 'Already at peace with specified kingdom.');
					
					$statement = trim(substr(trim($_REQUEST['statement']), 0, 512));
					
					$proposition = array(
						'round_id' => $_SESSION['round_id'], 
						'kingdom_id' => $_SESSION['kingdom_id'], 
						'player_id' => $_SESSION['player_id'], 
						'title' => 'Peace with ' . $target['name'], 
						'statement' => $statement, 
						'type' => PROPOSITION_PEACE, 
						'target_id' => $target['kingdom_id'], 
						'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
					$db_result = $this->sql->execute('propositions', $proposition);
					
					$proposition_id = mysql_insert_id();
					
					$_SESSION['status'][] = 'Proposition added.';
					redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
					break;
	// for
				case 'for':
					$proposition = &$this->data->proposition($proposition_id);
					$kingdom = &$this->data->kingdom($proposition['kingdom_id']);
					$target = &$this->data->kingdom($proposition['target_id']);
					
					if (empty($proposition['storage']) && !isset($kingdom['allies'][$target['kingdom_id']]))
					{
						$proposition = array(
							'round_id' => $_SESSION['round_id'], 
							'kingdom_id' => $proposition['target_id'], 
							'player_id' => $_SESSION['player_id'], 
							'title' => 'Peace with ' . $kingdom['name'], 
							'statement' => $proposition['statement'], 
							'type' => PROPOSITION_PEACE, 
							'storage' => $proposition['kingdom_id'], 
							'target_id' => $proposition['kingdom_id'], 
							'expires' => microfloat() + ($_SESSION['round_speed'] * 86400));
						$db_result = $this->sql->execute('propositions', $proposition);
					}
					else
					{
						if (isset($kingdom['enemies'][$proposition['target_id']]))
							unset($kingdom['enemies'][$proposition['target_id']]);
						if (isset($kingdom['allies'][$proposition['target_id']]))
							unset($kingdom['allies'][$proposition['target_id']]);
						
						if (isset($target['enemies'][$proposition['kingdom_id']]))
							unset($target['enemies'][$proposition['kingdom_id']]);
						if (isset($target['allies'][$proposition['kingdom_id']]))
							unset($target['allies'][$proposition['kingdom_id']]);
							
						$news = array(
							'kingdom_id' => $proposition['kingdom_id'], 
							'kingdom_name' => $kingdom['name'], 
							
							'player_id' => $proposition['player_id'], 
							'player_name' => $player['name'], 
							
							'target_id' => $target['kingdom_id'], 
							'target_name' => $target['name'], 
							
							'statement' => $proposition['statement'], 
							
							'posted' => microfloat());
						
						add_news_entry(NEWS_PEACE, $news);
					}
					
					$this->data->save();
					break;
	// against
				case 'against':
					// diplomatic_message(
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
		
	/* Proposition Template
		function proposition_name($flag = '')
		{
			$proposition_id = $this->proposition_id;
			
			$valid_flags = array('details', 'details_submit');
			$flag = $this->flagcheck($valid_flags, $flag);
			
			switch ($flag)
			{
	// details
				case 'details':
					break;
	// details_submit
				case 'details_submit':
					break;
	// for
				case 'for':
					break;
	// against
				case 'against':
					break;
	// neutral
				case 'neutral':
					break;
				default:
					error(__FILE__, __LINE__, 'INVALID_FLAG', 'Invalid internal flag for proposition');
					break;
			}
		}
	*/
		
		function info()
		{
			$proposition_id = $this->proposition_id;
			
			$this->sql->where(array(
				array('propositions', 'proposition_id', $proposition_id), 
				array('propositions', 'round_id', $_SESSION['round_id']), 
				array('propositions', 'kingdom_id', $_SESSION['kingdom_id'])
			));
			$this->sql->limit(1);
			
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			if (!$db_result || mysql_num_rows($db_result) == 0)
			{
				error(__FILE__, __LINE__, 'ID_INVALID', 'Invalid proposition id');
			}
			
			$proposition = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if ($proposition['type'] == PROPOSITION_DESCRIPTION)
			{
				$proposition['storage'] = nl2br(htmlspecialchars($proposition['storage']));
			}
			elseif ($proposition['type'] == PROPOSITION_WAR)
			{
				$kingdom = &$this->data->kingdom($proposition['target_id']);
				$proposition['kingdom'] = $kingdom['name'];
			}
			
			$proposition['statement'] = nl2br(htmlspecialchars($proposition['statement']));
			$proposition['expires'] = format_time($proposition['expires'] - microfloat());
			
			$this->smarty->assign('proposition', $proposition);
			$this->smarty->display('propositions_info.tpl');
		}
		
		function vote()
		{
			if (empty($_REQUEST['proposition_id']))
			{
				error(__FILE__, __LINE__, 'ID_UNSPECIFIED', 'Unspecified proposition id');
			}
			
			$votes = array('for', 'against', 'neutral');
			if (!in_array($_REQUEST['vote'], $votes))
			{
				error(__FILE__, __LINE__, 'VOTE_INVALID', 'Invalid vote');
			}
			
			$proposition_id = (int)$_REQUEST['proposition_id'];
			$vote = $_REQUEST['vote'];
			
			$this->sql->where(array(
				array('propositions', 'proposition_id', $proposition_id), 
				array('propositions', 'round_id', $_SESSION['round_id']), 
				array('propositions', 'kingdom_id', $_SESSION['kingdom_id'])
			));
			$this->sql->limit(1);
			
			$db_query = $this->sql->generate();
			$db_result = mysql_query($db_query);
			
			if (!$db_result || mysql_num_rows($db_result) == 0)
			{
				error(__FILE__, __LINE__, 'ID_INVALID', 'Invalid proposition id');
			}
			
			$proposition = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if ($proposition['status'] != 0)
			{
				error(__FILE__, __LINE__, 'VOTE_INVALID', 'Invalid vote');
			}
			
			$proposition['voted'] = unserialize($proposition['voted']);
			
			if (!empty($proposition['voted'][$_SESSION['player_id']]))
			{
				error(__FILE__, __LINE__, 'VOTE_DUPE', 'Vote already placed');
			}
			
			$proposition['voted'][$_SESSION['player_id']] = true;
			
			$db_query = "
				SELECT COUNT(*) AS 'members' 
				FROM `players` 
				WHERE 
					`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
					`rank` >= '" . RANK_SENATOR . "' AND 
					`lastactive` >= " . (microfloat() - ($_SESSION['round_speed'] * 172800));
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$proposition[$vote]++;
			
			$this->sql->set(array(
				array('propositions', $vote, $proposition[$vote]), 
				array('propositions', 'voted', serialize($proposition['voted']))));
			$this->sql->where(array('propositions', 'proposition_id', $proposition_id));
			$this->sql->limit(1);
			
			$twothirds = 2 / 3;
			
			// for
			if ($db_row['members'] * $twothirds <= $proposition['for'] + ($proposition['neutral'] * .5))
			{
				$this->sql->set(array('propositions', 'status', 1));
				$flag = 'for';
			}
			// against
			elseif ($db_row['members'] * $twothirds < $proposition['against'] + ($proposition['neutral'] * .5))
			{
				$this->sql->set(array('propositions', 'status', 2));
				$flag = 'against';
			}
			// stalemate
			elseif ($db_row['members'] == $proposition['for'] + $proposition['against'] + $proposition['neutral'])
			{
				$this->sql->set(array('propositions', 'status', 3));
				$flag = 'neutral';
			}
			
			$db_result = $this->sql->execute();
			
			if (!empty($flag))
			{
				$types = array(
					PROPOSITION_DESCRIPTION => 'description', 
					PROPOSITION_AVATAR => 'avatar', 
					PROPOSITION_PROMOTE => 'promote', 
					PROPOSITION_DEMOTE => 'demote', 
					PROPOSITION_EXECUTE => 'execute', 
//					 PROPOSITION_ALLY => 'ally', 
//					 PROPOSITION_MERGE => 'merge', 
					PROPOSITION_WAR => 'war', 
					PROPOSITION_PEACE => 'peace');
				
				$proposition_fn = $types[$proposition['type']];
				$this->$proposition_fn($flag);
			}
			
			$_SESSION['status'][] = 'Vote counted.';
			redirect('propositions.php?fn=propositions_info&proposition_id=' . $proposition_id);
		}
	}
?>