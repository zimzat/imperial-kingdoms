{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input name="fn" type="hidden" value="status_search_submit" />
		<table class="forumline">
			<tr>
				<td class="header" colspan="2">Search</td>
			</tr>
			<tr>
				<td class="row1">Kingdom Name:</td>
				<td class="row1"><input name="kingdom_name" id="kingdom_name" class="post" size="20" /></td>
			</tr>
			<tr>
				<td class="row2">Player Name:</td>
				<td class="row2"><input name="player_name" id="player_name" class="post" size="20" /></td>
			</tr>
			<tr>
				<td class="row1">Kingdom ID:</td>
				<td class="row1"><input name="kingdom_id" id="kingdom_id" class="post" size="4" /></td>
			</tr>
			<tr>
				<td class="row2">Player ID:</td>
				<td class="row2"><input name="player_id" id="player_id" class="post" size="4" /></td>
			</tr>
			<tr>
				<td class="row1" colspan="2"><input class="mainoption" type="submit" name="search" id="search" value="Search"></td>
			</tr>
		</table>
		</form>

{if !empty($search)}
		<table class="forumline">
			<tr>
				<td class="subheader">Results</td>
			</tr>
	{if !empty($results)}
		{if $search == "player_id"}
			<tr>
				<td><a href="{$baseurl}status.php?fn=status_player&player_id={$results.player_id}">{$results.name}</a> ({$results.player_id})</td>
			</tr>
		{elseif $search == "kingdom_id"}
			<tr>
				<td><a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$results.kingdom_id}">{$results.name}</a> ({$results.kingdom_id})</td>
			</tr>
		{elseif $search == "kingdom_name"}
			{foreach from=$results item=kingdom}
			<tr>
				<td><a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$kingdom.kingdom_id}">{$kingdom.name}</a> ({$kingdom.kingdom_id})</td>
			</tr>
			{/foreach}
		{elseif $search == "player_name"}
			{foreach from=$results item=player}
			<tr>
				<td><a href="{$baseurl}status.php?fn=status_player&player_id={$player.player_id}">{$player.name}</a> ({$player.player_id})</td>
			</tr>
			{/foreach}
		{/if}
	{else}
			<tr>
				<td class="row1 i">No results found.</td>
			</tr>
	{/if}
		</table>
{/if}

{include file="footer.tpl"}