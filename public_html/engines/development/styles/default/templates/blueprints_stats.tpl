{* Smarty *}

{if !empty($blueprint_stats)}
{if $blueprint_display == "stats" || $blueprint_display == ""}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
{if $blueprint_stats.accuracy == ""}
	<tr>
		<td class="row1 al-c b">Attack</td>
		<td class="row2 al-c b">Defense</td>
		<td class="row1 al-c b">Armor</td>
		<td class="row2 al-c b">Hull</td>
{if $blueprint_stats.speed != "" || $blueprints_stats.cargo != ""}
		<td class="row1 al-c b">Speed</td>
		<td class="row2 al-c b">Cargo</td>
{/if}
		<td class="row1 al-c b">Size</td>
		<td class="row2 al-c b">Level</td>
	</tr>
	<tr>
		<td class="row1 al-c">{$blueprint_stats.attack}%</td>
		<td class="row2 al-c">{$blueprint_stats.defense}%</td>
		<td class="row1 al-c">{$blueprint_stats.armor}</td>
		<td class="row2 al-c">{$blueprint_stats.hull}</td>
{if $blueprint_stats.speed != "" || $blueprints_stats.cargo != ""}
		<td class="row1 al-c">{$blueprint_stats.speed}</td>
		<td class="row2 al-c">{$blueprint_stats.cargo}kg</td>
{/if}
		<td class="row1 al-c">{$blueprint_stats.size}kg</td>
		<td class="row2 al-c">{$blueprint_stats.techlevel}</td>
	</tr>
{else}
	<tr>
		<td class="row1 al-c b">Accuracy</td>
		<td class="row2 al-c b">Rate of Fire</td>
		<td class="row1 al-c b">Area Damage</td>
		<td class="row2 al-c b">Power</td>
		<td class="row1 al-c b">Damage</td>
		<td class="row2 al-c b">Size</td>
		<td class="row1 al-c b">Level</td>
	</tr>
	<tr>
		<td class="row1 al-c">{$blueprint_stats.accuracy}%</td>
		<td class="row2 al-c">{$blueprint_stats.rateoffire}</td>
		<td class="row1 al-c">{$blueprint_stats.areadamage}</td>
		<td class="row2 al-c">{$blueprint_stats.power}</td>
		<td class="row1 al-c">{$blueprint_stats.damage}</td>
		<td class="row2 al-c">{$blueprint_stats.size}g</td>
		<td class="row1 al-c">{$blueprint_stats.techlevel}</td>
	</tr>
{/if}
</table>
{elseif $blueprint_display == "weapons"}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="subheader" colspan="2">Weapons</td>
	</tr>
{foreach from=$blueprint_stats.weapons item=weapon key=weapon_id}{cycle values="row1,row2" assign="rowclass"}
	<tr>
		<td class="{$rowclass} al-r nowrap">{$weapon.count}</td>
		<td class="{$rowclass}" width="100%"><a href="{$baseurl}blueprints.php?fn=blueprints_info&blueprint_id={$weapon_id}&type=weapon">{$weapon.name}</a></td>
	</tr>
{foreachelse}
	<tr>
		<td class="al-c i" colspan="2">None</td>
	</tr>
{/foreach}
</table>
{/if}
{/if}