{* Smarty *}

{if !empty($declarations)}
		<table class="forumline" border="0" width="100%" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="2">Declarations</td>
			</tr>
			<tr>
				<td class="subheader">Kingdom</td>
				<td class="subheader">Status</td>
			</tr>
{foreach from=$declarations item=kingdom key=kingdom_id}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data"><a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$kingdom_id}">{$kingdom.name}</a></td>
				<td class="{$rowclass} data">{$kingdom.status|capitalize}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="row1 data i al-c">No declarations</td>
			</tr>
{/foreach}
		</table>
{/if}