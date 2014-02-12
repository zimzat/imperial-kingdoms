{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_player_save" />
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="2"><b>Player Options</b></td>
			</tr>
			<tr>
				<td class="nowrap">Description:</td>
				<td width="100%"><textarea class="post" id="description" name="description" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 512);" rows="1" cols="60" wrap="virtual">{$options.description}</textarea><br />
				<input readonly class="post" id="description_count" name="description_count" size="4" type="text" value="512" tabindex="-1" /> Characters Left</td>
			</tr>
			<tr>
				<td class="nowrap">Avatar:</td>
				<td width="100%">
					{if $options.image != ""}<center><img src="{$options.image}" /></center><br />{/if}
					<input name="avatar_reset" type="checkbox" class="post" /> Check this box to reset your avatar.<br />
					<input name="MAX_FILE_SIZE" type="hidden" value="40960" /><input name="avatar" type="file" class="post" /><br />
					Max File Size: 40,960 bytes
				</td>
			</tr>
			<tr>
				<td class="subheader" colspan="2"><input name="submit" type="submit" class="mainoption" value="Save" /> <input name="reset" type="reset" class="liteoption" value="Reset" /></td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

{literal}
<script language="JavaScript">
<!-- 
	countCharacters(xGetElementById('description'), xGetElementById('characters'), 1024);
// -->
</script>
{/literal}

{if $options.image != ""}
{include file="js_avatar.tpl"}
{/if}

{include file="footer.tpl"}