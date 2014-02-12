{* Smarty *}

			<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr>
					<td colspan="3" class="subheader">Scores</td>
				</tr>
{foreach from=$scores item=score}
{cycle values="row1,row2" assign="rowclass"}
{if $score.kingdom_id > 0}
{if $score.kingdom_id == $kingdom_id}{assign var="highlight" value="_alert"}{else}{assign var="highlight" value=""}{/if}
				<tr>
					<td class="{$rowclass}{$highlight} data al-l nowrap">{$score.position}</td>
					<td class="{$rowclass}{$highlight} data al-l nowrap"><a href="{$baseurl}status.php?fn=status_kingdom&kingdom_id={$score.kingdom_id}">{$score.name}</a></td>
					<td class="{$rowclass}{$highlight} data al-r nowrap">{$score.score}</td>
				</tr>
{else}
				<tr>
					<td colspan="3"><hr /></td>
				</tr>
{/if}
{foreachelse}
				<tr>
					<td class="al-c i" colspan="3">No Score Data.</td>
				</tr>
{/foreach}
			</table>