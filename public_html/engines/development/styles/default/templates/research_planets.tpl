{* Smarty *}

{if $output == "javascript"}
var varPlanets = xGetElementById('option_planets');
varPlanets.options.length = 0;
{foreach name=planets from=$planets item=planet key=planet_id}
varPlanets.options[{$smarty.foreach.planets.iteration-1}] = new Option("{if $planet.researching > 0}* {/if}P#{$planet_id} {$planet.name}{if $planet.researchbonus > 0} ({$planet.researchbonus}% Bonus){/if}", "{$planet_id}");
{/foreach}
{else}
						<select name="planet_id" id="option_planets" onChange="fnWarptimeResearch()">
{foreach name=planets from=$planets item=planet key=planet_id}
{if $smarty.foreach.planets.first}{assign var="warptime" value=$planet.warptime_research}{/if}
							<option value="{$planet_id}">{if $planet.researching > 0}* {/if}P#{$planet_id} {$planet.name}{if $planet.researchbonus > 0} ({$planet.researchbonus}% Bonus){/if}</option>
{foreachelse}
							<option value="">None</option>
{/foreach}
						</select>
						<div id="divWarptimeContainer" name="divWarptimeContainer"{if !$warptime} style="display: none; visibility: hidden;"{/if}>
							<input name="warptime" id="warptime" type="checkbox" /> Warp Time Left: <div id="divWarptime" name="divWarptime" style="display: inline;">{$warptime}</div><br />
						</div>
{/if}

{if $output != "javascript"}
<script language="JavaScript">
{/if}

var varWarptimeResearch = Array();
{foreach from=$planets item=planet key=planet_id}
varWarptimeResearch[{$planet_id}] = "{$planet.warptime_research}";
{/foreach}
fnWarptimeResearch();

{if $output != "javascript"}
</script>
{/if}