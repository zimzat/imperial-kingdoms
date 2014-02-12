{* Smarty *}

{if $output == 'html'}
{include file="header.tpl"}
{literal}
		<script language="javascript">
		<!--
			var varPlanets = new Array();
			var varPlayers = new Array();
			
{/literal}
{include file="map.tpl" output="javascript"}
{literal}
		// -->
		</script>
{/literal}
		
		<table style="border-collapse: collapse;" border="0" cellpadding="0" cellspacing="0">
{section name="y" loop="16"}
			<tr>
{section name="x" loop="16"}
{assign var="planet_id" value=$map[x][y]}
				<td style="border: 1px solid black; width: 30px; height: 30px;" align="center" valign="middle">{if $planet_id != ""}<img style="cursor: pointer; cursor: hand;" onMouseOver="fnPlanetHover({$planet_id});" onClick="fnPlanetClick({$planet_id});" src="{$baseurl}styles/{$style}/images/planet{$planets.$planet_id.picture}.gif"><div style="top: 0px; left: 0px;">{$planet_id}</div>{else}&nbsp;{/if}</td>
{/section}
			</tr>
{/section}
		</table>
{include file="footer.tpl"}
{elseif $output == 'javascript'}
{foreach name=planets from=$planets item=planet key=planet_id}{foreach name=attribute from=$planet item=value key=attribute}
varPlanets[{$planet_id}][{$attribute}] = "{$value}";
{/foreach}{/foreach}

{foreach name=players from=$players item=player key=player_id}{foreach name=attribute from=$player item=value key=attribute}
varPlayers[{$player_id}][{$attribute}] = "{$value}";
{/foreach}{/foreach}
{/if}