{* Smarty *}

		<table class="forumline" cellpadding="4" cellspacing="1">
{if $planet_form == true}
			<tr>
				<td class="header" colspan="7">Planet Management System</td>
			</tr>
{/if}
{assign var="planet_number" value=0}
{foreach from=$planets item=planet}
{cycle values="row2,row1" assign="rowclass"}
{assign var="planet_number" value=$planet_number+1}
{if $planet_form == true}{capture assign="row_click"} onclick="fnSelectPlanet({$planet_number}, event);"{/capture}{/if}
			<tr{$row_click}>
{if $planet_form == true}
				<td class="{$rowclass} data al-c nowrap" rowspan="5"><input id="planet_{$planet_number}" type="checkbox" name="planet_ids[{$planet.planet_id}]" /></td>
{/if}
				<td class="{$rowclass} data" rowspan="5"{if $planet_form != true} colspan="2"{/if}><img alt="P#{$planet.planet_id}" title="P#{$planet.planet_id}" src="{$baseurl}styles/{$style}/images/map/planet{$planet.type}-e.gif" /> {$planet.name}</td>
			</tr>
			
			<tr{$row_click}>
				<td class="{$rowclass} data nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food" title="Food" /> {$planet.food}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.foodrate}</td>
				
				<td class="{$rowclass} data"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/crane.gif" alt="Constructing:" /> {$planet.construction.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.construction.completion|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.cranes}x{$planet.planning}</td>
			</tr>
			
			<tr{$row_click}>
				<td class="{$rowclass} data nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers" title="Workers" /> {$planet.workers}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.workersrate}</td>
				
				<td class="{$rowclass} data"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/flask.gif" alt="Researching:" /> {$planet.research.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.research.completion|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.researchbonus}%</td>
			</tr>
			
			<tr{$row_click}>
				<td class="{$rowclass} data nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy" title="Energy" /> {$planet.energy}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.energyrate}</td>
				
				<td class="{$rowclass} data" colspan="3"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/armyunits.gif" alt="Army:" /> [army production]</td>
			</tr>
			
			<tr{$row_click}>
				<td class="{$rowclass} data nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/minerals.gif" alt="Minerals" title="Minerals" /> {$planet.minerals}</td>
				<td class="{$rowclass} data al-r nowrap">&nbsp;{$planet.mineralsrate}</td>
				
				<td class="{$rowclass} data" colspan="3"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/navyunits.gif" alt="Navy:" /> [navy production]</td>
			</tr>
{foreachelse}
			<tr>
				<td class="row2 al-c i" colspan="5">You do not own any planets.</td>
			</tr>
{/foreach}
		</table>