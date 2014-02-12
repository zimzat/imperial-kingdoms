{* Smarty *}

{if $output == 'html'}
	{if $status_array == "true"}
		{foreach name=status from=$status item=item}
			{if is_array($item)}
				{include file="status.tpl" status_array="true" status=$item}
			{else}
			
				{$item}{if $smarty.foreach.status.last != true}<br />{/if}
			{/if}
		{/foreach}
	{elseif $status != ""}
		<div id="statusmessage">
		<table class="forumline" cellspacing="1" cellpadding="4">
		{if $error == "true"}
			<tr>
				<td class="header">Error</td>
			</tr>
		{/if}
			<tr>
				<td>
					<table width="100%" cellspacing="0" cellpadding="1">
						<tr>
							<td><span class="gensmall">&nbsp;</span></td>
						</tr>
						<tr>
							<td align="center"><span class="status">{include file="status.tpl" status_array="true" status=$status}</span></td>
						</tr>
						<tr>
							<td{if $status_hide != "false"} align="right" style="text-align: right;"{/if}><span class="gensmall">{if $status_hide == "false"}&nbsp;{else}<a href="#" onClick="xVisibility('statusmessage', false); xDisplay('statusmessage', 'none'); return false;">Hide</a>{/if}</span></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<br clear="all" />
		</div>
	{/if}
{elseif $output == 'javascript'}
	{if $status_array == 'true'}
		{foreach name=status from=$status item=item}
			{if is_array($item)}
				{include file="status.tpl" status_array="true" status=$item}
			{else}
				{$item}
				{if $smarty.foreach.status.last != true}
					
				{/if}
			{/if}
		{/foreach}
	{elseif $status != ""}
		alert("{include file="status.tpl" status_array="true" status=$status}");
	{/if}
{/if}