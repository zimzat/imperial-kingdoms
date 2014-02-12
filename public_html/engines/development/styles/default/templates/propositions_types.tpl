{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition Types</th>
			</tr>
			<tr>
				<td valign="middle" align="justify" class="row1" width="100%">Below is a list of propositions available to propose. Select one and continue to enter the details required.</td>
			</tr>
			<tr>
				<td valign="middle" align="center" class="row1" width="100%">
					<form method="post" action="{$actionurl}" name="propositions" id="propositions">
					<input name="action" type="hidden" value="details" />
					
					{html_options options=$types name="fn"}<br />
					<br />
					
					<input class="mainoption" type="submit" value="Continue" />
					</form>
				</td>
			</tr>
		</table>

{include file="menu_status_kingdom.tpl"}

{include file="footer.tpl"}