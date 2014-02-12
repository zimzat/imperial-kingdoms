{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input name="fn" type="hidden" value="groups_process_create" />
		<table border="0" align="center" cellpadding="4" cellspacing="1" class="forumline">
			<tr>
				<th width="100%" colspan="2" align="center" class="thCornerR" nowrap="nowrap">Create Group</th>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap"><span class="postdetails">Name: </span></td>
				<td class="row1" valign="middle" nowrap="nowrap"><input name="name" type="text" class="post" maxlength="20" size="20" value="{$name}" /></td>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap"><span class="postdetails">Planet: </span></td>
				<td class="row1" valign="middle" nowrap="nowrap">
					<select name="planet_id" class="post">
						<option value=""></option>
{foreach from=$planets item=planet key=list_planet_id}
						<option value="{$list_planet_id}"{if $planet_id == $list_planet_id} selected="selected"{/if}>{$planet}</option>
{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap"><span class="postdetails">Type: </span></td>
				<td class="row1" valign="middle" nowrap="nowrap">
					<select name="group_type" class="post">
						<option value=""></option>
						<option value="army"{if $group_type == "army"} selected="selected"{/if}>Army</option>
						<option value="navy"{if $group_type == "navy"} selected="selected"{/if}>Navy</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="row1" colspan="2" align="center" valign="middle" nowrap="nowrap"><input name="submit" type="submit" class="mainoption" value="Create" /> <input name="reset" type="reset" class="liteoption" value="Reset" /></td>
			</tr>
		</table>
		</form>

{include file="menu_groups.tpl"}

<br clear="left" />

{include file="menu_military.tpl"}

{include file="footer.tpl"}