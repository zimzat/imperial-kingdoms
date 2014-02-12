{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input name="fn" id="fn" type="hidden" value="blueprints_create" />
		<input name="name" id="name" type="hidden" value="{$design.name}" />
		<input name="{$design.type}design_id" id="{$design.type}design_id" type="hidden" value="{$design.design_id}" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Select Weapons</th>
			</tr>
			<tr>
				<td>Max Weapon Weight: {$weaponsload}g</td>
			</tr>
{section name="slot" loop=$weaponslots}
			<tr>
				<td>
					<select name="weaponslot[{$smarty.section.slot.index}]" class="post">
						<option value="" selected="selected">None</option>
{foreach from=$weapons item=weapon key=weapon_id}
						<option value="{$weapon_id}">{$weapon.name}</option>
{/foreach}
					</select>
					
					<select name="weapons[{$smarty.section.slot.index}]" class="post">
{foreach from=$weaponsperslot item=weaponslotcount}
						<option value="{$weaponslotcount}">{$weaponslotcount}</option>
{/foreach}
					</select>
				</td>
			</tr>
{/section}
			<tr>
				<td><input name="submit" class="mainoption" type="submit" value="Create"></form></td>
			</tr>
		</table>
		
{include file="menu_research.tpl"}

<br clear="left" />

{include file="menu_blueprints.tpl"}

{include file="footer.tpl"}