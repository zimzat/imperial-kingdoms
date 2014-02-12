{* Smarty *}
{include file="header.tpl"}

{if !empty($units)}
		<div id="divQuickAction" name="divQuickAction" style="position: absolute; visibility: hidden;">
			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" type="hidden" value="units_commission" />
			<input name="planet_id" id="planet_id" type="hidden" value="{$planet_id}" />
			<input name="unit_id" id="unit_id" type="hidden" value="" />
			<input name="unit_type" id="unit_type" type="hidden" value="" />
			<table class="forumline" style="width: auto;" cellspacing="1" cellpadding="4">
				<tr>
					<td>
						<b>Units:</b> <input name="units" id="units" class="post" size="4" maxlength="4" value="0" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="commission" id="commission" class="mainoption" value="Commission" onClick="return fnQuickAction();">
					</td>
				</tr>
			</table>
			</form>
		</div>
		
{literal}
		<script language="JavaScript">
		<!--
{/literal}
			var varActionUrl = '{$actionurl}';var varCommissioned = 0;var varError = 0;var varPendingRequests = new Object ();{literal}function fnInitQuickAction(varUnitId,varUnitType,varObject,varEvent){xGetElementById('unit_id').value=varUnitId;xGetElementById('unit_type').value=varUnitType;varX=xPageX(varObject)+xWidth(varObject)+10;varY=xPageY(varObject)+xHeight(varObject)+5;var varClientHeight=xClientHeight()*1;var varScrollTop=xScrollTop()*1;var varHeight=xHeight('divQuickAction')*1;if((varY+varHeight)>(varClientHeight+varScrollTop)){varY=(varClientHeight+varScrollTop)-varHeight;}xVisibility('divQuickAction',false);xMoveTo('divQuickAction',varX,varY);xVisibility('divQuickAction',true);xStopPropagation(varEvent);}function fnQuickAction(){xVisibility('divQuickAction',false);var varXTR=getXTR();if(!varXTR){alert("The wireless connection is unavailable at the moment. Re-routing through cables.");return true;}var varPlanetId=xGetElementById('planet_id').value;var varUnitId=xGetElementById('unit_id').value;var varUnitType=xGetElementById('unit_type').value;var varUnits=xGetElementById('units').value;if(varUnits==0){alert("Must commission at least one unit...");return false;}varPendingRequests[varUnitId]=varXTR;var state_callback=function(){if(varXTR.readyState!=4){return;}if(varXTR.status==200){eval(varXTR.responseText);}else{alert("Error contacting server.");varError=1;}};var error_callback=function(){varError=1;};varXTR.onreadystatechange=state_callback;varXTR.open("POST",varActionUrl,true);var varPostData="fn=units_commission";varPostData+="&mode=js";varPostData+="&planet_id="+varPlanetId;varPostData+="&unit_id="+varUnitId;varPostData+="&unit_type="+varUnitType;varPostData+="&units="+varUnits;varXTR.setRequestHeader("Content-Type","application/x-www-form-urlencoded");varXTR.send(varPostData);var flash=function(){if(varError){}else if(varCommissioned){document.location.href=document.location.href;}else{window.setTimeout(flash,50);}};window.setTimeout(flash,5);return false;}xAddEventListener(document,'click',fnDocumentClick,false);xAddEventListener(xGetElementById('divQuickAction'),'click',fnQuickActionClick,false);{/literal}
{*{literal}
			function fnInitQuickAction (varUnitId, varUnitType, varObject, varEvent)
			{
				xGetElementById('unit_id').value = varUnitId;
				xGetElementById('unit_type').value = varUnitType;
				
				varX = xPageX(varObject) + xWidth(varObject) + 10;
				varY = xPageY(varObject) + xHeight(varObject) + 5;
				
				var varClientHeight = xClientHeight() * 1;
				var varScrollTop = xScrollTop() * 1;
				var varHeight = xHeight('divQuickAction') * 1;
				
				if ((varY + varHeight) > (varClientHeight + varScrollTop))
				{
					varY = (varClientHeight + varScrollTop) - varHeight;
				}
				
				xVisibility('divQuickAction', false);
				xMoveTo('divQuickAction', varX, varY);
				xVisibility('divQuickAction', true);
				
				xStopPropagation(varEvent);
			}
			
			function fnQuickAction ()
			{
				xVisibility('divQuickAction', false);
				
				var varXTR = getXTR();
				if (! varXTR)
				{
					alert ("The wireless connection is unavailable at the moment. Re-routing through cables.");
					return true;
				}
				
				var varPlanetId = xGetElementById('planet_id').value;
				var varUnitId = xGetElementById('unit_id').value;
				var varUnitType = xGetElementById('unit_type').value;
				var varUnits = xGetElementById('units').value;
				
				if (varUnits == 0)
				{
					alert ("Must commission at least one unit...");
					return false;
				}
				
				varPendingRequests[varUnitId] = varXTR;
				
				var state_callback = function ()
				{
					if (varXTR.readyState != 4)
					{
						return;
					}
					
					if (varXTR.status == 200)
					{
						eval(varXTR.responseText);
					}
					else
					{
						alert("Error contacting server.");
						varError = 1;
					}
				};
				
				var error_callback = function ()
				{
					varError = 1;
				};
				
				varXTR.onreadystatechange = state_callback;
				varXTR.open("POST", varActionUrl, true);
				
				var varPostData = "fn=units_commission";
				varPostData += "&mode=js";
				varPostData += "&planet_id=" + varPlanetId;
				varPostData += "&unit_id=" + varUnitId;
				varPostData += "&unit_type=" + varUnitType;
				varPostData += "&units=" + varUnits;
				
				varXTR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				varXTR.send(varPostData);
				
				var flash = function () {
					if (varError) {
						// let timer expire due to error
					} else if (varCommissioned) {
						document.location.href = document.location.href;
					} else {
						window.setTimeout(flash, 50);
					}
				};
				
				window.setTimeout(flash, 5);
				return false;
			}
			
			xAddEventListener(document, 'click', fnDocumentClick, false);
			xAddEventListener(xGetElementById('divQuickAction'), 'click', fnQuickActionClick, false);
{/literal}*}
{literal}
		// -->
		</script>
{/literal}
{/if}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="{if !empty($beingcommissioned)}4{else}3{/if}">Units</td>
			</tr>
{foreach from=$units item=types key=unit_type}
			<tr>
				<td class="subheader" width="100%">{$unit_type|capitalize} Units</td>
				<td class="subheader">Count</td>
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/time.gif" alt="Time:" title="Time:" /></td>
{if !empty($beingcommissioned)}
				<td class="subheader">&nbsp;</td>
{/if}
			</tr>
{foreach from=$types item=unit key=unit_id}{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data" width="100%"><img class="icon qa" onclick="return fnInitQuickAction({$unit_id}, '{$unit_type}', this, event);" src="{$baseurl}styles/{$style}/images/symbols/16x16/quick{$unit_type}.gif" alt="Quick {$unit_type|capitalize}" title="Quick {$unit_type|capitalize}" /> <a href="{$baseurl}units.php?fn=units_info&unit_id={$unit_id}&unit_type={$unit_type}">{$unit.name}</a> (#{$unit_id})</td>
				<td class="{$rowclass} data al-c nowrap">{$unit.count|default:"0"}</td>
				<td class="{$rowclass} data al-r nowrap">{$unit.time}</td>
{if !empty($beingcommissioned)}
				<td class="{$rowclass} data al-r nowrap">{$beingcommissioned.$unit_type.$unit_id}</td>
{/if}
			</tr>
{/foreach}
{foreachelse}
			<tr>
				<td class="al-c i" colspan="{if !empty($beingcommissioned)}4{else}3{/if}">There are no unit blueprints available.</td>
			</tr>
{/foreach}
		</table>

{include file="menu_unit.tpl"}

<br clear="left" />

{include file="menu_building.tpl"}

{include file="footer.tpl"}