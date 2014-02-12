{* Smarty *}

{if !empty($status)}
<div class="ikBlock">

{foreach name=status from=$status item=item}
	{if is_array($item)}
		{foreach name=item from=$item item=text}
			{$text}{if $smarty.foreach.item.last != true}<br />{/if}
		{/foreach}
	{else}
		{$item}{if $smarty.foreach.status.last != true}<br />{/if}
	{/if}
{/foreach}

</div>

<br style="clear: all;" />
{/if}