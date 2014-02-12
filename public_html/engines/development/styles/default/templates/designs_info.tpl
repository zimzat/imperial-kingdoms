{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table class="forumline" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr>
					<td class="header">{$design.name}</td>
				</tr>
{if $design.image != "" || $design.description != ""}
				<tr>
					<td><img src="{$baseurl}images/buildings/{$design.image}" />{$design.description}</td>
				</tr>
{/if}
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" id="fn" type="hidden" value="designs_upgrade" />
			<input name="{$design.type}design_id" id="design_id" type="hidden" value="{$design.design_id}" />
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">Upgradable Attributes</td>
				</tr>
				<tr>
					<td>
						<table cellpadding="0" cellspacing="1" border="0">
							<tr>
								<td class="row2 al-c nowrap b">&nbsp;</td>
								<td class="row2 al-c nowrap b" width="100%">Name</td>
								<td class="row2 al-c nowrap b">Current</td>
								<td class="row2 al-c nowrap b">&nbsp;</td>
								<td class="row2 al-c nowrap b">Size</td>
							</tr>
{foreach name=upgrades from=$design.upgrades item=upgrade key=attribute}
{cycle values="row1,row2" assign="rowclass"}
							<tr>
								<td class="{$rowclass} nowrap"><input type="radio" name="attribute" value="{$attribute}" /></td>
								<td class="{$rowclass}">{$upgrade.name}</td>
								<td class="{$rowclass} nowrap">{$upgrade.current}</td>
								<td class="{$rowclass} nowrap">+{$upgrade.increase}</td>
								<td class="{$rowclass} nowrap">+{$upgrade.sizeincrease}g</td>
							</tr>
{foreachelse}
							<tr>
								<td class="al-c i" colspan="5">None</td>
							</tr>
{/foreach}
						</table>
					</td>
				</tr>
			</table>
			
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="al-c">
{include file="research_planets.tpl"}
					</td>
				</tr>
				<tr>
					<td class="al-c">
						<input type="submit" name="upgrade" id="upgrade" class="mainoption" value="Upgrade" />
					</td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top" width="50%">
			{include file="resourcecosts.tpl" resources=$design.resources resource_title="Research Costs"}
		</td>
	</tr>
</table>

{include file="menu_research.tpl"}

{include file="footer.tpl"}