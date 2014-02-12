{* Smarty *}
{include file="header.tpl"}

					<table width="100%" cellspacing="2" cellpadding="2" border="0" align="center" valign="top">
						<tr>
							<td valign="top" width="30%">
								<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
									<tr>
										<th class="thHead" align="center" width="100%" height="25" nowrap="nowrap">Games</th>
									</tr>
									<tr>
										<td class="cat" width="100%"><span class="cattitle">Current</span></td>
									</tr>
{section name=element loop=$games.current}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">{$games.current[element].name}<br />
										Length: {if $games.current[element].stoptime.days > 0}{$games.current[element].stoptime.days} Day{if $games.current[element].stoptime.days > 1}s{/if} {/if}{if $games.current[element].stoptime.hours > 0}{$games.current[element].stoptime.hours} Hr{if $games.current[element].stoptime.hours > 1}s{/if} {/if}{if $games.current[element].stoptime.minutes > 0}{$games.current[element].stoptime.minutes} Min{if $games.current[element].stoptime.minutes > 1}s{/if} {/if}{if $games.current[element].stoptime.days == 0 && $games.current[element].stoptime.seconds > 0}{$games.current[element].stoptime.seconds} Sec{if $games.current[element].stoptime.seconds > 1}s{/if} {/if}<br />
										Remaining: {if $games.current[element].starttime.days > 0}{$games.current[element].starttime.days} Day{if $games.current[element].starttime.days > 1}s{/if} {/if}{if $games.current[element].starttime.hours > 0}{$games.current[element].starttime.hours} Hr{if $games.current[element].starttime.hours > 1}s{/if} {/if}{if $games.current[element].starttime.minutes > 0}{$games.current[element].starttime.minutes} Min{if $games.current[element].starttime.minutes > 1}s{/if} {/if}{if $games.current[element].starttime.days == 0 && $games.current[element].starttime.seconds > 0}{$games.current[element].starttime.seconds} Sec{if $games.current[element].starttime.seconds > 1}s{/if} {/if}</span></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">None</span></td>
									</tr>
{/section}
									<tr>
										<td class="cat" width="100%"><span class="cattitle">Future</span></td>
									</tr>
{section name=element loop=$games.future}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">{$games.future[element].name}<br />
										Length: {if $games.future[element].stoptime.days > 0}{$games.future[element].stoptime.days} Day{if $games.future[element].stoptime.days > 1}s{/if} {/if}{if $games.future[element].stoptime.hours > 0}{$games.future[element].stoptime.hours} Hr{if $games.future[element].stoptime.hours > 1}s{/if} {/if}{if $games.future[element].stoptime.minutes > 0}{$games.future[element].stoptime.minutes} Min{if $games.future[element].stoptime.minutes > 1}s{/if} {/if}{if $games.future[element].stoptime.days == 0 && $games.future[element].stoptime.seconds > 0}{$games.future[element].stoptime.seconds} Sec{if $games.future[element].stoptime.seconds > 1}s{/if} {/if}<br />
										Starts In: {if $games.future[element].starttime.days > 0}{$games.future[element].starttime.days} Day{if $games.future[element].starttime.days > 1}s{/if} {/if}{if $games.future[element].starttime.hours > 0}{$games.future[element].starttime.hours} Hr{if $games.future[element].starttime.hours > 1}s{/if} {/if}{if $games.future[element].starttime.minutes > 0}{$games.future[element].starttime.minutes} Min{if $games.future[element].starttime.minutes > 1}s{/if} {/if}{if $games.future[element].starttime.days == 0 && $games.future[element].starttime.seconds > 0}{$games.future[element].starttime.seconds} Sec{if $games.future[element].starttime.seconds > 1}s{/if} {/if}</span></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">None</span></td>
									</tr>
{/section}
									<tr>
										<td class="cat" width="100%"><span class="cattitle">Previous</span></td>
									</tr>
{section name=element loop=$games.previous}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall"><a href="{$baseurl}scores.php?game={$games.previous[element].game_id}">{$games.previous[element].name}</a><br />
										Length: {if $games.previous[element].stoptime.days > 0}{$games.previous[element].stoptime.days} Day{if $games.previous[element].stoptime.days > 1}s{/if} {/if}{if $games.previous[element].stoptime.hours > 0}{$games.previous[element].stoptime.hours} Hr{if $games.previous[element].stoptime.hours > 1}s{/if} {/if}{if $games.previous[element].stoptime.minutes > 0}{$games.previous[element].stoptime.minutes} Min{if $games.previous[element].stoptime.minutes > 1}s{/if} {/if}{if $games.previous[element].stoptime.days == 0 && $games.previous[element].stoptime.seconds > 0}{$games.previous[element].stoptime.seconds} Sec{if $games.previous[element].stoptime.seconds > 1}s{/if} {/if}</span></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">None</span></td>
									</tr>
{/section}
								</table>
							</td>
							<td valign="top" width="70%">
								<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
									<tr>
										<th width="50%" align="center" height="25" class="thCornerL" nowrap="nowrap">&nbsp;Player&nbsp;</th>
										<th width="50%" align="center" class="thTop" nowrap="nowrap">&nbsp;Kingdom&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Resource&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Resource Peak&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Military&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Military Peak&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Total&nbsp;</th>
										<th width="75" align="center" class="thTop" nowrap="nowrap">&nbsp;Total Peak&nbsp;</th>
										<th align="center"  nowrap="nowrap" class="thCornerR" nowrap="nowrap">&nbsp;Military&nbsp;</th>
									</tr>
{section name=element loop=$scores}
									<tr>
										<td class="row1" width="100%"><span class="topictitle">{$scores[element].player}</span><span class="gensmall"><br /></span></td>
										<td class="row2" align="center" valign="middle"><span class="postdetails">{$scores[element].kingdom}</span></td>
										<td class="row1" align="center" valign="middle"><span class="postdetails">{$scores[element].resource}</span></td>
										<td class="row2" align="center" valign="middle"><span class="postdetails">{$scores[element].resource_peak}</span></td>
										<td class="row1" align="center" valign="middle"><span class="postdetails">{$scores[element].military}</span></td>
										<td class="row2" align="center" valign="middle"><span class="postdetails">{$scores[element].military_peak}</span></td>
										<td class="row1" align="center" valign="middle"><span class="postdetails">{$scores[element].total}</span></td>
										<td class="row3Right" align="center" valign="middle" nowrap="nowrap"><span class="postdetails">{$scores[element].total_peak}</span></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" colspan="6" height="30" align="center" valign="middle"><span class="gen">There is no news at this time.</span></td>
									</tr>
{/section}
								</table>
							</td>
						</tr>
					</table>
					
{include file="footer.tpl"}