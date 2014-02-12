<?php
	function recalculate_scores()
	{
		global $sql;
		
		$db_query = "SELECT * FROM `buildings`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$score = ($db_row['workers'] * SCORE_WORKERS) + ($db_row['energy'] * SCORE_ENERGY) + ($db_row['minerals'] * SCORE_MINERALS);
			
			$score_amounts['buildings'][$db_row['building_id']] = $score;
			
			$db_query_blueprint = "UPDATE `buildings` SET `score` = '" . $score . "' WHERE `building_id` = '" . $db_row['building_id'] . "' LIMIT 1";
			$db_result_blueprint = mysql_query($db_query_blueprint);
		}
		
		$db_query = "SELECT * FROM `concepts`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$score = ($db_row['workers'] * SCORE_WORKERS) + ($db_row['energy'] * SCORE_ENERGY) + ($db_row['minerals'] * SCORE_MINERALS);
			
			$score_amounts['concepts'][$db_row['concept_id']] = $score;
			
			$db_query_blueprint = "UPDATE `concepts` SET `score` = '" . $score . "' WHERE `concept_id` = '" . $db_row['concept_id'] . "' LIMIT 1";
			$db_result_blueprint = mysql_query($db_query_blueprint);
		}
		
		// ##################################################
		// Add score to blueprints
		foreach (array('army', 'navy', 'weapon') as $type)
		{
			$db_query = "SELECT * FROM `" . $type . "blueprints`";
			$db_result = mysql_query($db_query);
			while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
			{
				$score = ($db_row['workers'] * SCORE_WORKERS) + ($db_row['energy'] * SCORE_ENERGY) + ($db_row['minerals'] * SCORE_MINERALS);
				$score_amounts['blueprints'][$type][$db_row[$type . 'blueprint_id']] = $score;
				
				$db_query_update = "
					UPDATE `" . $type . "blueprints` 
					SET `score` = '" . $score . "' 
					WHERE `" . $type . "blueprint_id` = '" . $db_row[$type . 'blueprint_id'] . "' 
					LIMIT 1";
				$db_result_update = mysql_query($db_query_update);
			}
		}
		
		unset($score, $time);
		
		print_r($score_amounts);
		
		$db_query = "SELECT * FROM `planets`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['minerals'] = unserialize($db_row['minerals']);
			$db_row['buildings'] = unserialize($db_row['buildings']);
			$db_row['units'] = unserialize($db_row['units']);
			
			$score_temp = array(
				'planet' => 0, 
				'player' => 0, 
				'kingdom' => 0
			);
			$score_temp['planet'] += ($db_row['food'] * SCORE_FOOD) + 
				($db_row['workers'] * SCORE_WORKERS) + 
				($db_row['energy'] * SCORE_ENERGY) + 
				(array_sum($db_row['minerals']) * SCORE_MINERALS);
			
			foreach ($db_row['buildings'] as $building_id => $count)
			{
				if ($building_id == 9 || $building_id == 8) continue;
				$score_temp['planet'] += $score_amounts['buildings'][$building_id] * $count * 2;
			}
			
			foreach ($db_row['units'] as $type => $units)
			{
				foreach ($units as $unit_id => $count)
				{
					$score_temp['player'] += $score_amounts['blueprints'][$type][$unit_id] * $count * 2;
				}
			}
			
			if (empty($score['players'][$db_row['player_id']]))
			{
				$score['players'][$db_row['player_id']] = 0;
			}
			if (empty($score['kingdoms'][$db_row['kingdom_id']]))
			{
				$score['kingdoms'][$db_row['kingdom_id']] = 0;
			}
			if (empty($resources[$db_row['kingdom_id']]))
			{
				$resources[$db_row['kingdom_id']] = array(
					'food' => 0, 
					'workers' => 0, 
					'energy' => 0, 
					'minerals' => 0, 
					'foodrate' => 0, 
					'workersrate' => 0, 
					'energyrate' => 0, 
					'mineralsrate' => 0
				);
			}
			
			$resources[$db_row['kingdom_id']]['food'] += $db_row['food'];
			$resources[$db_row['kingdom_id']]['workers'] += $db_row['workers'];
			$resources[$db_row['kingdom_id']]['energy'] += $db_row['energy'];
			$resources[$db_row['kingdom_id']]['minerals'] += array_sum($db_row['minerals']);
			$resources[$db_row['kingdom_id']]['foodrate'] += $db_row['foodrate'];
			$resources[$db_row['kingdom_id']]['workersrate'] += $db_row['workersrate'];
			$resources[$db_row['kingdom_id']]['energyrate'] += $db_row['energyrate'];
			$resources[$db_row['kingdom_id']]['mineralsrate'] += $db_row['mineralsrate'];
			
			$score['planets'][$db_row['planet_id']] = $score_temp['planet'];
			$score['players'][$db_row['player_id']] += $score_temp['planet'] + $score_temp['player'];
			$score['kingdoms'][$db_row['kingdom_id']] += $score_temp['planet'] + $score_temp['player'] + $score_temp['kingdom'];
		}
		
		$db_query = "SELECT * FROM `kingdoms`";
		$db_result = mysql_query($db_query);
		while ($db_row = mysql_fetch_array($db_result, MYSQL_ASSOC))
		{
			$db_row['researched'] = unserialize($db_row['researched']);
			
			if (empty($score['kingdoms'][$db_row['kingdom_id']]))
			{
				$score['kingdoms'][$db_row['kingdom_id']] = 0;
			}
			
			if (empty($db_row['researched']))
			{	
				continue;
			}
			
			foreach ($db_row['researched'] as $concept_id => $value)
			{
				$score['kingdoms'][$db_row['kingdom_id']] += $score_amounts['concepts'][$concept_id] * 2;
			}
		}
		
		
	
		foreach ($score['planets'] as $planet_id => $score_new)
		{
			$db_query = "
				UPDATE `planets` 
				SET 
					`score` = '" . $score_new.  "', 
					`score_peak` = '" . $score_new . "' 
				WHERE `planet_id` = '" . $planet_id . "' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
		}
		
		foreach ($score['players'] as $player_id => $score_new)
		{
			$db_query = "
				UPDATE `players` 
				SET 
					`score` = '" . $score_new.  "', 
					`score_peak` = '" . $score_new . "' 
				WHERE `player_id` = '" . $player_id . "' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
		}
		
		foreach ($score['kingdoms'] as $kingdom_id => $score_new)
		{
			$sql->set(array(
				array('kingdoms', 'score', $score_new), 
				array('kingdoms', 'score_peak', $score_new)));
			$sql->where(array('kingdoms', 'kingdom_id', $kingdom_id));
			$sql->limit(1);
			$db_result = $sql->execute();
		}
		
		foreach ($resources as $kingdom_id => $resource)
		{
			$db_query = "
				UPDATE `kingdoms` 
				SET 
					`food` = '" . $resource['food'] . "', 
					`workers` = '" . $resource['workers'] . "', 
					`energy` = '" . $resource['energy'] . "', 
					`minerals` = '" . $resource['minerals'] . "', 
					`foodrate` = '" . $resource['foodrate'] . "', 
					`workersrate` = '" . $resource['workersrate'] . "', 
					`energyrate` = '" . $resource['energyrate'] . "', 
					`mineralsrate` = '" . $resource['mineralsrate'] . "' 
				WHERE `kingdom_id` = '" . $kingdom_id . "' 
				LIMIT 1";
			$db_result = mysql_query($db_query);
		}
	}
?>