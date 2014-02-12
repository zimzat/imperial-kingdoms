{* Smarty *}
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Combat Report</td>
			</tr>
			<tr>
				<td class="row1 nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader">Defender</td>
							<td class="subheader">Date</td>
							<td class="subheader">Location</td>
						</tr>

						<tr>
							<td class="row1 data al-c">{$combatreport.header.defender}</td>
							<td class="row1 data al-c">{$combatreport.header.date}</td>
							<td class="row1 data al-c">{$combatreport.header.location.name} (P#{$combatreport.header.location.planet_id})</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		
		<br clear="all" />
		
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Participants</td>
			</tr>
			<tr>
				<td class="row1 nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" width="40%">Kingdom</td>
							<td class="subheader">Army Count</td>
							<td class="subheader">Navy Count</td>
						</tr>
{foreach from=$combatreport.participants item=totals key=kingdom_id}{cycle values="row1,row2" assign="rowclass"}
						<tr>
							<td class="{$rowclass} data" width="40%">K#{$kingdom_id} {$combatreport.names.kingdoms.$kingdom_id}</td>
							<td class="{$rowclass} data al-c nowrap">{$totals.army|default:"0"}</td>
							<td class="{$rowclass} data al-c nowrap">{$totals.navy|default:"0"}</td>
						</tr>
{/foreach}
					</table>
				</td>
			</tr>
		</table>
		
{if !empty($combatreport.details)}
		<br clear="all" />
		
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Combat Details</td>
			</tr>
			<tr>
				<td class="row1 nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
{foreach from=$combatreport.details item=kingdoms key=kingdom_id}
						<tr>
							<td class="subheader" colspan="7">{$combatreport.names.kingdoms.$kingdom_id} (#{$kingdom_id})</td>
						</tr>
						<tr>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="15">&nbsp;</td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="15">&nbsp;</td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="*"><b>Weapon</b></td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="*"><b>Target</b></td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="30"><b>Hits</b></td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="30"><b>Damage</b></td>
							<td class="row1 data nowrap" style="border-bottom: 1px solid white;" width="30"><b>Kills</b></td>
						</tr>
{foreach from=$kingdoms item=types key=type}
{foreach from=$types item=groups key=group_id}
						<tr>
							<td class="row1 data nowrap" colspan="7">{$combatreport.names.groups.$type.$group_id} (#{$group_id})</td>
						</tr>
{foreach from=$groups item=units key=unit_id}
						<tr>
							<td class="row2 data nowrap" width="15">&nbsp;</td>
							<td class="row2 data nowrap" colspan="4">{$combatreport.names.units.$type.$unit_id} (#{$unit_id})</td>
							<td class="row2 data al-r nowrap" width="30"><b>Total:</b> </td>
							<td class="row2 data al-r nowrap" width="30">{$combatreport.casualties.$kingdom_id.$type.$group_id.$unit_id.killed+$combatreport.casualties.$kingdom_id.$type.$group_id.$unit_id.remaining}</td>
						</tr>
{foreach from=$units item=weapons key=weapon_id}
{foreach from=$weapons item=weapon}{assign var=target_unit_id value=$weapon.target_unit_id}
						<tr>
							<td class="row1 data nowrap" width="15">&nbsp;</td>
							<td class="row1 data nowrap" width="15">&nbsp;</td>
							<td class="row1 data nowrap">{$combatreport.names.weapons.$weapon_id} (#{$weapon_id})</td>
							<td class="row1 data nowrap">{$combatreport.names.units.$type.$target_unit_id} (#{$target_unit_id})</td>
							<td class="row1 data al-r nowrap" width="30">{$weapon.hits}</td>
							<td class="row1 data al-r nowrap" width="30">{$weapon.damage}</td>
							<td class="row1 data al-r nowrap" width="30">{$weapon.kills}</td>
						</tr>
{/foreach}
{/foreach}
{/foreach}
{/foreach}
{/foreach}
{/foreach}
					</table>
				</td>
			</tr>
		</table>
{/if}

{if !empty($combatreport.casualties)}
		<br clear="all" />
		
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Casualties</td>
			</tr>
			<tr>
				<td class="row1 nowrap">
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td class="subheader" colspan="2" width="30"></td>
							<td class="subheader">Unit</td>
							<td class="subheader" width="30">Killed</td>
							<td class="subheader" width="30">Remaining</td>
						</tr>
{foreach from=$combatreport.casualties item=kingdoms key=kingdom_id}
						<tr>
							<td class="row1 data" colspan="5">{$combatreport.names.kingdoms.$kingdom_id} (#{$kingdom_id})</td>
						</tr>
{foreach from=$kingdoms item=types key=type}
{foreach from=$types item=groups key=group_id}
						<tr>
							<td class="row2 data" width="15"></td>
							<td class="row2 data" colspan="4">{$combatreport.names.groups.$type.$group_id} (#{$group_id})</td>
						</tr>

{foreach from=$groups item=units key=unit_id}
						<tr>
							<td class="row1 data" width="15"></td>
							<td class="row1 data" width="15"></td>
							<td class="row1 data">{$combatreport.names.units.$type.$unit_id} (#{$unit_id})</td>
							<td class="row1 data al-r nowrap" width="30">{$units.killed}</td>
							<td class="row1 data al-r nowrap" width="30">{$units.remaining}</td>
						</tr>
{/foreach}
{/foreach}
{/foreach}
{/foreach}
					</table>
				</td>
			</tr>
		</table>
{/if}