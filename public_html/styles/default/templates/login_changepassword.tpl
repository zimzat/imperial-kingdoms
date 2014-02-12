{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<div class="ikBlock">
	<div class="ikHead">Forgot Password</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="changepassword">

		<div>
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" maxlength="40" value="{$username}" />
		</div>

		<div>
			<label for="resetkey">Reset Key:</label>
			<input type="text" name="resetkey" id="resetkey" maxlength="255" value="{$resetkey}" />
		</div>

		<div>
			<label for="newpassword">New Password:</label>
			<input type="text" name="newpassword" id="newpassword" maxlength="255" value="" />
		</div>

		<div>
			<label for="newpasswordconfirm">Confirm Password:</label>
			<input type="text" name="newpasswordconfirm" id="newpasswordconfirm" maxlength="255" value="" />
		</div>

		<div>
			<input type="submit" name="submit" id="submit" value="Submit" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</div>

		</form>
	</div>
</div>

{include file="footer.tpl"}