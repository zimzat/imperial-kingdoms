<?php

class TechInfo {
	function concept($id) {
		$db_query = "
			SELECT `name`, `image`, `description` 
			FROM `concepts` 
			WHERE `concept_id` = '" . $id . "' 
			LIMIT 1";
		$valueStrings = array(
			'image' => '<img src="images/illustrations/%s" alt="" />');
		return $this->wrapInDiv($this->getQuery($db_query), $valueStrings);
	}
	
	function armyconcept($id) {
		$db_query = "
			SELECT `name`, `attack_base`, `defense_base`, `armor_base`, `hull_base`, 
				`weaponslots`, `weaponsperslot`, `weaponsload_base`, `size_base` 
			FROM `armyconcepts` 
			WHERE `armyconcept_id` = '" . $id . "' 
			LIMIT 1";
		$valueStrings = array(
			'attack_base' => $this->coatSpan('Attack Base:') . ' %s', 
			'defense_base' => $this->coatSpan('Defense Base:') . ' %s', 
			'armor_base' => $this->coatSpan('Armor Base:') . ' %s', 
			'hull_base' => $this->coatSpan('Hull Base:') . ' %s', 
			'weaponslots' => $this->coatSpan('Weapon Slots:') . ' %s', 
			'weaponsperslot' => $this->coatSpan('Weapons Per Slot:') . ' %s', 
			'weaponsload_base' => $this->coatSpan('Weapons Load Base:') . ' %s', 
			'size_base' => $this->coatSpan('Size Base:') . ' %s');
		return $this->wrapInDiv($this->getQuery($db_query), $valueStrings);
	}
	
	function navyconcept($id) {
		$db_query = "
			SELECT `name`, `attack_base`, `defense_base`, `armor_base`, `hull_base`, 
				`weaponslots`, `weaponsperslot`, `weaponsload_base`, `size_base`, 
				`cargo_base`, `speed_base` 
			FROM `navyconcepts` 
			WHERE `navyconcept_id` = '" . $id . "' 
			LIMIT 1";
		$valueStrings = array(
			'attack_base' => $this->coatSpan('Attack Base:') . ' %s', 
			'defense_base' => $this->coatSpan('Defense Base:') . ' %s', 
			'armor_base' => $this->coatSpan('Armor Base:') . ' %s', 
			'hull_base' => $this->coatSpan('Hull Base:') . ' %s', 
			'weaponslots' => $this->coatSpan('Weapon Slots:') . ' %s', 
			'weaponsperslot' => $this->coatSpan('Weapons Per Slot:') . ' %s', 
			'weaponsload_base' => $this->coatSpan('Weapons Load Base:') . ' %s', 
			'size_base' => $this->coatSpan('Size Base:') . ' %s', 
			'speed_base' => $this->coatSpan('Speed Base:') . ' %s', 
			'cargo_base' => $this->coatSpan('Cargo Base:') . ' %s');
		return $this->wrapInDiv($this->getQuery($db_query), $valueStrings);
	}
	
	function weaponconcept($id) {
		$db_query = "
			SELECT `name`, `accuracy_base`, `areadamage_base`, `rateoffire_base`, 
				`power_base`, `damage_base`, `size_base` 
			FROM `weaponconcepts` 
			WHERE `weaponconcept_id` = '" . $id . "' 
			LIMIT 1";
		$valueStrings = array(
			'accuracy_base' => $this->coatSpan('Accuracy Base:') . ' %s', 
			'areadamage_base' => $this->coatSpan('Area Damage Base:') . ' %s', 
			'rateoffire_base' => $this->coatSpan('Rate of Fire Base:') . ' %s', 
			'power_base' => $this->coatSpan('Power Base:') . ' %s', 
			'damage_base' => $this->coatSpan('Damage Base:') . ' %s', 
			'size_base' => $this->coatSpan('Size Base:') . ' %s');
		return $this->wrapInDiv($this->getQuery($db_query), $valueStrings);
	}
	
	function building($id) {
		$db_query = "
			SELECT `name`, `image`, `description` 
			FROM `buildings` 
			WHERE `building_id` = '" . $id . "' 
			LIMIT 1";
		$valueStrings = array(
			'image' => '<img src="images/illustrations/%s" alt="" />');
		return $this->wrapInDiv($this->getQuery($db_query), $valueStrings);
	}
	
	function getQuery($db_query) {
		$db_result = mysql_query($db_query);
		return mysql_fetch_array($db_result, MYSQL_ASSOC);
	}
	
	function wrapInDiv($array, $valueStrings=array()) {
		$output = '';
		foreach ($array as $key => $value) {
			if (empty($value)) {
				continue;
			}
			if (!empty($valueStrings[$key])) {
				$value = sprintf($valueStrings[$key], $value);
			}
			$output .= $this->coatDiv($value, $key, 'ikBlock');
		}
		return $output;
	}
	
	function coatDiv($content, $id='', $class='') {
		$div = "\t\t" . '<div';
		if ($class) {
			$div .= ' class="' . $class . '"';
		}
		if ($id) {
			$div .= ' id="' . $id . '"';
		}
		$div .= '>' . $content . '</div>' . "\n";
		return $div;
	}
	
	function coatSpan($content, $id='', $class='ikTitle') {
		$span = '<span';
		if ($class) {
			$span .= ' class="' . $class . '"';
		}
		if ($id) {
			$span .= ' id="' . $id . '"';
		}
		$span .= '>' . $content . '</span>';
		return $span;
	}
}

?>
