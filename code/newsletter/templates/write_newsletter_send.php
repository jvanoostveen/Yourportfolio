<script type="text/javascript">
active_view = 'send';
</script>

<?PHP
$newsletter = $data['newsletter'];
?>

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="120" nowrap align="right">&nbsp;</td>
	<td width="50%">&nbsp;</td>
	<td width="50%">&nbsp;</td>
	<td width="100">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>

<?PHP
require('write_newsletter_menu.php');
?>

<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
	<form action="newsletter_write.php" method="POST" enctype="multipart/form-data" name="sendForm" id="sendForm">
	<input type="hidden" name="target" value="newsletter_sender">
	<input type="hidden" name="data[action]" value="send_to_me">
	<input type="hidden" name="data[task]" value="<?=$data['task']?>">
	<input type="hidden" name="data[redirect]" value="nid=<?=$newsletter->id?>&task=<?=$data['task']?>">
	<input type="hidden" name="data[send][newsletter][id]" value="<?=$newsletter->id?>">
	<?=gettext('stuur test versie naar jezelf')?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('naam')?>:</td>
	<td><input type="text" name="data[send][name]" value="<?=(!empty($settings['last_name']) ? $settings['last_name'] : '')?>"></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('e-mailadres')?>:</td>
	<td><input type="text" name="data[send][email]" value="<?=(!empty($settings['last_email']) ? $settings['last_email'] : '')?>"></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="<?=_('verstuur')?>"></form></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</form>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
	<form action="newsletter_write.php?task=mailing&nid=<?=$newsletter->id?>" method="POST" enctype="multipart/form-data" name="sendForm" id="sendForm">
	<input type="hidden" name="target" value="newsletter_sender">
	<input type="hidden" name="data[action]" value="prepare_mailing">
	<input type="hidden" name="data[task]" value="<?=$data['task']?>">
	<input type="hidden" name="data[redirect]" value="nid=<?=$newsletter->id?>&task=<?=$data['task']?>">
	<input type="hidden" name="data[send][newsletter][id]" value="<?=$newsletter->id?>">
	<?=gettext('Begin mailing naar geselecteerde groepen.')?>
	<?PHP
	if( isset($data['sending_error']) && $data['sending_error'] == true )
	{
		?><p class="error"><img src="design/img/error.gif" border="0" alt="error" style="float: left; padding: 5px;"><?=gettext('U heeft nog geen geadresseerde groepen geselecteerd, aan de verzending kan nog niet worden begonnen');?></p>
		<?PHP
	}
	?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><input type="submit" value="<?=gettext('begin mailing')?>"></form></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>

<form action="newsletter_write.php" method="POST" enctype="multipart/form-data" name="theForm" id="theForm">
<input type="hidden" name="target" value="newsletter">
<input type="hidden" name="data[action]" value="ignore">
<input type="hidden" name="data[task]" id="task" value="<?=$data['task']?>">
<input type="hidden" name="data[newsletter][id]" value="<?=$newsletter->id?>">
</form>
