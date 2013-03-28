<?PHP
require_once(CODE.'classes/Guestbook.php');
$guestbook = new Guestbook($album->id);

$guestbook->loadMessages();
?>
<?=gettext('(Gastenboek is nog in ontwikkeling)')?><br>
<form action="album.php?aid=<?=$album->id?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="guestbook">
<input type="hidden" name="guestbookForm[action]" value="guestbook_quick_save">

<input type="hidden" name="guestbookForm[guestbook][album_id]" value="<?=$album->id?>">

<br>
<table border="0">
<tr>
	<td class='bold'><?=gettext('zichtbaar')?></td>
	<td class='bold'><?=gettext('naam')?></td>
	<td width='10'><img src="<?=IMAGES?>spacer.gif" width='10' height='1'></td>
	<td class='bold'><?=gettext('bericht')?></td>
</tr>
<? foreach($guestbook->messages as $message_data) : ?>
<? $message = new GuestbookMessage($message_data); ?>
<tr>
	<td align='center' valign='top'><input type="checkbox" name="guestbookForm[guestbook][online][]" value="<?=$message->id?>" <?=($message->online == 'Y' ? 'checked' : '')?>></td>
	<td valign='top'><a href="album.php?aid=<?=$album->id?>&mid=<?=$message->id?>" class="fg_black default"><?=$canvas->filter($message->name)?></a></td>
	<td>&nbsp;</td>
	<td valign='top'><?=$canvas->filter($message->message)?></td>
</tr>
<? endforeach; ?>
</table>
</form>