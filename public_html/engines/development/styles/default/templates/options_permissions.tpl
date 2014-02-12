{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Global Permissions</td>
			</tr>
			<tr>
				<td class="row1">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="options_permissions_set" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" colspan="3">Player</td>
							<td class="subheader">Build</td>
							<td class="subheader">Research</td>
							<td class="subheader">Commission</td>
							<td class="subheader">Military</td>
						</tr>
						<tr>
							<td class="row1 al-c" colspan="3"><select style="width: 100%;" multiple="multiple" size="5" name="player_id[]">{foreach from=$players item=player key=player_id}<option value="{$player_id}">{$player.name}</option>{/foreach}</select></td>
							<td class="row1 al-c"><input type="checkbox" name="permissions[build]" /></td>
							<td class="row1 al-c"><input type="checkbox" name="permissions[research]" /></td>
							<td class="row1 al-c"><input type="checkbox" name="permissions[commission]" /></td>
							<td class="row1 al-c"><input type="checkbox" name="permissions[military]" /></td>
						</tr>
						<tr>
							<td class="subheader">Remove</td>
							<td class="subheader">&nbsp;</td>
							<td class="subheader">Name</td>
							<td class="subheader" colspan="5"><input class="mainoption" type="submit" value="Grant/Deny" /></td>
						</tr>
{foreach from=$permissions item=permission key=permission_id}
{cycle values="row2,row1" assign="rowclass"}
						<tr>
							<td class="{$rowclass} data al-c nowrap"><input type="checkbox" name="permission_id[{$permission_id}]" /></td>
							<td class="{$rowclass} data">{$permission.player_name}</td>
							<td class="{$rowclass} data">{$permission.name}</td>
							<td class="{$rowclass} data al-c nowrap">{if $permission.build == "1"}Granted{else}Denied{/if}</td>
							<td class="{$rowclass} data al-c nowrap">{if $permission.research == "1"}Granted{else}Denied{/if}</td>
							<td class="{$rowclass} data al-c nowrap">{if $permission.commission == "1"}Granted{else}Denied{/if}</td>
							<td class="{$rowclass} data al-c nowrap">{if $permission.military == "1"}Granted{else}Denied{/if}</td>
						</tr>
{foreachelse}
						<tr>
							<td class="row2 al-c i" colspan="6">No permissions set.</td>
						</tr>
{/foreach}
					</table>
					</form>
				</td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

{include file="footer.tpl"}