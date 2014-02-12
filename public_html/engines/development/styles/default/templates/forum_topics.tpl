{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" border="0" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header" colspan="4">Kingdom Forum</td>
			</tr>
			<tr>
				<td class="subheader">Subject</td>
				<td class="subheader">Replies</td>
				<td class="subheader">Last Poster</td>
				<td class="subheader">Last Post Time</td>
			</tr>
{foreach from=$topics item=topic}
{cycle values="row1,row2" assign="rowclass"}
			<tr>
				<td class="{$rowclass} data">
					<a href="{$baseurl}forum.php?fn=forum_messages&forum_topic_id={$topic.forum_topic_id}">{$topic.subject}</a><br />
{if $topic.pages > 1}
		[ Goto page {section name="topic_skipcount" loop=$topic.pages}<a href="forum.php?fn=forum_messages&forum_topic_id={$topic.forum_topic_id}&skip={$smarty.section.topic_skipcount.index}">{$smarty.section.topic_skipcount.index+1}</a>{if !$smarty.section.topic_skipcount.last}, {/if}{/section} ]
{/if}
				</td>
				<td class="{$rowclass} data nowrap al-c">{$topic.replies}</td>
				<td class="{$rowclass} data nowrap al-c">{$topic.name_lastposter}</td>
				<td class="{$rowclass} data nowrap al-r">{$topic.lastpost}</td>
			</tr>
{foreachelse}
			<tr>
				<td class="al-c i" colspan="4">There are no topics in the kingdom forum.</td>
			</tr>
{/foreach}
		</table>

{if $count > 1}
		<div class="forumline" style="margin-top: 3px; padding-left: 3px; padding-right: 3px; float: right;"><b>Goto page {if $skip != 0}<a href="{$baseurl}forum.php?fn=forum_topics&skip={$skip-1}">Previous</a> {/if}{section name="skipcount" loop=$count}{if $skip != $smarty.section.skipcount.index}<a href="forum.php?fn=forum_topics&skip={$smarty.section.skipcount.index}">{$smarty.section.skipcount.index+1}</a>{else}{$smarty.section.skipcount.index+1}{/if}{if !$smarty.section.skipcount.last}, {/if}{/section}{if $skip < $count-1} <a href="{$baseurl}forum.php?fn=forum_topics&skip={$skip+1}">Next</a>{/if}</b></div>
{/if}

{include file="menu_forum.tpl"}

{include file="footer.tpl"}