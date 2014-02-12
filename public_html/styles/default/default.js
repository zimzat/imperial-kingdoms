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
