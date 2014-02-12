{* Smarty *}

<div class="ikBlock" id="round_info">
	<div class="ikHead">Round Stats</div>

	<div class="ikBody">
		<div class="ikCell" style="font-weight: bold;">
			Name:<br />
			Description:<br />
			<br />
			Start Time:<br />
			Stop Time:<br />
			<br />
			Engine:<br />
			<br />
			Stars Per Quadrant:<br />
			Planets Per Star:<br />
			<br />
			Resistance:<br />
			<br />
			Speed:<br />
			Resources:<br />
			Combat:<br />
			<br />
			Attack Limit:
		</div>
		<div class="ikCell">
			{$round.name}<br />
			{$round.description}<br />
			<br />
			{$round.starttime}<br />
			{$round.stoptime}<br />
			<br />
			{$round.round_engine|capitalize}<br />
			<br />
			{$round.starsystems}<br />
			{$round.planets}<br />
			<br />
			{$round.resistance}<br />
			<br />
			{$round.speed}<br />
			{$round.resourcetick}<br />
			{$round.combattick}<br />
			<br />
			{if $round.attack_limit > 0}{$round.attack_limit}%{else}None{/if}
		</div>
	</div>
</div>