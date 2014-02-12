{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input type="hidden" name="fn" value="mail_send" />
		<table border="0" cellpadding="0" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th colspan="2" width="30" align="center" height="25" class="thCornerL" nowrap="nowrap">Compose Mail</th>
			</tr>
			<tr>
				<td width="30" valign="middle" nowrap="nowrap"><span class="topictitle">To: </span></td>
				<td width="100%" valign="middle" nowrap="nowrap"><input type="text" class="post" name="to" size="40" maxlength="60" style="width: 350px;" value="{$mail.to}" /></td>
			</tr>
			<tr>
				<td valign="middle" nowrap="nowrap"><span class="topictitle">Subject: </span></td>
				<td valign="middle" nowrap="nowrap"><input type="text" class="post" name="subject" size="40" maxlength="60" style="width: 350px;" value="{$mail.subject}" /></td>
			</tr>
			<tr>
				<td colspan="2" valign="middle" nowrap="nowrap"><span class="topictitle">Body:</span><br /><textarea name="body" rows="12" cols="40" wrap="virtual" style="width: 400px;" class="post">{$mail.body}</textarea><br />BBCodes [quote(=text)], [b], [u], [i], and [s] are enabled.</td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" name="send" id="send" class="mainoption" value="Send" /></td>
			</tr>
		</table>
		</form>
		
		<div class="button"><a href="{$baseurl}mail.php">List</a></div>

{include file="footer.tpl"}