{* Smarty *}

{if !empty($weapons)}
			<tr>
				<td class="row1" valign="middle" nowrap="nowrap">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="groups_modify_targets" />
					<input name="group_id" type="hidden" value="{$group_id}" />
					<input name="group_type" type="hidden" value="{$group_type}" />
					<input name="group_view" type="hidden" value="{$group_view}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="catBottom" valign="middle" align="left" width="100%" nowrap="nowrap">Weapon</td>
							<td class="catBottom" valign="middle" align="center" nowrap="nowrap">Target</td>
						</tr>
{foreach from=$weapons item=weapon key=weapon_id}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass}">{$weapon.name}</td>
							<td class="{$rowclass}" align="right"><select name="weapons[{$weapon_id}]"><option></option>{foreach from=$targets item=target key=target_id}<option value="{$target_id}"{if $weapon.target_id == $target_id} selected="selected"{/if}>{$target.name}</option>{/foreach}</select></td>
						</tr>
{/foreach}
						<tr>
							<td colspan="4" class="row1" align="right" valign="middle" nowrap="nowrap"><span class="postdetails"><input name="target" type="submit" class="mainoption" value="Target" /></span></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
{/if}