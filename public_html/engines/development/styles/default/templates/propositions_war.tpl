{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition: War Declaration</th>
			</tr>
			<tr>
				<td valign="middle" align="justify" class="row1" width="100%">Enter the ID of the kingdom you would like to declare war on.</td>
			</tr>
			<tr>
				<td valign="middle" align="center" class="row1" width="100%">
					<form method="post" action="{$actionurl}" name="propositions" id="propositions">
					<input name="fn" type="hidden" value="propositions_war" />
					<input name="action" type="hidden" value="details_submit" />
					
					Declare war on: (Kingdom ID):<br />
					<input type="text" name="target_kingdom_id" class="post" /><br />
					<br />
					
					Make a statement to convince your kingdom to vote for your proposal. Should it pass this statement will also be in the news.<br />
					<textarea class="post" name="statement" id="statement" onkeyup="fnResizeTextArea(this); fnCountCharacters(this, 512);" rows="1" cols="80" wrap="virtual"></textarea><br />
					<input class="post" name="statement_count" id="statement_count" type="text" size="4" value="512" tabindex="-1" readonly /> Characters Left<br />
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