<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	if (!empty($_REQUEST['concept_id']))
	{
		$concept_id = (int)$_REQUEST['concept_id'];
		
		$techtree = array(
			$concept_id => array()
		);
	}
	else
	{
		$techtree = array(
			2 => array(), 
			27 => array(), 
			33 => array(), 
			51 => array()
		);
	}
	
	ksort($techtree);
	
	$cacheFile = 'cache/tree-' . implode('-', array_keys($techtree)) . '.html';
	
	if (file_exists($cacheFile)) {
		readfile($cacheFile);
		exit;
	}
	
	foreach ($techtree as $key => $value)
	{
		$techtree[$key] = exploreconcepts($key);
	}
	
	ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
	"http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
	<head>
		<title>Imperial Kingdoms - Dynamic Technology Tree</title>
		
		<!-- Javascript utilities file for the tree-->
		<script src="styles/global/utils.js" type="text/javascript"></script>
		
		<!-- basic Javascript file for the tree-->
		<script src="styles/global/tree.js" type="text/javascript"></script>
		
		<!-- basic CSS file for the tree-->
		<link href="styles/global/tree.css" type="text/css" rel="stylesheet" />
		
		<!-- CSS file for the lines in the tree-->
		<link href="styles/global/tree-lines.css" type="text/css" rel="stylesheet" />
		
		<script type="text/javascript">
			function init() {
				var designtree = new Zapatec.Tree("designtree", { dynamic: true });
				designtree.onItemSelect = function(item_id) {
					var itemConsole = document.getElementById("itemConsoleFrame");
					itemConsole.src = '/techinfo.php?itemId=' + item_id;
				};
				
				designtree.config.expandOnLabel = false;
				designtree.config.selectOnExpand = false;
			}
		</script>
	</head>
	<body>
		<div style="float: right;">
			<a href="javascript:Zapatec.Tree.all['designtree'].collapseAll()">Collapse all</a> -- Collapse the whole tree and only show the top branches<br>
			<a href="javascript:Zapatec.Tree.all['designtree'].expandAll()">Expand all</a>  -- Expand the tree and show all branches. <br>
			<div id="itemConsole">
				<iframe id="itemConsoleFrame" height="300px" width="100%"></iframe>
			</div>
		</div>
		<div style="float: left;">
<?php
	$indention = 2;
	$indentString = '  ';
	ul_generate($techtree, ' id="designtree"');
?>
		</div>
		<script type="text/javascript">
			init();
		</script>
	</body>
</html>
<?php

	$contents = ob_get_contents();
	$cacheHandle = fopen($cacheFile, 'w');
	fwrite($cacheHandle, $contents);
	fclose($cacheHandle);
	chmod($cacheFile, 0777);

	ob_end_flush();

	function ul_generate($array, $ul_id = '')
	{
		global $indention;
		
		$indention++;
		
		echo indent() . '<ul' . $ul_id . '>' . "\n";
		
		foreach ($array as $item)
		{
			$indention++;
			
			echo indent() . '<li id="' . $item['type'] . ':' . $item['id'] . '">' . "\n";
			
			echo indent(1) . getIcon($item['type']) . "\n";
			
			echo indent(1) . $item['name'] . "\n";
			
			if (!empty($item['children'])) {
				echo ul_generate($item['children']);
			}
			
			echo indent() . '</li>' . "\n";
			
			$indention--;
		}
		
		echo indent() . '</ul>' . "\n";
		
		$indention--;
	}
	
	function getIcon($type) {
		$iconHtml = '<img src="images/symbols/16x16/';
		switch ($type) {
		case 'concept':
			$iconHtml .= 'flask.gif';
			break;
		case 'armyconcept':
			$iconHtml .= 'armyunits.gif';
			break;
		case 'navyconcept':
			$iconHtml .= 'navyunits.gif';
			break;
		case 'weaponconcept':
			$iconHtml .= 'weapon.gif';
			break;
		case 'building':
			$iconHtml .= 'crane.gif';
			break;
		}
		$iconHtml .= '">';
		return $iconHtml;
	}
		
	
	function indent($change=0) {
		global $indention, $indentString;
		
		echo str_repeat($indentString, $indention + $change);
	}
	
	function exploreconcepts($concept_id)
	{
		$db_query = "SELECT `concept_id` as 'id', `name`, `grants` FROM `concepts` WHERE `concept_id` = '" . $concept_id . "' LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$grants = unserialize($db_row['grants']);
		
		$techtree = array(
			'id' => $db_row['id'], 
			'name' => $db_row['name'], 
			'type' => 'concept');
		
		foreach (array('building', 'armyconcept', 'navyconcept', 'weaponconcept', 'concept') as $grant_type)
		{
			if (empty($grants[$grant_type . 's'])) continue;
			
			$fn = 'explore' . $grant_type . 's';
			foreach ($grants[$grant_type . 's'] as $key => $value)
			{
				$techtree['children'][] = $fn($key);
			}
		}
		
		return $techtree;
	}
	
	function explorebuildings($building_id)
	{
		$db_query = "
			SELECT `building_id` as 'id', `name` 
			FROM `buildings` 
			WHERE `building_id` = '" . $building_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$db_row['type'] = 'building';
		
		return $db_row;
	}
	
	function explorearmyconcepts($armyconcept_id)
	{
		$db_query = "
			SELECT `armyconcept_id` as 'id', `name` 
			FROM `armyconcepts` 
			WHERE `armyconcept_id` = '" . $armyconcept_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$db_row['type'] = 'armyconcept';
		
		return $db_row;
	}
	
	function explorenavyconcepts($navyconcept_id)
	{
		$db_query = "
			SELECT `navyconcept_id` as 'id', `name` 
			FROM `navyconcepts` 
			WHERE `navyconcept_id` = '" . $navyconcept_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$db_row['type'] = 'navyconcept';
		
		return $db_row;
	}
	
	function exploreweaponconcepts($weaponconcept_id)
	{
		$db_query = "
			SELECT `weaponconcept_id` as 'id', `name` 
			FROM `weaponconcepts` 
			WHERE `weaponconcept_id` = '" . $weaponconcept_id . "' 
			LIMIT 1";
		$db_result = mysql_query($db_query);
		$db_row = mysql_fetch_array($db_result, MYSQL_ASSOC);
		$db_row['type'] = 'weaponconcept';
		
		return $db_row;
	}
?>