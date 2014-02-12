{* Smarty *}

{include file="header.tpl"}

{if $content == "news" || $content == ""}
	{include file="index_news.tpl"}
{elseif $content == "info"}
	{include file="index_info.tpl"}
{elseif $content == "scores"}
	{include file="index_scores.tpl"}
{/if}

{include file="footer.tpl"}