<?php
	class IK_Smarty {
		function IK_Smarty() {
			data::initialize();
		}
		
		function getHelpId($params, &$smarty_obj) {
			if (empty($params['file']) || empty($params['time'])) {
				return 'Error';
			}
			
			$this->sql->select(array('help_urls', 'id'));
			$this->sql->where(array(
				array('help_urls', 'file', $params['file']), 
				array('help_urls', 'time', $params['time'])));
			$this->sql->orderby(array('help_urls', 'id', 'ASC'));
			$db_result = $this->sql->execute();
			if (mysql_num_rows($db_result) == 0) {
				$helpUrl_insert = array(
					'file' => $params['file'], 
					'time' => $params['time']);
				$this->sql->execute('help_urls', $helpUrl_insert);
				return $this->getHelpId($params, $smarty_obj);
			} else if (mysql_num_rows($db_result) > 1) {
				$first = true;
				$delete_rows = array();
				while ($rows = mysql_fetch_array($db_result, MYSQL_ASSOC)) {
					if (!$first) {
						$delete_rows[] = $rows['id'];
					} else {
						$db_row = $rows;
						$first = false;
					}
				}
				if (!empty($delete_rows)) {
					$db_query = "
						DELETE FROM `help_urls` 
						WHERE `id` IN ('" . implode("', '", $delete_rows) . "')";
					$db_result = mysql_query($db_query);
				}
			} else {
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
			}
			
			return $db_row['id'];
		}
		
		function getHelpUrl($params, &$smarty_obj) {
			$helpUrl = 'http://www.imperialkingdoms.com/guide/index.php/In-Game_Help:';
			return $helpUrl . $this->getHelpId($params, $smarty_obj);
		}
	}
?>