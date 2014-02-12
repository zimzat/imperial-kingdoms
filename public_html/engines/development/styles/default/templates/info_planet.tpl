{* Smarty *}
{include file="header.tpl" pagebackground="true"}

{if $planet.permissions.build == true}
{literal}
		<script type="text/javascript" language="javascript">
		<!--
			fnRefreshIf('./buildings.php', parent.main);
		// -->
		</script>
{/literal}
{/if}
		
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td class="header" colspan="3">Planet Info</td>
			</tr>
{* Name, Location, Number *}
			<tr>
{cycle values="row1,row2" assign="rowclass"}
				<td class="{$rowclass}" colspan="3">
					{if $planet.permissions.owner == true}<a href="{$baseurl}planet.php" target="main">{/if}{$planet.name} (#{$planet.planet_id}){if $planet.permissions.owner == true}</a>{/if}<br />
					Q: <a href="{$baseurl}map.php?fn=quadrant&quadrant_id={$planet.quadrant_id}" target="quadrant" onClick="window.fnZoom(2);">{$planet.quadrant_id}</a> &nbsp; S: <a href="{$baseurl}map.php?fn=starsystem&starsystem_id={$planet.starsystem_id}" target="starsystem" onClick="window.fnZoom(3);">{$planet.starsystem_id}</a> &nbsp; P: {$planet.planet_id}
				</td>
			</tr>
{if $planet.permissions.build == true || $planet.permissions.research == true || $planet.permissions.commission == true}
{* Food *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food" title="Food" /></td>
				<td class="{$rowclass} data al-l nowrap" width="50%">{$planet.food}</td>
				<td class="{$rowclass} data al-r nowrap{if $planet.fooddeficiency != ""} b red{/if}" width="50%">{$planet.foodrate|default:"0"}</td>
			</tr>
{* Workers *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers:" /></td>
				<td class="{$rowclass} data al-l nowrap" width="50%">{$planet.workers}</td>
				<td class="{$rowclass} data al-r nowrap{if $planet.workersdeficiency != ""} b red{/if}" width="50%">{$planet.workersrate|default:"0"}</td>
			</tr>
{* Energy *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy:" /></td>
				<td class="{$rowclass} data al-l nowrap" width="50%">{$planet.energy}</td>
				<td class="{$rowclass} data al-r nowrap{if $planet.energydeficiency != ""} b red{/if}" width="50%">{$planet.energyrate|default:"0"}</td>
			</tr>
{* Minerals *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/minerals.gif" alt="Minerals:" /></td>
				<td class="{$rowclass} data al-l nowrap" width="50%">{$planet.minerals}</td>
				<td class="{$rowclass} data al-r nowrap{if $planet.mineralsdeficiency != ""} b red{/if}" width="50%">{$planet.mineralsrate|default:"0"}</td>
			</tr>
{* Building *}
{if $planet.permissions.build == true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/crane.gif" alt="Building:" /></td>
				<td class="{$rowclass} data al-l" width="50%">{$planet.building.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap" width="50%">{$planet.building.time|default:"-"}</td>
			</tr>
{/if}
{* Researching *}
{if $planet.permissions.research == true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/flask.gif" alt="Researching:" /></td>
				<td class="{$rowclass} data al-l" width="50%">{$planet.researching.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap" width="50%">{$planet.researching.time|default:"-"}</td>
			</tr>
{/if}
{* Army *}
{if $planet.permissions.commission == true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/armyunits.gif" alt="Army:" /></td>
				<td class="{$rowclass} data al-l" width="50%">{$planet.army.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap" width="50%">{$planet.army.time|default:"-"}</td>
			</tr>
{* Navy *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-c nowrap"><img src="{$baseurl}styles/{$style}/images/symbols/16x16/navyunits.gif" alt="Navy:" /></td>
				<td class="{$rowclass} data al-l" width="50%">{$planet.navy.name|default:"-"}</td>
				<td class="{$rowclass} data al-r nowrap" width="50%">{$planet.navy.time|default:"-"}</td>
			</tr>
{/if}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-l nowrap" width="50%" colspan="2">Next Update:</td>
				<td class="{$rowclass} data al-r nowrap" width="50%">{$planet.nextupdate|default:"er"}</td>
			</tr>
{/if}
{* Score *}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-l nowrap" colspan="3"><div style="float: left;">Score:</div><div style="float: right;">{$planet.score|default:"-"}{if $planet.score_peak > $planet.score} ({$planet.score_peak}){/if}</div></td>
			</tr>
{* Owner *}
{if $planet.permissions.owner != true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-l nowrap" colspan="3"><div style="float: left;">Owner:</div><div style="float: right;">{if !empty($planet.player_id)}<a href="{$baseurl}status.php?fn=status_player&player_id={$planet.player_id}" target="main">{$planet.player_name|default:"-"}</a>{else}-{/if}</div></td>
			</tr>
{/if}
{* Kingdom *}
{if $planet.permissions.grant != true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-l nowrap" colspan="3"><div style="float: left;">Kingdom:</div><div style="float: right;">{if !empty($planet.kingdom_id)}<a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$planet.kingdom_id}" target="main">{$planet.kingdom_name|default:"-"}</a>{else}-{/if}</div></td>
			</tr>
{/if}
{if $planet.npc_player == true}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data al-l b nowrap" colspan="3">NPC Player</td>
			</tr>
{/if}
		</table>

{include file="footer.tpl"}