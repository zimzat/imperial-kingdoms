<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	
	require_once(dirname(__FILE__) . '/constants.php');
	require_once(dirname(__FILE__) . '/functions.php');
	
	
	// ###############################################
	// This is how we remember you're not the boogy man.
	// ... Or that you ARE the boogy man. heheheh
	ini_set('session.gc_maxlifetime', 7200);
	ini_set('session.gc_divisor', 1000);
	session_cache_limiter('nocache');
	session_start();
	
	
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
	
	
	// ###############################################
	// Make sure we're always logged in
	if (empty($_SESSION['user_id']) || empty($_SESSION['round_id']) || (basename($_SERVER['PHP_SELF']) != 'join.php' && empty($_SESSION['player_id'])))
	{
		// Redirect to the login page
		redirect('login.php', '');
	}
	
	
	// ###############################################
	// Initialize Database Connection & SQL Generator
	@$db_link = @mysql_connect('localhost', 'zimzatik', 'your-database-password-here') or die('Could not connect to database server.');
	@mysql_select_db('zimzatik') or die('Could not select database.');
	
	
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/sql_generator.php');
	$sql = new SQL_Generator;
	
	
	$_SESSION['style'] = 'default';
	
	
	// ###############################################
	// Initialize the Smarty template engine
	define('SMARTY_DIR', dirname(__FILE__) . '/smarty/');
	require(SMARTY_DIR . 'Smarty.class.php');
	$smarty = new Smarty;
	
	// Set smarty directories
	$smarty->template_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates/';
	$smarty->compile_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates_c/';
	$smarty->cache_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/cache/';
	$smarty->config_dir = dirname(__FILE__) . '/configs/';
	
	// Assign variables used site-wide
	$smarty->assign('siteurl', 'http://' . $_SERVER['HTTP_HOST'] . '/');
//	 $smarty->assign('baseurl', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER["PHP_SELF"]) . '/');
	$smarty->assign('actionurl', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	$smarty->assign('style', $_SESSION['style']);
	
	
	include_once(dirname(__FILE__) . '/data.php');
	$data = new Data;
	
	
	if (!empty($_SESSION['status']))
	{
		$smarty->append('status', $_SESSION['status']);
		unset($_SESSION['status']);
	}
?>
