{* Smarty *}

		<div class="button"><a href="{$baseurl}groups.php?fn=groups_view&group_view=units&group_type={$group_type}&group_id={$group_id}">Units</a></div>
		<div class="button"><a href="{$baseurl}groups.php?fn=groups_view&group_view=targets&group_type={$group_type}&group_id={$group_id}">Targets</a></div>
{if $group_type == "navy"}
		<div class="button"><a href="{$baseurl}groups.php?fn=groups_view&group_view=cargo&group_type={$group_type}&group_id={$group_id}">Cargo</a></div>
		<div class="button"><a href="{$baseurl}groups.php?fn=groups_view&group_view=destination&group_type={$group_type}&group_id={$group_id}">Destination</a></div>
{/if}