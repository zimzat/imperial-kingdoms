{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="4" width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap"><b>Round Stats</b></th>
			</tr>
			<tr>
				<td colspan="4" class="catBottom" valign="middle" align="center" height="25" width="100%"><span class="topictitle">{$round.name}</span></td>
			</tr>
			<tr>
				<td class="row1" valign="top"><span class="gen"><b>Description:</b></span></td>
				<td class="row1" valign="top" colspan="3">{$round.description}</td>
			</tr>
			<tr>
				<td class="row1" valign="top"><span class="gen"><b>Start Time:</b></span></td>
				<td class="row1" valign="top">{$round.starttime}</td>
				<td class="row1" valign="top"><span class="gen"><b>Engine:</b></span></td>
				<td class="row1" valign="top">{$round.round_engine|capitalize}</td>
			</tr>
			<tr>
				<td class="row2" valign="top"><span class="gen"><b>Stop Time:</b></span></td>
				<td class="row2" valign="top">{$round.stoptime}</td>
				<td class="row2" valign="top"><span class="gen"><b>Star Systems / Quadrant:</b></span></td>
				<td class="row2" valign="top">{$round.starsystems}</td>
			</tr>
			<tr>
				<td class="row1" valign="top"><span class="gen"><b>Speed:</b></span></td>
				<td class="row1" valign="top">{$round.speed}</td>
				<td class="row1" valign="top"><span class="gen"><b>Planets / Star System:</b></span></td>
				<td class="row1" valign="top">{$round.planets}</td>
			</tr>
			<tr>
				<td class="row2" valign="top"><span class="gen"><b>Resources:</b></span></td>
				<td class="row2" valign="top">{$round.resourcetick}</td>
				<td class="row2" valign="top"><span class="gen"><b>Resistance:</b></span></td>
				<td class="row2" valign="top">{$round.resistance}</td>
			</tr>
			<tr>
				<td class="row1" valign="top"><span class="gen"><b>Combat:</b></span></td>
				<td class="row1" valign="top">{$round.combattick}</td>
				<td class="row1" valign="top"><span class="gen"><b>Attack Limit:</b></span></td>
				<td class="row1" valign="top">{if $round.attack_limit > 0}{$round.attack_limit}%{else}None{/if}</td>
			</tr>
		</table>

{include file="footer.tpl"}