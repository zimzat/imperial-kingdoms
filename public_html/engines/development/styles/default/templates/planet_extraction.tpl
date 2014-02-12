{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

		<table class="forumline" cellpadding="4" cellspacing="1">
			<tr>
				<td class="header">Extraction Rates: {$planet_name} (#{$planet_id})</td>
			</tr>
			<tr>
				<td class="row1">
					<form method="post" action="{$actionurl}" name="multiform" id="multiform">
					<input name="fn" type="hidden" value="planet_extraction_set" />
					<input name="planet_id" type="hidden" value="{$planet_id}" />
					<table border="0" cellpadding="0" cellspacing="1" width="100%">
						<tr>
							<td>FE: </td>
							<td>{$minerals.fe|default:"0"}</td>
							<td>{$mineralsremaining.fe|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[fe]" type="text" align="right" class="post" value="{$extraction.fe|default:"0"}" />%</td>
							<td></td>
							
							<td>O: </td>
							<td>{$minerals.o|default:"0"}</td>
							<td>{$mineralsremaining.o|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[o]" type="text" align="right" class="post" value="{$extraction.o|default:"0"}" />%</td>
						</tr>
						<tr>
							<td>SI: </td>
							<td>{$minerals.si|default:"0"}</td>
							<td>{$mineralsremaining.si|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[si]" type="text" align="right" class="post" value="{$extraction.si|default:"0"}" />%</td>
							<td></td>
							
							<td>MG: </td>
							<td>{$minerals.mg|default:"0"}</td>
							<td>{$mineralsremaining.mg|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[mg]" type="text" align="right" class="post" value="{$extraction.mg|default:"0"}" />%</td>
						</tr>
						<tr>
							<td>NI: </td>
							<td>{$minerals.ni|default:"0"}</td>
							<td>{$mineralsremaining.ni|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[ni]" type="text" align="right" class="post" value="{$extraction.ni|default:"0"}" />%</td>
							<td></td>
							
							<td>S: </td>
							<td>{$minerals.s|default:"0"}</td>
							<td>{$mineralsremaining.s|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[s]" type="text" align="right" class="post" value="{$extraction.s|default:"0"}" />%</td>
						</tr>
						<tr>
							<td>HE: </td>
							<td>{$minerals.he|default:"0"}</td>
							<td>{$mineralsremaining.he|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[he]" type="text" align="right" class="post" value="{$extraction.he|default:"0"}" />%</td>
							<td></td>
							
							<td>H: </td>
							<td>{$minerals.h|default:"0"}</td>
							<td>{$mineralsremaining.h|default:"0"}</td>
							<td><input size="3" maxlength="3" name="extraction[h]" type="text" align="right" class="post" value="{$extraction.h|default:"0"}" />%</td>
						</tr>
						<tr>
							<td class="subheader" colspan="9"><input class="mainoption" name="set" type="submit" value="Set" /></td>
						</tr>
					</table>
					</form>
				</td>
			</tr>
		</table>
		</form>

{include file="menu_planet.tpl"}

{include file="footer.tpl"}