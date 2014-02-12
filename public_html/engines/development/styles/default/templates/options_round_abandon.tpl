{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_round_delete" />
		<table width="100%" class="forumline">
			<tr>
				<td class="header">Abandon Round</td>
			</tr>
			<tr>
				<td>
					Please enter your password to confirm the intention of abandoning your player.<br />
					<input class="field" type="password" name="password" size="25" maxlength="100" />
				</td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" name="confirm_abandon" /> 
					By checking here you understand that you are abandoning your player.<br />
					<ul>
					<li>You will be able to create a new player.</li>
					<li>Your current player will remain in the round out of your control.</li>
					<li>You <b>will not</b> be able to choose this name again.</li>
					</ul>
				</td>
			</tr>
			<tr>
				<td class="al-c"><input type="submit" name="abandon" class="mainoption" value="Abandon" /></td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

{include file="footer.tpl"}