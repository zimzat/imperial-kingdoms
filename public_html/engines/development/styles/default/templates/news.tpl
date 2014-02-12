{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="forumline">
	<tr>
		<td colspan="3" class="header">News</td>
	</tr>
	<tr>
		<td colspan="3" class="data">{$playerstats.recentplayers} active players in the last {$playerstats.active_time}. There are {$playerstats.totalplayers} players in the round.</td>
	</tr>
	<tr>
		<td width="30%" valign="top">
			{include file="news_score.tpl"}
			
			{include file="news_online.tpl"}
		</td>
		<td width="70%" valign="top" align="center">
			{include file="news_news.tpl"}
		</td>
	</tr>
</table>

{include file="footer.tpl"}