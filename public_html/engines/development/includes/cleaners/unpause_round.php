<?php
	function unpause_round()
	{
		global $sql;
		
		$round_id = $_REQUEST['round_id'];
		
		$sql->select(array(
			array('rounds', 'pause_time'), 
			array('rounds', 'stoptime')));
		$sql->where(array('rounds', 'round_id', $round_id));
		$sql->limit(1);
		$db_result = $sql->execute();
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		
		$fastforward = microfloat() - $db_row['pause_time'];
		
		if (!empty($_REQUEST['skip'])) $fastforward -= abs((int)$_REQUEST['skip']);
		
		$sql->set(array(
			array('rounds', 'stoptime', "raw:`rounds`.`stoptime` + $fastforward"), 
			array('rounds', 'pause_time', 0)));
		$sql->where(array('rounds', 'round_id', $round_id));
		$sql->limit(1);
		$db_result = $sql->execute();
		
		if ($db_result && mysql_affected_rows() > 0)
		{
			$unpause = array('combat' => 'completion', 'planets' => 'lastupdated', 'tasks' => 'completion');
			foreach ($unpause as $table => $field)
			{
				$sql->set(array($table, $field, "raw:`$table`.`$field` + $fastforward"));
				$sql->where(array($table, 'round_id', $round_id));
				$db_result = $sql->execute();
			}
		}
	}
?>