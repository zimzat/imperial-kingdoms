{* Smarty *}

		<div class="button"><a href="{$baseurl}groups.php?group_type=army">Army</a></div>
		<div class="button"><a href="{$baseurl}groups.php?group_type=navy">Navy</a></div>
		<div class="button"><a href="{$baseurl}groups.php?fn=groups_create{if !empty($group_type)}&group_type={$group_type}{/if}">Create</a></div>