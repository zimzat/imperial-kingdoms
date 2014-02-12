<?php
	if (!defined('IK_AUTHORIZED'))
	{
		define('IK_AUTHORIZED', true);
		require_once(dirname(__FILE__) . '/includes/init.php');
		
		prisoner_filter($_SESSION['player_id']);
		
		$valid_functions = array(
			'default' => 'designs_overview', 
			'designs_list', # Depreciated in favor of designs_overview
			'designs_info', 
			'designs_upgrade');
		$fn = validate_fn($valid_functions, __FILE__, __LINE__);
		
		$fn = substr($fn, 8);
		if ($fn == 'list') $fn = 'overview';
		
		$designs = new Designs($data, $smarty);
		$designs->$fn();
	}
	
	class Designs
	{
		var $data;
		var $smarty;
		var $sql;
		
		var $planet_id;
		var $concept_id;
		
		function Designs(&$data, &$smarty)
		{
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
			
			$this->planet_id = request_variable('planet_id');
			$this->concept_id = request_variable('concept_id');
			
			$this->data->updater->update($_SESSION['kingdom_id']);
		}
		
		// ###############################################
		// Show the list of upgradable designs
		function overview()
		{
			$designs = array('army', 'navy', 'weapon');
			
			$designlist = array();
			$beingupgraded = array();
			
			foreach ($designs as $type)
			{
				$db_query = "
					SELECT 
						`" . $type . "design_id`, 
						`name`, 
						`techlevel_current`, 
						`time` 
					FROM `" . $type . "designs` 
					WHERE 
						`round_id` = '" . $_SESSION['round_id'] . "' AND 
						`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
						`techlevel_current` < `techlevel_max` AND 
						`size_base` < `size_max`";
				
				$db_result = mysql_query($db_query);
				while($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					$designlist[$type][$db_row[$type . 'design_id']] = array(
						'name' => $db_row['name'], 
						'techlevel_upgrade' => $db_row['techlevel_current'] + 1, 
						'time' => format_time(timeparser($db_row['time'] * $_SESSION['round_speed']))
					);
				}
			}
			
			$db_query = "
				SELECT 
					`tasks`.`design_id`, 
					`tasks`.`number`, 
					`tasks`.`completion`, 
					`planets`.`name`, 
					`planets`.`planet_id` 
				FROM 
					`tasks`, 
					`planets` 
				WHERE 
					`planets`.`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
					`planets`.`round_id` = '" . $_SESSION['round_id'] . "' AND 
					`tasks`.`round_id` = `planets`.`round_id` AND 
					`planets`.`planet_id` = `tasks`.`planet_id` AND 
					`tasks`.`type` = '3' 
				ORDER BY `completion` DESC";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$beingupgraded[$designs[$db_row['number']]][$db_row['design_id']] = 'P#' . $db_row['planet_id'] . ' ' . $db_row['name'] . ' ' . format_time(timeparser($db_row['completion'] - microfloat()));
			}
			
			research_planets();
			
			$this->smarty->assign('designs', $designlist);
			$this->smarty->assign('beingupgraded', $beingupgraded);
			$this->smarty->display('designs_list.tpl');
		}
		
		
		function info()
		{
			$designs['main'] = array('army', 'navy', 'weapon');
			$designs['army'] = array('attack', 'defense', 'armor', 'hull', 'weaponsload');
			$designs['navy'] = array('attack', 'defense', 'armor', 'hull', 'weaponsload', 'cargo', 'speed');
			$designs['weapon'] = array('accuracy', 'areadamage', 'rateoffire', 'power', 'damage');
			
			foreach ($designs['main'] as $value)
			{
				if (!empty($_REQUEST[$value . 'design_id']))
				{
					$design_id = (int)$_REQUEST[$value . 'design_id'];
					$design_name = $value;
					break;
				}
			}
			
			if (empty($design_name))
			{
				error(__FILE__, __LINE__, 'DATA_NULL', 'Null design selected');
			}
			
			$db_query = "
				SELECT * 
				FROM `" . $design_name . "designs` 
				WHERE 
					`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
					`" . $design_name . "design_id` = '" . $design_id . "' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) == 0)
			{
				error(__FILE__, __LINE__, 'DATA_INVALID', 'Invalid design selected');
			}
			
			$design = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$design['mineralspread'] = unserialize($design['mineralspread']);
			
			$upgrades = array();
			foreach ($designs[$design_name] as $value)
			{
				// =IF(C1 < $B$2; ROUNDDOWN(C1 + (($B$4 / 100) * C1) + $B$3); "")
				if ($design[$value . '_base'] < $design[$value . '_max'])
				{
					$increment = floor((($design[$value . '_per'] / 100) * $design[$value . '_base']) + $design[$value . '_inc']);
					if ($design[$value . '_base'] + $increment > $design[$value . '_max'])
					{
						$increment = $design[$value . '_max'] - $design[$value . '_base'];
					}
					
					$sizeincrement = floor((($design[$value . '_size'] / 100) * $design['size_base']) + $design[$value . '_sizeinc']);
					if ($design['size_base'] + $sizeincrement > $design['size_max'])
					{
						$sizeincrement = $design['size_max'] - $design['size_base'];
					}
					
					if ($value == 'weaponsload')
					{
						$upgrades[$value]['name'] = 'Weapon Load';
					}
					else
					{
						$upgrades[$value]['name'] = ucfirst($value);
					}
					
					$upgrades[$value]['current'] = $design[$value . '_base'];
					$upgrades[$value]['increase'] = $increment;
					$upgrades[$value]['sizeincrease'] = $sizeincrement;
				}
			}
			
			if (!empty($design['mineralspread']))
			{
				$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
				foreach($design['mineralspread'] as $key => $value)
				{
					$resources['resources']['minerals'][$mineralnames[$key]] = format_number($design['minerals'] * ($value / 100), true);
				}
			}
			
			$resources['name'] = $design['name'];
			$resources['design_id'] = $design_id;
			$resources['type'] = $design_name;
			$resources['resources']['time'] = format_time(timeparser($design['time'] * $_SESSION['round_speed']));
			$resources['resources']['workers'] = $design['workers'];
			$resources['resources']['energy'] = $design['energy'];
			
			$resources['upgrades'] = $upgrades;
			
			research_planets();
			
			$this->smarty->assign('design', $resources);
			$this->smarty->display('designs_info.tpl');
			exit;
		}
		
		
		function upgrade()
		{
			$planet_id = $this->planet_id;
			
			if (isset($_POST['mode']) && $_POST['mode'] == 'js')
				$output_mode = 'javascript';
			else
				$output_mode = '';
			
			permissions_check(PERMISSION_PLANET, $planet_id, 'research');
			
			$designs['main'] = array(0 => 'army', 1 => 'navy', 2 => 'weapon');
			foreach ($designs['main'] as $key => $value)
			{
				if (!empty($_POST[$value . 'design_id']))
				{
					$design_id = (int)$_POST[$value . 'design_id'];
					$design_name = $value;
					$type = $key;
					break;
				}
			}
			
			$attribute = $_POST['attribute'];
			
			if (empty($design_name) || empty($attribute))
			{
				$status[] = 'No upgrade selected.';
				if ($output_mode == 'javascript')
				{
					echo 'alert(\'' . implode("\n", $status) . '\'); varUpgrading = false; varError = true;';
					exit;
				}
				
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			if ($attribute != mysql_real_escape_string($attribute))
			{
				error(__FILE__, __LINE__, 'DATA_INVALID', 'Invalid attribute selected');
			}
			
			$db_query = "
				SELECT 
					`time`, 
					`workers`, 
					`energy`, 
					`minerals`, 
					`mineralspread`, 
					`techlevel_current`, 
					`techlevel_max`, 
					`" . $attribute . "_base`, 
					`" . $attribute . "_max`, 
					`" . $attribute . "_inc`, 
					`" . $attribute . "_per`, 
					`" . $attribute . "_size`, 
					`" . $attribute . "_sizeinc`, 
					`size_base`, 
					`size_max` 
				FROM 
					`" . $design_name . "designs` 
				WHERE 
					`kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND 
					`" . $design_name . "design_id` = '" . $design_id . "' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
			if (mysql_num_rows($db_result) == 0)
			{
				error(__FILE__, __LINE__, 'DATA_INVALID', 'Invalid design selected');
			}
			$design = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$design['mineralspread'] = unserialize($design['mineralspread']);
			
			
			if ($design['techlevel_current'] >= $design['techlevel_max'])
			{
				$status[] = 'The tech level has been maxed out.';
			}
			
			if ($design[$attribute . '_base'] == $design[$attribute . '_max'])
			{
				$status[] = 'That attribute has been maxed out.';
			}
			
			if (!empty($status))
			{
				if ($output_mode == 'javascript')
				{
					echo 'alert(\'' . implode("\n", $status) . '\'); varUpgrading = false; varError = true;';
					exit;
				}
				
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			
			$db_query = "SELECT `planet_id` FROM `tasks` WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "' AND `type` IN ('2', '3') AND (`planet_id` = '" . $planet_id . "' OR `design_id` = '" . $design_id ."') LIMIT 2";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				if ($db_row['planet_id'] == $planet_id)
				{
					$status[] = 'That planet is already researching something.';
				}
				else
				{
					$status[] = 'That design is already being researched elsewhere';
				}
			}
			
			if (!empty($status))
			{
				if ($output_mode == 'javascript')
				{
					echo 'alert(\'' . implode("\n", $status) . '\'); varUpgrading = false; varError = true;';
					exit;
				}
				
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			
			$db_query = "SELECT `player_id`, `workers`, `energy`, `minerals`, `researchbonus` FROM `planets` WHERE `planet_id` = '" . $planet_id . "' LIMIT 1";
			$db_result = mysql_query($db_query);
			$planet = mysql_fetch_array($db_result, MYSQL_ASSOC);
			$planet['minerals'] = unserialize($planet['minerals']);
					
			$resources = array('workers', 'energy');
			foreach ($resources as $value)
			{
				$planet[$value] -= $design[$value];
				if ($planet[$value] < 0)
				{
					$status[] = 'Not enough ' . $value . '.';
				}
			}
			
			if (!empty($design['mineralspread']))
			{
				$mineralnames = array(0 => 'fe', 1 => 'o', 2 => 'si', 3 => 'mg', 4 => 'ni', 5 => 's', 6 => 'he', 7 => 'h');
				foreach ($design['mineralspread'] as $key => $value)
				{
					$mineral = ($value / 100) * $design['minerals'];
					$planet['minerals'][$key] -= $mineral;
					if ($planet['minerals'][$key] < 0)
					{
						$status[] = 'Not enough ' . $mineralnames[$key] . '.';
					}
				}
			}
			
			if (!empty($status))
			{
				if ($output_mode == 'javascript')
				{
					echo 'alert(\'' . implode("\n", $status) . '\'); varUpgrading = false; varError = true;';
					exit;
				}
				
				$this->smarty->append('status', $status);
				$this->overview();
				exit;
			}
			
			$completion = $design['time'] * $_SESSION['round_speed'] * ((100 - $planet['researchbonus']) / 100);
			
			$warptime = request_variable('warptime');
			if (!is_null($warptime))
			{
				data::initialize();
				
				$data_planet = &$this->data->planet($planet_id);
				
				if ($data_planet['warptime_research'] > $completion)
				{
					$data_planet['warptime_research'] -= $completion;
					$completion = 0;
				}
				else
				{
					$completion -= $data_planet['warptime_research'];
					$data_planet['warptime_research'] = 0;
				}
				
				$this->data->save();
			}
			
			
			$insert_design = array(
				'round_id' => $_SESSION['round_id'], 
				'kingdom_id' => $_SESSION['kingdom_id'], 
				'planet_id' => $planet_id, 
				'type' => 3, 
				'design_id' => $design_id, 
				'attribute' => $attribute, 
				'number' => $type, 
				'start' => microfloat(),
				'completion' => microfloat() + $completion);
			$db_result = $this->sql->execute('tasks', $insert_design);
			
			
			$this->sql->set(array(
				array('planets', 'researching', 1), 
				array('planets', 'workers', $planet['workers']), 
				array('planets', 'energy', $planet['energy']), 
				array('planets', 'minerals', serialize($planet['minerals']))));
			$this->sql->where(array('planets', 'planet_id', $planet_id));
			$this->sql->limit(1);
			$db_result = $this->sql->execute();
			
			
			if ($output_mode == 'javascript')
			{
				echo 'varUpgrading = true; varError = false;';
				exit;
			}
			
			$_SESSION['status'][] = 'Upgrade successfully started.';
			redirect('designs.php');
		}
	}
?>