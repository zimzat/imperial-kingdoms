{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="4">{$group_type|capitalize} Groups</td>
			</tr>
			<tr>
				<td class="subheader">&nbsp;</td>
				<td class="subheader" width="100%">Name</td>
				<td class="subheader">Location</td>
				<td class="subheader">{if $group_type == 'army'}Size{else}Cargo{/if}</td>
			</tr>
{foreach from=$groups item=group key=group_id}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data nowrap">{if $group_type == 'army'}A#{else}N#{/if}{$group_id}</td>
				<td class="{$rowclass} data"><a href="{$baseurl}groups.php?fn=groups_view&group_type={$group_type}&group_id={$group_id}">{$group.name}</a></td>
				<td class="{$rowclass} data al-c nowrap">{if $group_type == 'navy'}<img src="{$baseurl}styles/{$style}/images/symbols/fleet-{if $group.transit == true}transit{else}orbit{/if}.gif" width="16" height="6" /> {/if}{$group.location}</td>
				<td class="{$rowclass} data al-c nowrap">{$group.size}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="al-c i" colspan="4">You don't have any {$group_type} groups.</td>
			</tr>
{/foreach}
		</table>

{include file="menu_groups.tpl"}

<br clear="left" />

{include file="menu_military.tpl"}

{include file="footer.tpl"}