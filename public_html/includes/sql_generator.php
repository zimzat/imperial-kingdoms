<?php
/*
	// Example code.
	
	$sql = new SQL_Generator;
	
	
	// Overview of functions available
	// $sql->property();
	// $sql->select();
	// $sql->from();
	// $sql->leftjoin();
	// $sql->where();
	// $sql->orderby();
	// $sql->limit();
	// $sql->generate();
	// $sql->execute();
	// $sql->insert();
	// $sql->reset();
	
	
	// These can be called from outside the class to generate only a specific part of the query:
	// $sql->generate_select();
	// $sql->generate_update();
	// $sql->generate_from();
	// $sql->generate_leftjoin();
	// $sql->generate_where();
	// $sql->generate_orderby();
	// $sql->generate_limit();
	
	
	// All the SQL functions can be called multiple times 
	// to build different parts of the query selectively
	
	// All SQL functions accept a second parameter, $add, which defaults to true.
	// If made false, it will remove the argument instead of adding it
	
	// value fields beginning with 'raw:' will be used without any escaping or other processes
	
	// SELECT
	// Ommitting SELECT will default to SELECT * 
	$sql->select(array(
		// `table_1`.`field_1`
		array('table_1', 'field_1'), 
		
		// `table_1`.`field_2` AS 'alternative'
		array('table_1', 'field_2', 'alternative')
	));
	
	
	// FROM
	// The code will automatically fill in the FROM fields
	
	
	// LEFT JOIN
	$sql->leftjoin(array(
		// `table_3` ON `table_3`.`field_1` = `table_1`.`field_2`
		array('table_3', 'field_1', array('table_1', 'field_2')), 
		
//		 // `table_2 USING (`field_1`, `field_3`, `field_4`)
//		 array('table_2', array('field_1', 'field_3', 'field_4'))
	));
	
	// WHERE
	$sql->where(array(
		// `table_1`.`field_1` = 'value_1'
		array('table_1', 'field_1', 'value_1'), 
		
		// `table_1`.`field_2` = `table_2`.`field_1`
		array('table_1', 'field_2', array('table_2', 'field_1')), 
		
		// `table_2`.`field_2` > '2'
		array('table_2', 'field_2', 2, '>'), 
		
		// `table_2`.`field_1` IN ('value1', 'value3', 'value12')
		array('table_2', 'field_1', array('value1', 'value3', 'value12'), 'IN'), 
		
		// `table_3`.`field_1` > UNIX_TIMESTAMP(NOW()) + 300
		array('table_3', 'field_1', 'raw:UNIX_TIMESTAMP(NOW()) + 300', '>')
	));
	
	
	// ORDER BY
	$sql->orderby(array(
		// `table_1`.`field_2` ASC
		array('table_1', 'field_2', 'asc'), 
		
		// `table_3`.`field_2` DESC
		array('table_3', 'field_2', 'desc')
	));
	
	
	// LIMIT 1 (rows)
	$sql->limit(1);
	
	// LIMIT 3, 5 (offset, rows)
	$sql->limit(array(5, 3));
	
	
	// RAW
	// This allows you to append otherwise-ungeneratable SQL commands to the end of a part
	// These sections allow a raw append: select, from, leftjoin, update, set, where, orderby, limit
	$sql->raw(array(
		'select' => "UNIX_TIMESTAMP(NOW()) AS 'now'", 
		'where' => "AND (`table_1`.`field_1` = '60' OR `table_1`.`field_2` = '60')"
	));
	
	// Generate query
	// Passing 'false' keeps the query in memory for later modifications.
	// Ommitting it defaulst to 'true', meaing the query will be flushed after returned
	$db_query = $sql->generate(false);
	
	// Execute query
	// Bypass returning the query itself and return the mysql result
	// execute() accepts the same same parameter as generate() to keep the query in memory
	$db_result = $sql->execute();
	
	
	// INSERT
	$insert_table = array(
		'field_1' => 'value_1', 
		'field_2' => 'value_15', 
		
		// arrays will be serialized before inserted
		'field_4' => array(1, 3, 5, 78)
	);
	$db_query = $sql->insert('table', $insert_table);
	
	// execute() can also be used in this instance:
	$db_result = $sql->execute('table', $insert_table);
	
	
	// UPDATE
	// UPDATE can be ommited just like FROM
	// The update/set functions can generate queries using multiple tables, 
		// however MySQL prior to version 4.xx do not support them
	
	// SET
	$sql->set(array(
		// `table_1`.`field_1` = 'abc'
		array('table_1', 'field_1', 'abc'), 
		
		// `table_1`.`field_2` = `table_1`.`field_3`
		array('table_1', 'field_2', array('table_1', 'field_2'))
	));
	
	
	// UPDATE's WHERE clause uses the same syntax as above.
	
	
	// WHERE
	// LIMIT
	// These can also be used with SET and work the same as above:
	
	
	// generate / execute
	// The code automatically detects if it should return a UPDATE or SELECT statement based on if set has been used.
	
	
	// reset
	// calling reset resets the query in memory.
	// This is automatically done if generate or execute is not set false or is set to true
	$sql->reset();
*/

	class SQL_Generator
	{
		var $escapevalues = true;
		
		var $properties = array();
		var $select = array();
		var $from = array();
		var $leftjoin = array();
		
		var $update = array();
		var $set = array();
		
		var $where = array();
		
		var $groupby = array();
		var $having = array();
		var $orderby = array();
		
		var $limit = array();
		
		
// property
// Set or Unset properties to include in string
		function property($property, $add = true)
		{
			if (is_array($property))
			{
				foreach ($property as $value)
				{
					$this->properties($value);
				}
			}
			
			$property = str_replace(array(' ', '-'), '_', strtoupper($property));
			
			$presql = array('SMALL_RESULT', 'BIG_RESULT', 'BUFFER_RESULT', 'CACHE', 'NO_CACHE', 'CALC_FOUND_ROWS');
			if (in_array($property, $presql))
			{
				$property = 'SQL_' . $property;
			}
			
			if ($add)
			{
				$this->properties[$property] = true;
			}
			else
			{
				unset($this->properties[$property]);
			}
		}
		
// $field accepts $array or an array of unlimited nested $array
// $array = array('table' => 'name', 'field' => 'name', 'alias' => 'name');
		function select($field, $add = true)
		{
			if (is_array($field[0]))
			{
				foreach ($field as $value)
				{
					$this->select($value, $add);
				}
			}
			else
			{
				$temp = array_altkey($field, array('table', 'field', 'value'));
				if (empty($temp[2])) $temp[2] = '';
				
				if ($add)
				{
					$this->select[$temp[0]][$temp[1]] = $temp[2];
				}
				else
				{
					unset($this->select[$temp[0]][$temp[1]]);
				}
			}
		}
		
// $table accepts a string or array of strings
		function from($table, $add = true)
		{
			if (is_array($table))
			{
				foreach($table as $value)
				{
					$this->from($value, $add);
				}
			}
			else
			{
				if ($add)
				{
					$this->from[$table] = true;
				}
				else
				{
					unset($this->from[$table]);
				}
			}
		}
		
// leftjoin
// $array = array('table', 'field', array('table', 'field'));
		function leftjoin($join, $add = true)
		{
			if (is_array($join[0]))
			{
				foreach ($join as $value)
				{
					$this->leftjoin($value, $add);
				}
			}
			else
			{
				$temp = array_altkey($join, array('table', 'field', 'on'));
				
				if (is_array($temp[2]))
				{
					$temp[2] = array_altkey($temp[2], array('table', 'field'));
				}
				
				if ($add)
				{
					$this->leftjoin[$temp[0]][$temp[1]] = $temp[2];
				}
				else
				{
					unset($this->leftjoin[$temp[0]][$temp[1]]);
				}
			}
		}
		
		
// where
// $condition accepts $array or an array of unlimited nested $array
// $array = array('table' => 'name', 'field' => 'name', 'value' => $value, 'operator' => $operator);
// $value = 'value' OR array('table' => 'name', 'field' => 'name');
// $operator = '<' || '>' || '=' || 'IN';
		function where($condition, $add = true)
		{
			if (is_array($condition[0]))
			{
				foreach ($condition as $value)
				{
					$this->where($value, $add);
				}
			}
			else
			{
				$temp = array_altkey($condition, array('table', 'field', 'value', 'operator'));
				if (empty($temp[3])) $temp[3] = '=';
				
				if (is_array($temp[2]) && $temp[3] != 'IN')
				{
					$temp[2] = array_altkey($temp[2], array('table', 'field'));
				}
				
				if ($add)
				{
					$this->where[$temp[0]][$temp[1]][0] = $temp[2];
					$this->where[$temp[0]][$temp[1]][1] = $temp[3];
				}
				else
				{
					unset($this->where[$temp[0]][$temp[1]]);
				}
			}
		}
		
// orderby
// $order is $array or array of $array
// $array = array('table' => 'name', 'field' => 'name', 'direction' => 'asc/desc');
		function orderby($order, $add = true)
		{
			if (is_array($order[0]))
			{
				foreach ($order as $value)
				{
					$this->orderby($value, $add);
				}
			}
			else
			{
				$temp = array_altkey($order, array('table', 'field', 'direction'));
				
				if ($add)
				{
					$this->orderby[$temp[0]][$temp[1]] = $temp[2];
				}
				else
				{
					unset($this->orderby[$temp[0]][$temp[1]]);
				}
			}
		}
		
// limit
// $limit is an integer or $array
// $array = array('rowcount' => 30, 'offset' => 0);
		function limit($limit, $add = true)
		{
			if ($add)
			{
				if (is_array($limit))
				{
					$temp = array_altkey($limit, array('rowcount', 'offset'));
				}
				else
				{
					$temp[0] = $limit;
				}
				
				$this->limit = $temp;
			}
			else
			{
				unset($this->limit);
			}
		}
		
		
// $table accepts a string or array of strings
		function update($table, $add = true)
		{
			if (is_array($table))
			{
				foreach($table as $value)
				{
					$this->update($value, $add);
				}
			}
			else
			{
				if ($add)
				{
					$this->update[$table] = true;
				}
				else
				{
					unset($this->update[$table]);
				}
			}
		}
		
// set
// $set accepts $array or an array of unlimited nested $array
// $array = array('table' => 'name', 'field' => 'name', 'value' => $value);
// $value = 'value' || array('table' => 'name', 'field' => 'name');
		function set($set, $add = true)
		{
			if (is_array($set[0]))
			{
				foreach ($set as $value)
				{
					$this->set($value, $add);
				}
			}
			else
			{
				$temp = array_altkey($set, array('table', 'field', 'value'));
				
				if (is_array($temp[2]))
				{
					$temp[2] = array_altkey($temp[2], array('table', 'field'));
				}
				
				if ($add)
				{
					$this->set[$temp[0]][$temp[1]] = $temp[2];
				}
				else
				{
					unset($this->set[$temp[0]][$temp[1]]);
				}
			}
		}
		
// raw
// $raw = array(['select' => '',] ['from' => '',] ['leftjoin' => '',] ['where' => '',] ['orderby' => '',] ['limit' => '']);
		function raw($raw, $add = true)
		{
			if (!is_array($raw))
			{
				return;
			}
			
			$sections = array('select', 'from', 'leftjoin', 'update', 'set', 'where', 'orderby', 'limit');
			$whitespace = array(" ", "\n", "\r", "\t");
			
			foreach ($raw as $section => $code)
			{
				if (in_array($section, $sections))
				{
					if (!in_array($code{strlen($code) - 1}, $whitespace))
					{
						$code .= ' ';
					}
					
					if ($add)
					{
						if (empty($this->raw[$section])) $this->raw[$section] = '';
						
						$this->raw[$section] .= $code;
					}
					else
					{
						$this->raw[$section] = $code;
					}
				}
			}
		}
		
		
// Generate SQL
		function generate($flush = true)
		{
			$query = '';
			
			if (empty($this->set))
			{
				$query .= $this->generate_select();
				$query .= $this->generate_from();
				$query .= $this->generate_leftjoin();
				$query .= $this->generate_where();
				$query .= $this->generate_orderby();
				$query .= $this->generate_limit();
			}
			else
			{
				$query .= $this->generate_update();
				$query .= $this->generate_set();
				$query .= $this->generate_where();
				$query .= $this->generate_limit();
			}
			
			if ($flush)
			{
				$this->reset();
			}
			
			return $query;
		}
		
		function generate_select()
		{
			$query = '';
			
			$selectproperties = array(
				'ALL', 'DISTINCT', 'DISTINCTROW', 
				'HIGH_PRIORITY', 
				'STRAIGHT_JOIN', 
				'SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT', 
				array('SQL_CACHE', 'SQL_NO_CACHE'), 'SQL_CALC_FOUND_ROWS');
			
			$query .= 'SELECT ';
			
			foreach ($this->properties as $key => $value)
			{
				if (in_array($key, $selectproperties))
				{
					$query .= $key . ' ';
				}
			}
			
			$multiple = false;
			if (!empty($this->select))
			{
				foreach ($this->select as $table => $value)
				{
					foreach ($value as $field => $alias)
					{
						if ($multiple) $query .= ', ';
						$multiple = true;
						
						if (count($this->select) > 1 || count($value) > 1) $query .= "\n\t";
						
						$query .= '`' . $table . '`.`' . $field . '`';
						if (!empty($alias)) $query .= ' AS \'' . $alias . '\'';
					}
				}
				$query .= ' ';
			}
			else
			{
				$query .= '* ';
			}
			
			if (!empty($this->raw['select'])) $query .= $this->raw['select'];
			
			return $query;
		}
		
		function generate_from()
		{
			$query = '';
			
			if (!empty($this->select)) $this->from(array_keys($this->select));
			if (!empty($this->where)) $this->from(array_keys($this->where));
			if (!empty($this->orderby)) $this->from(array_keys($this->orderby));
			if (!empty($this->leftjoin)) $this->from(array_keys($this->leftjoin), false);
			
			if (!empty($this->from) || !empty($this->raw['from']))
			{
				$query .= "\n" . 'FROM ';
			}
			
			if (!empty($this->from))
			{
				$multiple = false;
				foreach ($this->from as $table => $value)
				{
					if ($multiple) $query .= ', ';
					$multiple = true;
					
					if (count($this->from) > 1) $query .= "\n\t";
					
					$query .= '`' . $table . '`';
				}
				$query .= ' ';
			}
			
			if (!empty($this->raw['from'])) $query .= $this->raw['from'];
			
			return $query;
		}
		
		function generate_leftjoin()
		{
			$query = '';
			
			if (!empty($this->leftjoin))
			{
				foreach ($this->leftjoin as $table => $temp)
				{
					foreach ($temp as $field => $on)
					{
						$query .= "\n" . 'LEFT JOIN `' . $table . '` ON `' . $table . '`.`' . $field . '` = `' . $on[0] . '`.`' . $on[1] . '` ';
					}
				}
			}
			
			if (!empty($this->raw['leftjoin'])) $query .= $this->raw['leftjoin'];
			
			return $query;
		}
		
		function generate_update()
		{
			$query = '';
			
			if (!empty($this->set)) $this->update(array_keys($this->set));
			if (!empty($this->where)) $this->update(array_keys($this->where));
			
			if (!empty($this->update) || !empty($this->raw['update']))
			{
				$query .= 'UPDATE ';
			}
			
			if (!empty($this->update))
			{
				$multiple = false;
				foreach ($this->update as $table => $value)
				{
					if ($multiple) $query .= ', ';
					$multiple = true;
					
					if (count($this->update) > 1) $query .= "\n\t";
					
					$query .= '`' . $table . '`';
				}
				$query .= ' ';
			}
			
			if (!empty($this->raw['update'])) $query .= $this->raw['update'];
			
			return $query;
		}
		
		function generate_set()
		{
			$query = '';
			
			if (!empty($this->set) || !empty($this->raw['set']))
			{
				$query .= "\n" . 'SET ';
			}
			
			if (!empty($this->set))
			{
				$multiple = false;
				foreach ($this->set as $table => $temp)
				{
					foreach ($temp as $field => $value)
					{
						if ($multiple) $query .= ', ';
						$multiple = true;
						
						if (count($this->set) > 1 || count($temp) > 1) $query .= "\n\t";
						
						$query .= '`' . $table . '`.`' . $field . '` = ';
						if (is_array($value))
						{
							$query .= '`' . $value[0] . '`.`' . $value[1] . '` ';
						}
						else
						{
							if (substr($value, 0, 4) == 'raw:')
							{
								$query .= substr($value, 4) . ' ';
							}
							else
							{
								if (is_array($value)) $value = serialize($value);
								if ($this->escapevalues) $value = mysql_real_escape_string($value);
								
								$query .= '\'' . $value . '\'';
							}
						}
					}
					
					$query .= ' ';
				}
			}
			
			if (!empty($this->raw['set'])) $query .= $this->raw['set'];
			
			return $query;
		}
		
		function generate_where()
		{
			$query = '';
			
			if (!empty($this->where) || !empty($this->raw['where']))
			{
				$query .= "\n" . 'WHERE ';
			}
			
			if (!empty($this->where))
			{
				$multiple = false;
				foreach ($this->where as $table => $temp)
				{
					foreach ($temp as $field => $value)
					{
						if ($multiple) $query .= 'AND ';
						$multiple = true;
						
						if (count($this->where) > 1 || count($temp) > 1) $query .= "\n\t";
						
						$query .= '`' . $table . '`.`' . $field . '` ' . $value[1] . ' ';
						if ($value[1] != 'IN' && is_array($value[0]))
						{
							$query .= '`' . $value[0][0] . '`.`' . $value[0][1] . '` ';
						}
						elseif ($value[1] == 'IN')
						{
							$multiple = false;
							
							$query .= "('";
							foreach ($value[0] as $wherevalue)
							{
								if ($multiple) $query .= "', '";
								$multiple = true;
								
								if (is_array($wherevalue)) $wherevalue = serialize($wherevalue);
								if ($this->escapevalues) $wherevalue = mysql_real_escape_string($wherevalue);
								
								$query .= $wherevalue;
							}
							$query .= "') ";
						}
						elseif ($value[1] == 'IS')
						{
							$query .= 'NULL ';
						}
						else
						{
							if (substr($value[0], 0, 4) == 'raw:')
							{
								$query .= substr($value[0], 4) . ' ';
							}
							else
							{
								if (is_array($value[0])) $value[0] = serialize($value[0]);
								if ($this->escapevalues) $value[0] = mysql_real_escape_string($value[0]);
								
								$query .= '\'' . $value[0] . '\' ';
							}
						}
					}
				}
			}
			
			if (!empty($this->raw['where'])) $query .= $this->raw['where'];
			
			return $query;
		}
		
		function generate_orderby()
		{
			$query = '';
			
			if (!empty($this->orderby) || !empty($this->raw['orderby']))
			{
				$query .= "\n" . 'ORDER BY ';
			}
			
			if (!empty($this->orderby))
			{
				$multiple = false;
				foreach ($this->orderby as $table => $temp)
				{
					foreach ($temp as $field => $direction)
					{
						if ($multiple) $query .= ', ';
						$multiple = true;
						
						if (count($this->orderby) > 1 || count($temp) > 1) $query .= "\n\t";
						
						$query .= '`' . $table . '`.`' . $field . '` ' . $direction;
					}
				}
				$query .= ' ';
			}
			
			if (!empty($this->raw['orderby'])) $query .= $this->raw['orderby'];
			
			return $query;
		}
		
		function generate_limit()
		{
			$query = '';
			
			if (!empty($this->limit) || !empty($this->raw['limit']))
			{
				$query .= "\n" . 'LIMIT ';
			}
			
			if (!empty($this->limit))
			{
				if (!empty($this->limit[1])) $query .= $this->limit[1] . ', ';
				$query .= $this->limit[0];
			}
			
			if (!empty($this->raw['limit'])) $query .= $this->raw['limit'];
			
			return $query;
		}
		
		
// insert
// $table == table name
// $values == associative array
		function insert($table, $values)
		{
			$query = 'INSERT INTO `' . $table . '` ' . "\n" . '(';
			
			$multiple = false;
			$insertkeys = array_keys($values);
			foreach ($insertkeys as $value)
			{
				if ($multiple) $query .= ', ';
				$multiple = true;
				
				$query .= "\n\t" . '`' . $value . '`';
			}
			
			$query .= "\n" . ') ' . "\n" . 'VALUES ' . "\n" . '(';
			
			$multiple = false;
			foreach ($values as $value)
			{
				if ($multiple) $query .= ', ';
				$multiple = true;
				
				$query .= "\n\t";
				
				if (!is_array($value) && substr($value, 0, 4) == 'raw:')
				{
					$query .= substr($value, 4);
				}
				else
				{
					if (is_array($value)) $value = serialize($value);
					if ($this->escapevalues) $value = mysql_real_escape_string($value);
					
					$query .= '\'' . $value . '\'';
				}
			}
			
			$query .= "\n" . ')';
			
			return $query;
		}
		
		
		function execute()
		{
			$arguments = func_num_args();
			
			if ($arguments <= 1)
			{
				if ($arguments == 1)
				{
					$flush = func_get_arg(0);
					$db_query = $this->generate($flush);
				}
				else
				{
					$db_query = $this->generate();
				}
			}
			elseif ($arguments == 2)
			{
				$table = func_get_arg(0);
				$insert = func_get_arg(1);
				$db_query = $this->insert($table, $insert);
			}
			else
			{
				return false;
			}
			
			return $this->query($db_query);
		}
		
		
		function query($db_query)
		{
			$db_result = mysql_query($db_query);
			
			if (mysql_errno())
			{
				error(__FILE__, __LINE__, 'INVALID_QUERY', 'Invalid SQL query<!-- ' . htmlspecialchars(mysql_error()) . ' -->');
			}
			
			return $db_result;
		}
		
// reset
		function reset()
		{
			$this->properties = array();
			$this->select = array();
			$this->from = array();
			$this->leftjoin = array();
			
			$this->update = array();
			$this->set = array();
			
			$this->where = array();
			
			$this->groupby = array();
			$this->having = array();
			$this->orderby = array();
			
			$this->raw = array();
			
			$this->limit = array();
		}
	}
?>