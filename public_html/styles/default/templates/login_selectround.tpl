{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<div class="ikBlock">
	<div class="ikHead">Select Round</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="selectround">

		{$passthrough}

		These rounds are currently available for you to select:

{section name=element loop=$rounds}
		<div>
			<label><input type="radio" name="round_id" value="{$rounds[element].round_id}" /> {$rounds[element].name}</label>
			Length: {if $rounds[element].stoptime.days > 0}{$rounds[element].stoptime.days} Day{if $rounds[element].stoptime.days > 1}s{/if} {/if}{if $rounds[element].stoptime.hours > 0}{$rounds[element].stoptime.hours} Hr{if $rounds[element].stoptime.hours > 1}s{/if} {/if}{if $rounds[element].stoptime.minutes > 0}{$rounds[element].stoptime.minutes} Min{if $rounds[element].stoptime.minutes > 1}s{/if} {/if}{if $rounds[element].stoptime.days == 0 && $rounds[element].stoptime.seconds > 0}{$rounds[element].stoptime.seconds} Sec{if $rounds[element].stoptime.seconds > 1}s{/if} {/if}<br>
			Remaining: {if $rounds[element].starttime.days > 0}{$rounds[element].starttime.days} Day{if $rounds[element].starttime.days > 1}s{/if} {/if}{if $rounds[element].starttime.hours > 0}{$rounds[element].starttime.hours} Hr{if $rounds[element].starttime.hours > 1}s{/if} {/if}{if $rounds[element].starttime.minutes > 0}{$rounds[element].starttime.minutes} Min{if $rounds[element].starttime.minutes > 1}s{/if} {/if}{if $rounds[element].starttime.days == 0 && $rounds[element].starttime.seconds > 0}{$rounds[element].starttime.seconds} Sec{if $rounds[element].starttime.seconds > 1}s{/if} {/if}
		</div>
{sectionelse}
		<div>
			None
		</div>
{/section}

		<div>
			<input type="submit" name="select" id="select" value="Select" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</div>

		</form>
	</div>
</div>

{include file="footer.tpl"}