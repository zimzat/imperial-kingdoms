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
							<td valign="top" colspan="2">
{include file="status.tpl"}

								<form action="{$actionurl}" method="POST">
								<input type="hidden" name="fn" value="modify">
{if $game.game_id != ""}
								<input type="hidden" name="game_id" value="{$game.game_id}">
{/if}
								<table border="0" cellpadding="3" cellspacing="1" width="100%" class="forumline">
									<tr>
										<th class="thHead" colspan="2" height="25" valign="middle">Create Game</th>
									</tr>
									<tr>
										<td class="row1" width="22%"><span class="gen"><b>Name:</b></span></td>
										<td class="row2" width="78%"><input name="name" type="text" class="post" style="width:450px" value="{$game.name}" maxlength="50" size="45" /></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Description:</b></span></td>
										<td class="row2"><textarea name="description" rows="15" cols="35" wrap="virtual" style="width:450px" class="post">{$game.description}</textarea></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Start Time:</b></span></td>
										<td class="row2"><input name="starttime" type="text" class="post" style="width:190px" value="{$game.starttime}" maxlength="19" size="19" /></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Stop Time:</b></span></td>
										<td class="row2"><input name="stoptime" type="text" class="post" style="width:190px" value="{$game.stoptime}" maxlength="19" size="19" /></td>
									</tr>{*
									<tr>
										<td class="row1"><span class="gen"><b>Quadrants:</b></span></td>
										<td class="row2">
											<table border="0" margin="0" cellspacing="0" cellpadding="0">
{section name="y" loop="7"}
												<tr>
{section name="x" loop="7"}
													<td><input type="checkbox" name="quandrant_{$smarty.section.x.index}{$smarty.section.y.index}" class="post"{if $quadrant[x][y].active != ""}checked="checked"{/if} /></td>
{/section}
												</tr>
{/section}
											</table>
										</td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Stars per Quadrant:</b></span></td>
										<td class="row2"><input name="quadrantstars" type="text" class="post" style="width:20px" value="{$game.quadrantstars}" maxlength="2" size="2" /> <span class="gensmall">(0 to 49)</span></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Planets per Starsystem:</b></span></td>
										<td class="row2"><input name="min-planets-per-starsystem" type="text" class="post" style="width:20px" value="{$game.starsystem_planets_min}" maxlength="2" size="2" /> Min <input name="max-planets-per-starsystem" type="text" class="post" style="width:20px" value="{$game.starsystem_planets_max}" maxlength="2" size="2" /> Max <span class="gensmall">(0 to 49)</span></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Max Players per team:</b></span></td>
										<td class="row2"><input name="players-per-team" type="text" class="post" style="width:20px" value="{$game.team_players}" maxlength="2" size="2" /> <span class="gensmall">(0 to 49)</span></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Buildings:</b></span></td>
										<td class="row2">&lt;list&gt;</td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Rates:</b></span></td>
										<td class="row2"><span class="gen">
											F: <input name="food-rate" type="text" class="post" style="width:50px" value="{$game.food_rate}" size="5" /> 
											W: <input name="workers-rate" type="text" class="post" style="width:50px" value="{$game.workers_rate}" size="5" /> 
											E: <input name="energy-rate" type="text" class="post" style="width:50px" value="{$game.energy_rate}" size="5" /> 
											M: <input name="minerals-rate" type="text" class="post" style="width:50px" value="{$game.minerals_rate}" size="5" />
										</span></td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Units:</b></span></td>
										<td class="row2">&lt;list&gt;</td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Concepts:</b></span></td>
										<td class="row2">&lt;list&gt;</td>
									</tr>
									<tr>
										<td class="row1"><span class="gen"><b>Stored:</b></span></td>
										<td class="row2"><span class="gen">
											F: <input name="food-rate" type="text" class="post" style="width:50px" value="{$game.food}" size="5" maxlength="10" /> 
											W: <input name="workers-rate" type="text" class="post" style="width:50px" value="{$game.workers}" size="5" maxlength="10" /> 
											E: <input name="energy-rate" type="text" class="post" style="width:50px" value="{$game.energy}" size="5" maxlength="10" /> 
											M: <input name="minerals-rate" type="text" class="post" style="width:50px" value="{$game.minerals}" size="5" maxlength="10" />
										</span></td>
									</tr>*}
									<tr>
										<td class="catBottom" colspan="2" align="center" height="28"><input type="submit" name="submit" class="mainoption" value="Submit" />{if $game.game_id} != ""} <input type="submit" name="delete" class="mainoption" value="Delete" onClick="return confirm('Delete?');">{/if}</td>
									</tr>
								</table>
								</form>

							</td>
						</tr>
					</table>

{include file="footer.tpl"}