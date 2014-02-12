{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<form action="{$actionurl}" method="post" enctype="multipart/form-data">
		<input name="fn" type="hidden" value="reports_list" />
		<table border="0" width="50%" align="center" cellpadding="4" cellspacing="1" class="forumline">
			<tr>
				<th width="100%" align="center" class="thCornerR" nowrap="nowrap" colspan="2">Search Battle Reports</th>
			</tr>
			<tr>
				<td class="row1" align="left" valign="middle" nowrap="nowrap" nowrap="nowrap">Planet #: </td>
				<td class="row1" align="left" valign="middle" nowrap="nowrap" nowrap="nowrap"><input name="planet_id" type="text" class="post" size="4" maxlength="8" /></td>
			</tr>
			<tr>
				<td class="row1" align="left" valign="middle" nowrap="nowrap" nowrap="nowrap">Time: </td>
				<td class="row1" align="left" valign="middle" nowrap="nowrap" nowrap="nowrap"><input name="time" type="text" class="post" size="4" maxlength="4" /></td>
			</tr>
			<tr>
				<td class="row1" align="left" valign="top" nowrap="nowrap" nowrap="nowrap">Status: </td>
				<td class="row1" align="left" valign="middle" nowrap="nowrap" nowrap="nowrap">
					<input name="status[0]" type="checkbox" class="post" /> Ongoing<br />
					<input name="status[1]" type="checkbox" class="post" /> Won<br />
					<input name="status[2]" type="checkbox" class="post" /> Lost
				</td>
			</tr>
			<tr>
				<td class="row1" align="center" valign="middle" nowrap="nowrap" nowrap="nowrap" colspan="2"><input name="submit" type="submit" class="mainoption" value="Search" /></td>
			</tr>
		</table>
		</form>

{include file="menu_military.tpl"}

<br clear="left" />

{include file="menu_reports.tpl"}

{include file="footer.tpl"}