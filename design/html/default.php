<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="10">
	<img src="<?=IMAGES_GENERAL?>spacer.gif" width="10" height="1">
	</td>
	<td width="200" valign="top">
	<!-- menu -->
	menu
<? require(HTML.$canvas->menu_template.".php"); ?>
	<!-- end menu -->
	</td>
	<td valign="top">
	<!-- main -->
<? require(HTML.$canvas->inner_template.".php"); ?>
	<!-- end main -->
	</td>
</tr>
</table>