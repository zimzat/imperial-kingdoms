{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="2" width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Proposition Info</th>
			</tr>
			<tr>
				<td colspan="2" valign="middle" align="center" class="catBottom" width="100%"><span class="topictitle">{$proposition.title}</span></td>
			</tr>
{if $proposition.statement != ""}
			<tr>
				<td colspan="2" valign="middle" align="left" class="row1" width="100%"><b>Statement:</b> {$proposition.statement}</td>
			</tr>
{/if}

{if $proposition.type == $smarty.const.PROPOSITION_DESCRIPTION}
			<tr>
				<td colspan="2" valign="middle" align="left" class="row1" width="100%"><b>Proposed Description:</b> {$proposition.storage|default:"[None]"}</td>
			</tr>
{elseif $proposition.type == $smarty.const.PROPOSITION_AVATAR}
			<tr>
				<td colspan="2" valign="middle" align="center" class="row1" width="100%"><b>Proposed Avatar:</b><br /><img src="{$baseurl}propositions.php?fn=propositions_avatar&action=avatar&proposition_id={$proposition.proposition_id}" /></td>
			</tr>
{elseif $proposition.type == $smarty.const.PROPOSITION_WAR}
			<tr>
				<td colspan="2" valign="middle" align="center" class="row1" width="100%"><b>War on Kingdom:</b> {$proposition.kingdom} (K#{$proposition.target_id})</td>
			</tr>
{/if}

			<tr>
				<td valign="top" align="center" class="row1" width="50%">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td><b>Status:</b></td>
							<td align="right">{if $proposition.status == "0"}Voting{elseif $proposition.status == "1"}Passed{elseif $proposition.status == "2"}Failed{else}Expired{/if}</td>
						</tr>
{if $proposition.expires != ""}
						<tr>
							<td><b>Expires:</b></td>
							<td align="right">{$proposition.expires}</td>
						</tr>
{/if}
					</table>
				</td>
				<td valign="middle" align="center" class="row1" width="50%">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td align="center" colspan="2" class="catBottom"><b>Votes</b></td>
						</tr>
						<tr>
							<td><b>For:</b></td>
							<td align="center">{$proposition.for}</td>
						</tr>
						<tr>
							<td><b>Against:</b></td>
							<td align="center">{$proposition.against}</td>
						</tr>
						<tr>
							<td><b>Neutral:</b></td>
							<td align="center">{$proposition.neutral}</td>
						</tr>
{if $proposition.status == "0"}
						<tr>
							<form method="post" action="{$actionurl}" name="propositions" id="propositions">
							<input name="fn" type="hidden" value="propositions_vote" />
							<input name="proposition_id" type="hidden" value="{$proposition.proposition_id}" />
							<td><input class="mainoption"type="submit" value="Vote" /></td>
							<td align="center"><select name="vote"><option value="for">For</option><option value="against">Against</option><option value="neutral">Neutral</option></select></td>
							</form>
						</tr>
{/if}
					</table>
				</td>
			</tr>
		</table>

{include file="menu_status_kingdom.tpl"}

{include file="footer.tpl"}