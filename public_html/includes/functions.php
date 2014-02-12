<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	function todo()
	{
		$backtrace = debug_backtrace();
		
		if (empty($backtrace[1])) return;
		
		$calling_function = '';
		$calling_location = '';
		if (!empty($backtrace[1]['class']))
		{
			$calling_function .= $backtrace[1]['class'] . $backtrace[1]['type'];
		}
		
		$calling_function .= $backtrace[1]['function'];
		$calling_location .= $backtrace[1]['line'] . ': ' . $backtrace[1]['file'];
		
		exit('TODO: ' . $calling_location . "<br />\n\t" . $calling_function);
	}
	
	// Associated Shuffle - shuffle the array while maintaining associations ('shuffle' resets keys)
	function ashuffle($array)
	{
		// Continuing right along with php's lax type-setting. >_>
		if (empty($array) || !is_array($array)) return array();
		
		$keys = array_keys($array);
		shuffle($keys);
		
		$shuffled_array = array();
		foreach ($keys as $key)
		{
			$shuffled_array[$key] = $array[$key];
		}
		
		return $shuffled_array;
	}
	
	function redirect($page)
	{
		if (func_num_args() > 1) $directory = func_get_arg(1);
		else
		{
			if (!empty($_SERVER['PHP_SELF'])) $directory = dirname($_SERVER['PHP_SELF']);
			elseif (!empty($_SERVER['SCRIPT_NAME'])) $directory = dirname($_SERVER['SCRIPT_NAME']);
			else
			{
				trigger_error('Cannot set redirect directory.', E_USER_WARNING);
				$directory = '';
			}
		}
		
		if ($directory == '') $directory = '/';
		else
		{
			if ($directory{0} != '/') $directory = '/' . $directory;
			if ($directory{strlen($directory) - 1} != '/') $directory .= '/';
		}
		
		while ($page{0} == '/' || substr($page, 0, 2) == './' || substr($page, 0, 3) == '../')
		{
			while ($page{0} == '/') $page = substr($page, 1);
			while (substr($page, 0, 2) == './') $page = substr($page, 2);
			while (substr($page, 0, 3) == '../') $page = substr($page, 3);
		}
		
		$location = 'http://' . $_SERVER['HTTP_HOST'] . $directory . $page;
		
		session_write_close();
		
		if (!headers_sent()) header('Location: ' . $location);
		else
		{
			global $smarty;
			if (!empty($smarty) && is_object($smarty))
			{
				$smarty->assign('location', $location);
				$smarty->display('redirect.tpl');
			}
			else
			{
				echo '<script language="javascript">' . "\n" . 
					'<!--' . "\n\t" . 
					'document.location = "' . $location . '";' . "\n" . 
					'// -->' . "\n" . 
					'</script>';
			}
		}
		
		echo 'If you are not automatically redirected, please <a href="' . $location . '">click here</a>.';
		exit;
	}
	
	function request_variable($variable, $method = 'request', $default = NULL)
	{
		if (empty($method)) $method = 'request';
		elseif (!in_array($method, array('request', 'post', 'get')))
		{
			trigger_error('Invalid request variable method specified.', E_USER_WARNING);
			$method = 'request';
		}
		
		switch ($method)
		{
			case 'request':
				$method = &$_REQUEST;
				break;
			case 'post':
				$method = &$_POST;
				break;
			case 'get':
				$method = &$_GET;
				break;
		}
		
		if (!isset($method[$variable]))
		{
			return $default;
		}
		else
		{
			return $method[$variable];
		}
	}
	
	// ###############################################
	// Returns microtime as a float for math and database use
	function microfloat()
	{
		$microtime = explode(' ', microtime());
		return $microtime[1] . substr($microtime[0], 1);
	}
	
	# Deprecated in favor of str_shorten
	function strshort($string, $length, $append = '...')
	{
		return str_shorten($string, $length, $append);
	}
	
	function str_shorten($string, $length, $append = '...')
	{
		if (strlen($string) > $length)
		{
			$string = substr($string, 0, $length) . $append;
		}
		
		return $string;
	}
	
	function str_check($string, $settings = array())
	{
		if (!is_string($string)) $string = (string)$string;
		
		$checks = array_altkey($settings, array('min', 'max', 'regexp'));
		$strlen = strlen($string);
		
		$results = array();
		
		if (isset($checks[0]) && isset($checks[1]) && $checks[0] == $checks[1])
		{
			if ($strlen != $checks[0])
				$results[] = 'Not exactly ' . $checks[0] . ' characters.';
		}
		else
		{
			if (isset($checks[0]) && $strlen < $checks[0])
				$results[] = 'Less than ' . $checks[0] . ' characters.';
			
			if (isset($checks[1]) && $strlen > $checks[1])
				$results[] = 'More than ' . $checks[1] . ' characters.';
		}
		
		if (isset($checks[2]) && preg_match($checks[2], $string))
			$results[] = 'Does not match allowed characters rule.';
		
		if (empty($results)) return false;
		else return $results;
	}
	
	function array_altkey($array, $keys)
	{
		foreach ($keys as $value => $string)
		{
			if (isset($array[$string])) $temp[$value] = $array[$string];
			elseif (isset($array[$value])) $temp[$value] = $array[$value];
		}
		
		return $temp;
	}
	
	// ###############################################
	// return elements of an array that match $needles
	function array_find($needles, $haystacks, $reverse = false)
	{
		if (empty($needles) || !is_array($needles) || 
			empty($haystacks) || !is_array($haystacks))
		{
			return;
		}
		
		$function = 'array_find:';
		$options = array(
			'haystack' => array($function . 'empty', $function . '!empty'), 
			'needle' => array($function . 'key', $function . '!key'));
		$found = array();
		
		if (!$reverse)
		{
			foreach ($haystacks as $haystack_id => $haystack)
			{
				$match = true;
				foreach ($needles as $needle_id => $needle)
				{
					if (in_array($needle, $options['haystack']))
					{
						switch ($needle)
						{
							case $function . 'empty':
								if (empty($haystack[$needle_id])) break;
								
								$match = false; break 2;
							case $function . '!empty':
								if (!empty($haystack[$needle_id])) break;
								
								$match = false; break 2;
							default:
								$match = false; break 2;
						}
					}
					elseif (in_array($needle_id, $options['needle']))
					{
						switch ($needle_id)
						{
							case $function . 'key':
								if (is_array($needle))
								{
									foreach ($needle as $value)
									{
										if (!isset($haystack[$value]))
										{
											$match = false;
											break 3;
										}
									}
									break;
								}
								elseif (isset($haystack[$needle])) break;
								
								$match = false; break 2;
							case $function . '!key':
								if (is_array($needle))
								{
									foreach ($needle as $value)
									{
										if (isset($haystack[$value]))
										{
											$match = false;
											break 3;
										}
									}
									break;
								}
								elseif (!isset($haystack[$needle])) break;
								
								$match = false; break 2;
							default:
								$match = false; break 2;
						}
					}
					elseif (!isset($haystack[$needle_id]))
					{
						$match = false;
						break;
					}
					else
					{
						if (is_array($needle))
						{
							if (!in_array($haystack[$needle_id], $needle))
							{
								$match = false;
								break;
							}
						}
						elseif ($haystack[$needle_id] !== $needle)
						{
							$match = false;
							break;
						}
					}
				}
				
				if ($match)
				{
					$found[$haystack_id] = $haystack;
				}
			}
		}
		else
		{
			foreach ($haystacks as $haystack_id => $haystack)
			{
				$match = true;
				foreach ($needles as $needle_id => $needle)
				{
					if (in_array($needle, $options['haystack']))
					{
						switch ($needle)
						{
							case $function . 'empty':
								if (!empty($haystack[$needle_id])) break;
								
								$match = false; break 2;
							case $function . '!empty':
								if (empty($haystack[$needle_id])) break;
								
								$match = false; break 2;
							default:
								$match = false; break 2;
						}
					}
					elseif (in_array($needle_id, $options['needle']))
					{
						switch ($needle_id)
						{
							case $function . 'key':
								if (is_array($needle))
								{
									foreach ($needle as $value)
									{
										if (isset($haystack[$value]))
										{
											$match = false;
											break 3;
										}
									}
									break;
								}
								elseif (!isset($haystack[$needle])) break;
								
								$match = false; break 2;
							case $function . '!key':
								if (is_array($needle))
								{
									foreach ($needle as $value)
									{
										if (!isset($haystack[$value]))
										{
											$match = false;
											break 3;
										}
									}
									break;
								}
								elseif (isset($haystack[$needle])) break;
								
								$match = false; break 2;
							default:
								$match = false; break 2;
						}
					}
					elseif (isset($haystack[$needle_id]))
					{
						if (is_array($needle))
						{
							if (in_array($haystack[$needle_id], $needle))
							{
								$match = false;
								break;
							}
						}
						elseif ($haystack[$needle_id] === $needle)
						{
							$match = false;
							break;
						}
					}
				}
				
				if ($match) $found[$haystack_id] = $haystack;
			}
		}
		
		return $found;
	}
	
	function dirname_parent($file, $levels = 0)
	{
		for ($i = 0; $i <= $levels; $i++)
		{
			$file = dirname($file);
		}
		
		return $file;
	}
	
	// ###############################################
	// Parse time into an array that is user readable
	function timeparser($seconds = 0)
	{
		if ($seconds > 0)
		{
			$milliseconds = $seconds - round(floor($seconds));
			$seconds = round(floor($seconds));
			
			$time = array(
				'days' => floor($seconds / 86400),
				'hours' => floor($seconds / 3600) % 24,
				'minutes' => floor($seconds / 60) % 60,
				'seconds' => $seconds % 60,
				'milliseconds' => floor($milliseconds * 1000)
			);
		}
		else
		{
			$time = array(
				'days' => 0,
				'hours' => 0,
				'minutes' => 0,
				'seconds' => 0,
				'milliseconds' => 0
			);
		}
		
		return $time;
	}
	
	// ###############################################
	// Return a timestamp formatted in the way a user prefers it.
	function format_timestamp($timestamp = 0)
	{
		if ($timestamp == 0)
		{
			$timestamp = time();
		}
		
		return htmlentities(date($_SESSION['preferences']['timestamp_format'], $timestamp + (3600 * $_SESSION['preferences']['timezone'])));
	}
	
	// ###############################################
	// Format a time into the smallest reasonable clauses.
	function format_time($time = array(), $maxaccuracy = 2)
	{
		if (!is_array($time))
		{
			$time = timeparser($time);
		}
		
		$time_str = '';
		$timeaccuracy = 0;
		
		$timeunits = array(
			array(0 => 'days', 1 => 'd'), 
			array(0 => 'hours', 1 => 'h'), 
			array(0 => 'minutes', 1 => 'm'), 
			array(0 => 'seconds', 1 => 's'), 
			array(0 => 'milliseconds', 1 => 'ms')
		);
		
		foreach ($timeunits as $value)
		{
			if (!empty($time[$value[0]]) && ($value[0] != 'milliseconds' || $timeaccuracy == 0))
			{
				$timeaccuracy++;
				if ($timeaccuracy > 1)
				{
					$time_str .= $_SESSION['preferences']['thousands_seperator'] . ' ';
				}
				
				$time_str .= $time[$value[0]] . $value[1];
			}
			
			if ($timeaccuracy >= $maxaccuracy)
			{
				break;
			}
		}
		
		return htmlentities($time_str);
	}
	
	// ###############################################
	// Format a number according to $_SESSION['preferences'] and to look nice.
	function format_number($number, $shorten = false, $positive_sign = false)
	{
		$thousands = 0;
		$decimal = 0;
		if ($shorten == true)
		{
			while (abs($number) >= 1000 && $thousands < 5)
			{
				$number = $number / 1000;
				$thousands++;
				$decimal = 1;
			}
		}
		
		if ($positive_sign == true && $number <= 0)
		{
			$positive_sign = false;
		}
		
		$number = number_format($number, $decimal, $_SESSION['preferences']['decimal_symbol'], $_SESSION['preferences']['thousands_seperator']);
		
		if ($positive_sign == true)
		{
			$number = '+' . $number;
		}
		
		if ($thousands > 0)
		{
			$numabbr = array(1 => 'K', 2 => 'M', 3 => 'B', 4 => 'T', 5 => 'Q');
			$number .= $numabbr[$thousands];
		}
		
		return htmlentities($number);
	}
	
	// ###############################################
	// Global fn validation. Hand it an array of valid and default values and it'll hand back a valid one.
	// If it doesn't see a valid one it'll call the error function with $file and $line passed to it.
	function validate_fn($valid_functions, $file, $line)
	{
		if (isset($_REQUEST['fn']))
		{
			$fn = $_REQUEST['fn'];
			if (!in_array($fn, $valid_functions))
			{
				$fn = 'invalid';
			}
		}
		else
		{
			if (empty($valid_functions['default']))
			{
				$fn = 'invalid';
			}
			else
			{
				$fn = $valid_functions['default'];
			}
		}
		
		if ($fn == 'invalid')
		{
			error($file, $line, 'FUNCTION_INVALID', 'Invalid function selected.');
		}
		
		return $fn;
	}
	
	// ###############################################
	// We'd prefer to have automatic quoting disabled if we can, but 
	// this function fixes that if we can't.
	function fix_magic_quotes ($var = NULL, $sybase = NULL)
	{
		// if sybase style quoting isn't specified, use ini setting
		if ( !isset ($sybase) )
		{
			$sybase = ini_get ('magic_quotes_sybase');
		}
		
		// if no var is specified, fix all affected superglobals
		if ( !isset ($var) )
		{
			// if magic quotes is enabled
			if ( get_magic_quotes_gpc () )
			{
				// workaround because magic_quotes does not change $_SERVER['argv']
				$argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : NULL; 
				
				// fix all affected arrays
				foreach ( array ('_ENV', '_REQUEST', '_GET', '_POST', '_COOKIE', '_SERVER') as $var )
				{
					$GLOBALS[$var] = fix_magic_quotes ($GLOBALS[$var], $sybase);
				}
				
				$_SERVER['argv'] = $argv;
				
				// turn off magic quotes, this is so scripts which
				// are sensitive to the setting will work correctly
				ini_set ('magic_quotes_gpc', 0);
			}
			
			// disable magic_quotes_sybase
			if ( $sybase )
			{
				ini_set ('magic_quotes_sybase', 0);
			}
			
			// disable magic_quotes_runtime
			set_magic_quotes_runtime (0);
			return TRUE;
		}
		
		// if var is an array, fix each element
		if ( is_array ($var) )
		{
			foreach ( $var as $key => $val )
			{
				$var[$key] = fix_magic_quotes ($val, $sybase);
			}
			
			return $var;
		}
		
		// if var is a string, strip slashes
		if ( is_string ($var) )
		{
			return $sybase ? str_replace ('\'\'', '\'', $var) : stripslashes ($var);
		}
		
		// otherwise ignore
		return $var;
	}
	
	// ###############################################
	// If there's an error, we handle it here
	// Types: DATA_INVALID, DATA_MISSING, FUNCTION_INVALID
	// error(__FILE__, __LINE__, 'DATA_INVALID', 'Invalid data encountered.')
	function error($error_file, $error_line, $error_type, $error_string)
	{
		global $smarty;
		
		if (empty($error_file))
		{
			$error_file = $_SERVER['PHP_SELF'];
		}
		
		if (empty($error_line))
		{
			$error_line = '';
		}
		
		if (empty($error_type))
		{
			$error_type = 'UNKNOWN';
		}
		
		if (empty($error_string))
		{
			$error_string = 'It seems your staff has hit the wrong button again. The computer, in all its glory, had a meltdown. Your advisors have ordered a replacement in hopes that you won\'t notice.';
		}
		
		$backtrace = debug_backtrace();
		
		$db_query = "INSERT INTO `errorlog` (`file`, `line`, `type`, `backtrace`, `remote_address`) VALUES ('" . $error_file . "', '" . $error_line . "', '" . $error_type . "', '" . addslashes(serialize($backtrace)) . "', '" . $_SERVER['REMOTE_ADDR'] . "')";
		$db_result = mysql_query($db_query);
		
		$error_id = mysql_insert_id();
		
		if (empty($_SESSION['admin']) || $_SESSION['admin'] != true)
		{
			$status[] = "<p align=\"left\"><b>REPORT_ID:</b> $error_id<br /><b>ERROR:</b> $error_string</p>";
		}
		else
		{
			$status[] = "<p align=\"left\"><b>REPORT_ID:</b> $error_id<br /><b>FILE:</b> $error_file<br /><b>LINE:</b> $error_line<br /><b>TYPE:</b> $error_type<br /><b>REPORT:</b> $error_string<br /><b>BACKTRACE:</b><br /><pre>" . print_r($backtrace, true) . "</pre></p>";
		}
		
		if (!empty($smarty) && is_object($smarty))
		{
			$smarty->assign('status_hide', 'false');
			$smarty->append('status', $status);
			$smarty->display('error.tpl');
			exit;
		}
		else
		{
			exit(implode("<br />\n", $status));
		}
	}
?>