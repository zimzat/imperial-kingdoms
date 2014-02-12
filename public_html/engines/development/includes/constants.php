<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	define('DATABASE', 'zimzatik');
//	 define('TABLE', 'xenocide_');

	define('SMARTY_DIR', dirname(__FILE__) . '/smarty/');
	
	define('REGEXP_NAME', '/^[\_\\d\\s]|_|[^\-\ \'\.\,\\d\\w]|[\\d]{3,}|[\-\.\'\,\_]{2,}|  |[^\\d\\w]$/i');
	define('REGEXP_NAME_PLANET', '/^[\_\\d\\s]|_|[^\-\ \'\.\,\\d\\w]|[\\d]{4,}|[\-\.\'\,\_]{3,}|  |[^\\d\\w]$/i');
	
	define('SCORE_PLANET', 1);
	define('SCORE_PLAYER', 2);
	define('SCORE_KINGDOM', 4);
	
	define('SCORE_FOOD', 0.001);
	define('SCORE_WORKERS', 0.002);
	define('SCORE_ENERGY', 0.003);
	define('SCORE_MINERALS', 0.004);
	
	define('TEAMS_SOLO', 0);
	define('TEAMS_BOTH', 1);
	define('TEAMS_TEAMS', 2);
	
	define('ATTACK_LIMIT', 0.2);
	
	define('PLANETSTATUS_EMPTY', 0);
	define('PLANETSTATUS_RESERVED', 1);
	define('PLANETSTATUS_OCCUPIED', 2);
	
	define('TASK_BUILD', 1);
	define('TASK_RESEARCH', 2);
	define('TASK_UPGRADE', 3);
	define('TASK_UNIT', 4);
	define('TASK_NAVY', 5);
	
	define('MINERAL_FE', 0);
	define('MINERAL_O', 1);
	define('MINERAL_SI', 2);
	define('MINERAL_MG', 3);
	define('MINERAL_NI', 4);
	define('MINERAL_S', 5);
	define('MINERAL_HE', 6);
	define('MINERAL_H', 7);
	define('MINERALS_ARRAY', serialize(array(
		MINERAL_FE => 'fe', 
		MINERAL_O => 'o', 
		MINERAL_SI => 'si', 
		MINERAL_MG => 'mg', 
		MINERAL_NI => 'ni', 
		MINERAL_S => 's', 
		MINERAL_HE => 'he', 
		MINERAL_H => 'h')));
	
	
	define('PERMISSION_PLAYER', 0);
	define('PERMISSION_PLANET', 1);
	define('PERMISSION_ARMY', 2);
	define('PERMISSION_NAVY', 3);
	
	define('PERMISSION_RESEARCH', 1);
	define('PERMISSION_BUILD', 2);
	define('PERMISSION_COMMISSION', 4);
	define('PERMISSION_MILITARY', 8);
	
	define('PROPOSITION_DESCRIPTION', 1);
	define('PROPOSITION_AVATAR', 2);
	define('PROPOSITION_PROMOTE', 3);
	define('PROPOSITION_DEMOTE', 4);
	define('PROPOSITION_EXECUTE', 5);
	define('PROPOSITION_ALLY', 6);
	define('PROPOSITION_MERGE', 7);
	define('PROPOSITION_WAR', 8);
	define('PROPOSITION_PEACE', 9);
	
	// written # - implemented
	define('NEWS_WAR', 1); // w 1 - i
	define('NEWS_PEACE', 2); // w 1 - i
	define('NEWS_ALLY', 3); // w 1 - i
	define('NEWS_FIRSTRESEARCH', 4); // w 1 - i
	define('NEWS_RESEARCH', 5); // w 1 - i
	define('NEWS_BUILDING', 6);
	define('NEWS_PLANETCONQUERED', 7); // w 1 - i
	define('NEWS_PLAYERCAPTURED', 8); // w 1 - i
	define('NEWS_KINGDOMDEFEATED', 9); // w 1 - i
	define('NEWS_COMMISSION', 10); // w 1
	define('NEWS_EXECUTION', 11); // w 1 - i
	define('NEWS_GAMEANNOUNCEMENT', 12);
	
	define('BLUEPRINT_TIME', 5);
	
	define('MAILSTATUS_DELETED', 0);
	define('MAILSTATUS_UNREAD', 1);
	define('MAILSTATUS_READ', 2);
	
	define('RANK_DIFFERENCE', 20);
	define('RANK_PRISONER', 0);
	define('RANK_GOVERNOR', 20);
	define('RANK_STEWARD', 40);
	define('RANK_COMMANDER', 60);
	define('RANK_SENATOR', 80);
	define('RANK_EMPEROR', 100);
?>