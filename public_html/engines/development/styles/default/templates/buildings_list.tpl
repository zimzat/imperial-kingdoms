{* Smarty *}
{include file="header.tpl"}

{if !empty($buildings)}
		<div id="divQuickAction" name="divQuickAction" style="position: absolute; visibility: hidden;">
			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" type="hidden" value="buildings_build" />
			<input name="planet_id" id="planet_id" type="hidden" value="{$planet_id}" />
			<input name="building_id" id="building_id" type="hidden" value="" />
			<table class="forumline" style="width: auto;" cellspacing="1" cellpadding="4">
				<tr>
					<td>
						<b>Cranes:</b> <input name="cranes" id="cranes" class="post" size="3" maxlength="3" value="{$available_cranes}" /> <b>x</b> <input name="planning" id="planning" class="post" size="3" maxlength="3" value="{$available_planning}" />
					</td>
				</tr>
				<tr>
					<td>
{if $warptime != ""}
						<input name="warptime" type="checkbox" /> Warp Time Left: {$warptime}<br />
{/if}
						<input class="mainoption" type="submit" name="build" id="build" value="Build" onClick="return fnQuickAction();">
					</td>
				</tr>
			</table>
			</form>
		</div>
		
{literal}
		<script language="JavaScript">
		<!--
{/literal}
			var varAvailableCranes = {$available_cranes};var varAvailablePlanning = {$available_planning};var varActionUrl = '{$actionurl}';var varBuilt = 0;var varError = 0;var varPendingRequests = new Object ();{literal}function fnInitQuickAction(varBuildingId,varObject,varEvent){var varXTR=getXTR();if(!varXTR){}else{var varPlanetId=xGetElementById('planet_id').value;var state_callback=function(){if(varXTR.readyState!=4){return;}if(varXTR.status==200){eval(varXTR.responseText);xGetElementById('cranes').value=varAvailableCranes;xGetElementById('planning').value=varAvailablePlanning;}};varXTR.onreadystatechange=state_callback;varXTR.open("POST",varActionUrl,true);var varPostData="fn=buildings_available";varPostData+="&mode=js";varPostData+="&planet_id="+varPlanetId;varXTR.setRequestHeader("Content-Type","application/x-www-form-urlencoded");varXTR.send(varPostData);}xGetElementById('building_id').value=varBuildingId;xGetElementById('cranes').value=varAvailableCranes;xGetElementById('planning').value=varAvailablePlanning;varX=xPageX(varObject)+xWidth(varObject)+10;varY=xPageY(varObject)+xHeight(varObject)+5;var varClientHeight=xClientHeight()*1;var varScrollTop=xScrollTop()*1;var varHeight=xHeight('divQuickAction')*1;if((varY+varHeight)>(varClientHeight+varScrollTop)){varY=(varClientHeight+varScrollTop)-varHeight;}xVisibility('divQuickAction',false);xMoveTo('divQuickAction',varX,varY);xVisibility('divQuickAction',true);xStopPropagation(varEvent);}function fnQuickAction(){xVisibility('divQuickAction',false);var varXTR=getXTR();if(!varXTR){alert("The wireless connection is unavailable at the moment. Re-routing through cables.");return true;}var varPlanetId=xGetElementById('planet_id').value;var varBuildingId=xGetElementById('building_id').value;var varCranes=xGetElementById('cranes').value;var varPlanning=xGetElementById('planning').value;var varWarpTime=xGetElementById('warptime').checked;varPendingRequests[varBuildingId]=varXTR;if(varCranes<=0||varPlanning<=0){alert("Must use at least one crane and planning facility.");return false;}var state_callback=function(){if(varXTR.readyState!=4){return;}if(varXTR.status==200){eval(varXTR.responseText);}else{alert("Error contacting server.");varError=1;}};var error_callback=function(){varError=1;};varXTR.onreadystatechange=state_callback;varXTR.open("POST",varActionUrl,true);var varPostData="fn=buildings_build";varPostData+="&mode=js";varPostData+="&planet_id="+varPlanetId;varPostData+="&building_id="+varBuildingId;varPostData+="&cranes="+varCranes;varPostData+="&planning="+varPlanning;if(varWarpTime)varPostData+="&warptime=on";varXTR.setRequestHeader("Content-Type","application/x-www-form-urlencoded");varXTR.send(varPostData);var flash=function(){if(varError){}else if(varBuilt){document.location.href=document.location.href;}else{window.setTimeout(flash,50);}};window.setTimeout(flash,5);return false;}xAddEventListener(document,'click',fnDocumentClick,false);xAddEventListener(xGetElementById('divQuickAction'),'click',fnQuickActionClick,false);{/literal}
{*{literal}
			function fnInitQuickAction (varBuildingId, varObject, varEvent)
			{
				var varXTR = getXTR();
				if (! varXTR)
				{
					// skip
				}
				else
				{
					var varPlanetId = xGetElementById('planet_id').value;
					
					var state_callback = function ()
					{
						if (varXTR.readyState != 4)
						{
							return;
						}
						
						if (varXTR.status == 200)
						{
							eval(varXTR.responseText);
							xGetElementById('cranes').value = varAvailableCranes;
							xGetElementById('planning').value = varAvailablePlanning;
						}
					};
					
					varXTR.onreadystatechange = state_callback;
					varXTR.open("POST", varActionUrl, true);
					
					var varPostData = "fn=buildings_available";
					varPostData += "&mode=js";
					varPostData += "&planet_id=" + varPlanetId;
					
					varXTR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					varXTR.send(varPostData);
				}
				
				xGetElementById('building_id').value = varBuildingId;
				xGetElementById('cranes').value = varAvailableCranes;
				xGetElementById('planning').value = varAvailablePlanning;
				
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
				var varBuildingId = xGetElementById('building_id').value;
				var varCranes = xGetElementById('cranes').value;
				var varPlanning = xGetElementById('planning').value;
				var varWarpTime = xGetElementById('warptime').checked;
				varPendingRequests[varBuildingId] = varXTR;
				
				
				if (varCranes <= 0 || varPlanning <= 0)
				{
					alert ("Must use at least one crane and planning facility.");
					return false;
				}
				
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
				
				var varPostData = "fn=buildings_build";
				varPostData += "&mode=js";
				varPostData += "&planet_id=" + varPlanetId;
				varPostData += "&building_id=" + varBuildingId;
				varPostData += "&cranes=" + varCranes;
				varPostData += "&planning=" + varPlanning;
				if (varWarpTime) varPostData += "&warptime=on";
				
				varXTR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				varXTR.send(varPostData);
				
				var flash = function () {
					if (varError) {
						// let timer expire due to error
					} else if (varBuilt) {
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

		<table class="forumline">
			<tr>
				<td class="header" colspan="{if !empty($beingbuilt)}7{else}6{/if}">Buildings: {$planet_name} (#{$planet_id})</td>
			</tr>
			<tr>
				<td class="subheader">Name</td>
{if !empty($beingbuilt)}
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/time.gif" alt="Time:" title="Time:" /></td>
{/if}
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food:" title="Food:" /></td>
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers:" title="Workers:" /></td>
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy:" title="Energy:" /></td>
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/minerals.gif" alt="Minerals:" title="Minerals:" /></td>
				<td class="subheader"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/crane.gif" alt="Built:" title="Built:" /></td>
			</tr>
{foreach from=$buildings item=building}
{assign var="building_id" value=$building.building_id}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data"><img class="icon qa" onclick="return fnInitQuickAction({$building_id}, this, event);" src="{$baseurl}styles/{$style}/images/symbols/16x16/quickbuild.gif" alt="Quick Build" title="Quick Build" /> <a href="{$baseurl}buildings.php?fn=buildings_info&building_id={$building_id}&planet_id={$planet_id}">{$building.name}</a></td>
{if !empty($beingbuilt)}
				<td class="{$rowclass} data al-r nowrap">{$beingbuilt.$building_id|default:"&nbsp;"}</td>
{/if}
				<td class="{$rowclass} data al-r nowrap">{$building.foodrate|default:"0"}</td>
				<td class="{$rowclass} data al-r nowrap">{$building.workersrate|default:"0"}</td>
				<td class="{$rowclass} data al-r nowrap">{$building.energyrate|default:"0"}</td>
				<td class="{$rowclass} data al-r nowrap">{$building.mineralsrate|default:"0"}</td>
				<td class="{$rowclass} data al-r nowrap">{$building.built|default:"0"}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="al-c i" colspan="6">No buildings available on this planet.</td>
			</tr>
{/foreach}
			<tr>
				<td class="subheader">Total</td>
{if !empty($beingbuilt)}
				<td class="subheader data nowrap">&nbsp;</td>
{/if}
				<td class="subheader">{$total.foodrate|default:"0"}</td>
				<td class="subheader">{$total.workersrate|default:"0"}</td>
				<td class="subheader">{$total.energyrate|default:"0"}</td>
				<td class="subheader">{$total.mineralsrate|default:"0"}</td>
				<td class="subheader">{$total.built|default:"0"}</td>
			</tr>	
		</table>

{include file="menu_building.tpl"}

<br clear="left" />

{include file="menu_unit.tpl"}

{include file="footer.tpl"}