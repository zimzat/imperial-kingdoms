{* Smarty *}

{if !empty($resources)}
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">{$resource_title|default:"Costs"}</td>
				</tr>
				<tr>
					<td>FE: </td>
					<td class="al-r">{$resources.minerals.fe|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>O: </td>
					<td class="al-r">{$resources.minerals.o|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>SI: </td>
					<td class="al-r">{$resources.minerals.si|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>MG: </td>
					<td class="al-r">{$resources.minerals.mg|default:"0"}</td>
				</tr>
				<tr>
					<td>NI: </td>
					<td class="al-r">{$resources.minerals.ni|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>S: </td>
					<td class="al-r">{$resources.minerals.s|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>HE: </td>
					<td class="al-r">{$resources.minerals.he|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td>H: </td>
					<td class="al-r">{$resources.minerals.h|default:"0"}</td>
				</tr>
			</table>
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td width="16px"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/time.gif" alt="Time:" title="Time:" /></td>
					<td class="al-l">{$resources.time|default:"-"}</td>
					<td>&nbsp;</td>
					
{if $resources.food != ""}
					<td width="16px"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/food.gif" alt="Food:" title="Food:" /></td>
					<td class="al-l">{$resources.food|default:"0"}</td>
					<td>&nbsp;</td>
					
{/if}
					<td width="16px"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/workers.gif" alt="Workers:" title="Workers:" /></td>
					<td class="al-l">{$resources.workers|default:"0"}</td>
					<td>&nbsp;</td>
					
					<td width="16px"><img class="icon" src="{$baseurl}styles/{$style}/images/symbols/16x16/energy.gif" alt="Energy:" title="Energy:" /></td>
					<td class="al-l">{$resources.energy|default:"0"}</td>
				</tr>
			</table>
{/if}