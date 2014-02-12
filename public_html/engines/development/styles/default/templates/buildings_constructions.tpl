{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="constructioncancel">
		<input type="hidden" name="fn" value="buildings_cancel" />
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="5">Building Construction: {$planet_name} (#{$planet_id})</td>
			</tr>
			<tr>
				<td class="subheader">&nbsp;</td>
				<td class="subheader" width="100%">Name</td>
				<td class="subheader">Cranes</td>
				<td class="subheader">Planning</td>
				<td class="subheader">Completion</td>
			</tr>
{foreach from=$construction item=task}
{cycle values="row1,row2" assign="rowclass"}
{assign var="building_id" value=$task.building_id}
			<tr>
				<td class="{$rowclass} data nowrap"><input name="tasks[{$task.task_id}]" type="checkbox" value="{$task.task_id}" /></td>
				<td class="{$rowclass} data" width="100%"><a href="{$baseurl}buildings.php?fn=buildings_info&building_id={$task.building_id}&planet_id={$planet_id}">{$buildings.$building_id.name}</a></td>
				<td class="{$rowclass} data nowrap">{$task.cranes|default:"0"}</td>
				<td class="{$rowclass} data nowrap">{$task.planning|default:"0"}</td>
				<td class="{$rowclass} data nowrap">{$task.completion|default:"-"}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="al-c i" colspan="5">No construction tasks on this planet.</td>
			</tr>
{/foreach}
		</table>
		</form>
		
{include file="menu_building.tpl"}

{if !empty($construction)}
<div class="button"><a href="#" onClick="xGetElementById('constructioncancel').submit(); return false;">Cancel</a></div>
{/if}
<br clear="left" />

{include file="menu_unit.tpl"}

{include file="footer.tpl"}