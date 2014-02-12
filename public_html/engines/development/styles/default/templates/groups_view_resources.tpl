{* Smarty *}

{if !empty($resources.planet) || !empty($resources.group)}
			<tr>
				<td class="catBottom" align="center" valign="middle" nowrap="nowrap">Resources</td>
			</tr>
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="groups_modify_resources" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<input name="group_view" type="hidden" value="{$group_view}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td>FE: </td>
							<td>{$resources.planet.minerals.fe|default:"-"}</td>
							<td><input name="resources[minerals][fe]" type="text" align="right" class="post" value="{$resources.group.minerals.fe|default:"0"}" /></td>
							<td></td>
							
							<td>O: </td>
							<td>{$resources.planet.minerals.o|default:"-"}</td>
							<td><input name="resources[minerals][o]" type="text" align="right" class="post" value="{$resources.group.minerals.o|default:"0"}" /></td>
							<td></td>
						</tr>
						<tr>
							<td>SI: </td>
							<td>{$resources.planet.minerals.si|default:"-"}</td>
							<td><input name="resources[minerals][si]" type="text" align="right" class="post" value="{$resources.group.minerals.si|default:"0"}" /></td>
							<td></td>
							
							<td>MG: </td>
							<td>{$resources.planet.minerals.mg|default:"-"}</td>
							<td><input name="resources[minerals][mg]" type="text" align="right" class="post" value="{$resources.group.minerals.mg|default:"0"}" /></td>
						</tr>
						<tr>
							<td>NI: </td>
							<td>{$resources.planet.minerals.ni|default:"-"}</td>
							<td><input name="resources[minerals][ni]" type="text" align="right" class="post" value="{$resources.group.minerals.ni|default:"0"}" /></td>
							<td></td>
							
							<td>S: </td>
							<td>{$resources.planet.minerals.s|default:"-"}</td>
							<td><input name="resources[minerals][s]" type="text" align="right" class="post" value="{$resources.group.minerals.s|default:"0"}" /></td>
							<td></td>
						</tr>
						<tr>
							<td>HE: </td>
							<td>{$resources.planet.minerals.he|default:"-"}</td>
							<td><input name="resources[minerals][he]" type="text" align="right" class="post" value="{$resources.group.minerals.he|default:"0"}" /></td>
							<td></td>
							
							<td>H: </td>
							<td>{$resources.planet.minerals.h|default:"-"}</td>
							<td><input name="resources[minerals][h]" type="text" align="right" class="post" value="{$resources.group.minerals.h|default:"0"}" /></td>
						</tr>
						<tr>
							<td>Food:</td>
							<td>{$resources.planet.food|default:"-"}</td>
							<td><input name="resources[food]" type="text" align="right" class="post" value="{$resources.group.food|default:"0"}" /></td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td>Workers:</td>
							<td>{$resources.planet.workers|default:"-"}</td>
							<td><input name="resources[workers]" type="text" align="right" class="post" value="{$resources.group.workers|default:"0"}" /></td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td>Energy:</td>
							<td>{$resources.planet.energy|default:"-"}</td>
							<td><input name="resources[energy]" type="text" align="right" class="post" value="{$resources.group.energy|default:"0"}" /></td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td colspan="7" class="row1" align="right" valign="middle" nowrap="nowrap"><span class="postdetails"><input name="load" type="submit" class="mainoption" value="Load" /> <input name="unload" type="submit" class="mainoption" value="Unload" /></span></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
{/if}