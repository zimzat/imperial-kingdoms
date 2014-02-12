{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" border="0" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="3">Kingdom Forum</td>
			</tr>
			<tr>
				<td class="subheader" colspan="3">{$subject}</td>
			</tr>
{foreach name="messages" from=$messages item=message}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data"><b>{$message.name_poster}<b></td>
				<td class="{$rowclass} data al-c">Posted: {$message.posttime}</td>
				<td class="{$rowclass} data al-r">{if $message.marked != ""}{$message.marked}/{$player_count}{else}<a href="{$baseurl}forum.php?fn=forum_mark&forum_message_id={$message.forum_message_id}&skip={$skip|default:"0"}">{if $player_count > 1}Mark for Deletion{else}Delete{/if}</a>{/if}</td>
			</tr>
			<tr>
				<td class="{$rowclass}" colspan="3">{$message.message}</td>
			</tr>
{if $smarty.foreach.messages.last != "true"}
			<tr>
				<td class="spaceRow" colspan="3" height="1"><img src="/forum/templates/subSilver/images/spacer.gif" alt="" width="1" height="1" /></td>
			</tr>
{/if}
{foreachelse}
			<tr>
				<td class="al-c i" colspan="3">There are no messages in this thread. ... WHAT?! (please pm admin, k thx bye)</td>
			</tr>
{/foreach}
{assign var="message" value=""}
		</table>

{if $count > 1}
		<div class="forumline" style="margin-top: 3px; padding-left: 3px; padding-right: 3px; float: right;"><b>Goto page {if $skip != 0}<a href="{$baseurl}forum.php?fn=forum_messages&forum_topic_id={$forum_topic_id}&skip={$skip-1}">Previous</a> {/if}{section name="skipcount" loop=$count}{if $skip != $smarty.section.skipcount.index}<a href="forum.php?fn=forum_messages&forum_topic_id={$forum_topic_id}&skip={$smarty.section.skipcount.index}">{$smarty.section.skipcount.index+1}</a>{else}{$smarty.section.skipcount.index+1}{/if}{if !$smarty.section.skipcount.last}, {/if}{/section}{if $skip < $count-1} <a href="{$baseurl}forum.php?fn=forum_messages&forum_topic_id={$forum_topic_id}&skip={$skip+1}">Next</a>{/if}</b></div>
{/if}

{include file="menu_forum.tpl"}

{include file="footer.tpl"}