<script language="JavaScript">
	var swfu;
	
	var current = 0;
	var total = 0;
	
	$(function () {
		$('#upload_done').hide();
		$('#upload_error').hide();
		$('#last_image').hide();
		$('#button').unbind('click').click(start);
		
		var settings = {
			flash_url: '<?php echo SWFS.'swfupload.swf'; ?>',
			upload_url: 'parse.php',
			post_params: {
				aid: <?php echo $album_id; ?>,
				sid: <?php echo $section_id; ?>,
				PHPSESSID: '<?php echo session_id(); ?>'
			},
			file_size_limit: '<?php echo UPLOAD_MAX_SIZE; ?>B',
			file_types: '<?php echo $file_types; ?>',
			file_types_description: '<?php echo $file_description; ?>',
			file_upload_limit: 0,
			file_queue_limit: 0,
			debug: false,
			
			button_image_url: '<?php echo IMAGES;?>ftp_import_select_sprite.gif',
			button_width: 120,
			button_height: 90,
			button_placeholder_id: 'uploader',
			
			file_queue_error_handler: fileQueueError,
			file_dialog_complete_handler: fileDialogComplete,
			upload_start_handler: uploadStart,
			upload_progress_handler: uploadProgress,
			upload_success_handler: uploadSucces,
			upload_complete_handler: uploadComplete,
			queue_complete_handler: queueComplete
		};
		
		swfu = new SWFUpload(settings);
	});
	
	function fileQueueError(file, errorCode, message)
	{
		var msg = '';
		switch (errorCode)
		{
			case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
				//
				break;
			case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
				msg = '<?php echo sprintf(_('Geselecteerd bestand "{{FILE}}" is te groot, maximale bestandsgrootte is %sB.'), UPLOAD_MAX_SIZE); ?>';
				break;
			case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
				msg = '<?php echo sprintf(_('Alleen bestanden met extensie %s zijn toegestaan. "{{FILE}}" zal niet worden geupload.'), $file_types); ?>';
				break;
			case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
				msg = '<?php echo _('"{{FILE}}" is een leeg bestand.'); ?>';
				break;
			default:
				msg = 'Unknown error: ' + errorCode + ', message: ' + message;
				break;
		}
		
		msg = msg.replace('{{FILE}}', file.name);
		alert(msg);
	}
	
	function fileDialogComplete(num_files_selected, num_files_queued)
	{
		total = this.getStats().files_queued;
		update();
	}
	
	function start()
	{
		if (total == 0)
			return;
		
		$('#title').text('<?php echo _('Bezig met uploaden...'); ?>');
		
		$('#button').text('<?php echo _('annuleer'); ?>');
		$('#button').unbind('click').click(cancel);
		
		$('#last_image').show();
		
		current = 1;
		swfu.startUpload();
	}
	
	function cancel()
	{
		swfu.stopUpload();
		$('#upload_holder').hide();
		$('#upload_error').show();
		
		$('#button').text('<?php echo _('sluit'); ?>');
		$('#button').unbind('click').click(closePopup);
	}
	
	function update()
	{
		$('#progress_txt').text('<?=gettext('voortgang')?>: ' + current + ' <?=gettext('van de')?> ' + total);
	}
	
	function closePopup()
	{
		window.close();
	}
	
	function setFileProgress(percent)
	{
		$('#file_progress').text('');
		if (percent > -1)
		{
			if (percent == 100)
				$('#file_progress').text('(<?php echo _('verwerken'); ?>)');
			else
				$('#file_progress').text('(' + percent + '%)');
		}
	}
	
	function uploadStart(file)
	{
		update();
		
		setFileProgress(0);
		
		return true;
	}
	
	function uploadProgress(file, bytesLoaded, bytesTotal)
	{
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		setFileProgress(percent);
	}
	
	function uploadSucces(file, data, received_response)
	{
		$('#file_name_txt').text(file.name);
		
		if (data.indexOf('.jpg'))
		{
			$('#last_image > img').attr('src', data);
		}
	}
	
	function uploadComplete(file)
	{
		if (this.getStats().files_queued > 0)
		{
			current++;
			update();
		}
		
		setFileProgress(-1);
	}
	
	function queueComplete()
	{
		$('#upload_holder').hide();
		$('#last_image').hide();
		$('#upload_done').show();
		
		$('#title').text('<?php echo _('Upload gereed'); ?>');
		$('#file_name_txt').text('');
		$('#progress_txt').text('');
		setFileProgress(-1);
		
		$('#button').text('<?php echo _('sluit'); ?>');
		$('#button').unbind('click').click(closePopup);
		
		if (window.opener)
			window.opener.location.reload(true);
	}
</script>

<style type="text/css">
	body {
		overflow: hidden;
	}
	
	.holder {
		position: relative;
		width: 120px;
		height: 90px;
	}
	
	#last_image {
		position: absolute;
		top: 0px;
		width: 120px;
		height: 90px;
		text-align: center;
	}
	
	#file_name_txt {
		width: 170px;
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
	}
	
	#progress_txt {
		float: left;
		margin-top: 4px;
	}
	
	#file_progress {
		float: left;
		margin-left: 5px;
		margin-top: 4px;
	}
	
	#button_holder {
		float: right;
		margin-top: 4px;
	}
</style>

<img src="<?=IMAGES?>spacer.gif" width="1" height="15">
<table width="325" height="195" border="0" cellpadding="0" cellspacing="0" align="center">
<tr>
	<td width="1" height="28"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	<td>
	<table width="309" height="28" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('folder_import_header')?>" width="31" height="28" class="special"></td>
		<td class="namebar" valign="middle" id="title"><?=gettext('Selecteer bestanden')?></td>
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
		<div class="holder">
			<div id="upload_holder"><div id="uploader"><img src="<?=IMAGES?>ftp_import_select.gif" width="120" height="90" /></div></div>
			<div id="last_image"><img src="<?=IMAGES?>spacer.gif"></div>
			<div id="upload_done"><img src="<?=IMAGES?>ftp_import_ok.gif" width="120" height="90" /></div>
			<div id="upload_error"><img src="<?=IMAGES?>ftp_import_error.gif" width="120" height="90" /></div>
		</div>
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
		<td colspan="2" class="txt_medium padding" align="left" height="30"><div id="progress_txt"></div><div id="file_progress"></div>
			<div id="button_holder"><a href="#" class="save_black" id="button"><?php echo _('start'); ?></a></div>
		</td>
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
