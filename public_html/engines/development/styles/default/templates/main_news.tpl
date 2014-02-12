{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
	<tr>
		<th width="100%" align="center" height="25" class="thCornerL" nowrap="nowrap">&nbsp;News&nbsp;</th>
		<th width="50" align="center" class="thTop" nowrap="nowrap">&nbsp;Replies&nbsp;</th>
		<th align="center"  nowrap="nowrap" class="thCornerR" nowrap="nowrap">&nbsp;Last Post&nbsp;</th>
	</tr>
{section name=element loop=$news}
	<tr>
		<td class="row1" width="100%"><span class="topictitle"><a href="{$siteurl}forum/viewtopic.php?t={$news[element].topic}" class="topictitle" target="forums">{$news[element].title}</a></span></td>
		<td class="row2" align="center" valign="middle"><span class="postdetails">{$news[element].replies}</span></td>
		<td class="row3Right" align="center" valign="middle" nowrap="nowrap"><span class="postdetails">
			{$news[element].time|date_format:"%d %b %Y %I:%M %p"}<br />
			<a href="{$siteurl}forum/profile.php?mode=viewprofile&u={$news[element].user_id}" target="forums">{$news[element].username}</a> 
			<a href="{$siteurl}forum/viewtopic.php?p={$news[element].post_id}#{$news[element].post_id}" target="forums"><img src="{$siteurl}forum/templates/subSilver/images/icon_latest_reply.gif" alt="View latest post" title="View latest post" border="0"></a>
		</span></td>
	</tr>
{sectionelse}
	<tr>
		<td class="row1" colspan="3" height="30" align="center" valign="middle"><span class="gen">There is no news at this time.</span></td>
	</tr>
{/section}
</table>

{include file="footer.tpl"}