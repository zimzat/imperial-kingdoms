{* Smarty *}
{include file="header.tpl"}

{*		<div id="divQuickAction" name="divQuickAction" style="position: absolute; visibility: hidden;">
			<form method="post" action="{$actionurl}" name="QuickForm" id="QuickForm">
			<input name="fn" type="hidden" value="research_research" />
			<input name="concept_id" id="concept_id" type="hidden" value="" />
			<table class="forumline" cellspacing="1" cellpadding="4" border="0">
				<tr>
					<td>
{include file="research_planets.tpl"}
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="research" id="research" class="mainoption" value="Research" onClick="return fnQuickResearch();">
					</td>
				</tr>
			</table>
			</form>
		</div>
		
{literal}
		<script language="JavaScript">
		<!--
{/literal}
			var varActionUrl = '{$actionurl}';
			var varResearching = 0;
			var varError = 0;
			var varPendingRequests = new Object ();
{literal}
			
			function fnInitQuickAction (varConceptId, varObject, varEvent)
			{
				xGetElementById('concept_id').value = varConceptId;
				
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
			
			function fnQuickResearch ()
			{
				xVisibility('divQuickAction', false);
				
				var varXTR = getXTR();
				if (! varXTR)
				{
					alert ("The wireless connection is unavailable at the moment. Re-routing through cables.");
					xGetElementById('QuickForm').submit();
					return false;
				}
				
				var varConceptId = xGetElementById('concept_id').value;
				var varFormPlanetId = document.forms['QuickForm'].planet_id;
				var varPlanetId = varFormPlanetId[varFormPlanetId.selectedIndex].value;
				var varWarpTime = xGetElementById('warptime').checked;
				varPendingRequests[varConceptId] = varXTR;
				
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
						alert("Error contacting server. Rerouting through conventional methods.");
						xGetElementById('QuickForm').submit();
						varError = true;
					}
				};
				
				var error_callback = function ()
				{
					varError = true;
				};
				
				varXTR.onreadystatechange = state_callback;
				varXTR.open("POST", varActionUrl, true);
				
				var varPostData = "fn=research_research";
				varPostData += "&mode=js";
				varPostData += "&planet_id=" + varPlanetId;
				varPostData += "&concept_id=" + varConceptId;
				if (varWarpTime) varPostData += "&warptime=on";
				
				varXTR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				varXTR.send(varPostData);
				
				var flash = function () {
					if (varError) {
						// let timer expire due to error
					} else if (varResearching) {
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
		// -->
		</script>
{/literal}*}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<td class="header" width="100%">Design Upgrades</td>
				<td class="header"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/flask.gif" alt="Research" title="Research" /></td>
{if !empty($beingupgraded)}
				<td class="header">&nbsp;</td>
{/if}
			</tr>
{foreach from=$designs item=type key=type_name}
			<tr>
				<td class="subheader" colspan="{if !empty($beingupgraded)}3{else}2{/if}">{$type_name|capitalize}</td>
			</tr>
{foreach from=$type item=upgrade key=upgrade_id}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data" width="100%">{*<img class="icon aq" onclick="return fnInitQuickAction({$upgrade_id}, this, event);" src="{$baseurl}styles/{$style}/images/symbols/16x16/quickresearch.gif" alt="Quick Research" title="Quick Research" /> *}<a href="{$baseurl}designs.php?fn=designs_info&{$type_name}design_id={$upgrade_id}">{$upgrade.name} Mk {$upgrade.techlevel_upgrade}</a></td>
				<td class="{$rowclass} data al-r nowrap">{$upgrade.time}</td>
{if !empty($beingupgraded)}
				<td class="{$rowclass} data al-r nowrap">{$beingupgraded.$type_name.$upgrade_id|default:"&nbsp;"}</td>
{/if}
			</tr>
{/foreach}
{foreachelse}
			<tr>
				<td class="al-c i" colspan="2">No upgrades available to be researched.</td>
			</tr>
{/foreach}
		</table>
		
{include file="menu_research.tpl"}

{include file="footer.tpl"}