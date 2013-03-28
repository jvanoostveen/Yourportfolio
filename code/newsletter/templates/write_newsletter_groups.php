<script type="text/javascript">
active_view = 'groups';
</script>

<?PHP
$newsletter = $data['newsletter'];
?>

<form action="newsletter_write.php" method="POST" enctype="multipart/form-data" name="theForm" id="theForm">
<input type="hidden" name="target" value="newsletter">
<input type="hidden" name="data[action]" value="save_groups">
<input type="hidden" name="data[task]" id="task" value="<?=$data['task']?>">
<input type="hidden" name="data[newsletter][id]" value="<?=$newsletter->id?>">
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
	<td><?=gettext('Groepen waarnaar deze nieuwsbrief gestuurd moet worden')?>:</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? foreach ($data['groups'] as $group) : ?>
<tr>
	<td>&nbsp;</td>
	<td><input type="checkbox" name="data[newsletter][groups][]" value="<?=$group['id']?>" <?=(in_array($group['id'], $newsletter->groups) ? 'checked' : '')?> id="group_<?=$group['id']?>"> <label for="group_<?=$group['id']?>"><?=$group['name']?></label></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endforeach; ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>

</form>