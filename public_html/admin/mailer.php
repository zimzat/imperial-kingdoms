<?php
	if (empty($_GET['password']) || $_GET['password'] != 'testing-admin-password-here') exit;
	
	define('IK_AUTHORIZED', true);
	require_once(dirname(dirname(__FILE__)) . '/includes/init.php'); 
	include_once(dirname(dirname(__FILE__)) . '/includes/sql_generator.php');
	$sql = new SQL_Generator;
	
	if (empty($_REQUEST['pass']) || $_REQUEST['pass'] != 'deathabounds') exit;
	
	$subject = 'Imperial Kingdoms: 2x Rapid Rounds Saturday (2006-05-13)';
	
	$message = wordwrap("Dear Imperial Kingdoms Players:\n\nI apologize for the lack of any rounds or development over the last few weeks. I have been busy applying for the Google Summer of Code and getting involved in the project I applied for. They won't have a response if I've been chosen to do a project until the 23rd so I'll be doing some development in the mean time.\n\nNEW ROUNDS!\nWe'll be running two rapid rounds Saturday. One will be before and one after noon CST. This will allow our players from different time zones to get a chance to play, and the hard core players who have the time can get double the dosage. Please see the front page for specific times.\nhttp://www.imperialkingdoms.com/\n\nThe rounds will have a couple of feature improvements. The unit creation speed and resource cost formula has been improved with a larger base time but smaller increments and max time (by a lot). They will also have a modified combat formula that caps the effect of rate of fire related to kills and area damage.\n\nSunday we're also looking at the start of a month long round. A week or two after that, depending on how things go, we may also be looking at a two month long round.\n\n--\n\nYou have received this email because you are a registered user of Imperial Kingdoms. These emails are sent to notify you of important notices, rounds, and milestones. If you do not wish to receive these emails anymore please reply to this email asking to be unsubscribed from notifications.", 70);
	
//	 $message = wordwrap("Imperial Kingdoms is proud to announce a new long-term round has started. This round will run for 160 days at a slightly slow speed as last round. New features, including a rewritten combat system and the ability to see and change your building queues, are introduced in this round, including many other bug fixes. To join the game, head to http://www.imperialkingdoms.com/\n\nA rapid round is also being scheduled later this weekend.\n\n--\n\nYou have received this email because you are a registered user of Imperial Kingdoms. These emails are sent to notify you of important notices, rounds, and milestones. If you do not wish to receive these emails anymore please reply to this email saying so.", 70);
	
	$headers = 
		'MIME-Version: 1.0' . "\r\n" . 
		'X-Sender-IP: ' . $_SERVER['REMOTE_ADDR'] . "\r\n" . 
		'From: Imperial Kingdoms <admin@imperialkingdoms.com>' . "\r\n" . 
		'Reply-To: Imperial Kingdoms <admin@imperialkingdoms.com>' . "\r\n" . 
		'X-Mailer: PHP/' . phpversion() . "\r\n";
	
	$emails = array('abs5656@rit.edu', 'aksel.olsen@pbe.oslo.kommune.no', 'soulmirago@hotmail.com');
	
	$sql->property('distinct');
	$sql->select(array('users', 'email'));
//	 $sql->where(array('users', 'user_id', 1));
	$db_results = $sql->execute();
	
	while ($db_row = mysql_fetch_array($db_results, MYSQL_ASSOC))
	{
		if (in_array($db_row['email'], $emails)) continue;
		
		$to = $db_row['email'];
		
		echo '<p>Sending to ' . $to . ' ... ';
		
		$result = mail($to, $subject, $message, $headers);
		if ($result == true) $result = 'Sent';
		else $result = 'Failed';
		
		echo '<b>' . $result . "</b></p>\n\n";
	}
?>
