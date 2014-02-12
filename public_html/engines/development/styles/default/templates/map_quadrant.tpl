{* Smarty *}
{include file="header.tpl" pagebackground="true"}

<table class="map_quadrant" cellpadding="0" cellspacing="0">
{section name="y" loop="7"}
	<tr>
{section name="x" loop="7"}
		<td class="map_tile">{if $quadrant[x][y].exists == true}<a id="link-{$smarty.section.x.index}-{$smarty.section.y.index}" onClick="fnMoveMark({$smarty.section.x.index},{$smarty.section.y.index});" href="{$baseurl}map.php?fn=starsystem&starsystem_id={$quadrant[x][y].starsystem_id}{if $quadrant[x][y].target != ""}&target_id={$target_id}{/if}" target="starsystem" onClick="fnZoom(3);">{/if}<img src="{$baseurl}styles/{$style}/images/map/starsystem{if $quadrant[x][y].exists == true || $quadrant[x][y].kingdom == true || $quadrant[x][y].target == true}-{if $quadrant[x][y].exists == true}e{/if}{if $quadrant[x][y].kingdom == true}k{/if}{if $quadrant[x][y].target == true}t{/if}{/if}.gif" class="map_tile"{if $quadrant[x][y].exists == true} title="S#{$quadrant[x][y].starsystem_id}"{/if} />{if $quadrant[x][y].exists == true}</a>{/if}</td>
{/section}
	</tr>
{/section}
</table>

<div id="map_mark"><a id="link-mark" target="starsystem"><img class="map_tile" src="{$baseurl}styles/{$style}/images/map/mark.gif" /></a></div>

{include file="footer.tpl"}