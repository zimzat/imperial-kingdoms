{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_save" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="2" width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap"><b>Options</b></th>
			</tr>
			
			<tr>
				<td  colspan="2" class="catBottom" valign="middle" align="center" height="25" width="100%"><span class="topictitle">Player Information</span></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top" width="30%"><span class="gen">Description:</span></td>
				<td class="{$rowclass}" valign="top"><textarea name="description" class="post" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 512);" cols="60" rows="1" wrap="virtual">{$options.description}</textarea></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Avatar:</span></td>
				<td class="{$rowclass}" valign="top">
					{if $options.image != ""}<center><img src="{$options.image}" /></center><br />{/if}
					<input name="avatar_reset" type="checkbox" class="post" /> Check this box to reset your avatar.<br />
					<input name="MAX_FILE_SIZE" type="hidden" value="40960" /><input name="avatar" type="file" class="post" />
				</td>
			</tr>
			
			<tr>
				<td colspan="2" class="catBottom" valign="middle" align="center" height="25" width="100%"><span class="topictitle">User Preferences</span></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Style:</span></td>
				<td class="{$rowclass}" valign="top">
					<select name="style" class="post">
{foreach from=$options.styles item=stylename}
						<option value="{$stylename}">{$stylename|capitalize:true}</option>
{foreachelse}
						<option value="default">Default</option>
{/foreach}
					</select>
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
					 <select name="timezone" class="post">
{foreach from=$timezones item=timezone}
						 <option value="{$timezone}"{if $options.preferences.timezone == $timezone} selected="selected"{/if}>GMT{if $timezone <> 0} {$timezone} Hours{/if}</option>
{/foreach}
					 </select>
				</td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Timestamp Format:</span></td>
				<td class="{$rowclass}" valign="top"><input name="timestamp_format" type="text" class="post" size="25" value="{$options.preferences.timestamp_format}" /></td>
			</tr>
			<tr>
				<td colspan="2" class="catBottom" align="center" valign="middle" height="25" width="100%"><span class="topictitle">Game Commands</span></td>
			</tr>
			<tr>
				<td class="{$rowclass}" valign="top"><span class="gen">Abandon Ship:</span></td>
				<td class="{$rowclass}" valign="top"><input onclick="return confirm('By leaving the game you can start again, however your \nplayer and kingdom name cannot be re-used.\nStill abandon ship?');" class="post" name="abandon" type="checkbox" value="abandon" /></td>
			</tr>
			<tr>
				<td colspan="2" class="catBottom" align="center" valign="middle" height="25" width="100%"><input name="submit" type="submit" class="mainoption" value="Save" /> <input name="reset" type="reset" class="liteoption" value="Reset" /></td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

{include file="footer.tpl"}