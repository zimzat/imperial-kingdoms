<?php
	class data
	{
		var $data;
		var $smarty;
		var $sql;
		
		function initialize()
		{
			global $data, $smarty, $log;
			
			unset($this->log, $this->data, $this->smarty, $this->sql);
			
			$this->log = &$log;
			$this->data = &$data;
			$this->smarty = &$smarty;
			$this->sql = new SQL_Generator;
		}
	}
?>