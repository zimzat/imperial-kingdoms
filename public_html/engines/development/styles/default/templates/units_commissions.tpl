{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="commissioncancel">
		<input type="hidden" name="fn" value="units_cancel" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<td class="header" colspan="4">Unit Commission: {$planet_name} (#{$planet_id})</td>
			</tr>
			<tr>
				<td class="subheader">&nbsp;</td>
				<td class="subheader" width="100%">Name</td>
				<td class="subheader">Number</td>
				<td class="subheader">Completion</td>
			</tr>
{foreach from=$commissions item=task}
{assign var="unit_id" value=$task.unit_id}
{assign var="unit_type" value=$task.attribute}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data nowrap"><input name="tasks[{$task.task_id}]" type="checkbox" value="{$task.task_id}" /></td>
				<td class="{$rowclass} data" width="100%"><a href="{$baseurl}units.php?fn=units_info&unit_id={$task.unit_id}&unit_type={$unit_type}&planet_id={$planet_id}">{$units.$unit_type.$unit_id}</a></td>
				<td class="{$rowclass} data al-c nowrap">{$task.number}</td>
				<td class="{$rowclass} data al-r nowrap">{$task.completion|default:"-"}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="al-c i" colspan="4">No commission tasks on this planet.</td>
			</tr>
{/foreach}
		</table>
		</form>
		
{include file="menu_unit.tpl"}

<div class="button"><a href="#" onClick="xGetElementById('commissioncancel').submit(); return false;">Cancel</a></div>

<br clear="left" />

{include file="menu_building.tpl"}

{include file="footer.tpl"}