{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="maildelete">
		<input type="hidden" name="fn" value="mail_delete" />
		<table class="forumline" cellpadding="0" cellspacing="1">
			<tr>
				<td class="header" colspan="4">Mail</td>
			</tr>
			<tr>
				<td class="subheader" colspan="2" width="100%">Subject</td>
				<td class="subheader">Sender</td>
				<td class="subheader">Time</td>
			</tr>
{foreach from=$mail item=item key=mail_id}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass}"><input name="mail_id[]" type="checkbox" value="{$mail_id}" /></td>
				<td class="{$rowclass}"><a href="{$baseurl}mail.php?fn=mail_read&mail_id={$mail_id}">{$item.subject}</a></td>
				<td class="{$rowclass} al-c nowrap">{if $item.from_player_id > 0}<a href="{$baseurl}status.php?fn=status_player&amp;player_id={$item.from_player_id}">{/if}{$item.from_player_name}{if $item.from_player_id > 0}</a>{/if}</td>
				<td class="{$rowclass} nowrap{if $item.status == $smarty.const.MAILSTATUS_UNREAD} b{/if}">{$item.time}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="row1" colspan="4" valign="middle" align="center"><span class="postdetails">Your inbox is empty.</span></td>
			</tr>
{/foreach}
		</table>
		</form>
		
		<div class="button"><a href="{$baseurl}mail.php">List</a></div>
		<div class="button"><a href="{$baseurl}mail.php?fn=mail_compose">Compose</a></div>
		<div class="button"><a href="#" onClick="xGetElementById('maildelete').submit(); return false;">Delete</a></div>

{include file="footer.tpl"}