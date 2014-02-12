{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

{if !empty($idle_planets)}
		<form method="post" action="{$actionurl}" name="multiform" id="multiform">
		<input name="fn" type="hidden" value="planet_massmanage_set" />
{include file="planet_compactinfo.tpl" planet_form=true planets=$idle_planets}
		<div class="forumline" style="margin-top: 3px; padding-left: 3px; padding-right: 3px; float: left;"><a href="#" onclick="fnCheckAll({$idle_planet_count}); return false;">Check All</a> - <a href="#" onclick="fnCheckNone({$idle_planet_count}); return false;">Check None</a></div>
		<div class="forumline" style="margin-top: 3px; padding-left: 3px; padding-right: 3px; float: right;">Click one then hold shift and click another to select a range of planets</div>
		
		<br /><br clear="both" />
		
		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td rowspan="2" width="50%">
					<select name="building_id" size="5">
					{html_options options=$buildings}
					</select>
				</td>
				<td width="50%">Cranes: <input name="cranes" id="cranes" class="post" size="3" maxlength="3" value="{$available_cranes}" /> x <input name="planning" id="planning" class="post" size="3" maxlength="3" value="{$available_planning}" /></td>
			</tr>
			<tr>
				<td width="50%">
{if $warptime != ""}
						<input name="warptime" type="checkbox" /> Warp Time Left: {$warptime}<br />
{/if}
					<input type="submit" name="build" id="build" class="mainoption" value="Build" />
				</td>
			</tr>
		</table>
		</form>
{/if}

{if count($busy_planets) > 0}{include file="planet_compactinfo.tpl" planets=$busy_planets planet_form=false}{/if}

{include file="menu_planet.tpl"}

{include file="footer.tpl"}