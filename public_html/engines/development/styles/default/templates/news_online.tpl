{* Smarty *}

			<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="2">Recent Players</td>
				</tr>
{foreach from=$players item=player key=player_id}
{cycle values="row1,row2" assign="rowclass"}
				<tr>
					<td class="{$rowclass} data al-l nowrap "><a href="{$baseurl}status.php?fn=status_player&player_id={$player.player_id}">{$player.name}</a></td>
					<td class="{$rowclass} data al-r nowrap">{$player.lastactive}</td>
				</tr>
{foreachelse}
				<tr>
					<td class="al-c i" colspan="3">No Recent Players.</td>
				</tr>
{/foreach}
			</table>