{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

{if !empty($declarations)}
{include file="military_declarations.tpl"}
		
		<br clear="all" />
{/if}
		
		<table border="0" width="100%" align="center" cellpadding="4" cellspacing="1" class="forumline">
			<tr>
				<th width="100%" align="center" class="thCornerR" nowrap="nowrap">Military Overview</th>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="catBottom" valign="middle" align="center" nowrap="nowrap">Direction</td>
							<td class="catBottom" valign="middle" align="center" nowrap="nowrap">Planet</td>
							<td class="catBottom" valign="middle" align="center" width="100%" nowrap="nowrap">Group</td>
							<td class="catBottom" valign="middle" align="center" nowrap="nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/time.gif" alt="Time:" title="Time:" /></td>
						</tr>
{foreach from=$incomingfleets item=fleet key=fleet_id}
{cycle values="row1,row2" assign="rowclass"}
{if $fleet.direction == 'Incoming'}{assign var="alert_tag" value="_alert"}{else}{assign var="alert_tag" value=""}{/if}
						<tr>
							<td class="{$rowclass}{$alert_tag}" nowrap="nowrap">{$fleet.direction}</td>
							<td class="{$rowclass}{$alert_tag}" nowrap="nowrap">P#{$fleet.planet_id} {$fleet.planetname}</td>
							<td class="{$rowclass}{$alert_tag}">N#{$fleet.navygroup_id} {$fleet.navygroupname}</td>
							<td class="{$rowclass}{$alert_tag}" nowrap="nowrap">{$fleet.time}</td>
						</tr>
{foreachelse}
						<tr>
							<td colspan="3" class="row1" align="center" valign="middle" nowrap="nowrap"><span class="postdetails">There are no outgoing or incoming fleets.</span></td>
						</tr>
{/foreach}
					</table>
				</td>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="catBottom" valign="middle" align="center" width="100%">Planet</td>
							<td class="catBottom" valign="middle" align="center" nowrap="nowrap"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/time.gif" alt="Time:" title="Time:" /></td>
						</tr>
{foreach from=$combatlocations item=combat key=combat_id}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass}" width="100%">P#{$combat.planet_id} {$combat.planetname}</td>
							<td class="{$rowclass}" nowrap="nowrap">{$combat.time}</td>
						</tr>
{foreachelse}
						<tr>
							<td colspan="2" class="row1" align="center" valign="middle" nowrap="nowrap"><span class="postdetails">No combat is taking place anywhere.</span></td>
						</tr>
{/foreach}
					</table>
				</td>
			</tr>
		</table>

{include file="menu_military.tpl"}

{include file="footer.tpl"}