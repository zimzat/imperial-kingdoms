{* Smarty *}
{include file="header.tpl" pagebackground="true"}

<table class="map_cluster" cellpadding="0" cellspacing="0">
{section name="y" loop="7"}
	<tr>
{section name="x" loop="7"}
		<td class="map_tile">{if $cluster[x][y].active == true}<a id="link-{$smarty.section.x.index}-{$smarty.section.y.index}" onClick="fnMoveMark({$smarty.section.x.index},{$smarty.section.y.index});" href="{$baseurl}map.php?fn=quadrant&quadrant_id={$cluster[x][y].quadrant_id}{if $cluster[x][y].target != ""}&target_id={$target_id}{/if}" target="quadrant" onClick="fnZoom(2);">{/if}<img src="{$baseurl}styles/{$style}/images/map/quadrant{if $cluster[x][y].active == true || $cluster[x][y].kingdom == true || $cluster[x][y].target == true}-{if $cluster[x][y].active == true}a{/if}{if $cluster[x][y].kingdom == true}k{/if}{if $cluster[x][y].target == true}t{/if}{/if}.gif" class="map_tile"{if $cluster[x][y].active == true} title="Q#{$cluster[x][y].quadrant_id}"{/if} />{if $cluster[x][y].active == true}</a>{/if}</td>
{/section}
	</tr>
{/section}
</table>

<div id="map_mark"><a id="link-mark" target="quadrant"><img class="map_tile" src="{$baseurl}styles/{$style}/images/map/mark.gif" /></a></div>

{include file="footer.tpl"}