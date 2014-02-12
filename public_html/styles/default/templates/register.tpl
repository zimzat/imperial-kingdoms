{* Smarty *}
{include file="header.tpl"}

{include file="status.tpl"}

{if $success != true}
<div class="ikBlock">
	<div class="ikHead">Registration Information</div>
	<div class="ikBody ikForm">
		<form action="{$actionurl}" method="POST">
		<input type="hidden" name="fn" id="fn" value="register">

		<div>
			<input name="forumaccount" id="forumaccount" type="checkbox" {if $forumaccount != ""}selected="selected" {/if}/>
			If you have a forum account, check here and only enter your forum username and password to have them imported. You will still need to set the preferences below.<br />Accounts imported from the forum do not need to be confirmed and can start playing immediately.
		</div>

		<div>
			<label for="username">Username:</label>
			<input type="text" name="username" id="username" maxlength="40" value="{$username}" /><br />
			Username is <b>not</b> case-sensitive
		</div>

		<div>
			<label for="password">Password:</label>
			<input type="password" name="password" id="password" maxlength="100" /></br >
			Password is case-sensitive
		</div>

		<div>
			<label for="password_confirm">Confirm password:</label>
			<input type="password" name="password_confirm" id="password_confirm" maxlength="100" />
		</div>

		<div>
			<label for="email">Email:</label>
			<input type="text" name="email" id="email" maxlength="255" value="{$email}" />
		</div>

		<div class="ikSubhead">Preferences</div>

		<div>
			<label for="decimal_symbol">Decimal symbol:</label>
			<input type="text" name="decimal_symbol" id="decimal_symbol" value="{$preferences.decimal_symbol|default:"."}" maxlength="1" />
		</div>

		<div>
			<label for="thousands_seperator">Thousands seperator:</label>
			<input type="text" name="thousands_seperator" id="thousands_seperator" value="{$preferences.thousands_seperator|default:","}" maxlength="1" />
		</div>

		<div>
			<label for="timezone">Timezone:</label>
			<select name="timezone" id="timezone" class="post">
{foreach from=$timezones item=timezone}
				<option value="{$timezone}"{if $preferences.timezone == $timezone} selected="selected"{/if}>GMT{if $timezone <> 0} {$timezone} Hours{/if}</option>
{/foreach}
			</select>
		</div>

		<div>
			<label for="timestamp_format">Timestamp format:</label>
			<input type="text" name="timestamp_format" id="timestamp_format" value="{$preferences.timestamp_format|default:"Y-m-d H:i:s"}" maxlength="14" /><br />
			The syntax used is identical to the PHP <a href='http://www.php.net/date' target='_other'>date()</a> function.
		</div>

		<div>
			<input type="submit" name="register" id="register" value="Register" />&nbsp;&nbsp;
			<input type="reset" name="reset" id="reset" value="Reset" />
		</div>
	</div>
</div>
{/if}

{include file="footer.tpl"}