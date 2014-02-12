{* Smarty *}

					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" colspan="3">Members</td>
						</tr>
{foreach from=$kingdom.members item=member}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass} data al-c nowrap">{$member.rank}&nbsp;&nbsp;&nbsp;</td>
							<td class="{$rowclass} data" width="100%">{if $member.on == true}[on] {/if}<a href="{$baseurl}status.php?fn=status_player&player_id={$member.player_id}">{$member.name} (#{$member.player_id})</a></td>
							<td class="{$rowclass} data al-r nowrap">{$member.score}</td>
						</tr>
{foreachelse}
						<tr>
							<td class="al-c i" colspan="3">There are no members in this kingdom.</td>
						</tr>
{/foreach}
					</table>