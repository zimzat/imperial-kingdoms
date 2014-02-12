function fnGetInformation(varInformationObject, varDepth)
{
	varDepth = parseInt(varDepth) - 1;
	
	var i = 0;
	var varAlertMsg = '<div style="border: 1px solid grey; float: left;">';
	for (x in varInformationObject)
	{
		if (i > 0) varAlertMsg = varAlertMsg + '<br />';
		varAlertMsg = varAlertMsg + x + ": " + varInformationObject[x];
		
		if ((typeof(varInformationObject[x]) == 'function' || typeof(varInformationObject[x]) == 'object') && varDepth > 0)
		{
			varAlertMsg = varAlertMsg + '<div style="left: +30px;">' + fnGetInformation(varInformationObject[x], varDepth) + '</div>';
		}
		
		i = i + 1;
	}
	
	varAlertMsg = varAlertMsg + "</div>\n";
	
	return varAlertMsg;
}

function fnNewWindowMessage(varWindowMessage)
{
	var varNewWindow = window.open('', 'alert', 'width=600,height=600');
	varNewWindow.document.write(varWindowMessage);
	varNewWindow.document.close;
}

// fnNewWindowMessage(fnGetInformation(window), 2));