{* Smarty *}
{if $status != ""}
<table class="forumline" width="100%" cellspacing="1" cellpadding="4" border="0">
	<tr>
		<td>
			<table width="100%" cellspacing="0" cellpadding="1" border="0">
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center"><span class="status">
{foreach name=status from=$status item=item}{if is_array($item)}
{foreach name=item from=$item item=text}
							{$text}{if $smarty.foreach.item.last != true}<br />{/if}
{/foreach}{else}
							{$item}{if $smarty.foreach.status.last != true}<br />{/if}
{/if}{/foreach}
				
					</span></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br clear="all" />
{/if}