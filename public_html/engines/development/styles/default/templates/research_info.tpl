{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<table class="forumline" cellpadding="0" cellspacing="0">
	<tr>
		<td colspan="2">
			<table border="0" cellpadding="4" cellspacing="1" width="100%">
				<tr>
					<td class="header">{$concept.name}</td>
				</tr>
{if $concept.image != "" || $concept.description != ""}
				<tr>
					<td>{if $concept.image != ""}<img style="border: 1px solid #0075B2; float: left;" src="{$siteurl}images/illustrations/{$concept.image}" />{/if}{$concept.description}</td>
				</tr>
{/if}
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top" width="50%">
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">Grants</td>
				</tr>
{foreach name=grants from=$concept.grants item=grants}
{cycle values="row1,row2" assign="rowclass"}
				<tr>
					<td class="{$rowclass}">{$grants.type}: {$grants.name}</td>
				</tr>
{foreachelse}
				<tr>
					<td class="al-c i">None</td>
				</tr>
{/foreach}
			</table>

			<form method="post" action="{$actionurl}" name="multiform" id="multiform">
			<input name="fn" id="fn" type="hidden" value="research_research" />
			<input name="concept_id" id="concept_id" type="hidden" value="{$concept.concept_id}" />
			<table border="0" cellpadding="0" cellspacing="1" width="100%">
				<tr>
					<td class="subheader" colspan="11">Research</td>
				</tr>
				<tr>
					<td class="al-c">
{include file="research_planets.tpl"}
					</td>
				</tr>
				<tr>
					<td class="al-c">
						<input type="submit" name="research" id="research" class="mainoption" value="Research" />
					</td>
				</tr>
			</table>
			</form>
		</td>
		<td valign="top" width="50%">
			{include file="resourcecosts.tpl" resources=$concept.resources resource_title="Research Costs"}
		</td>
	</tr>
</table>

{include file="menu_research.tpl"}

{include file="footer.tpl"}