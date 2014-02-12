{* Smarty *}

{if $bodyline == "true"}
				</td>
			</tr>
		</table>
{/if}

{if $alert != ""}
{literal}	<script type="text/javascript">
	<!-- {/literal}
{foreach from=$alert item=value key=type}
		fnAlert('{$type}', '{$value|default:"false"}');
{/foreach}
{literal}	// -->
	</script>{/literal}
{/if}

	</body>
</html>