{* Smarty *}
{include file="header.tpl" bodyline="true"}

{include file="status.tpl"}

{literal}
<script language="JavaScript">
<!--
	function fnClickIndependant()
	{
		xVisibility('kingdomname', true);
		xVisibility('independantinfo', true);
		xVisibility('jointeaminfo', false);
		xVisibility('createteaminfo', false);
		
		xDisplay('kingdomname', 'block');
		xDisplay('independantinfo', 'block');
		xDisplay('jointeaminfo', 'none');
		xDisplay('createteaminfo', 'none');
	}
	
	function fnClickJoin()
	{
		xVisibility('kingdomname', false);
		xVisibility('independantinfo', false);
		xVisibility('jointeaminfo', true);
		xVisibility('createteaminfo', false);
		
		xDisplay('kingdomname', 'none');
		xDisplay('independantinfo', 'none');
		xDisplay('jointeaminfo', 'block');
		xDisplay('createteaminfo', 'none');
	}
	
	function fnClickCreate()
	{
		xVisibility('kingdomname', true);
		xVisibility('independantinfo', false);
		xVisibility('jointeaminfo', false);
		xVisibility('createteaminfo', true);
		
		xDisplay('kingdomname', 'block');
		xDisplay('independantinfo', 'none');
		xDisplay('jointeaminfo', 'none');
		xDisplay('createteaminfo', 'block');
	}
// -->
</script>
{/literal}

<form action="{$actionurl}" method="POST">
<input type="hidden" name="fn" value="joinround">
<table cellpadding="0" cellspacing="0" class="forumline">
	<tr>
		<td class="header" colspan="2">Join Round</td>
	</tr>
	<tr>
		<td>
			<h2 style="text-align: center;">Welcome to Imperial Kingdoms!</h2>
			Welcome to another round of Imperial Kingdoms. To get started simply fill out the form below with the appropriate options.
		</td>
	</tr>
	<tr>
		<td>
			<table margin="0" border="0" cellpadding="0" cellspacing="0" width="100%">
{if $description != ""}
				<tr>
					<td class="row1" width="30%">Description:</td>
					<td class="row2">{$description}</td>
				</tr>
{/if}
				<tr>
					<td class="row1">Kingdom Mode:</td>
					<td class="row2">
{if $teams == "0"}
						<input type="hidden" name="kingdom_mode" value="independant" />
						Independant Only
{elseif $teams == "1"}
						<input type="radio" name="kingdom_mode" value="independant" onClick="fnClickIndependant();"{if $kingdom_mode == "independant" || $kingdom_mode == ""} checked="checked"{/if} />
						Independant&nbsp;&nbsp;
{/if}
{if $teams == "1" || $teams == "2"}
						<input type="radio" name="kingdom_mode" value="jointeam" onClick="fnClickJoin();"{if $kingdom_mode == "jointeam"} checked="checked"{/if} />
						Join Team&nbsp;&nbsp;
						<input type="radio" name="kingdom_mode" value="createteam" onClick="fnClickCreate();"{if $kingdom_mode == "createteam"} checked="checked"{/if} />
						Create Team
{/if}
					</td>
				</tr>
				<tr>
					<td class="row1" width="30%">Player Name:</td>
					<td class="row2"><input type="text" class="post" style="width:200px" name="player_name" size="25" maxlength="40" value="{$player_name}" /></td>
				</tr>
				<tr>
					<td class="row1">Planet Name:</td>
					<td class="row2"><input type="text" class="post" style="width:200px" name="planet_name" size="25" maxlength="40" value="{$planet_name}" /></td>
				</tr>
			</table>
			<div id="kingdomname" style="visibility: visible; display: block;">
			<table margin="0" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="row1" width="30%">Kingdom Name:</td>
					<td class="row2"><input type="text" class="post" style="width:200px" name="kingdom_name" size="25" maxlength="40" value="{$kingdom_name}" /></td>
				</tr>
			</table>
			</div>
{if $teams != "2"}
			<div id="independantinfo" style="visibility: visible; display: block;">
			<table margin="0" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="row1" width="30%">Planets:</td>
					<td class="row2">
{if $min_planets == $max_planets}
						{$max_planets}
{else}
						<select name="planets">{section name=planets start=$min_planets loop=$max_planets+1}<option value="{$smarty.section.planets.index}"{if $planets == $smarty.section.planets.index} selected="selected"{/if}>{$smarty.section.planets.index}</option>{/section}</select>
{/if}
					</td>
				</tr>
				<tr>
					<td class="row1">Bonus:</td>
					<td class="row2">
{if $bonus == "0"}
						No Bonus
{else}
						<select name="bonus"><option value="0"{if $planet_bonus == 0 || $planet_bonus == ""} selected="selected"{/if}>-30% Research Time split between Planets</option><option value="1"{if $planet_bonus == 1} selected="selected"{/if}>-30% Construction Time split between Planets</option></select>
{/if}
					</td>
				</tr>
			</table>
			</div>
{/if}
{if $teams == "1" || $teams == "2"}
			<div id="jointeaminfo" style="visibility: hidden; display: none;">
			<table margin="0" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="row1" width="30%">Planet Code:</td>
					<td class="row2"><input type="text" class="post" style="width:200px" name="planet_code" size="25" maxlength="32" value="{$planet_code}" /></td>
				</tr>
			</table>
			</div>
			<div id="createteaminfo" style="visibility: hidden; display: none;">
			<table margin="0" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="row1" width="30%">Note:</td>
					<td class="row2">You will receive an in-game mail containing your team's planet codes.</td>
				</tr>
			</table>
			</div>
{/if}
		</td>
	</tr>
	<tr>
		<td class="subheader" colspan="2"><input type="submit" name="submit" value="Join" class="mainoption" />&nbsp;&nbsp;<input type="reset" value="Reset" name="reset" class="liteoption" onClick="xVisibility('kingdomname'); xVisibility('independantinfo'); xVisibility('jointeaminfo'); xVisibility('createteaminfo');" /></td>
	</tr>
</table>
</form>

{literal}
<script language="JavaScript">
<!--
{/literal}
{if $kingdom_mode == "jointeam"}
	fnClickJoin();
{elseif $kingdom_mode == "createteam"}
	fnClickCreate();
{else}
	fnClickIndependant();
{/if}
{literal}
// -->
</script>
{/literal}

{include file="footer.tpl"}