<?php
	// ###############################################
	// Make sure anything trying to call this is authorized
	if (!defined('IK_AUTHORIZED'))
	{
		die('Invalid security clearance.');
	}
	
	class NPC
	{
		var $round_id;
		var $player_id;
		var $kingdom_id;
		var $planet_id;
		
		function NPC($round_id, $player_id, $kingdom_id)
		{
			$this->round_id = $round_id;
			$this->player_id = $player_id;
			$this->kingdom_id = $kingdom_id;
		}
		
		function build($planet)
		{
			// Figure out what we need and what's available to us.
			// Food -> Workers -> Energy -> Minerals
			// Can we afford a LIC/HC/0g?
			
			
		}
		
		function research()
		{
			// Primary goal is to get better resource buildings
			// Secondary goal is to make LIC
			// Ignore military branch
			
			// See what is available to us.
			// What do we need the most. Give concepts a 'rating' based on criteria.
			// 
			
//			 What are we currently able to research? Food/Workers/Energy/Minerals of each concept.
//			 Where are we currently able to research from? Food/Workers/Energy/Minerals of each planet.
//			 Match one to the best of the other according to preference (food, workers, energy, minerals, 
			
			
//			 Find the thing that gives food the most and see what it needs.
			
		}
	}
?>