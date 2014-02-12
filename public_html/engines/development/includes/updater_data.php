<?php
	require_once('dal.php');
	
	class Updater_Data extends Data_Abstraction_Layer
	{
		
//   ----------------------------------------------------------------------------- {
		
		var $design_types = array('army', 'navy', 'weapon');
		
		var $serialized = array(
			'round' => array('quadrants', 'buildings', 'concepts', 'researched'), 
			'planets' => array('buildings', 'production', 'units', 'minerals', 'mineralsremaining', 'extractionrates'), 
			'kingdoms' => array('buildings', 'units', 'concepts', 'researched', 'planets', 'allies', 'enemies', 'members'), 
			'players' => array('planets', 'planets_permissions'), 
			'buildings' => array('mineralspread', 'features'), 
			'concepts' => array('grants', 'mineralspread'), 
			'armygroups' => array('units', 'targets'), 
			'navygroups' => array('units', 'targets', 'cargo'), 
			'armyblueprints' => array('mineralspread', 'weapons'), 
			'navyblueprints' => array('mineralspread', 'weapons'), 
			'weaponblueprints' => array('mineralspread', 'targets'), 
			'armydesigns' => array('mineralspread'), 
			'navydesigns' => array('mineralspread'), 
			'weapondesigns' => array('mineralspread'), 
			'tasks' => array(), 
			'propositions' => array(), 
			'users' => array(), 
		);
		
		var $roundindependent = array('buildings', 'concepts', 'armyconcepts', 'navyconcepts', 'weaponconcepts', 'users');
		
// } ----------------------------------------------------------------------------- {
		
		function Updater_Data()
		{
			$this->sql = new SQL_Generator;
		}
		
		function update_to()
		{
			if (empty($this->data['update_to']))
			{
				$this->data['update_to'] = microfloat();
			}
			
			return $this->data['update_to'];
		}
		
		function save()
		{
			$savable_data = array('round', 'kingdoms', 'planets', 'players', 'groups', 'designs', 'tasks', 'users');
			
			foreach ($savable_data as $save)
			{
				$save_data = 'save_' . $save;
				
				$this->$save_data();
				
				if (!empty($this->data_old[$save]) || !empty($this->data[$save]))
				{
					$this->data_old[$save] = $this->data[$save];
				}
			}
		}
		
// } ----------------------------------------------------------------------------- {
		
		function &round()
		{
			if (empty($this->data['round']))
			{
				$this->get_round();
			}
			
			$return = &$this->data['round'];
			
			return $return;
		}
		
		function &kingdom($kingdom_id)
		{
			$return = &$this->item('kingdoms', 'kingdom_id', $kingdom_id);
			
			return $return;
		}
		
		function &planet($planet_id)
		{
			$return = &$this->item('planets', 'planet_id', $planet_id);
			
			return $return;
		}
		
		function &player($player_id)
		{
			$return = &$this->item('players', 'player_id', $player_id);
			
			if (!empty($return))
			{
				if (is_array($player_id))
				{
					foreach (array_keys($return) as $player_id)
					{
						if (isset($return[$player_id]['planets']['current']))
						{
							$return[$player_id]['planet_current'] = $return[$player_id]['planets']['current'];
							unset($return[$player_id]['planets']['current']);
						}
					}
				}
				else
				{
					if (isset($return['planets']['current']))
					{
						$return['planet_current'] = $return['planets']['current'];
						unset($return['planets']['current']);
					}
				}
			}
			
			return $return;
		}
		
		function &building($building_id)
		{
			$return = &$this->item('buildings', 'building_id', $building_id);
			
			return $return;
		}
		
		function &concept($concept_id)
		{
			$return = &$this->item('concepts', 'concept_id', $concept_id);
			
			return $return;
		}
		
		function &group($type, $group_id)
		{
			if (empty($type))
			{
				return;
			}
			
			$return = &$this->item('groups', 'group_id', $group_id, $type);
			
			return $return;
		}
		
		function &blueprint($type, $blueprint_id)
		{
			if (empty($type))
			{
				return;
			}
			
			$return = &$this->item('blueprints', 'blueprint_id', $blueprint_id, $type);
			
			return $return;
		}
		
		function &design($type_enum, $design_id)
		{
			$type = $this->design_types[$type_enum];
			
			if (empty($type))
			{
				return;
			}
			
			$return = &$this->item('designs', 'design_id', $design_id, $type);
			
			return $return;
		}
		
		function &task($task_id)
		{
			$return = &$this->item('tasks', 'task_id', $task_id);
			
			return $return;
		}
		
		function &proposition($proposition_id)
		{
			$return = &$this->item('propositions', 'proposition_id', $proposition_id);
			
			return $return;
		}
		
		function &user($user_id)
		{
			$return = &$this->item('users', 'user_id', $user_id);
			
			return $return;
		}
		
// } ----------------------------------------------------------------------------- {
		
		function get_round()
		{
			$this->sql->where(array('rounds', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			$round = mysql_fetch_array($db_result, MYSQL_ASSOC);
			
			foreach ($this->serialized['round'] as $unserialize)
				$round[$unserialize] = unserialize($round[$unserialize]);
			
			$round['speed'] /= 1000;
			$round['resourcetick'] /= 1000;
			$round['combattick'] /= 1000;
			
			$this->data['round'] = $round;
			$this->data_old['round'] = $round;
		}
		
// } ----------------------------------------------------------------------------- {
		
		function save_round()
		{
			if (empty($this->data['round']) && empty($this->data['round'])) return;
			if ($this->data['round']['researched'] === $this->data_old['round']['researched']) return;
			
			$this->sql->set(array('rounds', 'researched', serialize($this->data['round']['researched'])));
			$this->sql->where(array('rounds', 'round_id', $_SESSION['round_id']));
			$db_result = $this->sql->execute();
			
			if (!$db_result || mysql_affected_rows() == 0)
			{
				$this->log(mysql_error(), 'DB_ERROR_UPDATE');
			}
		}
		
		function save_kingdoms()
		{
			$this->save_item('kingdoms', 'kingdom_id');
		}
		
		function save_planets()
		{
			$this->save_item('planets', 'planet_id');
		}

		function save_players()
		{
			$this->save_item('players', 'player_id');
		}
		
		function save_designs()
		{
			$this->save_item('designs', 'design_id', 'weapon');
			$this->save_item('designs', 'design_id', 'army');
			$this->save_item('designs', 'design_id', 'navy');
		}
		
		function save_groups()
		{
			$this->save_item('groups', 'group_id', 'army');
			$this->save_item('groups', 'group_id', 'navy');
		}
		
		function save_tasks()
		{
			$this->save_item('tasks', 'task_id');
		}
		
		function save_propositions()
		{
			$this->save_item('propositions', 'proposition_id');
		}
		
		function save_users()
		{
			$this->save_item('users', 'user_id');
		}
		
// } -----------------------------------------------------------------------------
		
	}
?>