{* Smarty *}

					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" colspan="2">Diplomatic Messages</td>
						</tr>
{foreach from=$kingdom.messages item=message}
{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass}"><a href="{$baseurl}news.php?fn=kingdom_message&message_id={$message.message_id}">{$message.name}</a></td>
							<td class="{$rowclass}">{$message.target}</td>
						</tr>
{foreachelse}
						<tr>
							<td class="al-c i" colspan="2">There are no messages to view.</td>
						</tr>
{/foreach}
					</table>