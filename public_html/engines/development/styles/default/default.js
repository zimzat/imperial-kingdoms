// For whatever reason this code does not work.
// xClientWidth() and xClientHeight() do not return the correct values, 
// thus it cannot resize the panes correctly.

// function fnResizePanes(varEvent)
// {
//	 var varClientWidth = xClientWidth();
//	 var varClientHeight = xClientHeight();
//	 
//	 if (varClientWidth < 750 || varClientHeight < 550)
//		 return;
//	 
//	 xWidth('pane_main', varClientWidth - xWidth('pane_info') - 45);
//	 if (varCompactMaps == true)
//	 {
//		 xHeight('pane_main', varClientHeight - 30);
//	 }
//	 else
//	 {
//		 xHeight('pane_main', varClientHeight - xHeight('pane_cluster') - 45);
//	 }
// }


function fnExpandCompact()
{
	if (varCompactMaps == false)
	{
		varCompactMaps = true;
		
		xHeight('pane_main', 520);
		
		fnZoom(varZoomLevel);
		
		var varLeftPane = xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true);
		
		xLeft('pane_quadrant', varLeftPane);
		xLeft('pane_starsystem', varLeftPane);
	}
	else
	{
		var quadrantleft = 175;
		var starsystemleft = 292;
		if (xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true) != 28)
		{
			quadrantleft = 750 - xGetComputedStyle(xGetElementById('pane_quadrant'), 'width', true) - quadrantleft;
			starsystemleft = 750 - xGetComputedStyle(xGetElementById('pane_starsystem'), 'width', true) - starsystemleft;
		}
		varCompactMaps = false;
		xHeight('pane_main', 398);
		xLeft('pane_quadrant', quadrantleft);
		xLeft('pane_starsystem', starsystemleft);
		xVisibility('pane_cluster', true);
		xVisibility('pane_quadrant', true);
		xVisibility('pane_starsystem', true);
	}
}


//var varMirrorPanes = false;
function fnMirrorPanes()
{
	var varDivs = new Array(
		'pane_main', 'pane_info', 
		'pane_cluster', 'pane_quadrant', 'pane_starsystem', 
		'menu_main', 'menu_navigation', 'menu_interface', 
		'alert_mail', 'alert_forum', 'alert_combat');
	
//	 var varClientWidth = xClientWidth();
	var varClientWidth = 750;
	
	for (x in varDivs)
	{
		xLeft(varDivs[x], (varClientWidth - xGetComputedStyle(xGetElementById(varDivs[x]), 'width', true) - xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true)));
	}
}


function fnZoom(varZoom)
{
	if (!varCompactMaps) return;
	
	// Convert 'in', 'out', and strings to numbers.
	if (varZoom == 'in' && varZoomLevel < 3) varZoom = varZoomLevel + 1;
	else if (varZoom == 'out' && varZoomLevel > 1) varZoom = varZoomLevel - 1;
	else varZoom = parseFloat(varZoom);
	
	switch (varZoom)
	{
		case 1:
			varZoomLevel = 1;
			xVisibility('pane_cluster', true);
			xVisibility('pane_quadrant', false);
			xVisibility('pane_starsystem', false);
			break
		case 2:
			varZoomLevel = 2;
			xVisibility('pane_quadrant', true);
			xVisibility('pane_starsystem', false);
			xVisibility('pane_cluster', false);
			break
		case 3:
			varZoomLevel = 3;
			xVisibility('pane_starsystem', true);
			xVisibility('pane_cluster', false);
			xVisibility('pane_quadrant', false);
			break
	}
}