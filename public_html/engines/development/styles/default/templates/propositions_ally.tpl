{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition: Ally Declaration</th>
			</tr>
			<tr>
				<td valign="middle" align="justify" class="row1" width="100%">Enter the ID of the kingdom you would like to propose an alliance with.</td>
			</tr>
			<tr>
				<td valign="middle" align="center" class="row1" width="100%">
					<form method="post" action="{$actionurl}" name="propositions" id="propositions">
					<input name="fn" type="hidden" value="propositions_ally" />
					<input name="action" type="hidden" value="details_submit" />
					
					Propose Alliance with: (Kingdom ID)<br />
					<input type="text" name="target_kingdom_id" class="post" /><br />
					<br />
					
					Make a statement to convince your kingdom and your potential ally to vote for your proposal. Should it pass this statement will also be included in the news.<br />
					<textarea name="statement" id="statement" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 512);" rows="1" cols="60" wrap="virtual" class="post"></textarea><br />
					<input readonly class="post" id="statement_count" name="statement_count" size="4" type="text" value="512" tabindex="-1" /> Characters Left<br />
					<br />
					
					<input class="mainoption" type="submit" value="Continue" />
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