{* Smarty *}

{if $group_type == "navy"}
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="groups_modify_destination" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<input name="group_view" type="hidden" value="{$group_view}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="catBottom" valign="middle" align="left" colspan="2" nowrap="nowrap">Travel Coordinates</td>
						</tr>
						<tr>
							<td align="left" valign="middle" nowrap="nowrap">Planet #:</td>
							<td align="left" valign="middle" nowrap="nowrap"><input name="destination[planet_id]" type="text" class="post" size="4" maxlength="6" /></td>
{*							<td class="row2" align="center" valign="middle" rowspan="3" nowrap="nowrap">OR</td>
							<td align="left" valign="middle" nowrap="nowrap">Cluster:</td>
							<td align="left" valign="middle" nowrap="nowrap"><input disabled="disabled" name="destination[cluster][x]" type="text" class="post" size="1" maxlength="1" />, <input disabled="disabled" name="destination[cluster][y]" type="text" class="post" size="1" maxlength="1" /></td>
						</tr>
						<tr>
							<td class="row2" align="center" valign="middle" colspan="2" nowrap="nowrap">OR</td>
							<td align="left" valign="middle" nowrap="nowrap">Quadrant:</td>
							<td align="left" valign="middle" nowrap="nowrap"><input disabled="disabled" name="destination[quadrant][x]" type="text" class="post" size="1" maxlength="1" />, <input disabled="disabled" name="destination[quadrant][y]" type="text" class="post" size="1" maxlength="1" /></td>
						</tr>
						<tr>
							<td align="left" valign="middle" nowrap="nowrap">Absolute:</td>
							<td align="left" valign="middle" nowrap="nowrap"><input disabled="disabled" name="destination[absolute][x]" type="text" class="post" size="3" maxlength="3" />, <input disabled="disabled" name="destination[absolute][y]" type="text" class="post" size="3" maxlength="3" /></td>
							<td align="left" valign="middle" nowrap="nowrap">Star System:</td>
							<td align="left" valign="middle" nowrap="nowrap"><input disabled="disabled" name="destination[starsystem][x]" type="text" class="post" size="1" maxlength="1" />, <input disabled="disabled" name="destination[starsystem][y]" type="text" class="post" size="1" maxlength="1" /></td>*}
						</tr>
						<tr>
							<td colspan="2" class="row1" align="right" valign="middle" nowrap="nowrap"><input name="move" type="submit" class="mainoption" value="Travel" /></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
{/if}