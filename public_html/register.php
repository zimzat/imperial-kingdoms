<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	// ###############################################
	// Validate function
	$valid_functions = array(
		'default' => 'register', 
		'confirm'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$fn();
	
	function register()
	{
		global $smarty;
		
		$timezones = array(-12, -11, -10, -9, -8, -7, -6, -5, -4, -3.5, -3, -2, -1, 0, 1, 2, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5, 7, 8, 9, 9.5, 10, 11, 12, 13);
		$smarty->assign('timezones', $timezones);
		
		if (!isset($_POST['fn']))
		{
			$smarty->display('register.tpl');
			exit;
		}
		
		
		
		$username = strtolower($_POST['username']);
		$password = $_POST['password'];
		$password_confirm = $_POST['password_confirm'];
		$email = $_POST['email'];
		
		$preferences['decimal_symbol'] = $_POST['decimal_symbol']{0};
		$preferences['thousands_seperator'] = $_POST['thousands_seperator']{0};
		$preferences['timezone'] = (int)$_POST['timezone'];
		$preferences['timestamp_format'] = $_POST['timestamp_format'];
		
		if (isset($_POST['forumaccount']))
		{
			$forumaccount = true;
			mysql_select_db('zimzatik2');
			$db_query = "
				SELECT 
					`username`, 
					`user_password`, 
					`user_regdate`, 
					`user_email`
				FROM `phpbb_users` 
				WHERE 
					`username` = '" . mysql_real_escape_string($username) . "' AND 
					`user_password` = '" . md5($password) . "' AND 
					`user_active` = '1' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
			if (!$db_result || mysql_num_rows($db_result) == 0)
			{
				$status[] = 'Username and password did not match any active forum accounts. If your forum account has not been activated yet, please do so and try again.';
			}
			else
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$username = $db_row['username'];
				$password = $db_row['user_password'];
				$email = $db_row['user_email'];
				$regdate = $db_row['user_regdate'];
			}
			mysql_select_db('zimzatik');
		}
		else
		{
			$forumaccount = false;
		}
		
		$email_regex = '/[-_\d\w]+@((\.|)[-_\d\w]{2,})+(\.[\w]{2,})+/';
		
		if (strlen($username) > 25 && !$forumaccount)
		{
			$status[] = 'Username longer than maximum length';
		}
		
		if ($password != $password_confirm && !$forumaccount)
		{
			$status[] = 'Passwords do not match. Please re-enter and try again.';
		}
		elseif (strlen($password) > 100 || strlen($password) < 4)
		{
			$status[] = 'Password longer than 100 characters or shorter than 4 characters. Please try a shorter/longer password and try again.';
		}
		
		if (strlen($email) > 255 && !$forumaccount)
		{
			$status[] = 'Your email is longer than our database can handle. If it is your valid email address please contact the administrator.';
		}
		elseif (preg_match($email_regex, $email) == 0 && !$forumaccount)
		{
			$status[] = 'Your email address did not validate. If it is a valid email address please contact the administrator.';
		}
		
		if (strlen($preferences['timestamp_format']) > 14)
		{
			$status[] = 'The timestamp format you entered was longer than acceptable. Please shorten it and try again.';
		}
		
		if (!empty($status))
		{
			$smarty->assign('username', $username);
			$smarty->assign('email', $email);
			$smarty->assign('preferences', $preferences);
			$smarty->append('status', $status);
			$smarty->display('register.tpl');
			exit;
		}
		
		$db_query = "
			SELECT 
				`username`, 
				`email`, 
				`activated` 
			FROM `users` 
			WHERE 
				`username` = '" . mysql_real_escape_string($username) . "' OR 
				`email` = '" . mysql_real_escape_string($email) . "' LIMIT 2";
		$db_result = mysql_query($db_query);
		while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			if ($db_row['username'] == $username)
			{
				$status[] = 'Username already taken. Please enter a different one and try again.';
			}
			
			if ($db_row['email'] == $email)
			{
				$status[] = 'That email address is already registered under another username.';
				if ($db_row['activated'] == 0)
				{
					 $status[] = 'Please do not create more than one account.';
				}
			}
		}
		
		if (strlen($status) > 0)
		{
			$smarty->assign('username', $username);
			$smarty->assign('email', $email);
			$smarty->assign('preferences', $preferences);
			$smarty->append('status', $status);
			$smarty->display('register.tpl');
			exit;
		}
		
		if ($forumaccount)
		{
			$activation = 0;
		}
		else
		{
			$activation = md5(rand(0, 35000) . '+' . rand(0, 35000));
		}
		
		$db_query = "
			INSERT INTO `users` 
			(`username`, `password`, `preferences`, `email`, `lastlogin`, `created`, `activated`) VALUES 
			(
				'" . mysql_real_escape_string($username) . "', 
				";
			if ($forumaccount)
			{
				$db_query .= "'" . mysql_real_escape_string($password) . "', ";
			}
			else
			{
				$db_query .= "'" . md5($password) . "', ";
			}
			$db_query .= "
				'" . mysql_real_escape_string(serialize($preferences)) . "', 
				'" . mysql_real_escape_string($email) . "', 
				'0', 
				'" . microfloat() . "', 
				'" . $activation . "'
			)";
		$db_result = mysql_query($db_query);
		
		if ($forumaccount)
		{
			$smarty->append('status', 'Your account has been imported successfully. You can now use the username and password from the forum in the game. Please note that if you change your forum password it will not change your game password.');
		}
		else
		{
			$message = 'Welcome to Imperial Kingdoms! You\'re only one step away from joining the action. Click on or manually go to the following URL and your account will be ready for you to use.

http://www.imperialkingdoms.com/register.php?fn=confirm&username=' . urlencode($username) . '&activation=' . urlencode($activation) . '

We look forward to seeing you in our community.

---

If you think you have received this email in error simply ignore it. If you continue to receive unsolicited messages please contact us to have your email blacklisted.';
			$message = wordwrap($message, 70);
			
			$headers = 'From: Imperial Kingdoms <admin@imperialkingdoms.com>' . "\r\n" .
				'Reply-To: Imperial Kingdoms Administration <admin@imperialkingdoms.com>' . "\r\n" .
				'X-Mailer: PHP/' . phpversion() . "\r\n\r\n";
			
			$result = mail($email, 'Imperial Kingdoms Confirmation Code', $message, $headers);
			
			if ($result != true)
			{
				$smarty->append('status', 'NOTE: Your confirmation email was not successfully sent. Please contact an administrator at <a href="mailto:admin@imperialkingdoms.com">admin@imperialkingdoms.com</a>');
			}
			else
			{
				$smarty->append('status', 'Your confirmation code has been sent to ' . htmlspecialchars($email, ENT_QUOTES) . ' and should arrive within a few minutes. If it hasn\'t arrived in 15 minutes check your junk folder for it. Hotmail accounts may have to wait several hours.');
			}
		}
		$smarty->assign('success', true);
		$smarty->display('register.tpl');
	}
	
	function confirm()
	{
		global $smarty;
		
		if (!isset($_POST['fn']) && !isset($_GET['fn']))
		{
			$smarty->display('register_confirm.tpl');
			exit;
		}
		
		if (isset($_GET['activation']))
		{
			$activation = $_GET['activation'];
			$username = $_GET['username'];
		}
		elseif (isset($_POST['activation']))
		{
			$activation = $_POST['activation'];
			$username = $_POST['username'];
		}
		
		$db_query = "
			SELECT `user_id`, `activated` 
			FROM `users` 
			WHERE `username` = '" . mysql_real_escape_string($username) . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		if (mysql_num_rows($db_result) == 0)
		{
			if (!empty($username))
			{
				$smarty->append('status', 'The username "' . htmlspecialchars($username, ENT_QUOTES) . '" has not been registered.');
			}
			$smarty->display('register_confirm.tpl');
			exit;
		}
		
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		if ($db_row['activated'] == '0' && !empty($username))
		{
			$smarty->append('status', 'The username "' . htmlspecialchars($username, ENT_QUOTES) . '" has already been activated. You may now log into the game.');
			$smarty->display('register_confirm.tpl');
			exit;
		}
		
		if ($db_row['activated'] != $activation)
		{
			$smarty->append('status', 'The confirmation code "' . htmlspecialchars($activation, ENT_QUOTES) . '" for username "' . htmlspecialchars($username, ENT_QUOTES) . '" does not match the one on file.');
			$smarty->display('register_confirm.tpl');
			exit;
		}
		
		$db_query = "
			UPDATE `users` 
			SET `activated` = '0' 
			WHERE `user_id` = '" . $db_row['user_id'] . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		
		$smarty->assign('success', true);
		$smarty->append('status', 'Username "' . htmlspecialchars($username, ENT_QUOTES) . '" has been successfully activated. To join any active rounds click on Play above.');
		$smarty->display('register_confirm.tpl');
		
	}
?>