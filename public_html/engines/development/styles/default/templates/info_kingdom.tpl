{* Smarty *}

{include file="header.tpl" pagebackground="true"}

		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="header" colspan="3">Kingdom Info</td>
			</tr>
{* Name, Number *}
			<tr>
				<td class="row1" colspan="3"><img id="avatar" src="{$siteurl}images/avatars/kingdoms/{$kingdom.image}" style="align: left;" />{$kingdom.name} (#{$kingdom.kingdom_id})</td>
			</tr>
{if $kingdom.member == true}
{* Food *}
			<tr>
				<td class="row1 al-c nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food:" /></td>
				<td class="row1 al-l nowrap" width="50%">{$kingdom.food}</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.foodrate|default:"0"}</td>
			</tr>
{* Workers *}
			<tr>
				<td class="row1 al-c nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers:" /></td>
				<td class="row1 al-l nowrap" width="50%">{$kingdom.workers}</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.workersrate|default:"0"}</td>
			</tr>
{* Energy *}
			<tr>
				<td class="row1 al-c nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy:" /></td>
				<td class="row1 al-l nowrap" width="50%">{$kingdom.energy}</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.energyrate|default:"0"}</td>
			</tr>
{* Minerals *}
			<tr>
				<td class="row1 al-c nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/minerals.gif" alt="Minerals:" /></td>
				<td class="row1 al-l nowrap" width="50%">{$kingdom.minerals}</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.mineralsrate|default:"0"}</td>
			</tr>
{/if}
{* Planets *}
			<tr>
				<td class="row1 al-l nowrap" width="50%" colspan="2">Planets:</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.planets|default:"-"}</td>
			</tr>
{* Members *}
			<tr>
				<td class="row1 al-l nowrap" width="50%" colspan="2">Members:</td>
				<td class="row1 al-r nowrap" width="50%">{$kingdom.members|default:"-"}</td>
			</tr>
		</table>

{include file="js_avatar.tpl"}

{include file="footer.tpl"}