{* Smarty *}

			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
{if !empty($units)}
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="groups_modify_units" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<input name="group_view" type="hidden" value="{$group_view}" />
{/if}
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" width="100%">Unit Name</td>
							<td class="subheader">{if $group_type == "navy"}Cargo {/if}Size</td>
							<td class="subheader">Planet</td>
							<td class="subheader">Group</td>
						</tr>
{if !empty($units)}
{foreach from=$units item=unit key=unit_id}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass} data">{$unit.name}</td>
							<td class="{$rowclass} data al-r">{if $group_type == "navy"}{$unit.cargo}{else}{$unit.size}{/if}</td>
							<td class="{$rowclass} data al-r">{$unit.planet|default:"-"}</td>
							<td class="{$rowclass} data al-r"><input name="units[{$unit_id}]" type="text" align="right" class="post" value="{$unit.group|default:"0"}" /></td>
						</tr>
{/foreach}
						<tr>
							<td colspan="4" class="al-r"><input name="load" type="submit" class="mainoption" value="Load" /> <input name="unload" type="submit" class="mainoption" value="Unload" /></td>
						</tr>
{else}
						<tr>
							<td colspan="4" class="al-c i">There are no units in or available to put in this group.</td>
						</tr>
{/if}
					</table>
{if !empty($units)}
					</form>
{/if}
				</td>
			</tr>
{if $group_empty == true}
			<tr>
				<td>
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="groups_abandon" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" width="100%">Abandon Group</td>
						</tr>
						<tr>
							<td class="al-c i"><input name="abandon" type="submit"  onclick="return confirm('Are you sure you want to abandon this group?');" class="mainoption" value="Abandon Group?" /></td>
						</tr>
					</table>
				</td>
			</tr>
{/if}