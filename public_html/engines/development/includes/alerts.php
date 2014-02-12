<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	class alerts
	{
		var $alert_change;
		
		function alerts()
		{
			data::initialize();
		}
		
		function get_alerts()
		{
			$alert['mail'] = $this->mail();
			$alert['forum'] = $this->forum();
			$alert['combat'] = $this->combat();
			
			$this->smarty->assign('alert', $alert);
			
			if (!empty($this->alert_change))
			{
				if (!empty($_SESSION['admin']))
				{
					$user = $this->data->user($_SESSION['user_id']);
					if (!$user['admin'])
					{
						return;
					}
				}
				$this->data->save();
			}
		}
		
		function mail()
		{
			$player =& $this->data->player($_SESSION['player_id']);
			
			if (basename($_SERVER['PHP_SELF']) == 'mail.php')
			{
				if ($player['mail'] != 0)
				{
					$this->alert_change['mail'] = true;
				}
				$player['mail'] = 0;
			}
			
			return $player['mail'];
		}
		
		function forum()
		{
			$player =& $this->data->player($_SESSION['player_id']);
			
			if (basename($_SERVER['PHP_SELF']) == 'forum.php')
			{
				if ($player['forum'] != 0)
				{
					$this->alert_change['forum'] = true;
				}
				$player['forum'] = 0;
			}
			
			return $player['forum'];
		}
		
		function combat()
		{
			$kingdom = $this->data->kingdom($_SESSION['kingdom_id']);
			$player = $this->data->player($_SESSION['player_id']);
			
			$combatalert = false;
			foreach (array('army', 'navy') as $type)
			{
				$this->sql->select(array(
					array($type . 'groups', $type . 'group_id', 'group_id'), 
				));
				$this->sql->where(array(
					array($type . 'groups', 'kingdom_id', $_SESSION['kingdom_id'], '!='), 
					array($type . 'groups', 'units', 'a:0:{}', '<>'), 
					array($type . 'groups', 'planet_id', array_keys($kingdom['planets']), 'IN'), 
				));
				$db_result = $this->sql->execute();
				if (mysql_num_rows($db_result) > 0)
				{
					$combatalert = true;
					break;
				}
			}
			
			if ((bool)$player['combat'] != $combatalert)
			{
				$db_query = "UPDATE `players` SET `combat` = '" . ((int)$combatalert) . "' WHERE `kingdom_id` = '" . $_SESSION['kingdom_id'] . "'";
				$db_result = mysql_query($db_query);
			}
			
			return $combatalert;
		}
	}
?>