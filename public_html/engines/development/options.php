<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'options_round', 
		'options_round_change', 
		'options_round_abandon', 
		'options_round_delete', 
		'options_planet', 
		'options_planet_save', 
		'options_player', 
		'options_player_save', 
		'options_permissions', 
		'options_permissions_set', 
		'options_user', 
		'options_user_save', 
		'options_logout'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$updater->update(0, 0, $_SESSION['player_id']);
	
	$fn();
	
	
	
	
	// ###############################################
	function options_logout()
	{
		session_destroy();
		
		redirect('login.php', '');
	}
	
	// ###############################################
	function options_round()
	{
		global $smarty;
		
		if (empty($_SESSION['admin']) || $_SESSION['admin'] != true)
		{
			$db_query = "
				SELECT 
					`round_id`, 
					`name`, 
					`starttime`, 
					`stoptime` 
				FROM `rounds` 
				WHERE 
					`stoptime` > '" . microfloat() . "' AND 
					`starttime` <= '" . microfloat() . "' AND 
					`public` >= '1' 
				ORDER BY `starttime` ASC";
		}
		else
		{
			// Get all current rounds
			$db_query = "
				SELECT 
					`round_id`, 
					`name`, 
					`starttime`, 
					`stoptime` 
				FROM `rounds` 
				WHERE 
					`stoptime` > '" . microfloat() . "' OR 
					`public` = '0' 
				ORDER BY `starttime` ASC";
		}
		$db_result = mysql_query($db_query);
		
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$temp = array();
			
			// Count the seconds until stop
			$seconds = $db_row['stoptime'] - time();
			$temp['started'] = true;
			
			// Merge the time arrays
			$temp += timeparser($seconds);
			$seconds = $db_row['stoptime'] - $db_row['starttime'];
			$db_row['starttime'] = $temp;
			
			$temp = array();
			$temp = timeparser($seconds);
			$db_row['stoptime'] = $temp;
			
			$rounds[] = $db_row;
		}
		
		$smarty->assign('rounds', $rounds);
		$smarty->display('options_round.tpl');
	}
	
	function options_round_change()
	{
		global $smarty;
		
		unset($_SESSION['round_id'], $_SESSION['player_id'], $_SESSION['round_speed'], $_SESSION['round_engine']);
		
		// If requesting a round...
		if (isset($_POST['round_id']))
		{
			// Clean it up then pass it on
			$_SESSION['round_id'] = (int)$_POST['round_id'];
			
			// Make sure that round is valid and running before allowing them in.
			$db_query = "
				SELECT 
					`round_engine`, 
					`starttime`, 
					`stoptime`, 
					`speed` 
				FROM `rounds` 
				WHERE `round_id` = '" . mysql_real_escape_string($_SESSION['round_id']) . "'";
			$db_result = mysql_query($db_query);
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if ($_SESSION['admin'] != true && ($db_row['starttime'] > time() || $db_row['stoptime'] < time()))
			{
				$status[] = 'That round is not currently running.';
			}
			else
			{
				$_SESSION['round_speed'] = $db_row['speed'] / 1000;
				$_SESSION['round_engine'] = $db_row['round_engine'];
			}
		}
		else
		{
			$_SESSION['round_id'] = 0;
			$status[] = 'You must select a round to play.';
		}
		
		if (!empty($status))
		{
			$smarty->append('status', $status);
			options_round();
			exit;
		}
		
		// Find out if the user is already in the round
		$db_query = "SELECT p.`player_id`, p.`kingdom_id` FROM `players` p, `rounds` g WHERE p.`user_id` = '" . $_SESSION['user_id'] . "' AND g.`round_id` = '" . $_SESSION['round_id'] . "' AND p.`round_id` = '" . $_SESSION['round_id'] . "'";
		$db_result = mysql_query($db_query);
		
		if (mysql_num_rows($db_result) > 0)
		{
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$_SESSION['player_id'] = $db_row['player_id'];
			$_SESSION['kingdom_id'] = $db_row['kingdom_id'];
			
			$forward = 'main.php';
		}
		else
		{
			$forward = 'join.php';
		}
		
		redirect($forward, 'engines/' . $_SESSION['round_engine']);
	}
	
	function options_round_abandon()
	{
		global $smarty;
		
		$smarty->display('options_round_abandon.tpl');
	}
	
	function options_round_delete()
	{
		global $data, $smarty;
		
		if (empty($_REQUEST['password']) || empty($_REQUEST['confirm_abandon']))
		{
			options_round_abandon();
			exit;
		}
		
		$user = $data->user($_SESSION['user_id']);
		if (md5($_REQUEST['password']) != $user['password'])
		{
			$smarty->append('status', 'Password does not match the one on file.');
			options_round_abandon();
			exit;
		}
		
		$db_query = "UPDATE `players` SET `user_id` = '0' WHERE `player_id` = '" . $_SESSION['player_id'] . "'";
		$db_result = mysql_query($db_query);
		
		redirect('login.php', '');
	}
	
	// ###############################################
	function options_planet()
	{
		global $smarty;
		
		$smarty->display('options_planet.tpl');
	}
	
	function options_planet_save()
	{
		if (empty($_REQUEST['code']))
		{
			$_SESSION['status'][] = 'Please enter a planet code first.';
			options_planet();
			exit;
		}
		
		$planet_code = $_REQUEST['code'];
		
		if (strlen($planet_code) != 32)
		{
			$_SESSION['status'][] = 'Invalid planet code entered. Please check the code and try again.';
			options_planet();
			exit;
		}
		
		$db_query = "
			SELECT `planet_id` 
			FROM `planets` 
			WHERE 
				`code` = '" . mysql_real_escape_string($planet_code) . "' AND 
				`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
				`player_id` = '0' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		if (mysql_num_rows($db_result) == 0)
		{
			$_SESSION['status'][] = 'That planet code has been taken already, does not exist, or is not in your kingdom.';
			options_planet();
			exit;
		}
		
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		global $data;
		
		$round = $data->round();
		$planet = &$data->planet($db_row['planet_id']);
		$kingdom = &$data->kingdom($planet['kingdom_id']);
		$player = &$data->player($_SESSION['player_id']);
		
		$planet['code'] = '';
		$planet['status'] = PLANETSTATUS_OCCUPIED;
		$planet['warptime_construction'] = $planet['warptime_research'] = 
				(microfloat() - $round['starttime']) * $round['warptime'];
		$planet['player_id'] = $_SESSION['player_id'];
		$planet['lastupdated'] = microfloat();
		$kingdom['planets'][$planet['planet_id']] = true;
		$player['planets'][$planet['planet_id']] = true;
		
		$data->save();
		
		global $smarty;
		
		$smarty->append('status', 'Planet successfully claimed.');
		
		options_planet();
		exit;
	}
	
	// ###############################################
	function options_player()
	{
		global $smarty;
		
		$db_query = "
			SELECT 
				`description`, 
				`image` 
			FROM `players` 
			WHERE `player_id` = '" . $_SESSION['player_id'] . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$options = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$options['description'] = nl2br(htmlentities($options['description']));
		$options['image'] = 'http://www.imperialkingdoms.com/images/avatars/players/' . $options['image'];
		
		$smarty->assign('options', $options);
		$smarty->display('options_player.tpl');
	}
	
	function options_player_save()
	{
		global $smarty, $sql;
		
		$options['description'] = substr($_POST['description'], 0, 1024);
		if (!empty($_POST['avatar_reset']) && empty($_FILES['avatar']['size']))
		{
			$options['image'] = '0-0.png';
		}
		elseif (!empty($_FILES['avatar']['size']))
		{
			require_once(dirname(__FILE__) . '/includes/fileupload.php');
			
			$upload_class = new Upload_Files;
			$upload_class->temp_file_name = trim($_FILES['avatar']['tmp_name']);
			$extension = strtolower(substr(trim($_FILES['avatar']['name']), strrpos(trim($_FILES['avatar']['name']), '.')));
			$upload_class->file_name = $_SESSION['round_id'] . '-' . $_SESSION['player_id'] . $extension;
			$upload_class->upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/players';
			$upload_class->upload_log_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/avatars/players/logs';
			$upload_class->ext_array = array('.jpg', '.gif', '.png', '.jpeg');
			$upload_class->max_file_size = 40960;
			$upload_class->banned_array = array('');
			
			$file_exists = $upload_class->existing_file();
			$valid_ext = $upload_class->validate_extension();
			
			if (!$valid_ext)
			{
				$status[] = 'Invalid avatar image extension.';
			}
			else
			{
				if ($file_exists)
				{
					unlink($upload_class->upload_dir . '/' . $upload_class->file_name);
					$file_exists = $upload_class->existing_file();
				}
				
				if ($file_exists)
				{
					$status[] = 'Avatar exists and could not be deleted. Notify administration.';
				}
				else
				{
					$upload_file = $upload_class->upload_file_with_validation();
					if (!$upload_file)
					{
						$status[] = 'The avatar could not be uploaded for an unknown reason. Contact administration.';
					}
					else
					{
						$options['image'] = $upload_class->file_name;
					}
				}
			}
			
			if (!empty($status))
			{
				$options['description'] = nl2br(htmlentities($options['description']));
				$options['image'] = 'http://www.imperialkingdoms.com/images/avatars/players/' . $options['image'];
				$options['preferences']['thousands_seperator'] = htmlentities($options['preferences']['thousands_seperator']);
				$options['preferences']['decimal_symbol'] = htmlentities($options['preferences']['decimal_symbol']);
				$options['preferences']['timezone'] = (float)$options['preferences']['timezone'];
				$options['preferences']['timestamp_format'] = htmlentities($options['preferences']['timestamp_format']);
				
				$timezones = array(-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 8, 9, 9.5, 10, 11, 12, 13);
				
				$smarty->assign('timezones', $timezones);
				$smarty->append('status', $status);
				$smarty->assign('options', $options);
				$smarty->display('options_list.tpl');
				exit;
			}
		}
		
		if (!empty($options['image']))
		{
			$sql->set(array('players', 'image', $options['image']));
		}
		$sql->set(array('players', 'description', $options['description']));
		$sql->where(array('players', 'player_id', $_SESSION['player_id']));
		$sql->limit(1);
		
		$db_query = $sql->generate();
		$db_result = mysql_query($db_query);
		
		$status[] = 'Saved options successfully.';
		$smarty->append('status', $status);
		options_player();
		exit;
	}
	
	// ###############################################
	function options_permissions()
	{
		global $smarty, $sql;
		
		$sql->select(array(
			array('players', 'player_id'), 
			array('players', 'name')));
		$sql->where(array(
			array('players', 'kingdom_id', $_SESSION['kingdom_id']), 
			array('players', 'rank', 0, '>'), 
			array('players', 'player_id', $_SESSION['player_id'], '!=')));
		$db_result = $sql->execute();
		if (mysql_num_rows($db_result) > 0)
		{
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$players[$db_row['player_id']] = $db_row;
			}
			$smarty->assign('players', $players);
		}
		
		$sql->select(array(
			array('permissions', 'permission_id'), 
			array('permissions', 'player_id'), 
			array('players', 'name', 'player_name'), 
			array('permissions', 'id'), 
			array('permissions', 'type'), 
			array('permissions', 'research'), 
			array('permissions', 'build'), 
			array('permissions', 'commission'), 
			array('permissions', 'military')));
		$sql->where(array(
			array('permissions', 'owner_id', $_SESSION['player_id']), 
			array('players', 'player_id', array('permissions', 'player_id'))));
		$sql->orderby(array(
			array('permissions', 'player_id', 'asc'), 
			array('permissions', 'type', 'asc')));
		$db_result = $sql->execute();
		
		if (mysql_num_rows($db_result) > 0)
		{
			$arrays = array();
			
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				switch ($db_row['type'])
				{
					case PERMISSION_PLAYER:
						$db_row['name'] == '[Global]';
						break;
					case PERMISSION_PLANET:
						$arrays['planet'][$db_row['id']][] = $db_row['permission_id'];
						break;
					case PERMISSION_ARMY:
						$arrays['armygroup'][$db_row['id']][] = $db_row['permission_id'];
						break;
					case PERMISSION_NAVY:
						$arrays['navygroup'][$db_row['id']][] = $db_row['permission_id'];
						break;
				}
				
				$permissions[$db_row['permission_id']] = $db_row;
			}
			
			foreach ($arrays as $type => $array)
			{
				if (!empty($array))
				{
					$sql->select(array(
						array($type . 's', $type . '_id'), 
						array($type . 's', 'name')));
					$sql->where(array(
						array($type . 's', $type . '_id', array_keys($array), 'IN'), 
						array($type . 's', 'player_id', $_SESSION['player_id'])));
					$db_result = $sql->execute();
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						foreach ($array[$db_row[$type . '_id']] as $value)
						{
							$permissions[$value]['name'] = '(' . strtoupper($type{0}) . '#' . $db_row[$type . '_id'] . ') ' . $db_row['name'];
							
						}
					}
				}
			}
			
			$smarty->assign('permissions', $permissions);
		}
		
		$smarty->display('options_permissions.tpl');
	}
	
	function options_permissions_set()
	{
		// permissions_update_planets($player_id);
	}
	
	// ###############################################
	function options_user()
	{
		global $smarty;
		
		$options = array();
		$styles = array('default' => 'Default');
		
		$db_query = "
			SELECT 
				`preferences`, 
				`style` 
			FROM `users` 
			WHERE `user_id` = '" . $_SESSION['user_id'] . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$options = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$options['preferences'] = unserialize($options['preferences']);
		$options['preferences']['thousands_seperator'] = htmlentities($options['preferences']['thousands_seperator']);
		$options['preferences']['decimal_symbol'] = htmlentities($options['preferences']['decimal_symbol']);
		$options['preferences']['timezone'] = (float)$options['preferences']['timezone'];
		$options['preferences']['timestamp_format'] = htmlentities($options['preferences']['timestamp_format']);
		
		$timezones = array(-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 8, 9, 9.5, 10, 11, 12, 13);
		
		$db_query = "
			SELECT `name`, `style` 
			FROM `styles` 
			WHERE 
				`engine` = '" . $_SESSION['round_engine'] . "' ";
		if (empty($_SESSION['admin']) || $_SESSION['admin'] == false)
		{
			$db_query .= "AND 
				(
					(`private` = '1' AND `creator_id` = '" . $_SESSION['user_id'] . "') OR 
					`private` = '0'
				)";
		}
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$styles[$db_row['style']] = $db_row['name'];
		}
		
		$smarty->assign('styles', $styles);
		$smarty->assign('timezones', $timezones);
		$smarty->assign('options', $options);
		$smarty->display('options_user.tpl');
	}
	
	function options_user_save()
	{
		global $smarty, $sql;
		
		$options['preferences']['thousands_seperator'] = $_POST['thousands_seperator']{0};
		$options['preferences']['decimal_symbol'] = $_POST['decimal_symbol']{0};
		$options['preferences']['timezone'] = (float)$_POST['timezone'];
		$options['preferences']['timestamp_format'] = substr($_POST['timestamp_format'], 0, 25);
		
		$sql->set(array('users', 'preferences', serialize($options['preferences'])));
		
		if ($_POST['style'] != $_SESSION['style']) 
		{
			if ($_POST['style'] != 'default')
			{
				$style_query = "
					SELECT `style` 
					FROM `styles` 
					WHERE 
						`engine` = '" . $_SESSION['round_engine'] . "' AND 
						`style` = '" . mysql_real_escape_string($_POST['style']) . "' 
					LIMIT 1";
				$style_result = mysql_query($style_query);
				
				if (mysql_num_rows($style_result) > 0)
				{
					$sql->set(array('users', 'style', $_POST['style']));
					$_SESSION['style'] = $_POST['style'];
					$changed_style = true;
				}
				else
				{
					$status[] = 'Invalid style selected.';
				}
			}
			else
			{
				$sql->set(array('users', 'style', $_POST['style']));
				$_SESSION['style'] = $_POST['style'];
				$changed_style = true;
			}
		}
		$sql->where(array('users', 'user_id', $_SESSION['user_id']));
		$sql->limit(1);
		$db_result = $sql->execute();
		
		$_SESSION['preferences'] = $options['preferences'];
		
		$status[] = 'Saved options successfully.';
		$_SESSION['status'][] = $status;
		
		if (!empty($changed_style))
		{
			redirect('main.php');
		}
		else
		{
			redirect('options.php?fn=options_user');
		}
	}
?>