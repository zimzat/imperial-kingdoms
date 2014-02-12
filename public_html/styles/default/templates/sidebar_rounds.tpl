{* Smarty *}

{*
<div class="ikBlock" id="roundSchedule">
	<div class="ikHead">Round Schedule</div>
	<div class="ikBody">
		<table style="width: 100%;">
			<tr>
				<td>Current: x</td>
				<td>Future: x</td>
				<td>Previous: x</td>
			</tr>
		</table>
	</div>
</div>
*}

<div class="ikBlock" id="roundlist">
	<div class="ikHead">Rounds</div>
	
	<div style="float: left; width: 34%;">
		<div class="ikSubhead">Current</div>
		<div class="ikBody">
{section name=element loop=$rounds.current}
{cycle values="ikRow1,ikRow2" assign="rowClass"}
			<div class="{$rowClass}">
				<a href="{$baseurl}index.php?fn=info&amp;round_id={$rounds.current[element].round_id}">{$rounds.current[element].name}</a><br />
{if $rounds.current[element].pause_time > 0}
				<b>Paused:</b> {$rounds.current[element].pause_message}<br />
{/if}
				<b>Length:</b> {$rounds.current[element].stoptime}<br />
				<b>Remaining:</b> {$rounds.current[element].starttime}
			</div>
{sectionelse}
			None
{/section}
		</div>
	</div>
	
	<div style="float: left; width: 33%;">
		<div class="ikSubhead">Future</div>
		<div class="ikBody">
{section name=element loop=$rounds.future}
{cycle values="ikRow1,ikRow2" assign="rowClass"}
			<div class="{$rowClass}">
				<a href="{$baseurl}index.php?fn=info&amp;round_id={$rounds.future[element].round_id}">{$rounds.future[element].name}</a><br />
				<b>Length:</b> {$rounds.future[element].stoptime}<br />
				<b>Starts In:</b> {$rounds.future[element].starttime}
			</div>
{sectionelse}
			None
{/section}
		</div>
	</div>
	
	<div style="float: left; width: 33%;">
		<div class="ikSubhead">Previous</div>
		<div class="ikBody">
{section name=element loop=$rounds.previous}
{cycle values="ikRow1,ikRow2" assign="rowClass"}
			<div class="{$rowClass}">
				<a href="{$baseurl}index.php?fn=info&amp;round_id={$rounds.previous[element].round_id}">{$rounds.previous[element].name}</a>
			</div>
{sectionelse}
		None
{/section}
		</div>
	</div>
	
	<br style="clear: both;" />
</div>
