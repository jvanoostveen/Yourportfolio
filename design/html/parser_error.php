<img src="<?=IMAGES?>spacer.gif" width="1" height="15">
<table width="325" height="195" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td width="1" height="28"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	<td>
	<table width="309" height="28" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('folder_import_header')?>" width="31" height="28" class="special"></td>
		<td class="namebar" valign="middle" id="title"><?=gettext('Er is een fout opgetreden')?></td>
	</tr>
	</table>
	</td>
	<td width="10" bgcolor="black"><img src="<?=IMAGES?>black_spacer.gif" width="10" height="28" class="special"></td>
	<td width="4" valign="top"><img src="<?=IMAGES?>round_right.gif" width="4" height="28" class="special"></td>
	<td width="1" valign="top"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
</tr>
<tr>
	<td width="1" class="verticalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
	<td class="bg_white" colspan="3">
	<br>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td nowrap class="txt_medium padding" width="145" align="right" valign="top">
		<?=gettext('afbeelding')?>:
		</td>
		<td valign="top">
		<div id="upload_holder"><div id="uploader"><img src="<?=IMAGES?>ftp_import_error.gif" width="120" height="90" /></div></div>
		</td>
	</tr>
	<tr>
		<td nowrap class="txt_medium padding" width="10%" align="right">
		<?=gettext('bestandsnaam')?>:
		</td>
		<td><div id='file_name_txt'>--</div></td>
	</tr>
	<tr>
		<td colspan="2" class="bg_black" height="1"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
	</tr>
	<tr>
		<td nowrap class="txt_medium padding" align="left" height="30" id="progress_txt"></td>
		<td class="padding" align="right"><a href="javascript:window.close();" class="save_black" id="stop_button_text"><?=gettext('sluit')?></a></td>
	</tr>
	</table>
	</td>
	<td width="1" class="verticalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
</tr>
<tr>
	<td width="1" height="1" class="dotline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
	<td width="1" height="1" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
	<td width="10" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="10" height="1" class="special" class="special"></td>
	<td width="4" valign="top" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="4" height="1" class="special"></td>
	<td width="1" class="dotline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
</tr>
</table>
