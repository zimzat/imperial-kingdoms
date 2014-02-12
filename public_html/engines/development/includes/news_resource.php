<?php
	class Smarty_News_Resource
	{
		function get_template(&$tpl_name, &$tpl_source, &$smarty_obj)
		{
			$sql = new SQL_Generator;
			
			$sql->select(array(
				array('news_entries', 'news_entry_id'), 
				array('news_entries', 'entry')));
			$sql->where(array('news_entries', 'news_entry_id', $tpl_name));
			$db_result = $sql->execute();
			
			if (mysql_num_rows($db_result))
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$tpl_source = $db_row['entry'];
				return true;
			}
			else
			{
				return false;
			}
		}
		
		function get_timestamp(&$tpl_name, &$tpl_timestamp, &$smarty_obj)
		{
			$sql = new SQL_Generator;
			
			$sql->select(array(
				array('news_entries', 'news_entry_id'), 
				array('news_entries', 'lastmodified')));
			$sql->where(array('news_entries', 'news_entry_id', $tpl_name));
			$db_result = $sql->execute();
			
			if (mysql_num_rows($db_result))
			{
				$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
				$tpl_timestamp = $db_row['lastmodified'];
				return true;
			}
			else
			{
				return false;
			}
		}
		
		function get_secure($tpl_name, &$smarty_obj)
		{
			// assume all templates are secure
			return true;
		}
		
		function get_trusted($tpl_name, &$smarty_obj)
		{
			// not used for templates
		}
	}
	
	$smarty->register_resource('news', array('Smarty_News_Resource', 'get_template', 'get_timestamp', 'get_secure', 'get_trusted'));
?>