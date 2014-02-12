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
		<!-- left-x, top-y, right-x, bottom-y.  -->
		
		<map name="menumap_main">
			<!-- game navigation -->
			<area shape="circle" coords="13,13,12" id="buildings" alt="Infrastructure" title="Infrastructure" href="{$baseurl}buildings.php" target="main">
			<area shape="circle" coords="39,13,12" id="military" alt="C&amp;C" title="C&amp;C" href="{$baseurl}military.php" target="main">
			<area shape="circle" coords="65,13,12" id="status" alt="Status" title="Status" href="{$baseurl}status.php" target="main">
			
			<area shape="circle" coords="13,39,12" id="research" alt="R&amp;D" title="R&amp;D" href="{$baseurl}research.php" target="main">
			<area shape="circle" coords="39,39,12" id="mail" alt="Mail" title="Mail" href="{$baseurl}mail.php" target="main">
			<area shape="circle" coords="65,39,12" id="news" alt="Headlines" title="Headlines" href="{$baseurl}news.php" target="main">
			
			<area shape="circle" coords="13,65,12" id="options" alt="Options" title="Options" href="{$baseurl}options.php" target="main">
			<area shape="circle" coords="39,65,12" id="forums" alt="Discussions" title="Discussions" href="{$siteurl}forum/" target="forums">
			<area shape="circle" coords="65,65,12" id="help" alt="Help" title="Help" href="{$baseurl}help.php" target="main">
		</map>
		
		<map name="menumap_navigation">
			<!-- planet navigation  -->
			<area shape="circle" coords="11,11,9" id="prev_permissions" alt="Previous Planet by Permissions" title="Previous Planet by Permissions" href="{$baseurl}info.php?fn=info_planet&planet_id=previous_permissions" target="info">
			<area shape="circle" coords="32,11,9" id="prev" alt="Previous Planet" title="Previous Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=previous" target="info">
			<area shape="circle" coords="53,11,9" id="kingdom" alt="Kingdom" title="Kingdom" href="{$baseurl}info.php?fn=info_kingdom" target="info">
			<area shape="circle" coords="74,11,9" id="next" alt="Next Planet" title="Next Planet" href="{$baseurl}info.php?fn=info_planet&planet_id=next" target="info">
			<area shape="circle" coords="95,11,9" id="next_permissions" alt="Next Planet by Permissions" title="Next Planet by Permissions" href="{$baseurl}info.php?fn=info_planet&planet_id=next_permissions" target="info">
		</map>
		
		<map name="menumap_interface">
			<!-- interface navigation -->
			<area shape="circle" coords="10,10,9" id="zoomout" alt="Zoom Out" title="Zoom Out" href="javascript:fnZoom('out')">
			<area shape="circle" coords="10,31,9" id="zoomin" alt="Zoom In" title="Zoom In" href="javascript:fnZoom('in')">
			
			<area shape="circle" coords="10,52,9" id="expandcollapse" alt="Expand/Collapse Maps/Central Pane" title="Expand/Collapse Maps/Central Pane" href="javascript:fnExpandCompact()">
			<area shape="circle" coords="10,73,9" id="mirrorpanes" alt="Mirror panes to opposite sides." title="Mirror panes to opposite sides." href="javascript:fnMirrorPanes()">
		</map>
		
		<img src="{$baseurl}styles/{$style}/images/spacer.gif" width="710" height="510" border="0" />
		
		<!-- Main Pane -->
		<div id="pane_main" style="left: 210px; width: 525px;">
			<iframe src="{$baseurl}main.php?fn=news" id="main" name="main" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="auto">
			</iframe>
		</div>
		
		<!-- Info Pane -->
		<div id="pane_info" class="forumline">
			<iframe src="{$baseurl}info.php?fn=info_kingdom" id="info" name="info" width="100%" height="100%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>
		
		
		
		<!-- Cluster Pane -->
		<div id="pane_cluster" class="forumline">
			<iframe src="{$baseurl}map.php?fn=cluster" id="cluster" name="cluster" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>
		
		<!-- Quadrant Pane -->
		<div id="pane_quadrant" class="forumline">
			<iframe src="{$baseurl}map.php?fn=quadrant" id="quadrant" name="quadrant" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>
		
		<!-- StarSystem Pane -->
		<div id="pane_starsystem" class="forumline">
			<iframe src="{$baseurl}map.php?fn=starsystem" id="starsystem" name="starsystem" width="105" height="105" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no">
			</iframe>
		</div>
		
		
		
		<!-- Game Menu -->
		<div id="menu_main">
			<img src="{$baseurl}styles/{$style}/images/menu_main.gif" border="0" usemap="#menumap_main" />
		</div>
		
		<!-- Navigation Menu -->
		<div id="menu_navigation">
			<img src="{$baseurl}styles/{$style}/images/menu_navigation.gif" border="0" usemap="#menumap_navigation" />
		</div>
		
		<!-- Interface Menu -->
		<div id="menu_interface">
			<img src="{$baseurl}styles/{$style}/images/menu_interface.gif" border="0" usemap="#menumap_interface" />
		</div>
		
		
		
		<!-- Mail Alert -->
		<div id="alert_mail">
			<a href="{$baseurl}mail.php" style="CURSOR: hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alert_mail.gif" border="0"></a>
		</div>
		
		<!-- Forum Alert -->
		<div id="alert_forum">
			<a href="{$baseurl}forum.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alert_forum.gif" border="0"></a>
		</div>
		
		<!-- Combat Alert -->
		<div id="alert_combat">
			<a href="{$baseurl}military.php" style="CURSOR:hand;" target="main"><img src="{$baseurl}styles/{$style}/images/alert_military.gif" border="0"></a>
		</div>

{include file="footer.tpl"}