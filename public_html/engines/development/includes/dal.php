<?php
	class Data_Abstraction_Layer
	{
		var $data = array();
		var $data_old = array();
		
		var $sql;
		
		var $serialized = array();
		var $roundindependent = array();
		
		function Data_Abstraction_Layer()
		{
			$this->sql = new SQL_Generator;
		}
		
		function log($log, $type = 'GENERAL')
		{
			if (empty($log)) return false;
			if (is_array($log)) $log = serialize($log);
			
			$log['log'] = $log;
			$log['round_id'] = $_SESSION['round_id'];
			$log['kingdom_id'] = $_SESSION['kingdom_id'];
			$log['player_id'] = $_SESSION['player_id'];
			$log['user_id'] = $_SESSION['user_id'];
			$log['logtime'] = date('m-d-Y H-i-s');
			$log['type'] = $type;
			
			$db_query = $this->sql->insert('logs', $log);
			$db_result = mysql_query($db_query);
			if (!$db_result || mysql_affected_rows() == 0)
			{
				return mysql_error();
			}
			else
			{
				return true;
			}
		}
		
		function &item($name_item, $name_id, $item_id, $type = '')
		{
			if (is_array($item_id))
			{
				$item_array = array();
				if (!empty($type))
				{
					foreach ($item_id as $value)
					{
						if (empty($this->data[$name_item][$type][$value]))
						{
							$item_array[] = $value;
						}
						else
						{
							$items[$value] = &$this->data[$name_item][$type][$value];
						}
					}
				}
				else
				{
					foreach ($item_id as $value)
					{
						if (empty($this->data[$name_item][$value]))
						{
							$item_array[] = $value;
						}
						else
						{
							$items[$value] = &$this->data[$name_item][$value];
						}
					}
				}
				
				$this->get_items($name_item, $name_id, $item_array, $type);
				
				if (!empty($type))
				{
					foreach ($item_array as $value)
					{
						$items[$value] = &$this->data[$name_item][$type][$value];
					}
				}
				else
				{
					foreach ($item_array as $value)
					{
						$items[$value] = &$this->data[$name_item][$value];
					}
				}
				
				return $items;
			}
			else
			{
				if (!empty($type))
				{
					if (empty($this->data[$name_item][$type][$item_id]))
					{
						$this->get_items($name_item, $name_id, $item_id, $type);
					}
					
					$return = &$this->data[$name_item][$type][$item_id];
				}
				else
				{
					if (empty($this->data[$name_item][$item_id]))
					{
						$this->get_items($name_item, $name_id, $item_id, $type);
					}
					
					$return = &$this->data[$name_item][$item_id];
				}
				
				return $return;
			}
		}
		
		function get_items($name_item, $name_id, $items, $type = '')
		{
			if (empty($items)) return;
			
			if (!is_array($items)) $items = array($items);
			
			if (!empty($type))
			{
				foreach ($items as $key => $value)
				{
					if (!empty($this->data[$name_item][$type][$value]))
					{
						unset($items[$key]);
					}
				}
			}
			else
			{
				foreach ($items as $key => $value)
				{
					if (!empty($this->data[$name_item][$value]))
					{
						unset($items[$key]);
					}
				}
			}
			
			if (empty($items)) return;
			
			$this->sql->where(array($type . $name_item, $type . $name_id, $items, 'IN'));
			
			if (!in_array($type . $name_item, $this->roundindependent))
				$this->sql->where(array($type . $name_item, 'round_id', $_SESSION['round_id']));
			
			$this->sql->limit(count($items));
			$db_result = $this->sql->execute();
			if (!$db_result)
			{
				$this->log(mysql_error(), 'DB_ERROR_SELECT');
			}
			else
			{
				while ($item = mysql_fetch_array($db_result, MYSQL_ASSOC))
				{
					foreach ($this->serialized[$type . $name_item] as $value)
					{
						if (empty($item[$value])) $item[$value] = array();
						else $item[$value] = unserialize($item[$value]);
					}
					
					if (!empty($type))
					{
						$this->data[$name_item][$type][$item[$type . $name_id]] = $item;
						$this->data_old[$name_item][$type][$item[$type . $name_id]] = $item;
					}
					else
					{
						$this->data[$name_item][$item[$name_id]] = $item;
						$this->data_old[$name_item][$item[$name_id]] = $item;
					}
				}
			}
		}
		
		function save_item($name_item, $name_id, $type = '')
		{
			if (empty($type))
			{
				if (empty($this->data[$name_item])) $item_new = array();
				else $item_new = &$this->data[$name_item];
				
				if (empty($this->data_old[$name_item])) $item_old = array();
				else $item_old = &$this->data_old[$name_item];
			}
			else
			{
				if (empty($this->data[$name_item][$type])) $item_new = array();
				else $item_new = &$this->data[$name_item][$type];
				
				if (empty($this->data_old[$name_item][$type])) $item_old = array();
				else $item_old = &$this->data_old[$name_item][$type];
			}
			
			if ((empty($item_new) && empty($item_old)) || $item_old === $item_new)
			{
				return;
			}
			
			foreach ($item_new as $key => $value) if (is_null($value)) unset($item_new[$key]);
			
			$keys_old = array_keys($item_old);
			$keys_new = array_keys($item_new);
			
			$keys_inserted = array_diff($keys_new, $keys_old);
			$keys_deleted = array_diff($keys_old, $keys_new);
			$keys_updated = array_intersect($keys_old, $keys_new);
			
			if (!empty($keys_deleted))
			{
				$db_query = "DELETE FROM `" . $type . $name_item . "` WHERE `" . $type . $name_id . "` IN ('" . implode("', '", $keys_deleted) . "') LIMIT " . count($keys_deleted);
				$this->log[] = 'DELETE RAN: ' . $db_query;
				$db_result = mysql_query($db_query);
			}
			
			foreach ($keys_inserted as $key)
			{
				$db_query = $this->sql->insert($type . $name_item, $item_new[$key]);
				$db_result = mysql_query($db_query);
			}
			
			foreach ($keys_updated as $key)
			{
				if ($item_old[$key] === $item_new[$key]) continue;
				
				foreach ($item_new[$key] as $field => $fieldvalue)
				{
					if ($fieldvalue === $item_old[$key][$field]) continue;
					
					if (is_array($fieldvalue)) $fieldvalue = serialize($fieldvalue);
					
					$this->sql->set(array($type . $name_item, $field, $fieldvalue));
				}
				
				$this->sql->where(array($type . $name_item, $type . $name_id, $key));
				$this->sql->limit(1);
				$db_result = $this->sql->execute();
				
				if (!$db_result || mysql_affected_rows() == 0)
				{
					$this->log(mysql_error(), 'DB_ERROR_UPDATE');
				}
			}
		}
	}
?>