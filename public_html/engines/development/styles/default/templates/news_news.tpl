{* Smarty *}

{if !empty($news)}
{*{assign var="cols" value="2"}*}
{* $cols is the number of columns you want *}
			<table>
{assign var="count" value="0"}
{foreach from=$news item=entry key=news_id}
{cycle values="row1,row2" assign="rowclass"}
{assign var="count" value="`$count+1`"}
{*				{section name=tr loop=$news step=$cols}*}
				<tr>
{*					{section name=td start=$smarty.section.tr.index	loop=$smarty.section.tr.index+$cols}*}
					<td class="{$rowclass} data al-j">
{*						<b>{$news[td].subject|default:"&nbsp;"}</b><br />*}
{*						{$news[td].body|default:"&nbsp;"}*}
						<b><a href="{$baseurl}news.php?news_id={$entry.news_id}">{$entry.subject}</a></b><br />
						{if $count <= 4}{$entry.body}{/if}
					</td>
{*					{/section}*}
				</tr>
{*			{/section}*}
{/foreach}
			</table>
{/if}