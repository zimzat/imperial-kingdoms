<?php
	define('IK_AUTHORIZED', true);
	require_once(dirname(__FILE__) . '/includes/init.php');
	
	if (!empty($_REQUEST['itemId'])) {
		list ($type, $id) = explode(':', $_REQUEST['itemId']);
		$id = (int)$id;
	} else {
		if (empty($_REQUEST['itemId']) || empty($_REQUEST['id']) || empty($_REQUEST['type'])) {
			echo 'Must specify tech type and id.';
			exit;
		}
		$id = (int)$_REQUEST['id'];
		$type = $_REQUEST['type'];
	}

	if (!in_array($type, array('concept', 'armyconcept', 'navyconcept', 'weaponconcept', 'building'))) {
		echo 'Unknown tech type: ' . htmlentities($type);
		exit;
	}

	$cacheFile = 'cache/info-' . $type . '-' . $id . '.html';
	if (file_exists($cacheFile)) {
		readfile($cacheFile);
		exit;
	}
	
	require_once('includes/techinfo.php');
	
	$techinfo = new TechInfo;
	$content = $techinfo->$type($id);
	
	ob_start();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
	"http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
	<head>
		<title>Imperial Kingdoms - Dynamic Technology Tree</title>
		<link href="styles/global/techinfo.css" type="text/css" rel="stylesheet" />
	</head>
	<body>
<?php
	echo $content;
?>
	</body>
</html>
<?php
	$cacheContents = ob_get_contents();
	$cacheHandle = fopen($cacheFile, 'w');
	fwrite($cacheHandle, $cacheContents);
	fclose($cacheHandle);
	chmod($cacheFile, 0777);
	
	ob_end_flush();
?>
