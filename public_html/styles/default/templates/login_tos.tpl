{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<div class="ikBlock" id="tos">
	<div class="ikHead">Terms of Service Agreement</div>
	<div class="ikBody">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="tos" />
		<input type="hidden" name="tos_hash" id="tos_hash" value="{$tos_hash}" />

		<p>By creating an account and playing Imperial Kingdoms, also referred to as the 'game', you agree to these terms of use. Anyone caught in violation of the terms will be repremanded and possibly banned.</p>
	
		<div style="width: 100%; height: 350px; overflow: auto;">
			<p><span class="maintitle">Your Account</span><br />
			You agree to have only one account and only for yourself. You may not create an account for anyone else to use, share your password, or log into anyone elses account for any reason. The game is designed so you only need one account. If you decide to start a new player in the same round you must abandon your current player first. Violating this part of the agreement is a bannable offense in which you will lose access to all accounts involved and possibly to the game itself.</p>
			
			<p><span class="maintitle">Your Access</span><br />
			You agree to only play the game in the intended manner, using a standard web browser. This forbids the use of bots, scripting, macros, and other automated methods of access and control. Any violation of this part of the agreement will be met with a permanent ban to all accounts of the player, not just the one being automated.</p>
			
			<p><span class="maintitle">Your Conduct</span><br />
			You agree to conduct yourself in a civilized manner. This means you are not to use vulgar language or subject other users to harrassment or personal threats. Any violations of this part of the agreement will be met with a warning and, if continues, a temporary banning. Further violations will be met with a permanent ban.</p>
			
			<p><span class="maintitle">The Game</span><br />
			The game is constantly under development and, as such, things may change without notice. Obvious bugs are not to be exploited and any exploiters will be repremanded accordingly.</p>
			
			<p><span class="maintitle">The Disclaimer</span><br />
			You agree not to hold Imperial Kingdoms and anyone associated with it liable for any direct, indirect, or incidental damage resulting from accessing or playing the game or any associated content, including but not limited to personal injury, divorce, or loss of income.</p>
			
			<p><span class="maintitle">The Privacy Policy</span><br />
			Imperial Kingdoms collects usage data of its users to help improve service for its users. Imperial Kingdoms will never give or sell any personal information about its users to anyone.</p>
		</div>

		<div>
			<input class="mainoption" type="submit" name="agree" id="agree" value="I Agree" />&nbsp;&nbsp;
			<input class="liteoption" type="submit" name="disagree" id="disagree" value="I Disagree"  />
		</div>

		</form>
	</div>
</div>

{include file="footer.tpl"}