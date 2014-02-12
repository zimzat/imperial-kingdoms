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


function fnExpandCompact()
{
	if (varCompactMaps == false)
	{
		varCompactMaps = true;
		
		xHeight('pane_main', 520);
		
		fnZoom(varZoomLevel);
		
		var varLeftPane = xLeft('pane_cluster');
		
		xLeft('pane_quadrant', varLeftPane);
		xLeft('pane_starsystem', varLeftPane);
	}
	else
	{
		var quadrantleft = 175;
		var starsystemleft = 292;
		if (xLeft('pane_cluster') != 28)
		{
			quadrantleft = 750 - xWidth('pane_quadrant') - quadrantleft;
			starsystemleft = 750 - xWidth('pane_starsystem') - starsystemleft;
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
	
	for (x in varDivs)
	{
		xLeft(varDivs[x], (750 - xWidth(varDivs[x]) - xLeft(varDivs[x])));
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