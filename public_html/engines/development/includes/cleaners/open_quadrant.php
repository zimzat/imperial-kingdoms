<?php
	function open_quadrant()
	{
		global $sql;
		
		$quadrant_id = $_REQUEST['quadrant_id'];
		
		require_once(dirname(dirname(dirname(__FILE__))) . '/join.php');
		
		populatequadrant($quadrant_id);
	}
?>