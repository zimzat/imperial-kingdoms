{* Smarty *}

{if !empty($groups)}
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<form method="post" action="{$actionurl}" name="cargo_groups" id="cargo_groups">
					<input name="fn" type="hidden" value="groups_modify_groups" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<input name="group_view" type="hidden" value="{$group_view}" />
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td class="subheader">Group Name</td>
							<td class="subheader">Size</td>
							<td class="subheader">Location</td>
							<td class="subheader">Action</td>
						</tr>
{foreach from=$groups item=group key=armygroup_id}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass}" align="left">A#{$armygroup_id} <a href="{$baseurl}groups.php?fn=groups_view&group_view=units&group_type=army&group_id={$armygroup_id}">{$group.name}</a></td>
							<td class="{$rowclass}" align="right">{$group.size}</td>
							<td class="{$rowclass}" align="right">{$group.location|capitalize}</td>
							<td class="{$rowclass} al-r">
{if $onplanet == true}
								<select name="groups[{$armygroup_id}]" style="width: 100%;">
									<option value=""></option>
{if $group.location == "planet"}
									<option value="group">Move To Group</option>
{/if}
{if $group.location == "group"}
									<option value="planet">Move To Planet</option>
{/if}
								</select>
{else}
								In Transit
{/if}
							</td>
						</tr>
{/foreach}
						<tr>
							<td colspan="4" class="row1" align="right" valign="middle" nowrap="nowrap">
								<input name="move" type="submit" class="mainoption" value="Move" />
							</td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
{/if}