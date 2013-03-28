<?PHP
require_once(CODE.'classes/GuestbookMessage.php');
$message = new GuestbookMessage();
$message->id = $message_id;
$message->load();
?>
<?=gettext('(Gastenboek is nog in ontwikkeling)')?><br>
<form action="album.php?aid=<?=$album->id?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="guestbook">
<input type="hidden" name="guestbookForm[action]" value="guestbook_message_save">

<input type="hidden" name="guestbookForm[guestbook][album_id]" value="<?=$album->id?>">
<input type="hidden" name="guestbookForm[guestbook][id]" value="<?=$message->id?>">

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td width="100">&nbsp;</td>
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
	<input type="hidden" name="guestbookForm[guestbook][online]" value="N">
	<input type="checkbox" name="guestbookForm[guestbook][online]" id="online" value="Y"<?=($message->online == 'Y') ? ' checked' :''?> accesskey="o"><label for="online"> <?=gettext('zichtbaar')?></label>
	</td>
	<td align="right">
	<? if ($message->id > 0) : /* existing message */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium" accesskey="d"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> <?=gettext('verwijder bericht')?></a><? endif; /* end delete link */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('datum')?>:</td>
	<td valign="top"><?=$canvas->readableDate($message->created)?></td>
	<td align="right" valign="top">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('naam')?>:</td>
	<td colspan="2">
	<input type="text" name="guestbookForm[guestbook][name]" id="name" value="<?=$canvas->edit_filter($message->name)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('e-mail')?>:</td>
	<td colspan="2">
	<input type="text" name="guestbookForm[guestbook][email]" id="email" value="<?=$canvas->edit_filter($message->email)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('bericht')?>:
	</td>
	<td colspan="2">
	<textarea name="guestbookForm[guestbook][message]" id="message" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($message->message)?></textarea>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>

<form action="album.php?aid=<?=$album->id?>" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit bericht wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="guestbook_message_delete">
<input type="hidden" name="deleteForm[delete][album_id]" value="<?=$album->id?>">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$message->id?>" id="deleteID">
</form>
