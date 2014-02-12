<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(dirname(__FILE__)) . '/includes/init.php'); 
	
	// ###############################################
	// Validate function
	$valid_functions = array(
		'default' => 'portal_index', 
		'portal_round'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$fn();
	
	function portal_index()
	{
		global $smarty, $sql;
		
		
		
		$smarty->display('portal_index.tpl');
	}
	
	function portal_round()
	{
		global $smarty, $sql;
		
		
		
		$smarty->display('portal_round.tpl');
	}
?>