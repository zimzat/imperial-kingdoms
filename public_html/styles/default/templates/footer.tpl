{* Smarty *}

</div>

{if $smarty.server.SCRIPT_NAME != "/login.php"}
<div id="footer">
	<div class="ikBlock" id="donate">
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but21.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
			<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
		</form>
		
		Support Imperial Kingdoms; Donate.
	</div>
</div>
{/if}
	</body>
</html>
