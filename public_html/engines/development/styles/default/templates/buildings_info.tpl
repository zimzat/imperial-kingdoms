{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table class="forumline" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr>
					<td class="header">Building Info: {$planet_name} (#{$planet_id})</td>
				</tr>
				<tr>
					<td class="subheader">{$building.name}</td>
				</tr>
				<tr>
					<td>{if $building.image != ""}<img class="building" src="{$siteurl}images/illustrations/{$building.image}" />{/if}{$building.description}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">Production & Upkeep</td>
				</tr>
				<tr>
					<td><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food" title="Food" /></td>
					<td>{$building.foodrate}</td>
					<td></td>
					
					<td><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers" title="Workers" /></td>
					<td>{$building.workersrate}</td>
					<td></td>
					
					<td><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy" title="Energy" /></td>
					<td>{$building.energyrate}</td>
					<td></td>
					
					<td><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/minerals.gif" alt="Minerals" title="Minerals" /></td>
					<td>{$building.mineralsrate}</td>
				</tr>
{if $building.maxbuildable > 0}
				<tr>
					<td class="row1" colspan="11">Max Buildings: {$building.maxbuildable}</td>
				</tr>
{/if}
{if $building.demolishable > 0}
				<tr>
					<td class="row1" colspan="11">Cannot be demolished</td>
				</tr>
{/if}
			</table>
{if $building.demolishable <= 0}
{literal}
			<script>
			<!--
				function fnConfirmDemolish()
				{
					if (confirm('Demolish building(s)?'))
					{
						xGetElementById('fn').value = 'buildings_demolish';
						return true;
					}
					else
					{
						return false;
					}
				}
			// -->
			</script>
{/literal}
{/if}
			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" id="fn" type="hidden" value="buildings_build" />
			<input name="planet_id" id="planet_id" type="hidden" value="{$planet_id}" />
			<input name="building_id" id="building_id" type="hidden" value="{$building.building_id}" />
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">Build{if $building.demolishable <= 0}/Demolish{/if}</td>
				</tr>
				<tr>
					<td class="al-c b">
						Cranes: <input name="cranes" id="cranes" class="post" size="3" maxlength="3" value="{$available_cranes}" /> x <input name="planning" id="planning" class="post" size="3" maxlength="3" value="{$available_planning}" />
					</td>
				</tr>
				<tr>
					<td class="al-c">
{if $warptime != ""}
						<input name="warptime" type="checkbox" /> Warp Time Left: {$warptime}<br />
{/if}
						<input type="submit" name="build" id="build" class="mainoption" value="Build" />{if $building.demolishable <= 0} <input type="submit" name="build" id="build" class="mainoption" value="Demolish" onClick="return fnConfirmDemolish();" />{/if}
					</td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top" width="50%">
			{include file="resourcecosts.tpl" resources=$building.resources resource_title="Construction Costs"}
		</td>
	</tr>
</table>

{include file="menu_building.tpl"}

<br clear="left" />

{include file="menu_unit.tpl"}

{include file="footer.tpl"}