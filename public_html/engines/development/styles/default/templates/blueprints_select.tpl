{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table border="0" cellpadding="4" cellspacing="1" width="100%" class="forumline">
			<tr>
				<th width="100%" valign="middle" align="center" height="25" class="thCornerL" nowrap="nowrap">Select Design</th>
			</tr>
{foreach from=$designs item=type key=type_name}
			<tr>
				<td colspan="2" valign="middle" align="center" class="catBottom" width="100%"><span class="topictitle">{$type_name|capitalize}</span></td>
			</tr>
			<tr>
				<td>
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" id="fn" type="hidden" value="blueprints_{if $type_name != "weapon"}weapons{else}create{/if}" />
					Design: <select name="{$type_name}design_id" class="post">
						<option value="" selected="selected"></option>
{foreach from=$type item=design key=design_id}
						<option value="{$design_id}">{$design.name} (Mk {$design.techlevel})</option>
{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>Name: <input name="name" class="post" type="text" maxlen="32"></td>
			</tr>
			<tr>
				<td><input name="submit" class="mainoption" type="submit" value="Create"></form></td>
			</tr>
{foreachelse}
			<tr>
				<td class="row1" valign="middle" align="center"><span class="postdetails">There are no designs researched.</span></td>
			</tr>
{/foreach}
		</table>
		
{include file="menu_research.tpl"}

<br clear="left" />

{include file="menu_blueprints.tpl"}

{include file="footer.tpl"}