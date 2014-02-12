{* Smarty *}
{* When can we change the DOCTYPE to this? Do testing to see what all it changes and why. *}
{* <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> *}
{* <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> *}
{* <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> *}
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"> 
		
		<link type="text/css" href="{$baseurl}styles/global.css" rel="stylesheet">
		<link type="text/css" href="{$baseurl}styles/{$style}/default.css" rel="stylesheet">
		
		<script language="JavaScript" type="text/javascript" src="{$baseurl}styles/x_core.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$baseurl}styles/x_event.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$baseurl}styles/x_timer.js"></script>
		
		<script language="JavaScript" type="text/javascript" src="{$baseurl}styles/global.js"></script>
		<script language="JavaScript" type="text/javascript" src="{$baseurl}styles/{$style}/default.js"></script>
		<title>Imperial Kingdoms</title>
	</head>
{if $bodyline == "true"}
	<body class="index" onload="fnFrameBreak();">
		<table width="100%" cellspacing="0" cellpadding="10" border="0" align="center">
			<tr>
				<td class="bodyline">
{elseif $pagebackground == "true"}
	<body class="background">
{else}
	<body class="page">
{/if}