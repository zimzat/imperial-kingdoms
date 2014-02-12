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
<!--[if lt IE 7.]>
<script defer type="text/javascript" src="{$baseurl}styles/pngfix.js"></script>
<![endif]-->
		<title>Imperial Kingdoms</title>
	</head>
	<body class="index" onload="fnFrameBreak();">

<div id="header">
	<div class="ikBlock" id="logo"><img src="{$baseurl}images/ik_logo.png" alt="Imperial Kingdoms" /></div>

{if $smarty.server.SCRIPT_NAME != "/login.php"}
	<div id="ads">
<script type="text/javascript"><!--
google_ad_client = "pub-8166089000608659";
google_ad_width = 468;
google_ad_height = 60;
google_ad_format = "468x60_as";
google_ad_type = "text_image";
google_ad_channel ="";
google_color_border = "0075B2";
google_color_bg = "003952";
google_color_link = "8BC7FF";
google_color_url = "8BC7FF";
google_color_text = "FFFFFF";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
	</div>
{/if}

	<div class="ikBlock" id="menu">
		<a href="{$baseurl}login.php">Login</a>&nbsp;
		<a href="{$baseurl}register.php">Register</a><br />
		
		<a href="{$baseurl}guide/">Guide</a>{*&nbsp;
		<i>Screenshots</i>*}<br />
		
		<a href="{$baseurl}forum/">Forums</a>&nbsp;
		<a href="mailto:admin@imperialkingdoms.com">Contact</a>
	</div>
	
	<br style="clear: both;" />
</div>

<div id="content">
