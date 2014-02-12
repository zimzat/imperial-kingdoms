{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition: Change Kingdom Avatar</th>
			</tr>
			<tr>
				<td valign="middle" align="justify" class="row1" width="100%">Upload the image to be the new kingdom avatar.</td>
			</tr>
			<tr>
				<td valign="middle" align="center" class="row1" width="100%">
					<form method="post" action="{$actionurl}" name="propositions" id="propositions" enctype="multipart/form-data">
					<input name="fn" type="hidden" value="propositions_avatar" />
					<input name="action" type="hidden" value="details_submit" />
					
					The new kingdom avatar being proposed:<br />
					<input type="hidden" name="MAX_FILE_SIZE" value="40960" />
					<input type="file" name="avatar" id="avatar" class="post" /><br />
					Max File Size: 40,960 bytes<br />
					<br />
					
					Make a statement to convince your kingdom to vote for your proposal.<br />
					<textarea class="post" name="statement" id="statement" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 512);" rows="1" cols="80" wrap="virtual"></textarea><br />
					<input class="post" name="statement_count" id="statement_count" type="text" size="4" value="512" tabindex="-1" readonly /> Characters Left<br />
					<br />
					
					<input class="mainoption" type="submit" value="Submit" />
					</form>
				</td>
			</tr>
		</table>
		
{literal}
<script language="JavaScript">
<!-- 
	fnCountCharacters(xGetElementById('statement'), 512);
// -->
</script>
{/literal}

{include file="menu_status_kingdom.tpl"}

{include file="footer.tpl"}