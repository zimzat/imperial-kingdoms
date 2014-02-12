{* Smarty *}

<table class="ikBlock">
	<thead>
		<tr class="ikHead">
			<th>Player</th>
			<th>Kingdom</th>

			<th>Resource</th>
			<th>Resource Peak</th>

			<th>Military</th>
			<th>Military Peak</th>

			<th>Total</th>
		</tr>
	</thead>
	<tbody>
{section name=element loop=$scores}
{cycle values="ikRow1,ikRow2" assign="rowClass"}
		<tr class="{$rowClass}">
			<td>{$scores[element].player}</td>
			<td>{$scores[element].kingdom}</td>

			<td>{$scores[element].resource}</td>
			<td>{$scores[element].resource_peak}</td>

			<td>{$scores[element].military}</td>
			<td>{$scores[element].military_peak}</td>

			<td>{$scores[element].total}</td>
		</tr>
{sectionelse}
		<tr class="ikRow1">
			<td colspan="7">This round did not have any scores.</td>
		</tr>
{/section}
	</tbody>
</table>
