{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Settings: {$planet_name} (#{$planet_id})</td>
			</tr>
			<tr>
				<td class="row1">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="planet_settings_set" />
					<input name="planet_id" type="hidden" value="{$planet_id}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="row1 data">Name:</td>
							<td class="row1"><input class="post" name="planet_name" value="{$planet_name}" /></td>
							<td class="row1"><input class="mainoption" name="rename" type="submit" value="Rename" /></td>
						</tr>
						<tr>
							<td class="row1 data">Owner:</td>
							<td class="row1"><select name="transfer_id"><option></option>{foreach from=$players item=player key=player_id}<option value="{$player_id}">{$player.name}</option>{/foreach}</select></td>
							<td class="row1"><input class="mainoption" name="transfer" type="submit" value="Transfer" /></td>
						</tr>
						<tr>
							<td class="row1 data">Research:</td>
							<td class="row1"><input type="checkbox" name="confirm_research_cancel" /> Confirm Cancel</td>
							<td class="row1"><input class="mainoption" name="cancel_research" type="submit" value="Cancel" /></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
		</form>

{include file="menu_planet.tpl"}

{include file="footer.tpl"}