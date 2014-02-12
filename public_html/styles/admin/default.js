function fnFrameBreak()
{
	if (top.location != location)
	{
		top.location.href = document.location.href;
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


function fnMailAlert(varStatus)
{
	xVisibility('mailalert', varStatus);
}


function fnCombatAlert(varStatus)
{
	xVisibility('combatalert', varStatus);
}


function fnExpandCompact()
{
	if (xHeight('infopane') == 328)
	{
		varCompactMaps = true;
		
		xHeight('infopane', 450);
		
		fnZoom(varZoomLevel);
		
		var paneleft = xLeft('clusterpane');
		xLeft('quadrantpane', paneleft);
		xLeft('starsystempane', paneleft);
	}
	else
	{
		var quadrantleft = 175;
		var starsystemleft = 292;
		if (xLeft('clusterpane') != 28)
		{
			quadrantleft = 640 - xWidth('quadrantpane') - quadrantleft;
			starsystemleft = 640 - xWidth('starsystempane') - starsystemleft;
		}
		varCompactMaps = false;
		xHeight('infopane', 328);
		xLeft('quadrantpane', quadrantleft);
		xLeft('starsystempane', starsystemleft);
		xVisibility('clusterpane', true);
		xVisibility('quadrantpane', true);
		xVisibility('starsystempane', true);
		
	}
}


//var varMirrorPanes = false;
function fnMirrorPanes()
{
//	 if (varMirrorPanes) varMirrorPanes = false;
//	 else varMirrorPanes = true;
//	 
//	 var varToday = new Date();
//	 var varExpire = new Date(varToday.getTime() + 31 * 24 * 60 * 60 * 1000);
//	 setCookie('varMirrorPanes', varMirrorPanes, varExpire);
	
	xLeft('infopane', (640 - xWidth('infopane') - xLeft('infopane')));
	
	xLeft('planetpane', (640 - xWidth('planetpane') - xLeft('planetpane')));
	xLeft('menu_main', (640 - xWidth('menu_main') - xLeft('menu_main')));
	
	xLeft('clusterpane', (640 - xWidth('clusterpane') - xLeft('clusterpane')));
	xLeft('quadrantpane', (640 - xWidth('quadrantpane') - xLeft('quadrantpane')));
	xLeft('starsystempane', (640 - xWidth('starsystempane') - xLeft('starsystempane')));
	xLeft('menu_map', (640 - xWidth('menu_map') - xLeft('menu_map')));
	
	xLeft('combatalert', (640 - xWidth('combatalert') - xLeft('combatalert')));
	xLeft('mailalert', (640 - xWidth('mailalert') - xLeft('mailalert')));
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
			xVisibility('clusterpane', true);
			xVisibility('quadrantpane', false);
			xVisibility('starsystempane', false);
			break
		case 2:
			varZoomLevel = 2;
			xVisibility('quadrantpane', true);
			xVisibility('starsystempane', false);
			xVisibility('clusterpane', false);
			break
		case 3:
			varZoomLevel = 3;
			xVisibility('starsystempane', true);
			xVisibility('clusterpane', false);
			xVisibility('quadrantpane', false);
			break
	}
}