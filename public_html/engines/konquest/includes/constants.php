<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	define('REGEXP_NAME', '/^[\_\\d\\s]|_|[^\-\ \'\.\,\\d\\w]|[\\d]{3,}|[\-\.\'\,\_]{2,}|  |[^\\d\\w]$/i');
?>