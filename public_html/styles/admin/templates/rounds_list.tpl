{* Smarty *}
{include file="header.tpl"}

					<table width="100%" cellspacing="2" cellpadding="2" border="0" align="center" valign="top">
						<tr>
							<td valign="top" width="30%"><div style="text-align: left; font-size: 25px; font-weight: bold;">Imperial</div><br /><div style="text-align: right; font-size: 25px; font-weight: bold;">Kingdoms</div></td>
							<td align="center" valign="top" width="70%" style="font-size: 12px; font-weight: bold;">
								<a href="{$baseurl}login.php" onClick="window.open('{$baseurl}login.php','IK','toolbar=no,menubar=no,windowbar=no,titlebar=no,scrollbars=no,resizable=no,width=642,height=482'); return false;">Play</a><!-- <a href="{$baseurl}register.php">Register</a>--><br />
								<a href="{$baseurl}forum/">Forums</a>
							</td>
						</tr>
						<tr>
							<td valign="top" colspan="2" align="center">
{include file="status.tpl"}

								<table border="0" cellpadding="4" cellspacing="1" width="30%" class="forumline">
									<tr>
										<th class="thHead" align="center" width="100%" height="25" nowrap="nowrap">Games</th>
									</tr>
{section name=element loop=$games}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">{$games[element].name}<br />
										Start: {$games[element].starttime}<br />
										Stop: {$games[element].stoptime}<br />
										<a href="{$actionself}?fn=view&game={$games[element].game_id}">Modify</a></td>
									</tr>
{sectionelse}
									<tr>
										<td class="row1" valign="top" width="100%"><span class="gensmall">None</span></td>
									</tr>
{/section}
								</table>
								
								<a href="{$actionself}?fn=view">New</a></td>
							</td>
						</tr>
					</table>

{include file="footer.tpl"}