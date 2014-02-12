{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table class="forumline" cellpadding="0" cellspacing="0" >
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td class="header">{$blueprint.name}</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			{include file="blueprints_stats.tpl" blueprint_display="stats"}
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			{if $type != "weapon"}{include file="blueprints_stats.tpl" blueprint_display="weapons"}{/if}
		</td>
		<td valign="top" width="50%">
			{include file="resourcecosts.tpl" resources=$blueprint.resources resource_title="Commission Costs"}
		</td>
	</tr>
</table>

{include file="menu_research.tpl"}

{include file="footer.tpl"}