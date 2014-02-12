{* Smarty *}

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
										<td class="row1" valign="top" width="100%"><span class="gensmall"><a href="{$baseurl}index.php?game={$games.previous[element].game_id}">{$games.previous[element].name}</a><br />
										Length: {if $games.previous[element].stoptime.days > 0}{$games.previous[element].stoptime.days} Day{if $games.previous[element].stoptime.days > 1}s{/if} {/if}{if $games.previous[element].stoptime.hours > 0}{$games.previous[element].stoptime.hours} Hr{if $games.previous[element].stoptime.hours > 1}s{/if} {/if}{if $games.previous[element].stoptime.minutes > 0}{$games.previous[element].stoptime.minutes} Min{if $games.previous[element].stoptime.minutes > 1}s{/if} {/if}{if $games.previous[element].stoptime.days == 0 && $games.previous[element].stoptime.seconds > 0}{$games.previous[element].stoptime.seconds} Sec{if $games.previous[element].stoptime.seconds > 1}s{/if} {/if}</span></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">None</span></td>
									</tr>
{/section}
								</table>