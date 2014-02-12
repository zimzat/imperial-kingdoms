{* Smarty *}
{include file="header.tpl" pagebackground="true"}

<table class="map_starsystem" cellpadding="0" cellspacing="0">
{section name="y" loop="7"}
	<tr>
{section name="x" loop="7"}
		<td class="map_tile">{if $starsystem[x][y].exists == true}<a id="link-{$smarty.section.x.index}-{$smarty.section.y.index}" onClick="fnMoveMark({$smarty.section.x.index},{$smarty.section.y.index});" href="{$baseurl}info.php?fn=info_planet&planet_id={$starsystem[x][y].planet_id}" target="info">{/if}<img src="{$baseurl}styles/{$style}/images/map/planet{if $starsystem[x][y].exists == true || $starsystem[x][y].kingdom == true || $starsystem[x][y].target == true}{$starsystem[x][y].type}-{if $starsystem[x][y].exists == true}e{/if}{if $starsystem[x][y].kingdom == true}k{/if}{if $starsystem[x][y].target == true}t{/if}{/if}.gif" class="map_tile"{if $starsystem[x][y].exists == true} title="P#{$starsystem[x][y].planet_id} {$starsystem[x][y].planet_name}"{/if} />{if $starsystem[x][y].exists == true}</a>{/if}</td>
{/section}
	</tr>
{/section}
</table>

<div id="map_mark"><a id="link-mark" target="info"><img class="map_tile" src="{$baseurl}styles/{$style}/images/map/mark.gif" /></a></div>

{include file="footer.tpl"}