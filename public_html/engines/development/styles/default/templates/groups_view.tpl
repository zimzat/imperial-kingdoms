{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Modify Group</td>
			</tr>
{if $group_view == "units" || $group_view == ""}
{include file="groups_view_units.tpl"}
{elseif $group_view == "targets"}
{include file="groups_view_targets.tpl"}
{elseif $group_view == "cargo"}
{include file="groups_view_resources.tpl"}
{include file="groups_view_groups.tpl"}
{elseif $group_view == "destination"}
{include file="groups_view_destination.tpl"}
{/if}

		</table>

<br clear="left" />
{include file="menu_groups_view.tpl"}

<br clear="left" />
{include file="menu_groups.tpl"}

<br clear="left" />
{include file="menu_military.tpl"}

{include file="footer.tpl"}