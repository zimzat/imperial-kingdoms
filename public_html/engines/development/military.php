<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	
	// ###############################################
	// Prisoner filter
	prisoner_filter($_SESSION['player_id']);
	
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'military_overview', 
		'reports_search', 
		'reports_list', 
		'reports_view'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	$updater->update($_SESSION['kingdom_id']);
	
	$fn();
	
	
	
	function military_overview()
	{
		global $data, $smarty, $sql;
		
		$incomingfleets = array();
		$combatlocations = array();
		$declarations = array();
		$microtime = microfloat();
		
		$sql->select(array(
			array('planets', 'planet_id'), 
			array('planets', 'name', 'planetname'), 
			array('navygroups', 'navygroup_id'), 
			array('navygroups', 'name', 'navygroupname'), 
			array('tasks', 'kingdom_id'), 
//			array('tasks', 'target_kingdom_id'), 
			array('tasks', 'completion', 'time')));
		$sql->where(array(
			array('navygroups', 'round_id', $_SESSION['round_id']), 
			array('navygroups', 'navygroup_id', array('tasks', 'group_id')), 
			array('planets', 'round_id', $_SESSION['round_id']), 
			array('planets', 'planet_id', array('tasks', 'planet_id')), 
			array('tasks', 'round_id', $_SESSION['round_id']), 
			array('tasks', 'type', 5), 
			array('tasks', 'kingdom_id', array('tasks', 'target_kingdom_id'), '<>')));
		$db_query = $sql->generate();
		$db_query .= " AND (`tasks`.`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' OR `tasks`.`target_kingdom_id` = '" . $_SESSION['kingdom_id'] . "') ORDER BY `planets`.`planet_id` ASC, `tasks`.`completion` ASC";
		$db_result = mysql_query($db_query);
		if (mysql_num_rows($db_result) > 0)
		{
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				if ($db_row['kingdom_id'] == $_SESSION['kingdom_id'])
				{
					$db_row['direction'] = 'Outgoing';
				}
				else
				{
					$db_row['direction'] = 'Incoming';
				}
				unset($db_row['kingdom_id'], $db_row['target_kingdom_id']);
				
				$db_row['time'] = format_time(timeparser($db_row['time'] - $microtime));
				
				$incomingfleets[] = $db_row;
			}
		}
		
		
		
		$typearray = array('army', 'navy');
		foreach ($typearray as $type)
		{
			$sql->property('DISTINCT');
			$sql->select(array(
				array('planets', 'planet_id'), 
				array('planets', 'name', 'planetname'), 
				array('combat', 'completion')
			));
			$sql->where(array(
				array($type . 'groups', 'kingdom_id', array('planets', 'kingdom_id'), '!='), 
				array($type . 'groups', 'planet_id', array('planets', 'planet_id')), 
				array($type . 'groups', 'units', 'a:0:{}', '<>'), 
				array('combat', 'planet_id', array('planets', 'planet_id'))
			));
			if ($type == 'navy')
			{
				$sql->where(array(
					array('navygroups', 'x_current', array('navygroups', 'x_destination')), 
					array('navygroups', 'y_current', array('navygroups', 'y_destination'))
				));
			}
			$db_query = $sql->generate();
			$db_query .= " AND (`" . $type . "groups`.`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' OR `planets`.`kingdom_id` = '" . $_SESSION['kingdom_id'] . "') ORDER BY `combat`.`completion` ASC";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) > 0)
			{
				$location_restarts = array();
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$db_row['time'] = format_time(timeparser($db_row['completion'] - $microtime));
					$combatlocations[$db_row['planet_id']] = $db_row;
					
					// If the update hasn't finished after 30 seconds, it has died.
					if ($db_row['completion'] < $microtime + 30) $location_restarts[] = $db_row['planet_id'];
				}
				
				// Restart any dead combats.
				if (!empty($location_restarts))
				{
					$db_query = "UPDATE `combat` SET `beingupdated` = '0' WHERE `planet_id` IN ('" . implode("', '", $location_restarts) . "')";
					$db_result = mysql_query($db_query);
				}
			}
		}
		
		$sort_function = create_function('$a,$b', 'if ($a[\'completion\'] == $b[\'completion\']) return 0; return ($a[\'completion\'] < $b[\'completion\']) ? -1 : 1;');
		usort($combatlocations, $sort_function);
		
		
		
		// Declarations
		military_declarations();
		
		
		
		$smarty->assign('incomingfleets', $incomingfleets);
		$smarty->assign('combatlocations', $combatlocations);
		$smarty->display('military_overview.tpl');
	}
	
	
	
	function reports_list()
	{
		global $smarty, $sql;
		
		$sql->select(array(
			array('planets', 'planet_id'), 
			array('planets', 'name'), 
			array('combatreports', 'combatreport_id'), 
			array('combatreports', 'status'), 
			array('combatreports', 'date')));
		$sql->where(array(
			array('combatreports', 'round_id', $_SESSION['round_id']), 
			array('combatreports', 'kingdom_id', $_SESSION['kingdom_id']), 
			array('planets', 'planet_id', array('combatreports', 'planet_id'))));
		$sql->orderby(array('combatreports', 'date', 'desc'));
		$sql->limit(30);
		
		if (!empty($_POST['planet_id']))
		{
			$sql->where(array('combatreports', 'planet_id', abs((int)$_POST['planet_id'])));
		}
		
		if (!empty($_POST['time']))
		{
			$sql->where(array('combatreports', 'date', "raw:'" . microfloat() . "' - ('" . mysql_real_escape_string(abs((int)$_POST['time'])) . "' * (`rounds`.`combattick` / 1000))", '>='));
		}
		
		if (!empty($_POST['status']))
		{
			foreach ($_POST['status'] as $key => $value)
			{
				$key = abs((int)$key);
				if ($key >= 0 && $key <= 3)
				{
					$status[$key] = true;
				}
			}
			
			if (!empty($status))
			{
				$sql->where(array('combatreports', 'status', array_keys($status), 'IN'));
			}
		}
		
		$db_result = $sql->execute();
		
		$combatreports = array();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			unset($report);
			
			$report['combatreport_id'] = $db_row['combatreport_id'];
			$report['date'] = format_timestamp($db_row['date']);
			$report['location'] = 'P#' . $db_row['planet_id'] . ' ' . $db_row['name'];
			
			if ($db_row['status'] == 0)
			{
				$report['status'] = 'Ongoing';
			}
			elseif ($db_row['status'] == 1)
			{
				$report['status'] = 'Won';
			}
			else
			{
				$report['status'] = 'Lost';
			}
			
			$combatreports[$db_row['planet_id']][$db_row['combatreport_id']] = $report;
		}
		
		$smarty->assign('combatreports', $combatreports);
		$smarty->display('reports_list.tpl');
	}
	
	
	
	function reports_view()
	{
		global $smarty, $sql;
		
		if (!empty($_REQUEST['combatreport_id']))
		{
			$combatreport_id = abs((int) $_REQUEST['combatreport_id']);
		}
		
		
		$sql->select(array('combatreports', 'report'));
		$sql->where(array(
			array('combatreports', 'combatreport_id', $combatreport_id), 
			array('combatreports', 'round_id', $_SESSION['round_id']), 
			array('combatreports', 'kingdom_id', $_SESSION['kingdom_id'])));
		$sql->limit(1);
		$db_result = $sql->execute();
		
		if (!$db_result || mysql_num_rows($db_result) == 0)
		{
			error(__FILE__, __LINE__, 'INVALID_COMBATREPORT', 'Invalid combat report specified.');
		}
		
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$combatreport = unserialize($db_row['report']);
		
		$combatreport['header']['date'] = format_timestamp($combatreport['header']['date']);
		
		foreach ($combatreport['names'] as $category => $names)
		{
			if ($category == 'kingdoms' || $category == 'weapons')
			{
				foreach ($names as $id => $name)
				{
					$combatreport['names'][$category][$id] = strshort($name, 20, '<span title="' . $name . '">...</span>');
				}
			}
			else
			{
				foreach ($names as $type => $more_names)
				{
					foreach ($more_names as $id => $name)
					{
						$combatreport['names'][$category][$type][$id] = strshort($name, 20, '<span title="' . $name . '">...</span>');
					}
				}
			}
		}
		
		// Format all the numbers
		foreach ($combatreport['details'] as $kingdom_id => $types)
		{
			foreach ($types as $type => $groups)
			{
				foreach ($groups as $group_id => $units)
				{
					foreach ($units as $unit_id => $weapons)
					{
						foreach ($weapons as $weapon_id => $target)
						{
							foreach ($target as $detail_id => $details)
							{
								$combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['hits'] = format_number($combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['hits'], true);
								$combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['damage'] = format_number($combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['damage'], true);
								$combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['kills'] = format_number($combatreport['details'][$kingdom_id][$type][$group_id][$unit_id][$weapon_id][$detail_id]['kills'], true);
							}
						}
					}
				}
			}
		}
		
		$smarty->assign('combatreport', $combatreport);
		$smarty->display('reports_view.tpl');
	}
	
	
	
	function reports_search()
	{
		global $smarty;
		
		$smarty->display('reports_search.tpl');
	}
?>