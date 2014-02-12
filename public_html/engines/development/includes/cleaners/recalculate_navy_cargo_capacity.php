<?php

class RecalculateNavyCargoCapacity {
	var $unitBlueprints = array();

	function beginProcess() {
		$roundId = (int)$_REQUEST['round_id'];
		$query = "
			SELECT `navygroup_id`, `units`, `cargo_max` 
			FROM `navygroups` 
			WHERE `round_id` = " . $roundId;
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result)) {
			$units = unserialize($row['units']);
			$cargo_max = '0';

			$this->checkDesignCache(array_keys($units));

			foreach ($units as $unit_id => $unit_count) {
				$cargo_max = bcadd($cargo_max, bcmul($this->unitBlueprints[$unit_id], $unit_count));
			}

			$updateQuery = "
				UPDATE `navygroups` 
				SET `cargo_max` = " . $cargo_max . " 
				WHERE `navygroup_id` = " . $row['navygroup_id'];
			$updateResult = mysql_query($updateQuery);
		}
	}

	function checkDesignCache($unitIds) {
		$missingIds = array_diff($unitIds, array_keys($this->unitBlueprints));
		if (empty($missingIds)) {
			return;
		}

		$query = "
			SELECT `navyblueprint_id`, `cargo` 
			FROM `navyblueprints` 
			WHERE `navyblueprint_id` IN (" . implode(', ', $missingIds) . ")";
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result)) {
			$this->unitBlueprints[$row['navyblueprint_id']] = $row['cargo'];
		}

		return;
	}
}

function recalculate_navy_cargo_capacity() {
	$recalculateNavyCargoCapacity = new RecalculateNavyCargoCapacity;
	$recalculateNavyCargoCapacity->beginProcess();
}

?>
