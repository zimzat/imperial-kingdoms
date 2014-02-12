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
	xVisibility('divWarptimeContainer', false);
}


function fnChangeDiv(varDivName, varText)
{
	varDiv = xGetElementById(varDivName);
	varDiv.innerHTML = '';
	varDiv.innerHTML = varText;
}

function fnWarptimeResearch()
{
	var varPlanets = xGetElementById('option_planets');
	var varPlanetId = varPlanets.options[varPlanets.selectedIndex].value;
	
	fnChangeDiv('divWarptime', varWarptimeResearch[varPlanetId]);
	
	if (varWarptimeResearch[varPlanetId])
	{
		var varWarptimeVisible = true;
	}
	else
	{
		var varWarptimeVisible = false;
	}
	
	xDisplay('divWarptimeContainer', (varWarptimeVisible ? 'block' : 'none'));
	xVisibility('divWarptimeContainer', varWarptimeVisible);
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
	
// function fnGetInformation(varInformationObject)
// {
//	 var varAlertMsg = "";
//	 for (x in varInformationObject)
//	 {
//		 varAlertMsg = varAlertMsg + "<br />\n" + x + ": " + varInformationObject[x];
//		 
//		 if (typeof(varInformationObject[x]) == 'function' || typeof(varInformationObject[x]) == 'object')
//		 {
//			 varAlertMsg = varAlertMsg + '<div style="padding-left: 30px;">' + fnGetInformation(varInformationObject[x]) + '</div>';
//		 }
//	 }
// }
//		 
// function fnNewWindow(varAlertMsg)
// {
//	 var varNewWindow = window.open('', 'alert', 'width=600,height=600');
//	 varNewWindow.document.write(varAlertMsg);
//	 varNewWindow.document.close;
// }
// 
// fnNewWindow(fnGetInformation(window));
}

function fnCheckAll(varTotal, varStart)
{
	var varStart = (varStart == null) ? 1 : varStart;
	
	for (x = varStart; x <= varTotal; x++)
	{
		var varPlanetCheckbox = xGetElementById('planet_' + x);
		varPlanetCheckbox.checked = true;
	}
}

function fnCheckNone(varTotal, varStart)
{
	var varStart = (varStart == null) ? 1 : varStart;
	
	for (x = varStart; x <= varTotal; x++)
	{
		var varPlanetCheckbox = xGetElementById('planet_' + x);
		varPlanetCheckbox.checked = false;
	}
}


function fnAlert(varType, varStatus)
{
	if(parent.document.getElementById) var varAlert = parent.document.getElementById('alert_' + varType);
	else if(parent.document.all) var varAlert = parent.document.all['alert_' + varType];
	
	if (varStatus == '0' || varStatus == 0) varStatus = false;
	else varStatus = true;
	
	xVisibility(varAlert, varStatus);
}

function fnExpandCollapseRows(varPlanetId, varTotal, varObject, varEvent)
{
	var varFnCallback = function (varObject, varIsRow, varRow, varCol, varData) {
		if (varIsRow) {
			var varDisplay = (xDisplay(varObject) == 'none') ? true : false;
			xTableRowDisplay(varDisplay, varData, varRow);
		}
		return true;
	}
	xTableIterate('row_' + varPlanetId, varFnCallback, 'row_' + varPlanetId);
}