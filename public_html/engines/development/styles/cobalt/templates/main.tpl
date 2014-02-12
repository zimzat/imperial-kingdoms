{* Smarty *}

{include file="header.tpl" bodyline="true"}

{literal}
		<script language="JavaScript">
		<!--
			var varZoomLevel = 3;
			var varCompactMaps = false;
//			 window.onresize = fnResizePanes;
		// -->
		</script>
{/literal}

		<!-- left-x, top-y, right-x, bottom-y -->
		<map name="menumap_interface">
			<!-- interface navigation -->
			<area shape="rect" coords="0,0,18,22"id="prev" alt="Previous Planet" title="Previous Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=previous">
			<area shape="rect" coords="110,0,128,22" id="next" alt="Next Planet" title="Next Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=next" target="info">
			<area shape="rect" coords="160,0,180,15" id="kingdom" alt="Kingdom" title="Kingdom" href="{$baseurl}info.php?fn=info_kingdom" target="info">
		</map>

		<!-- game navigation -->
		<!-- menu off state roleovers -->
		<div id="off_build" onmouseover="fnOverMenu('build', '1');" onmouseout="fnOverMenu('build', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/build.jpg" border="0" alt="Infrastructure" />
		</div>
		<div id="off_combat" onmouseover="fnOverMenu('combat', '1');" onmouseout="fnOverMenu('combat', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/combat.jpg" border="0" alt="C&amp;C" />
		</div>
		<div id="off_status" onmouseover="fnOverMenu('status', '1');" onmouseout="fnOverMenu('status', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/empire.jpg" border="0" alt="Status" />
		</div>
		<div id="off_research" onmouseover="fnOverMenu('research', '1');" onmouseout="fnOverMenu('research', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/research.jpg" border="0" alt="R&amp;D" />
		</div>
		<div id="off_mail" onmouseover="fnOverMenu('mail', '1');" onmouseout="fnOverMenu('mail', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/mail.jpg" border="0" alt="Mail" />
		</div>
		<div id="off_news" onmouseover="fnOverMenu('news', '1');" onmouseout="fnOverMenu('news', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/status.jpg" border="0" alt="News" />
		</div>
		<div id="off_option" onmouseover="fnOverMenu('option', '1');" onmouseout="fnOverMenu('option', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/option.jpg" border="0" alt="Options" />
		</div>
		<div id="off_chat" onmouseover="fnOverMenu('chat', '1');" onmouseout="fnOverMenu('chat', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/forum.jpg" border="0" alt="Forums" />
		</div>
		<div id="off_databank" onmouseover="fnOverMenu('databank', '1');" onmouseout="fnOverMenu('databank', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/data.jpg" border="0" alt="Databank" />
		</div>

		<!-- menu on state roleovers -->
		<div id="on_build" onmouseover="fnOverMenu('build', '1');" onmouseout="fnOverMenu('build', '0');">
			<a href="{$baseurl}buildings.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/build.jpg" border="0" alt="Infrastructure" /></a>
		</div>
		<div id="on_combat" onmouseover="fnOverMenu('combat', '1');" onmouseout="fnOverMenu('combat', '0');">
			<a href="{$baseurl}military.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/combat.jpg" border="0" alt="C&amp;C" /></a>
		</div>
		<div id="on_status" onmouseover="fnOverMenu('status', '1');" onmouseout="fnOverMenu('status', '0');">
			<a href="{$baseurl}status.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/empire.jpg" border="0" alt="Status" /></a>
		</div>
		<div id="on_research" onmouseover="fnOverMenu('research', '1');" onmouseout="fnOverMenu('research', '0');">
			<a href="{$baseurl}research.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/research.jpg" border="0" alt="R&amp;D" /></a>
		</div>
		<div id="on_mail" onmouseover="fnOverMenu('mail', '1');" onmouseout="fnOverMenu('mail', '0');">
			<a href="{$baseurl}mail.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/mail.jpg" border="0" alt="Mail" /></a>
		</div>
		<div id="on_news" onmouseover="fnOverMenu('news', '1');" onmouseout="fnOverMenu('news', '0');">
			<a href="{$baseurl}news.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/status.jpg" border="0" alt="News" /></a>
		</div>
		<div id="on_option" onmouseover="fnOverMenu('option', '1');" onmouseout="fnOverMenu('option', '0');">
			<a href="{$baseurl}options.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/option.jpg" border="0" alt="Options" /></a>
		</div>
		<div id="on_chat" onmouseover="fnOverMenu('chat', '1');" onmouseout="fnOverMenu('chat', '0');">
			<a href="http://www.imperialkingdoms.com/forum/" target="forums"><img src="{$baseurl}styles/{$style}/images/menuon/forum.jpg" border="0" alt="Forums" /></a>
		</div>
		<div id="on_databank" onmouseover="fnOverMenu('databank', '1');" onmouseout="fnOverMenu('databank', '0');">
			<a href="{$baseurl}help.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/data.jpg" border="0" alt="Databank" /></a>
		</div>

		<!-- Mail Alert -->
		<div id="alert_mail" onmouseover="fnAlert('on_mail', '1');" onmouseout="fnAlert('on_mail', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/mail.jpg" border="0"></a>
		</div>
		<div id="alert_on_mail" onmouseover="fnAlert('on_mail', '1');" onmouseout="fnAlert('on_mail', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/mail.jpg" border="0"></a>
		</div>

		<!-- Forum Alert -->
		<div id="alert_forum" onmouseover="fnAlert('on_forum', '1');" onmouseout="fnAlert('on_forum', '0');">
			<a href="{$baseurl}forum.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/empire.jpg" border="0"></a>
		</div>
		<div id="alert_on_forum" onmouseover="fnAlert('on_forum', '1');" onmouseout="fnAlert('on_forum', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/empire.jpg" border="0"></a>
		</div>

		<!-- Combat Alert -->
		<div id="alert_combat" onmouseover="fnAlert('on_combat', '1');" onmouseout="fnAlert('on_combat', '0');">
			<a href="{$baseurl}military.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/combat.jpg" border="0"></a>
		</div>
		<div id="alert_on_combat" onmouseover="fnAlert('on_combat', '1');" onmouseout="fnAlert('on_combat', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/combat.jpg" border="0"></a>
		</div>

		<!-- Cluster Pane -->
		<div id="pane_cluster" class="astromap">
			<iframe src="map.php?fn=cluster" id="cluster" name="cluster" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>

		</div>

		<!-- Quadrant Pane -->
		<div id="pane_quadrant">
			<iframe src="map.php?fn=quadrant" id="quadrant" name="quadrant" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>

		</div>

		<!-- StarSystem Pane -->
		<div id="pane_starsystem">
			<iframe src="map.php?fn=starsystem" id="starsystem" name="starsystem" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>

		</div>

		<!-- Main Pane -->
		<div id="pane_main">
			<iframe src="main.php?fn=news" id="main" name="main" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="auto">
			</iframe>
		</div>

		<!-- Info Pane -->
		<div id="menu_navigation">
			<img src="{$baseurl}styles/{$style}/images/navplanet.gif" border="0" usemap="#menumap_interface" />
		</div>

		<div id="pane_info">
			<iframe src="info.php?fn=info_kingdom" id="info" name="info" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>


{include file="footer.tpl"}