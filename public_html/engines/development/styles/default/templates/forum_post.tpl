{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input name="fn" type="hidden" value="forum_post_process" />
{if $forum_topic_id != ""}
		<input name="forum_topic_id" type="hidden" value="{$forum_topic_id}" />
{/if}
		<table border="0" width="100%" align="center" cellpadding="4" cellspacing="1" class="forumline">
			<tr>
				<td class="header" colspan="3">Kingdom Forum</td>
			</tr>
			<tr>
				<td width="100%" align="center" class="subheader" nowrap="nowrap">Post Message</td>
			</tr>
{if empty($forum_topic_id)}
			<tr>
				<td class="row1" align="left" valign="middle" nowrap="nowrap"><span class="topictitle">Subject: </span><input name="subject" type="text" size="40" maxlength="64" class="post" value="{$subject}" /></td>
			</tr>
{/if}
			<tr>
				<td class="row1" align="left" valign="middle" nowrap="nowrap">
					<span class="topictitle">Message: </span><br />
					<textarea name="message" rows="6" cols="40" wrap="virtual" style="width: 400px;" class="post" />{$message}</textarea><br />
					BBCodes [quote(=text)], [b], [u], [i], and [s] are enabled.
				</td>
			</tr>
			<tr>
				<td class="row1" align="left" valign="middle" nowrap="nowrap"><input type="submit" name="post" id="post" class="mainoption" value="Post" /></td>
			</tr>
		</table>
		<form>

{include file="menu_forum.tpl"}

{include file="footer.tpl"}