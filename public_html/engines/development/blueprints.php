<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	
	
	// ###############################################
	// Prisoner filter
	prisoner_filter($_SESSION['player_id']);
	
	
	
	// ###############################################
	// Validate Function
	$valid_functions = array(
		'default' => 'blueprints_list', 
		'blueprints_info', 
		'blueprints_select', 
		'blueprints_weapons', 
		'blueprints_create', 
		'blueprints_deactivate'
	);
	
	$fn = validate_fn($valid_functions, __FILE__, __LINE__);
	
	
	$updater->update($_SESSION['kingdom_id']);
	
	
	$fn();
	
	
	function blueprints_list()
	{
		global $smarty, $sql;
		
		$active[] = 1;
		if (!empty($_REQUEST['showinactive']))
		{
			$active[] = 0;
		}
		
		$blueprints = array('army', 'navy', 'weapon');
		
		$blueprintlist = array();
		foreach ($blueprints as $value)
		{
			$sql->select(array(
				array($value . 'blueprints', $value . 'blueprint_id'), 
				array($value . 'blueprints', 'name'), 
				array($value . 'blueprints', 'active'), 
				array($value . 'blueprints', 'techlevel'), 
				array($value . 'concepts', 'name', 'type')));
			$sql->where(array(
				array($value . 'blueprints', 'round_id', $_SESSION['round_id']), 
				array($value . 'blueprints', 'kingdom_id', $_SESSION['kingdom_id']), 
				array($value . 'blueprints', 'active', $active, 'IN'), 
				array($value . 'concepts', $value . 'concept_id', array($value . 'blueprints', $value . 'concept_id'))));
			$sql->orderby(array(
				array($value . 'blueprints', $value . 'concept_id', 'desc'), 
				array($value . 'blueprints', $value . 'blueprint_id', 'desc')));
			
			$db_result = $sql->execute();
			while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$blueprintlist[$value][$db_row[$value . 'blueprint_id']] = $db_row;
			}
		}
		
		$smarty->assign('blueprints', $blueprintlist);
		$smarty->display('blueprints_list.tpl');
	}
	
	
	function blueprints_info()
	{
		global $smarty, $sql;
		
		$type = (isset($_REQUEST['type'])) ? $_REQUEST['type'] : '';
		$blueprint_id = (isset($_REQUEST['blueprint_id'])) ? $_REQUEST['blueprint_id'] : '';
		
		if (!in_array($type, array('army', 'navy', 'weapon')) || empty($blueprint_id))
		{
			error(__FILE__, __LINE__, 'INVALID_ID/TYPE', 'Invalid type and/or blueprint_id');
		}
		
		$sql->where(array($type . 'blueprints', $type . 'blueprint_id', $blueprint_id));
		$sql->limit(1);
		$db_result = $sql->execute();
		if (mysql_num_rows($db_result) == 0)
		{
			error(__FILE__, __LINE__, 'INVALID_ID', 'Invalid blueprint_id');
		}
		$blueprint = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		if ($blueprint['kingdom_id'] != $_SESSION['kingdom_id'])
		{
			error(__FILE__, __LINE__, 'INVALID_ACCESS', 'You do not have permission to access that blueprint.');
		}
		
		$blueprint['mineralspread'] = unserialize($blueprint['mineralspread']);
		
		$blueprint['resources']['time'] = format_time(timeparser(($blueprint['time'] * $_SESSION['round_speed'])));
		$blueprint['resources']['workers'] = format_number($blueprint['workers'], true);
		$blueprint['resources']['energy'] = format_number($blueprint['energy'], true);
		
		if (!empty($blueprint['mineralspread']))
		{
			$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
			foreach($blueprint['mineralspread'] as $key => $value)
			{
				$resources['minerals'][$mineralnames[$key]] = format_number($blueprint['minerals'] / $value, true);
			}
			$blueprint['resources']['minerals'] = $resources['minerals'];
		}
		
		if ($type == 'weapon')
		{
			$blueprint['targets'] = unserialize($blueprint['targets']);
		}
		else
		{
			$blueprint['weapons'] = unserialize($blueprint['weapons']);
		}
		
		blueprints_stats($type, $blueprint_id);
		
		$smarty->assign('type', $type);
		$smarty->assign('blueprint', $blueprint);
		$smarty->display('blueprints_info.tpl');
	}
	
	
	function blueprints_select()
	{
		global $smarty;
		
		$designs = array('army', 'navy', 'weapon');
		
		$designlist = array();
		foreach ($designs as $value)
		{
			$db_query = "
				SELECT 
					`" . $value . "design_id`, 
					`name`, 
					`techlevel_current` 
				FROM `" . $value . "designs` 
				WHERE 
					`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`kingdom_id` = '" . $_SESSION['kingdom_id'] . "'";
			$db_result = mysql_query($db_query);
			while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$designlist[$value][$db_row[$value . 'design_id']] = array(
					'name' => $db_row['name'], 
					'techlevel' => $db_row['techlevel_current']
				);
			}
		}
		
		$smarty->assign('designs', $designlist);
		$smarty->display('blueprints_select.tpl');
	}
	
	
	function blueprints_weapons()
	{
		global $smarty;
		
		$designs['main'] = array('army', 'navy', 'weapon');
		foreach ($designs['main'] as $value)
		{
			if (!empty($_REQUEST[$value . 'design_id']))
			{
				if ($value == 'weapon')
				{
					blueprints_create();
					exit;
				}
				
				$design_id = (int)$_POST[$value . 'design_id'];
				$design_name = $value;
				break;
			}
		}
		$name = $_POST['name'];
		
		
		
		if (empty($design_id))
		{
			$status[] = 'No design selected.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		
		if (strlen($name) < 3 || strlen($name) > 32)
		{
			$status[] = 'Name length incorrect. Must be at least 3 characters and no more than 32.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		
		
		
		$design['name'] = $name;
		$design['design_id'] = $design_id;
		$design['type'] = $design_name;
		$weaponlist = array();
		
		$db_query = "SELECT `weaponblueprint_id`, `name`, `size` FROM `weaponblueprints` WHERE `round_id` = '" . $_SESSION['round_id'] . "' AND `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `active` = '1'";
		$db_result = mysql_query($db_query);
		while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$weaponlist[$db_row['weaponblueprint_id']]['name'] = $db_row['name'] . ' (' . $db_row['size'] . 'g)';
		}
		
		$db_query = "SELECT `weaponslots`, `weaponsperslot`, `weaponsload_base` FROM `" . $design_name . "designs` WHERE `" . $design_name . "design_id` = '" . $design_id . "' LIMIT 1";
		$db_result = mysql_query($db_query);
		if (mysql_num_rows($db_result) == 0)
		{
			$status[] = 'Invalid or null design selected.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		for ($i = 0; $i < $db_row['weaponsperslot']; $i++)
		{
			$weaponsperslot[] = $i + 1;
		}
		
		$smarty->assign('weaponslots', $db_row['weaponslots']);
		$smarty->assign('weaponsperslot', $weaponsperslot);
		$smarty->assign('weaponsload', $db_row['weaponsload_base'] * 10);
		$smarty->assign('weapons', $weaponlist);
		$smarty->assign('design', $design);
		$smarty->display('blueprints_weapons.tpl');
	}
	
	
	function blueprints_create()
	{
		global $smarty, $sql;
		
		$designs['main'] = array('army', 'navy', 'weapon');
		$designs['army'] = array('attack', 'defense', 'armor', 'hull', 'size');
		$designs['navy'] = array('attack', 'defense', 'armor', 'hull', 'size', 'cargo', 'speed');
		$designs['weapon'] = array('accuracy', 'areadamage', 'rateoffire', 'power', 'damage', 'size');
		
		
		
		foreach ($designs['main'] as $value)
		{
			if (!empty($_REQUEST[$value . 'design_id']))
			{
				$design_id = (int)$_POST[$value . 'design_id'];
				$design_name = $value;
				break;
			}
		}
		$name = $_POST['name'];
		
		
		
		if (empty($design_id))
		{
			$status[] = 'No design selected.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		
		if (strlen($name) < 3 || strlen($name) > 32)
		{
			$status[] = 'Name length incorrect. Must be at least 3 characters and no more than 32.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		
		
		$db_query = "
			SELECT 
				`" . $design_name . "concept_id`, 
				`time`, 
				`workers`, 
				`energy`, 
				`minerals`, 
				`mineralspread`, 
				`techlevel_current`, ";
		if ($design_name != 'weapon')
		{
			$db_query .= "
				`weaponslots`, 
				`weaponsperslot`, 
				`weaponsload_base`, ";
		}
		$db_query .= "
				`" . implode("_base`, \n\t\t\t\t`", $designs[$design_name]) . "_base` 
			FROM `" . $design_name . "designs` 
			WHERE `" . $design_name . "design_id` = '" . $design_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$design = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		
		
		$db_query = "
			SELECT COUNT(*) as 'techlevel_count' 
			FROM `" . $design_name . "blueprints` 
			WHERE `" . $design_name . "design_id` = '" . $design_id . "' AND `techlevel` = '" . $design['techlevel_current'] . "'";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		if (($design_name == 'weapon' && $db_row['techlevel_count'] > 0) || $db_row['techlevel_count'] > 2)
		{
			$status[] = 'Selected design already has the max number (3) of blueprints for its current tech level.';
			$smarty->append('status', $status);
			blueprints_select();
			exit;
		}
		
		$design['time'] /= BLUEPRINT_TIME;
		$design['workers'] /= BLUEPRINT_TIME;
		$design['energy'] /= BLUEPRINT_TIME;
		$design['minerals'] /= BLUEPRINT_TIME;
		
		if ($design_name != 'weapon')
		{
			$weapons = array();
			
			for ($i = 0; $i < $design['weaponslots']; $i++)
			{
				if (!empty($_POST['weaponslot'][$i]))
				{
					@$weapons[$_POST['weaponslot'][$i]] += $_POST['weapons'][$i];
				}
			}
			
			if (!empty($weapons))
			{
				$weapon_size = 0;
				
				/*
					// This code increases build time and cost, which we don't want anymore.
					$weapon_time = 0;
					$weapon_cost = array(
						'workers' => 0, 
						'energy' => 0, 
						'minerals' => 0);
					
					$db_query = "SELECT `weaponblueprint_id`, `workers`, `energy`, `minerals`, `size`, `time` FROM `weaponblueprints` WHERE `weaponblueprint_id` IN ('" . implode("', '", array_keys($weapons)) . "')";
					$db_result = mysql_query($db_query);
					while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
					{
						$weapon_size += $db_row['size'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_time += $db_row['time'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_cost['workers'] += $db_row['workers'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_cost['energy'] += $db_row['energy'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_cost['minerals'] += $db_row['minerals'] * $weapons[$db_row['weaponblueprint_id']];
					}
				*/
				
				// Slight duplicate of the above commented code because we do still need the size information
				$db_query = "SELECT `weaponblueprint_id`, `size` FROM `weaponblueprints` WHERE `weaponblueprint_id` IN ('" . implode("', '", array_keys($weapons)) . "')";
				$db_result = mysql_query($db_query);
				while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$weapon_size += $db_row['size'] * $weapons[$db_row['weaponblueprint_id']];
					/*
						// This code increases build time and cost, which we don't want anymore.
						$weapon_cost['workers'] += $db_row['workers'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_cost['energy'] += $db_row['energy'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_cost['minerals'] += $db_row['minerals'] * $weapons[$db_row['weaponblueprint_id']];
						$weapon_time += $db_row['time'] * $weapons[$db_row['weaponblueprint_id']];
					*/
				}
				
				$weapon_size /= 10;
				
				if ($weapon_size > $design['weaponsload_base'])
				{
					$status[] = 'Unit can not hold that many weapons.';
					$smarty->append('status', $status);
					blueprints_select();
					exit;
				}
				
				$design['size_base'] += $weapon_size;
				
				/*
					// This code increases build time and cost, which we don't want anymore.
					$design['workers'] += $weapon_cost['workers'];
					$design['energy'] += $weapon_cost['energy'];
					$design['minerals'] += $weapon_cost['minerals'];
					$design['time'] += $weapon_time;
				*/
			}
			
			// I get the feeling something else was supposed to be here.
		}
		
		$insert_design = array(
			$design_name . 'design_id' => $design_id, 
			$design_name . 'concept_id' => $design[$design_name . 'concept_id'], 
			'round_id' => $_SESSION['round_id'], 
			'kingdom_id' => $_SESSION['kingdom_id'], 
			'name' => $name, 
			'time' => $design['time'], 
			'workers' => $design['workers'], 
			'energy' => $design['energy'], 
			'minerals' => $design['minerals'], 
			'mineralspread' => $design['mineralspread'], 
			'techlevel' => $design['techlevel_current']);
		
		if ($design_name != 'weapon')
		{
			$insert_design['weapons'] = $weapons;
		}
		
		foreach ($designs[$design_name] as $value)
		{
			$insert_design[$value] = $design[$value . '_base'];
		}
		
		$db_result = $sql->execute($design_name . 'blueprints', $insert_design);
		
		
		
		blueprints_list();
		exit;
	}
	
	function blueprints_deactivate()
	{
		global $smarty, $sql;
		
		$blueprints = array('army', 'navy', 'weapon');
		foreach ($blueprints as $value)
		{
			if (!empty($_POST[$value]))
			{
				$sql->set(array($value . 'blueprints', 'active', "raw:ABS(`" . $value . "blueprints`.`active` - 1)"));
				$sql->where(array(
					array($value . 'blueprints', $value . 'blueprint_id', $_POST[$value], 'IN'), 
					array($value . 'blueprints', 'kingdom_id', $_SESSION['kingdom_id'])
				));
				$db_result = $sql->execute();
//				 $db_result = mysql_query($db_query);
//				 while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
//				 {
//					 $results[] = $db_row[$value . 'blueprint_id'];
//				 }
//			 
//				 $db_query = "UPDATE `" . $value . "blueprints` SET `active` = ABS(`active` - 1) WHERE `" . $value . "blueprint_id` IN ('" . implode("', '", $results) . "')";
//				 $db_result = mysql_query($db_query);
			}
		}
		
		$status[] = '(De)Activated selected blueprint(s).';
		$smarty->append('status', $status);
		blueprints_list();
		exit;
	}
?>