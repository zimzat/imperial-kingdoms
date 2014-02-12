{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline">
			<tr>
				<td class="header" colspan="4">Recent Battles</td>
			</tr>
			<tr>
				<td class="subheader">&nbsp;</td>
				<td class="subheader" width="40%">Location</td>
				<td class="subheader" width="30%">Date</td>
				<td class="subheader" width="30%">Status</td>
			</tr>
{foreach from=$combatreports item=planet_reports key=planet_id}
{foreach from=$planet_reports name=reports item=combatreport key=combatreport_id}
{if $smarty.foreach.reports.iteration == 2}
			<tbody id="row_{$planet_id}">
{/if}
			<tr{if !$smarty.foreach.reports.first} style="display: none;"{/if}>
				<td class="row1 nowrap">
{if $smarty.foreach.reports.first && !$smarty.foreach.reports.last}
					<img class="icon qa" onclick="return fnExpandCollapseRows({$planet_id});" src="{$baseurl}styles/{$style}/images/symbols/16x16/tree-plus.gif" alt="+" title="Collapse/Expand List" />
{elseif !$smarty.foreach.reports.first && !$smarty.foreach.reports.last}
					<img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/tree-line-extend.gif" alt="|-" title="" />
{elseif !$smarty.foreach.reports.first && $smarty.foreach.reports.last}
					<img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/tree-line-stop.gif" alt="|-" title="" />
{else}
					&nbsp;
{/if}
				</td>
				<td class="row1"><a href="{$baseurl}military.php?fn=reports_view&combatreport_id={$combatreport_id}">{$combatreport.location}</a></td>
				<td class="row1 nowrap">{$combatreport.date}</td>
				<td class="row1 al-r nowrap">{$combatreport.status}</td>
			</tr>
{if !$smarty.foreach.reports.first && $smarty.foreach.reports.last}
			</tbody>
{/if}
{/foreach}
{foreachelse}
			<tr>
				<td class="row1 al-c i" colspan="4" width="*">No battle reports available.</td>
			</tr>
{/foreach}
		</table>

{include file="menu_military.tpl"}

<br clear="left" />

{include file="menu_reports.tpl"}

{include file="footer.tpl"}