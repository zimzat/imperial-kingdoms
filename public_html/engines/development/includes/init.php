<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	require_once(dirname(__FILE__) . '/constants.php');
	require_once(dirname(__FILE__) . '/functions.php');
	
	// Class Requirement(s)
//	require(SMARTY_DIR . 'Smarty.class.php');
//	require_once('ik-smarty.php');
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/sql_generator.php');
	require_once(dirname(__FILE__) . '/classes/data.php');
	include_once(dirname(__FILE__) . '/updater_data.php');
	
	// ###############################################
	// We don't like register_globals, so clean up after it if it's on.
	if (ini_get('register_globals'))
	{
		$superglobals = array($_SERVER, $_ENV, $_FILES, $_COOKIE, $_POST, $_GET);
		
		if (isset($_SESSION))
		{
			array_unshift($superglobals, $_SESSION);
		}
		
		foreach ($superglobals as $superglobal)
		{
			foreach ($superglobal as $global => $value)
			{
				unset($GLOBALS[$global]);
			}
		}
		
		ini_set('register_globals', false);
	}
	
	// ###############################################
	// Undo magic quotes so we can do our own validating.
	fix_magic_quotes();
	
	
	class ImperialKingdoms_Initialization
	{
		function ImperialKingdoms_Initialization()
		{
			$this->database();
			$this->style();
			$this->session();
			$this->smarty();
			$this->round();
			$this->updater();
			$this->status();
		}
		
		function database()
		{
			global $db_link, $data, $sql;
			
			// ###############################################
			// Initialize Database Connection & SQL Generator
			@$db_link = @mysql_connect('localhost', 'zimzatik', 'your-database-password-here') or die('Could not connect to database server.');
			@mysql_select_db(DATABASE) or die('Could not select database.');
			
			$data = new Updater_Data;
			$this->data =& $data;
			$this->sql = new SQL_Generator;
			$sql = new SQL_Generator;
		}
		
		function session()
		{
			// ###############################################
			// This is how we remember you're not the boogy man.
			// ... Or that you ARE the boogy man. heheheh
			ini_set('session.gc_maxlifetime', 7200);
			ini_set('session.gc_divisor', 1000);
			session_cache_limiter('nocache');
			session_start();
			
			// ###############################################
			// DEPRECIATED: Set $_SESSION['round_speed'] until we phase it out.
			$round = $this->data->round();
			$_SESSION['round_speed'] = $round['speed'];
		}
		
		function smarty()
		{
			global $smarty, $ik;
			
			// ###############################################
			// Initialize the Smarty template engine
			$smarty = new Smarty;
			$this->smarty =& $smarty;
			
			// Set smarty directories
			if ($_SESSION['style'] != 'default')
			{
				$this->smarty->template_dir = array(
					dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates/', 
					dirname(dirname(__FILE__)) . '/styles/default/templates/', 
				);
			}
			else
			{
				$this->smarty->template_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates/';
			}
			
			$this->smarty->compile_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates_c/';
			$this->smarty->cache_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/cache/';
			$this->smarty->config_dir = dirname(__FILE__) . '/configs/';
			
			if ($_SERVER['HTTP_HOST'] == 'ik.localhost')
			{
				$this->smarty->debugging_ctrl = 'URL';
			}
			
			// Assign variables used site-wide
			$ik = new IK_Smarty;
			$this->smarty->register_object('ik', $ik);
			$this->smarty->assign('style', $_SESSION['style']);
			$this->smarty->assign('engine', $_SESSION['round_engine']);
			$this->smarty->assign('siteurl', 'http://' . $_SERVER['HTTP_HOST'] . '/');
			$this->smarty->assign('actionurl', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
		}
		
		function style()
		{
			// ###############################################
			// If no style is set, change to the default
			if (isset($_SESSION['user_id']) && !isset($_SESSION['style']))
			{
				$this->data->user($_SESSION['user_id']);
				
				$_SESSION['style'] = $user['style'];
			}
			elseif (!isset($_SESSION['user_id']))
			{
				$_SESSION['style'] = 'default';
			}
		}
		
		function round()
		{
			// ###############################################
			// Make sure we're always logged in
			if (
					empty($_SESSION['user_id']) || 
					empty($_SESSION['round_id']) || 
					(
						basename($_SERVER['PHP_SELF']) != 'join.php' && 
						(
							empty($_SESSION['player_id']) || 
							empty($_SESSION['kingdom_id'])
						)
					)
				)
			{
				// Redirect to the login page
				redirect('login.php', '');
			}
			
			$round = $this->data->round();
			
			// ###############################################
			// Make sure the round the user is in has started or hasn't stopped
			if (empty($_SESSION['admin']) || $_SESSION['admin'] != true)
			{
				if ($round['starttime'] > time())
				{
					// Oops, too soon.
					$status[] = 'This round has not started yet. Please wait until it does.';
				}
				elseif ($round['stoptime'] < time())
				{
					// Tick, tick, tick... booom. Bye
					session_destroy();
					$status[] = 'You can not play this round because it is over. To view the high scores visit the main page and click on the round\'s name.';
				}
			
				if (!empty($status))
				{
					$_SESSION['status'][] = $status;
					redirect('login.php', '');
				}
			}
			
			// ###############################################
			// Check if the game is paused
			if ($round['pause_time'] > 0)
			{
				$_SESSION['status'][] = $round['pause_message'];
				redirect('login.php', '');
			}
		}
		
		function updater()
		{
			if (!isset($_SESSION['player_id']) || !empty($_SESSION['admin']))
			{
				return;
			}
			
			$player =& $this->data->player($_SESSION['player_id']);
			$kingdom =& $this->data->kingdom($_SESSION['kingdom_id']);
			$player['lastactive'] = microfloat();
			$kingdom['last_active'] = microfloat();
		}
		
		function status()
		{
			if (!empty($_SESSION['status']))
			{
				$this->smarty->append('status', $_SESSION['status']);
				unset($_SESSION['status']);
			}
		}
	}
	
	$init = new ImperialKingdoms_Initialization;
	
	if (!empty($_SESSION['player_id'])) {
		require_once(dirname(__FILE__) . '/updater_round.php');
		$updater = &new Updater_Round();
		$data->updater = &$updater;
		
		require_once(dirname(__FILE__) . '/alerts.php');
		$alerts = new alerts;
		$alerts->get_alerts();
	}
?>
