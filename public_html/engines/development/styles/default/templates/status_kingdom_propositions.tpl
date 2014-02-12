{* Smarty *}

					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" colspan="4">Political Propositions</td>
						</tr>
{foreach from=$kingdom.propositions item=proposition}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass}" width="100%"><a href="{$baseurl}propositions.php?fn=propositions_info&proposition_id={$proposition.proposition_id}">{$proposition.title}</a></td>
{if $proposition.status == "0"}
							<td class="{$rowclass} al-c b nowrap"><span style="color: green;">{$proposition.for}</span></td>
							<td class="{$rowclass} al-c b nowrap"><span style="color: red;">{$proposition.against}</span></td>
							<td class="{$rowclass} al-c b nowrap">{$proposition.blank}</td>
{else}
							<td class="{$rowclass} al-c b nowrap" colspan="3">{if $proposition.status < "3"}<span style="color: {if $proposition.status == "1"}green{else}red{/if};">{/if}{if $proposition.status == "1"}Passed{elseif $proposition.status == "2"}Failed{else}Expired{/if}{if $proposition.status < "3"}</span>{/if}</td>
{/if}
						</tr>
{foreachelse}
						<tr>
							<td class="al-c i" colspan="4">There are no propositions on the table.</td>
						</tr>
{/foreach}
					</table>