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
	varMark = xGetElementById('map_mark');
	varLinkMark = xGetElementById('link-mark');
	
	varLinkMark.href = varLink.href;
	xMoveTo(varMark, varX * xGetComputedStyle(xGetElementById('mark'), 'width', true), varY * xGetComputedStyle(xGetElementById('mark'), 'height', true));
	xVisibility(varMark, true);
}


function resetCount(varTextArea, varCounterField, varMaxLength)
{
	varCounterField.value = varMaxLength - varTextArea.value.length;
}


function fnCountCharacters(varTextArea, varMaxLength)
{
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

var varLastPlanetID = 0;
function fnSelectPlanet(varPlanetID, varEvent)
{
	if (varEvent.shiftKey && varLastPlanetID != 0 && varLastPlanetID != varPlanetID)
	{
		if (varPlanetID > varLastPlanetID)
		{
			var x1 = varLastPlanetID+1;
			var x2 = varPlanetID;
		}
		else
		{
			var x1 = varPlanetID;
			var x2 = varLastPlanetID-1;
		}
		
		for (x = x1; x <= x2; x++)
		{
			var varPlanetCheckbox = xGetElementById('planet_' + x)
			varPlanetCheckbox.checked = true;
		}
		
		if (document.selection) document.selection.empty();
		else window.find(' ');
	}
	else
	{
		if (varEvent.target.id != 'planet_' + varPlanetID)
		{
			var varPlanetCheckbox = xGetElementById('planet_' + varPlanetID);
			varPlanetCheckbox.checked = !varPlanetCheckbox.checked;
		}
	}
	
	varLastPlanetID = varPlanetID;
	
//	 var varAlertMsg = "";
//	 for (x in varEvent)
//		 varAlertMsg = varAlertMsg + "<br />\n" + x + ": " + varEvent[x];
//	 
//	 var varNewWindow = window.open('', 'alert', 'width=400,height=600');
//	 varNewWindow.document.write(varAlertMsg);
//	 varNewWindow.document.close;
}

function fnCheckAll(varTotal)
{
	for (x = 1; x <= varTotal; x++)
	{
		var varPlanetCheckbox = xGetElementById('planet_' + x);
		varPlanetCheckbox.checked = true;
	}
}

function fnCheckNone(varTotal)
{
	for (x = 1; x <= varTotal; x++)
	{
		var varPlanetCheckbox = xGetElementById('planet_' + x);
		varPlanetCheckbox.checked = false;
	}
}


function fnAlert(varType, varStatus)
{
	if(parent.document.getElementById) varAlert = parent.document.getElementById('alert_' + varType);
	else if(parent.document.all) varAlert = parent.document.all['alert_' + varType];
	
	if (varStatus == '0' || varStatus == 0) varStatus = false;
	else varStatus = true;
	
	xVisibility(varAlert, varStatus);
}


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