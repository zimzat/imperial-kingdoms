<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	
	require_once(dirname(__FILE__) . '/functions.php');
	
	
	// ###############################################
	// This is how we remember you're not the boogy man.
	// ... Or that you ARE the boogy man. heheheh
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
	
	if (dirname($_SERVER['PHP_SELF']) == '/admin')
	{
		if (empty($_SESSION['admin']) || $_SESSION['admin'] == false)
		{
			// Redirect to the login page
			redirect('login.php', '');
		}
		
		$_SESSION['style'] = 'admin';
	}
	else
	{
		$_SESSION['style'] = 'default';
	}
	
	if (empty($_SESSION['preferences']))
	{
		$_SESSION['preferences'] = array(
			'thousands_seperator' => ',', 
			'decimal_symbol' => '.', 
			'timezone' => 0, 
			'timestamp_format' => 'Y-m-d H:i:s');
	}
	
	
	// ###############################################
	// Initialize Database Connection
	$db_link = mysql_connect('mysql.imperialkingdoms.com', 'imperialkingdoms', 'your-database-password-here') or die('Could not connect to database server.');
	mysql_select_db('imperialkingdoms') or die('Could not select database.');
	
	require_once(dirname(__FILE__) . '/sql_generator.php');
	$sql = new SQL_Generator;
	
	// ###############################################
	// Initialize the Smarty template engine
	define('SMARTY_DIR', dirname(dirname(__FILE__)) . '/includes/smarty/');
	require(SMARTY_DIR . 'Smarty.class.php');
	
	$smarty = new Smarty;
	
	// Set smarty directories
	$smarty->template_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates/';
	$smarty->compile_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/templates_c/';
	$smarty->cache_dir = dirname(dirname(__FILE__)) . '/styles/' . $_SESSION['style'] . '/cache/';
	$smarty->config_dir = dirname(__FILE__) . '/configs/';
	
	// Assign variables used site-wide
	$smarty->assign('actionurl', 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	$smarty->assign('baseurl', 'http://' . $_SERVER['HTTP_HOST'] . '/');
	$smarty->assign('style', $_SESSION['style']);
	
	
	
	if (!empty($_SESSION['status']))
	{
		$smarty->append('status', $_SESSION['status']);
		unset($_SESSION['status']);
	}
?>
