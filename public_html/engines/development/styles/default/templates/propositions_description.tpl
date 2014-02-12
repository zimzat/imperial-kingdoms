{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition: Change Kingdom Description</th>
			</tr>
			<tr>
				<td valign="middle" align="justify" class="row1" width="100%">Enter the text for the kingdom description below. Be sure to spellcheck your description. Once submitted there is no way to change the description in the proposition.</td>
			</tr>
			<tr>
				<td valign="middle" align="center" class="row1" width="100%">
					<form method="post" action="{$actionurl}" name="propositions" id="propositions">
					<input name="fn" type="hidden" value="propositions_description" />
					<input name="action" type="hidden" value="details_submit" />
					
					The new kingdom description being proposed:<br />
					<textarea name="description" id="description" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 1024);" rows="1" cols="80" wrap="virtual" class="post">{$description}</textarea><br />
					<input readonly class="post" id="description_count" name="description_count" size="5" type="text" value="1024" tabindex="-1" /> Characters Left<br />
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
	fnCountCharacters(xGetElementById('description'), 512);
	fnCountCharacters(xGetElementById('statement'), 512);
// -->
</script>
{/literal}

{include file="menu_status_kingdom.tpl"}

{include file="footer.tpl"}