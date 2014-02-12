{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

<div class="ikBlock">
	<div class="ikHead">Forgot Username/Password</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="initchangepassword">

		<div>
			<label for="email">E-mail address:</label>
			<input type="text" name="email" id="email" maxlength="255" value="" />
		</div>

		<div>
			<input type="submit" name="submit" id="submit" value="Submit" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</div>
		
		</form>
	</div>
</div>

{include file="footer.tpl"}