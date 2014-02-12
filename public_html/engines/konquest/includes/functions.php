<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	// ###############################################
	// Load global functions
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/includes/functions.php');
	
	// ###############################################
	// Used in planet.php to find the next or previous planet in a player's list.
	function array_neighbor($arr, $key)
	{
		$keys = array_keys($arr);
		$keyIndexes = array_flip($keys);
		
		$return = array();
		if (isset($keys[$keyIndexes[$key] - 1]))
		{
			$return[] = $keys[$keyIndexes[$key] - 1];
		}
		else
		{
			$return[] = $keys[sizeof($keys) - 1];
		}
		
		if (isset($keys[$keyIndexes[$key] + 1]))
		{
			$return[] = $keys[$keyIndexes[$key] + 1];
		}
		else
		{
			$return[] = $keys[0];
		}
		
		return $return;
	}
?>