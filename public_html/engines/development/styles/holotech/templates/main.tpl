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
			<area shape="circle" coords="10,10,9" id="zoomout" alt="Zoom Out" title="Zoom Out" href="javascript:fnZoom('out')">
			<area shape="circle" coords="10,31,9" id="zoomin" alt="Zoom In" title="Zoom In" href="javascript:fnZoom('in')">
<!--
			<area shape="circle" coords="10,52,9" id="expandcollapse" alt="Expand/Collapse Maps/Central Pane" title="Expand/Collapse Maps/Central Pane" href="javascript:fnExpandCompact()">
			<area shape="circle" coords="10,73,9" id="mirrorpanes" alt="Mirror panes to opposite sides." title="Mirror panes to opposite sides." href="javascript:fnMirrorPanes()">
-->
		</map>
		<map name="menumap_navigation">
			<!-- planet navigation  -->
			<area shape="rect" coords="0,0, 8, 12" id="prev_permissions" alt="Previous Planet by Permissions" title="Previous Planet by Permissions" href="{$baseurl}info.php?fn=info_planet&planet_id=previous_permissions" target="info">
			<area shape="rect" coords="20,0, 30, 12" id="prev" alt="Previous Planet" title="Previous Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=previous" target="info">
			<area shape="rect" coords="46,0, 58, 12" id="kingdom" alt="Kingdom" title="Kingdom" href="{$baseurl}info.php?fn=info_kingdom" target="info">
			<area shape="rect" coords="78,0, 88, 12" id="next" alt="Next Planet" title="Next Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=next" target="info">
			<area shape="rect" coords="98,0, 106, 12" id="next_permissions" alt="Next Planet by Permissions" title="Next Planet by Permissions" href="{$baseurl}info.php?fn=info_planet&planet_id=next_permissions" target="info">
		</map>

		<!-- game navigation -->
		<!-- menu off state roleovers -->

		<div id="off_build" onmouseover="fnOverMenu('build', '1');" onmouseout="fnOverMenu('build', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/build.gif" border="0" alt="Infrastructure" />
		</div>
		<div id="off_combat" onmouseover="fnOverMenu('combat', '1');" onmouseout="fnOverMenu('combat', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/combat.gif" border="0" alt="C&amp;C" />
		</div>
		<div id="off_status" onmouseover="fnOverMenu('status', '1');" onmouseout="fnOverMenu('status', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/empire.gif" border="0" alt="Status" />
		</div>
		<div id="off_research" onmouseover="fnOverMenu('research', '1');" onmouseout="fnOverMenu('research', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/research.gif" border="0" alt="R&amp;D" />
		</div>
		<div id="off_mail" onmouseover="fnOverMenu('mail', '1');" onmouseout="fnOverMenu('mail', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/mail.gif" border="0" alt="Mail" />
		</div>
		<div id="off_news" onmouseover="fnOverMenu('news', '1');" onmouseout="fnOverMenu('news', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/status.gif" border="0" alt="News" />
		</div>
		<div id="off_option" onmouseover="fnOverMenu('option', '1');" onmouseout="fnOverMenu('option', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/option.gif" border="0" alt="Options" />
		</div>
		<div id="off_chat" onmouseover="fnOverMenu('chat', '1');" onmouseout="fnOverMenu('chat', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/forum.gif" border="0" alt="Forums" />
		</div>
		<div id="off_databank" onmouseover="fnOverMenu('databank', '1');" onmouseout="fnOverMenu('databank', '0');">
			<img src="{$baseurl}styles/{$style}/images/menuoff/data.gif" border="0" alt="Databank" />
		</div>

		<!-- menu on state roleovers -->

		<div id="on_build" onmouseover="fnOverMenu('build', '1');" onmouseout="fnOverMenu('build', '0');">
			<a href="{$baseurl}buildings.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/build.gif" border="0" alt="Infrastructure" /></a>
		</div>
		<div id="on_combat" onmouseover="fnOverMenu('combat', '1');" onmouseout="fnOverMenu('combat', '0');">
			<a href="{$baseurl}military.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/combat.gif" border="0" alt="C&amp;C" /></a>
		</div>
		<div id="on_status" onmouseover="fnOverMenu('status', '1');" onmouseout="fnOverMenu('status', '0');">
			<a href="{$baseurl}status.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/empire.gif" border="0" alt="Status" /></a>
		</div>
		<div id="on_research" onmouseover="fnOverMenu('research', '1');" onmouseout="fnOverMenu('research', '0');">
			<a href="{$baseurl}research.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/research.gif" border="0" alt="R&amp;D" /></a>
		</div>
		<div id="on_mail" onmouseover="fnOverMenu('mail', '1');" onmouseout="fnOverMenu('mail', '0');">
			<a href="{$baseurl}mail.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/mail.gif" border="0" alt="Mail" /></a>
		</div>
		<div id="on_news" onmouseover="fnOverMenu('news', '1');" onmouseout="fnOverMenu('news', '0');">
			<a href="{$baseurl}news.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/status.gif" border="0" alt="News" /></a>
		</div>
		<div id="on_option" onmouseover="fnOverMenu('option', '1');" onmouseout="fnOverMenu('option', '0');">
			<a href="{$baseurl}options.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/option.gif" border="0" alt="Options" /></a>
		</div>
		<div id="on_chat" onmouseover="fnOverMenu('chat', '1');" onmouseout="fnOverMenu('chat', '0');">
			<a href="{$siteurl}forum/" target="forums"><img src="{$baseurl}styles/{$style}/images/menuon/forum.gif" border="0" alt="Forums" /></a>
		</div>
		<div id="on_databank" onmouseover="fnOverMenu('databank', '1');" onmouseout="fnOverMenu('databank', '0');">
			<a href="{$baseurl}help.php" target="main"><img src="{$baseurl}styles/{$style}/images/menuon/data.gif" border="0" alt="Databank" /></a>
		</div>

		<!-- Mail Alert -->

		<div id="alert_mail" onmouseover="fnAlert('on_mail', '1');" onmouseout="fnAlert('on_mail', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/mail.gif" border="0"></a>
		</div>
		<div id="alert_on_mail" onmouseover="fnAlert('on_mail', '1');" onmouseout="fnAlert('on_mail', '0');">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/mail.gif" border="0"></a>
		</div>

		<!-- Forum Alert -->

		<div id="alert_forum" onmouseover="fnAlert('on_forum', '1');" onmouseout="fnAlert('on_forum', '0');">
			<a href="{$baseurl}forum.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/empire.gif" border="0"></a>
		</div>
		<div id="alert_on_forum" onmouseover="fnAlert('on_forum', '1');" onmouseout="fnAlert('on_forum', '0');">
			<a href="{$baseurl}forum.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/empire.gif" border="0"></a>
		</div>

		<!-- Combat Alert -->

		<div id="alert_combat" onmouseover="fnAlert('on_combat', '1');" onmouseout="fnAlert('on_combat', '0');">
			<a href="{$baseurl}military.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alertoff/combat.gif" border="0"></a>
		</div>
		<div id="alert_on_combat" onmouseover="fnAlert('on_combat', '1');" onmouseout="fnAlert('on_combat', '0');">
			<a href="{$baseurl}military.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alerton/combat.gif" border="0"></a>
		</div>

		<!-- Cluster Pane -->

		<div id="pane_cluster">
			<iframe src="{$baseurl}map.php?fn=cluster" id="cluster" name="cluster" width="224" height="224" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>

		<!-- Quadrant Pane -->

		<div id="pane_quadrant">
			<iframe src="{$baseurl}map.php?fn=quadrant" id="quadrant" name="quadrant" width="224" height="224" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>

		<!-- StarSystem Pane -->

		<div id="pane_starsystem">
			<iframe src="{$baseurl}map.php?fn=starsystem" id="starsystem" name="starsystem" width="224" height="224" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>

		<!-- Main Pane -->

		<div id="pane_main">
			<iframe src="{$baseurl}main.php?fn=news" id="main" name="main" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="auto">
			</iframe>
		</div>

		<!-- Info Pane -->
		<div id="pane_info">
			<iframe src="{$baseurl}info.php?fn=info_kingdom" id="info" name="info" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>

		<div id="pane_info_up" onclick="fnShrink('info')">
			<img src="{$baseurl}styles/{$style}/images/symbols/arrows/left.gif" style="CURSOR: hand;"  />
		</div>

		<div id="pane_info_nav">
			<img src="{$baseurl}styles/{$style}/images/planet_nav.gif" border="0" width="106" height="12" usemap="#menumap_navigation" />
		</div>

		<div id="pane_info_down" onclick="fnEnlarge('info')">
			<img src="{$baseurl}styles/{$style}/images/symbols/arrows/right.gif" style="CURSOR: hand;"  />
		</div>

		<div id="pane_info_graphic">
			<img src="{$baseurl}styles/{$style}/images/info_pane.gif" />
		</div>

		<!-- Interface Menu -->

		<div id="menu_interface">
			<img src="{$baseurl}styles/{$style}/images/menu_interface.gif" border="0" usemap="#menumap_interface" />
		</div>
<!--
		<div id="pane_astro_down" onclick="fnEnlarge('astro')">
			<img src="{$baseurl}styles/{$style}/images/symbols/arrows/right.gif" style="CURSOR: hand;"  />
		</div>
		<div id="pane_astro_up" onclick="fnShrink('astro')">
			<img src="{$baseurl}styles/{$style}/images/symbols/arrows/left.gif" style="CURSOR: hand;"  />
		</div>
-->

{include file="footer.tpl" bodyline="true"}