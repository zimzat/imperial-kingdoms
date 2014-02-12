{* Smarty *}
{include file="header.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="2">Player Status: {if $player.on == true}[on] {/if}{$player.name} (#{$player.player_id})</td>
			</tr>
			<tr>
				<td colspan="2">{if $player.image != ""}<img id="avatar" src="{$siteurl}images/avatars/players/{$player.image}" style="float: left;" />{/if}{$player.description}</td>
			</tr>
			<tr>
				<td class="row2" colspan="2">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="row2 b" width="15%">Planets:</td>
							<td class="row2">{$player.planet_count}</td>
							<td class="row2 b" width="15%">Score:</td>
							<td class="row2">{$player.score}{if $player.score_peak != ""} ({$player.score_peak}){/if}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="row1" width="100%"><b>Kingdom:</b> <a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$player.kingdom_id}">{$player.kingdom.name} (#{$player.kingdom_id})</a></td>
				<td class="row1 al-c nowrap">{$player.kingdom.score}{if $player.kingdom.score_peak != ""} ({$player.kingdom.score_peak}){/if}</td>
			</tr>
{if $player.npc == "1" || $player.npc == 1}
			<tr>
				<td class="row1 b" colspan="2">NPC Player</td>
			</tr>
{/if}
		</table>

{if $player.image != ""}
{include file="js_avatar.tpl"}
{/if}

{include file="menu_status_player.tpl"}

{include file="footer.tpl"}