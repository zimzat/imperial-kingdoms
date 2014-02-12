{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_round_change" />
		<table width="100%" cellpadding="4" cellspacing="1" border="0" class="forumline" align="center">
			<tr>
				<th height="25" class="thHead" nowrap="nowrap">Select Round</th>
			</tr>
			<tr>
				<td class="row1">
					<table align="center" border="0" cellpadding="3" cellspacing="1">
						<tr>
							<td colspan="2" align="left">
								These rounds are currently available for you to select:
							</td>
						</tr>
						<tr>
							<td colspan="2" align="left">
{section name=element loop=$rounds}
								<p><input type="radio" name="round_id" value="{$rounds[element].round_id}" /> {$rounds[element].name}<br />
								Length: {if $rounds[element].stoptime.days > 0}{$rounds[element].stoptime.days} Day{if $rounds[element].stoptime.days > 1}s{/if} {/if}{if $rounds[element].stoptime.hours > 0}{$rounds[element].stoptime.hours} Hr{if $rounds[element].stoptime.hours > 1}s{/if} {/if}{if $rounds[element].stoptime.minutes > 0}{$rounds[element].stoptime.minutes} Min{if $rounds[element].stoptime.minutes > 1}s{/if} {/if}{if $rounds[element].stoptime.days == 0 && $rounds[element].stoptime.seconds > 0}{$rounds[element].stoptime.seconds} Sec{if $rounds[element].stoptime.seconds > 1}s{/if} {/if}<br>
								Remaining: {if $rounds[element].starttime.days > 0}{$rounds[element].starttime.days} Day{if $rounds[element].starttime.days > 1}s{/if} {/if}{if $rounds[element].starttime.hours > 0}{$rounds[element].starttime.hours} Hr{if $rounds[element].starttime.hours > 1}s{/if} {/if}{if $rounds[element].starttime.minutes > 0}{$rounds[element].starttime.minutes} Min{if $rounds[element].starttime.minutes > 1}s{/if} {/if}{if $rounds[element].starttime.days == 0 && $rounds[element].starttime.seconds > 0}{$rounds[element].starttime.seconds} Sec{if $rounds[element].starttime.seconds > 1}s{/if} {/if}</p>
{sectionelse}
								None
{/section}
							</td>
						</tr>
						<tr align="center">
							<td colspan="2"><input type="submit" name="select" class="mainoption" value="Select" />&nbsp;&nbsp;<input type="reset" value="Reset" name="reset" class="liteoption" /></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</form>

{include file="menu_options.tpl"}

		<br clear="both">

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="options_round_abandon" />
		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<td valign="top" align="center"><input onclick="alert('Do not be alarmed, you are being forwarded to a confirmation page.');" class="liteoption" name="abandon" type="submit" value="Abandon Round" /></td>
			</tr>
		</table>
		</form>

{include file="footer.tpl"}