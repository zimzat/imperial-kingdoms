{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="0" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="2" width="30" align="center" height="25" class="thCornerL" nowrap="nowrap">Read Mail</th>
			</tr>
			<tr>
				<td width="30" valign="middle" nowrap="nowrap"><span class="topictitle">From: </span></td>
				<td width="100%" valign="middle" nowrap="nowrap">{$mail.from}</td>
			</tr>
			<tr>
				<td valign="middle" nowrap="nowrap"><span class="topictitle">Time: </span></td>
				<td valign="middle" nowrap="nowrap">{$mail.time}</td>
			</tr>
			<tr>
				<td valign="middle" nowrap="nowrap"><span class="topictitle">Subject: </span></td>
				<td valign="middle" nowrap="nowrap">{$mail.subject}</td>
			</tr>
			<tr>
				<td colspan="2" valign="middle"><span class="topictitle">Body:</span><hr />{$mail.body}</td>
			</tr>
		</table>

		<div class="button"><a href="{$baseurl}mail.php">List</a></div>
		<div class="button"><a href="{$baseurl}mail.php?fn=mail_delete&mail_id={$mail.mail_id}">Delete</a></div>
		<div class="button"><a href="{$baseurl}mail.php?fn=mail_compose&mail_id={$mail.mail_id}">Reply</a></div>

{include file="footer.tpl"}