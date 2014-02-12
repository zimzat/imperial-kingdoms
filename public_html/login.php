<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		
		// ###############################################
		// Validate function
		$valid_functions = array(
			'default' => 'login', 
			'authlogin', 
			'tos', 
			'selectround', 
			'forgotpassword', 
			'initchangepassword', 
			'changepassword'
		);
		
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn();
	}
	
	
	// ###############################################
	// The basic login page
	// Pretty much the same as the front page
	function login()
	{
		global $smarty;
		
		$smarty->display('login.tpl');
	}
	
	
	// ###############################################
	// Authorize users trying to log in
	function authlogin()
	{
		global $smarty, $sql;
		
		$username = strtolower($_POST['username']);
		$password = md5($_POST['password']);
		
		if (strpos($username, ':') !== false)
		{
			$users = explode(':', $username, 2);
			$username = $users[0];
			$user_passthrough = $users[1];
		}
		
		for ($i = 0; $i < 2; $i++)
		{
			$sql->select(array(
				array('users', 'user_id'), 
				array('users', 'username'), 
				array('users', 'password'), 
				array('users', 'style'), 
				array('users', 'preferences'), 
				array('users', 'activated'), 
				array('users', 'tos_hash'), 
				array('users', 'admin')));
			$sql->where(array(
				array('users', 'username', $username), 
				array('users', 'password', $password)));
			$sql->limit(1);
			$db_result = $sql->execute();
			
			// If nothing was returned they're not registered
			if (mysql_num_rows($db_result) == 0)
			{
				if (empty($user_passthrough))
				{
					$smarty->append('status', 'Invalid username and/or password.');
					login();
					exit;
				}
				else
				{
					$username = implode(':', $users);
					unset($user_passthrough);
				}
			}
			else
			{
				break;
			}
		}
		
		// Make sure they've been activated
		$user = mysql_fetch_array($db_result, MYSQL_ASSOC);
		if ($user['activated'] != '0')
		{
			$smarty->append('status', 'This account has not been activated yet. If you have not received your activation code please click <a href="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?fn=SendActivationCode&username=' . $_REQUEST['username'] . '">here</a> to resend it. If you do not receive it soon please contact an administrator.');
			login();
			exit;
		}
		
		session_regenerate_id();
		
		if ($user['admin'] == 1)
		{
			$_SESSION['admin'] = true;
		}
		else
		{
			$_SESSION['admin'] = false;
		}
		
		if ($_SESSION['admin'] && !empty($user_passthrough))
		{
			$sql->select(array(
				array('users', 'user_id'), 
				array('users', 'username'), 
				array('users', 'password'), 
				array('users', 'style'), 
				array('users', 'preferences'), 
				array('users', 'activated'), 
				array('users', 'tos_hash'), 
				array('users', 'admin')));
			$sql->where(array('users', 'username', $user_passthrough));
			$sql->limit(1);
			$db_result = $sql->execute();
			
			if (mysql_num_rows($db_result) > 0)
			{
				$user = mysql_fetch_array($db_result, MYSQL_ASSOC);
			}
			else
			{
				$smarty->append('status', 'Invalid username and/or password for passthrough login.');
				login();
				exit;
			}
		}
		
		// Get their default settings
		$_SESSION['user_id'] = $user['user_id'];
		$_SESSION['style'] = $user['style'];
		$_SESSION['preferences'] = unserialize($user['preferences']);
		
		tos($user['tos_hash']);
	}
	
	function tos($user_tos = '')
	{
		global $smarty, $sql;
		
		if (isset($_REQUEST['agree']))
		{
			$sql->set(array('users', 'tos_hash', $_REQUEST['tos_hash']));
			$sql->where(array('users', 'user_id', $_SESSION['user_id']));
			$sql->execute();
			getrounds();
		}
		elseif (isset($_REQUEST['disagree']))
		{
			$_SESSION['status'][] = 'You must agree to the terms of service agreement before you can play. If do not agree to them you will have to leave the site.';
			redirect('login.php', '');
		}
		else
		{
			$db_query = "SELECT `value` FROM `game_options` WHERE `option` = 'tos_hash' LIMIT 1";
			$db_result = mysql_query($db_query);
			$tos = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if ($tos['value'] != $user_tos)
			{
				$smarty->assign('tos_hash', $tos['value']);
				$smarty->display('login_tos.tpl');
			}
			else
			{
				getrounds();
			}
		}
	}
	
	function getrounds()
	{
		global $smarty, $sql;
		
		$rounds = array();
		if (!$_SESSION['admin'])
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
		
		if (count($rounds) == 1)
		{
			$_POST['round_id'] = $rounds[0]['round_id'];
			selectround();
			exit;
		}
		
		$smarty->assign('rounds', $rounds);
		$smarty->display('login_selectround.tpl');
	}
	
	
	function selectround()
	{
		global $smarty;
		
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
			login();
			exit;
		}
		
		// Find out if the user is already in the round
		$db_query = "
			SELECT 
				u.`style`, 
				u.`admin`, 
				p.`player_id`, 
				p.`kingdom_id`, 
				r.`public` 
			FROM 
				`players` p, 
				`rounds` r, 
				`users` u 
			WHERE 
				u.`user_id` = '" . $_SESSION['user_id'] . "' AND 
				p.`user_id` = '" . $_SESSION['user_id'] . "' AND 
				r.`round_id` = '" . $_SESSION['round_id'] . "' AND 
				p.`round_id` = '" . $_SESSION['round_id'] . "'";
		$db_result = mysql_query($db_query);
		
		if (mysql_num_rows($db_result) > 0)
		{
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			if ($db_row['admin'] == 1 && $db_row['public'] > 0)
			{
				$_SESSION['admin'] = false;
			}
			
			$_SESSION['player_id'] = $db_row['player_id'];
			$_SESSION['kingdom_id'] = $db_row['kingdom_id'];
			$_SESSION['style'] = $db_row['style'];
			
			$forward = 'main.php';
		}
		else
		{
			$forward = 'join.php';
		}
		
		redirect($forward, 'engines/' . $_SESSION['round_engine']);
	}
	
	// ###############################################
	// Forgotten password form
	function forgotpassword()
	{
		global $smarty;
		
		$smarty->display('login_forgotpassword.tpl');
	}
	
	function initchangepassword()
	{
		global $smarty;
		
		$email = $_REQUEST['email'];
		
		$db_query = "SELECT `user_id`, `username`, `email` FROM `users` WHERE `email` = '" . mysql_real_escape_string($email) . "' AND `activated` = '0'";
		$db_result = mysql_query($db_query);
		
		if (mysql_num_rows($db_result) > 0)
		{
			$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			$resetkey = md5(rand(0, 999999));
			
			$db_query = "UPDATE `users` SET `resetkey` = '" . $resetkey . "' WHERE `user_id` = '" . $db_row['user_id'] . "'";
			$db_result = mysql_query($db_query);
			
			$message = <<<PASSWORD_EMAIL
A username and/or password request has been initiated on your account. If you have not made this request simply ignore this message. If you continue to receive this message please contact the administration of Imperial Kingdoms.

Username: {$db_row['username']}

To change your password you must confirm this request by visiting the URL below.

http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}?fn=changepassword&username={$db_row['username']}&resetkey={$resetkey}

Reset Key: {$resetkey}

- Imperial Kingdoms Administration
PASSWORD_EMAIL;
			$message = str_replace("\n", "\r\n", $message);
			
			mail($db_row['email'], 'Imperial Kingdoms - Username / Password Request', $message,
				"From: admin@imperialkingdoms.com\r\n" .
				"Reply-To: admin@imperialkingdoms.com\r\n" .
				"X-Mailer: PHP/" . phpversion());
			
			$smarty->append('status', 'An email has been sent with a reset key attached. Please enter your username, the reset key, and the new password below.');
			$smarty->display('login_changepassword.tpl');
		}
		else
		{
			$smarty->append('status', 'The email requested does not exist or has not been activated.');
			forgotpassword();
			exit;
		}
	}
	
	
	// ###############################################
	// Changing password form
	function changepassword()
	{
		global $smarty;
		
		
		if (isset($_REQUEST['newpassword']))
		{
			$status = '';
			
			$username = $_REQUEST['username'];
			$resetkey = $_REQUEST['resetkey'];
			$newpassword = $_REQUEST['newpassword'];
			$newpasswordconfirm = $_REQUEST['newpasswordconfirm'];
			
			$db_query = "SELECT `user_id`, `resetkey` FROM `users` WHERE `username` = '" . mysql_real_escape_string($username) . "'";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) > 0)
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				
				if ($db_row['resetkey'] != $resetkey)
				{
					$status .= 'Reset key does not match the one on file. Make sure it was entered correctly and try again.<br />';
				}
			}
			else
			{
				$status .= 'Username does not exist.<br />';
			}
			
			if (strlen($newpassword) < 5)
			{
				$status .= 'Password must be at least five characters long.';
			}
			elseif ($newpassword != $newpasswordconfirm)
			{
				$status .= 'New password does not match. Please re-enter and try again.<br />';
			}
			
			if (strlen($status) > 0)
			{
				$username = (empty($_REQUEST['username'])) ? '' : $_REQUEST['username'];
				$resetkey = (empty($_REQUEST['resetkey'])) ? '' : $_REQUEST['resetkey'];
				
				$smarty->append('status', $status);
				$smarty->assign('username', $username);
				$smarty->assign('resetkey', $resetkey);
				$smarty->display('login_changepassword.tpl');
				exit;
			}
			else
			{
				$db_query = "UPDATE `users` SET `password` = '" . md5($newpassword) . "', `resetkey` = '' WHERE `user_id` = '" . $db_row['user_id'] . "' LIMIT 1";
				$db_result = mysql_query($db_query);
				
				$smarty->append('status', 'Password changed successfully.<br />');
				login();
			}
		}
		else
		{
			$username = (empty($_REQUEST['username'])) ? '' : $_REQUEST['username'];
			$resetkey = (empty($_REQUEST['resetkey'])) ? '' : $_REQUEST['resetkey'];
			
			$smarty->assign('username', $username);
			$smarty->assign('resetkey', $resetkey);
			$smarty->display('login_changepassword.tpl');
			exit;
		}
	}