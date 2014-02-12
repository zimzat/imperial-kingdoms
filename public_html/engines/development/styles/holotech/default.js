function fnFrameBreak()
{
	if (top.location != location)
	{
		top.location.href = document.location.href;
	}
}


function fnRefreshIf(varUrl, varFrame)
{
	if (!varFrame)
	{
		varFrame = self;
	}
	
	if (varFrame)
	{
		var a = varFrame.location.href;
		var b = a.match(/[\/|\\]([^\\\/]+)$/);
		
		var c = varUrl;
		var d = c.match(/[\/|\\]([^\\\/]+)$/);
		
		if (b[1] == d[1])
		{
			varFrame.location.replace(varUrl);
		}
	}
	
	return true;
}


function fnResizeTextArea(varTextArea)
{
	var varAgent = navigator.userAgent.toLowerCase();
	var varRows = 0;
	var varMaxRows = 5;
	var varColumns = varTextArea.cols;
	
	a = varTextArea.value.split('\n');
	
	for (x = 0; x < a.length; x++)
	{
		if (a[x].length >= varColumns) varRows += Math.ceil((a[x].length / varColumns) - 1);
	}
	
	varRows += a.length;
	if (varRows > varMaxRows) varRows = varMaxRows;
	if (varRows != varTextArea.rows && varAgent.indexOf('opera') == -1) varTextArea.rows = varRows;
}


function fnMoveMark(varX, varY)
{
	varLink = xGetElementById('link-' + varX + '-' + varY);
	varMark = xGetElementById('mark');
	varLinkMark = xGetElementById('link-mark');
	
	varLinkMark.href = varLink.href;
	xMoveTo(varMark, varX * 15, varY * 15);
	xVisibility(varMark, true);
}


function resetCount(varTextArea, varCounterField, varMaxLength)
{
	varCounterField.value = varMaxLength - varTextArea.value.length;
}


function fnCountCharacters(varTextArea, varMaxLength)
{
//	 var i = 0;
//	 for (x in varTextArea)
//	 {
//		 i++;
//		 if (i == 15)
//		 {
//			 alert(varAlert);
//			 varAlert = '';
//			 i = 0;
//		 }
//		 
//		 varAlert = varAlert + "\n" + x + ' = ' + varTextArea[x];
//	 }
//	 
//	 alert(varAlert);
	
	var varCounterField = xGetElementById(varTextArea['id'] + '_count');
	
	if(varTextArea != null && varTextArea.value != null)
	{
		if (varTextArea.value.length > varMaxLength)
		{
			alert("Your message may not exceed " +  varMaxLength +" characters in length.");
			varTextArea.value = varTextArea.value.substring(0, varMaxLength);
		}
		else
		{
			varCounterField.value = varMaxLength - varTextArea.value.length;
		}
	}
}	


function getXTR ()
{
	var xtr;
	var ex;
	
	if (typeof(XMLHttpRequest) != "undefined")
	{
		xtr = new XMLHttpRequest();
	}
	else
	{
		try
		{
			xtr = new ActiveXObject("Msxml2.XMLHTTP.4.0");
		}
		catch (ex)
		{
			try
			{
				xtr = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (ex)
			{
				
			}
		}
	}
	return xtr;
}


function fnQuickActionClick (varEvent)
{
	xStopPropagation(varEvent);
}

function fnDocumentClick ()
{
	xVisibility('divQuickAction', false);
}


function fnChangeDiv(varDivName, varText)
{
	varDiv = xGetElementById(varDivName);
	varDiv.innerHTML = '';
	varDiv.innerHTML = varText;
}


function fnAlert(varType, varStatus)
{
	if(parent.document.getElementById) varAlert = parent.document.getElementById('alert_' + varType);
	else if(parent.document.all) varAlert = parent.document.all['alert_' + varType];
	
	if (varStatus == '0' || varStatus == 0) varStatus = false;
	else varStatus = true;
	
	xVisibility(varAlert, varStatus);
}

function fnOverMenu(varType, varStatus)
{
	if(parent.document.getElementById) varAlert = parent.document.getElementById('on_' + varType);
	else if(parent.document.all) varAlert = parent.document.all['on_' + varType];
	
	if (varStatus == '0' || varStatus == 0) varStatus = false;
	else varStatus = true;
	
	xVisibility(varAlert, varStatus);
}

function fnExpandCompact()
{
	if (varCompactMaps == false)
	{
		varCompactMaps = true;
		
		fnZoom(varZoomLevel);

		var varLeftPane = xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true);
		
		xLeft('pane_quadrant', varLeftPane);
		xLeft('pane_starsystem', varLeftPane);
	}
	else
	{
		var quadrantleft = 165;
		var starsystemleft = 45;
		if (xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true) == 265 )
		{
			quadrantleft = 750 - (xWidth('pane_quadrant') + quadrantleft);
			starsystemleft = 750 - (xWidth('pane_starsystem') + starsystemleft);
		}
		varCompactMaps = false;
		xLeft('pane_quadrant', quadrantleft);
		xLeft('pane_starsystem', starsystemleft);
		xVisibility('pane_cluster', true);
		xVisibility('pane_quadrant', true);
		xVisibility('pane_starsystem', true);
		
	}
}

function fnEnlarge(varPane)
{
	if (varPane == 'info' )
	{
		var varDivs = new Array('menu_interface', 'pane_info', 'pane_info_graphic', 'pane_info_up', 'pane_info_down', 'pane_info_nav');

		for (x in varDivs)
		{
			xLeft(varDivs[x], (xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true) + 180));
		}

		varCompactMaps = true;
		
		fnZoom(varZoomLevel);

		var varLeftPane = xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true);

		xLeft('pane_quadrant', varLeftPane);
		xLeft('pane_starsystem', varLeftPane)
		var varDivs = new Array('pane_cluster', 'pane_quadrant', 'pane_starsystem');

		for (x in varDivs)
		{
			xLeft(varDivs[x], (xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true) + 225));
		}
		
		xVisibility('pane_info_up', true);
		xVisibility('pane_info_down', false);
	}

	if (xGetComputedStyle(xGetElementById('pane_main'), 'width', true) != 490)
	{
		xLeft('pane_main', (xGetComputedStyle(xGetElementById('pane_main'), 'left', true) + 195));
		xWidth('pane_main', 520);
	}
}

function fnShrink(varPane)
{
	if (varPane == 'info' )
	{
		var varDivs = new Array('menu_interface', 'pane_info', 'pane_info_graphic', 'pane_info_up', 'pane_info_down', 'pane_info_nav');

		for (x in varDivs)
		{
			xLeft(varDivs[x], (xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true) - 180));
		}

		varCompactMaps = true;
		
		fnZoom(varZoomLevel);

		var varLeftPane = xGetComputedStyle(xGetElementById('pane_cluster'), 'left', true);

		xLeft('pane_quadrant', varLeftPane);
		xLeft('pane_starsystem', varLeftPane)
		var varDivs = new Array('pane_cluster', 'pane_quadrant', 'pane_starsystem');

		for (x in varDivs)
		{
			xLeft(varDivs[x], (xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true) - 225));
		}

		xVisibility('pane_info_up', false);
		xVisibility('pane_info_down', true);

		if (xGetComputedStyle(xGetElementById('pane_main'), 'left', true) != 35)
		{
			xLeft('pane_main', 35);
			xWidth('pane_main', 715);
		}
	}
}

//var varMirrorPanes = false;
function fnMirrorPanes()
{
	var varDivs = new Array(
		'pane_info', 'pane_info_graphic', 'pane_info_down', 'pane_info_up', 'pane_info_nav',
		'pane_cluster', 'pane_quadrant', 'pane_starsystem', 'pane_astro_down', 'pane_astro_up', 
		'menu_interface', 'pane_main',
		'on_build', 'on_combat', 'on_status', 'on_research', 'on_mail', 'on_news', 'on_option', 'on_chat', 'on_databank',
		'off_build', 'off_combat', 'off_status', 'off_research', 'off_mail', 'off_news', 'off_option', 'off_chat', 'off_databank',
		'alert_mail', 'alert_forum', 'alert_combat', 'alert_on_mail', 'alert_on_forum', 'alert_on_combat');
	
	for (x in varDivs)
	{
		xLeft(varDivs[x], (750 - xWidth(varDivs[x]) - xGetComputedStyle(xGetElementById(varDivs[x]), 'left', true)));
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