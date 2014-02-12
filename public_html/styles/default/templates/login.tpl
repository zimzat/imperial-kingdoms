{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<div class="ikBlock">
	<div class="ikHead">Log In</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST" enctype="application/x-www-form-urlencoded">
		<input type="hidden" name="fn" id="fn" value="authlogin">

		<div>
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" maxlength="25" value="" />
		</div>

		<div>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" maxlength="100" />
		</div>

		<div>
			<input type="submit" name="login" id="login" value="Log In" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</div>

		</form>
	</div>
	<div class="ikFoot"><a href="{$actionurl}?fn=forgotpassword">Forgot Username/Password?</a></div>
</div>

{include file="footer.tpl"}