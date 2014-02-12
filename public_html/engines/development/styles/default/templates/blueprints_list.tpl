{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="activate">
		<input name="fn" id="fn" type="hidden" value="blueprints_deactivate" />
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header"></td>
				<td class="header" width="100%">Blueprints</td>
				<td class="header">Type</td>
				<td class="header">Level</td>
			</tr>
{foreach from=$blueprints item=types key=type}
			<tr>
				<td class="subheader" colspan="4">{$type|capitalize}</td>
			</tr>
{foreach from=$types item=blueprint key=blueprint_id}
{cycle values="row1,row2" assign="rowclass"}
{if $blueprint.active == 0}{assign var="inactive_unit" value=" i"}{else}{assign var="inactive_unit" value=""}{/if}
			<tr>
				<td class="{$rowclass} data nowrap{$inactive_unit}"><input name="{$type}[]" type="checkbox" value="{$blueprint_id}" /></td>
				<td class="{$rowclass} data nowrap{$inactive_unit}" width="100%"><a href="{$baseurl}blueprints.php?fn=blueprints_info&type={$type}&blueprint_id={$blueprint_id}">{$blueprint.name}</a> (#{$blueprint_id})</td>
				<td class="{$rowclass} data al-c nowrap{$inactive_unit}">{$blueprint.type}</td>
				<td class="{$rowclass} data al-c nowrap{$inactive_unit}">{$blueprint.techlevel}</td>
			</tr>
{/foreach}
{foreachelse}
			<tr>
				<td class="al-c i" colspan="3">There are no blueprints created.</td>
			</tr>
{/foreach}
		</table>
		</form>
		
{include file="menu_blueprints.tpl"}

<br clear="left" />

{include file="menu_research.tpl"}

{include file="footer.tpl"}