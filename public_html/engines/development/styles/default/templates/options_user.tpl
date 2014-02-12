{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_user_save" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="2" width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap"><b>User Options</b></th>
			</tr>
			<tr>
				<td colspan="2" class="catBottom" valign="middle" align="center" height="25" width="100%"><span class="topictitle">User Preferences</span></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Style:</span></td>
				<td class="{$rowclass}" valign="top">
					{html_options options=$styles name="style" selected=$style}
				</td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Decimal Symbol:</span></td>
				<td class="{$rowclass}" valign="top"><input name="decimal_symbol" type="text" class="post" size="1" value="{$options.preferences.decimal_symbol}" /></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Thousands Seperator:</span></td>
				<td class="{$rowclass}" valign="top"><input name="thousands_seperator" type="text" class="post" size="1" value="{$options.preferences.thousands_seperator}" /></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Timezone:</span></td>
				<td class="{$rowclass}" valign="top">
					 {html_options values=$timezones output=$timezones name="timezone" selected=$options.preferences.timezone}
				</td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Timestamp Format:</span></td>
				<td class="{$rowclass}" valign="top"><input name="timestamp_format" type="text" class="post" size="25" value="{$options.preferences.timestamp_format}" /></td>
			</tr>
			<tr>
				<td colspan="2" class="catBottom" align="center" valign="middle" height="25" width="100%"><input name="submit" type="submit" class="mainoption" value="Save" /> <input name="reset" type="reset" class="liteoption" value="Reset" /></td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

{include file="footer.tpl"}