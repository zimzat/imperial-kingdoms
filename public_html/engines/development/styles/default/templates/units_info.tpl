{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table class="forumline" cellpadding="0" cellspacing="0" >
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellpadding="4" cellspacing="1">
				<tr>
					<td class="header">{$unit.name}</td>
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
			{include file="blueprints_stats.tpl" blueprint_display="weapons"}
			
			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" id="fn" type="hidden" value="units_commission" />
			<input name="planet_id" id="planet_id" type="hidden" value="{$planet_id}" />
			<input name="unit_type" id="unit_type" type="hidden" value="{$unit_type}" />
			<input name="unit_id" id="unit_id" type="hidden" value="{$unit_id}" />
			<table width="100%" border="0" cellpadding="0" cellspacing="1">
				<tr>
					<td class="al-c">
						Units: <input name="units" type="text" class="post" maxlength="4" size="4" value="{$unit_count|default:"1"}" />
					</td>
				</tr>
				<tr>
					<td class="al-c">
						<input type="submit" name="commission" id="commission" class="mainoption" value="Commission" />
					</td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top" width="50%">
			{include file="resourcecosts.tpl" resources=$unit.resources resource_title="Commission Costs"}
		</td>
	</tr>
</table>

{include file="menu_unit.tpl"}

<br clear="left" />

{include file="menu_building.tpl"}

{include file="footer.tpl"}