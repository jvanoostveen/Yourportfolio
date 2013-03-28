<script type="text/javascript">
active_view = 'preview';
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
	<td><a href="newsletter_view.php?nid=<?=$newsletter->id?>" target="_blank" class="default fg_black txt_medium"><?=gettext('bekijk nieuwsbrief in een nieuw venster')?></a></td>
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

<iframe src="newsletter_view.php?nid=<?=$newsletter->id?>" width="650" height="700" id="preview_frame" style="margin-left: 75px;"></iframe>