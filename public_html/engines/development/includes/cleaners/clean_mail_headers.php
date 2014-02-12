<?php
	function clean_mail_headers()
	{
		global $sql;
		
		$sql->select(array(
			array('mail', 'mail_id'), 
			array('mail', 'subject')));
		$sql->where(array('mail', 'subject', 'Re: Re: %', 'LIKE'));
		$db_result = $sql->execute();
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$subject = preg_replace('/(Re: )+(.*)/i', 'Re: \\2', $db_row['subject']);
			
			$sql->set(array('mail', 'subject', $subject));
			$sql->where(array('mail', 'mail_id', $db_row['mail_id']));
			$sql->execute();
		}
	}
?>