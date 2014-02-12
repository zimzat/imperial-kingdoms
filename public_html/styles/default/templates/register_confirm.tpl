{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

{if $success != true}
<div class="ikBlock">
	<div class="ikHead">Confirmation Code</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="confirm">

		<div>
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" maxlength="20" value="{$username}" /><br />
			Username is Case-Sensitive
		</div>

		<div>
			<label for="activation">Confirmation code:</label>
			<input type="text" name="activation" id="activation" maxlength="32" value="{$activation}" />
		</div>

		<div>
			<input type="submit" name="register" id="register" value="Confirm" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</tr>

		</form>
	</div>
</div>
{/if}

{include file="footer.tpl"}