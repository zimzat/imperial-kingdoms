{* Smarty *}

{literal}
		<script type="text/javascript" language="javascript">
		<!--
			var varAvatar = xGetElementById('avatar');
			var varNewWidth = 50;
			var varNewHeight = 50;
			
			if (varAvatar.width > varNewWidth || varAvatar.height > varNewHeight)
			{
				if (varAvatar.width > varAvatar.height)
				{
					varNewHeight = varNewWidth * varAvatar.height / varAvatar.width;
				}
				else
				{
					varNewWidth = varNewHeight * varAvatar.width / varAvatar.height;
				}
				
				varAvatar.width = varNewWidth;
				varAvatar.height = varNewHeight;
			}
		// -->
		</script>
{/literal}