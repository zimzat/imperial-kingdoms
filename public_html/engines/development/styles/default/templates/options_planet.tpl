{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_planet_save" />
		<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline" align="center">
			<tr>
				<th height="25" class="thHead" nowrap="nowrap">Select Round</th>
			</tr>
			<tr>
				<td class="row1">
					<table align="center" border="0" cellpadding="3" cellspacing="1">
						<tr>
							<td colspan="2" align="left">
								Please enter the planet code you would like to claim:
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="text" name="code" width="32" />
							</td>
						</tr>
						<tr align="center">
							<td colspan="2"><input type="submit" name="select" class="mainoption" value="Claim" />&nbsp;&nbsp;<input type="reset" value="Reset" name="reset" class="liteoption" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

		<br clear="both">

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_round_delete" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<td valign="top" align="center"><input onclick="return confirm('By leaving the round you can start again, however your \nplayer and kingdom name cannot be re-used.\nStill abandon ship?');" class="liteoption" name="abandon" type="submit" value="Abandon Player" /></td>
			</tr>
		</table>
		</form>

{include file="footer.tpl"}