{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="2">Kingdom Status: {$kingdom.name} (#{$kingdom.kingdom_id})</td>
			</tr>
			<tr>
				<td colspan="2">{if $kingdom.image != ""}<img id="avatar" src="{$siteurl}images/avatars/kingdoms/{$kingdom.image}" style="float: left;" />{/if}{$kingdom.description}</td>
			</tr>
			<tr>
				<td class="row2" colspan="2">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="row2 b" width="15%">Planets:</td>
							<td class="row2">{if $kingdom.member != true}<a href="{$baseurl}map.php?fn=cluster&target_id={$kingdom.kingdom_id}" target="cluster">{/if}{$kingdom.planet_count}{if $kingdom.member != true}</a>{/if}</td>
							<td class="row2 b" width="15%">Score:</td>
							<td class="row2">{$kingdom.score}{if $kingdom.score_peak != ""} ({$kingdom.score_peak}){/if}</td>
						</tr>
					</table>
				</td>
			</tr>
{if $kingdom.messages != "" || $kingdom.propositions != ""}
			<tr>
				<td class="row1" valign="top" width="50%">
{include file="status_kingdom_propositions.tpl"}
{include file="status_kingdom_messages.tpl"}
				</td>
				<td class="row1" valign="top" width="50%">
{include file="status_kingdom_members.tpl"}
				</td>
			</tr>
{else}
			<tr>
				<td class="row1" colspan="2">
{include file="status_kingdom_members.tpl"}
				</td>
			</tr>
{/if}
		</table>

{if !empty($declarations)}
		<br clear="all" />
		
{include file="military_declarations.tpl"}
{/if}

{if $kingdom.image != ""}
{include file="js_avatar.tpl"}
{/if}

{include file="menu_status_kingdom.tpl"}

{include file="footer.tpl"}