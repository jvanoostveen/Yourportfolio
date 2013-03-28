<script type="text/javascript">
active_view = 'content';
</script>
<script type="text/javascript">
<!--
	function insert_text(open, close)
	{
		msgfield = (document.all) ? document.all.content : document.forms['theForm']['content'];

		// IE support
		if (document.selection && document.selection.createRange)
		{
			msgfield.focus();
			sel = document.selection.createRange();
			sel.text = open + sel.text + close;
			msgfield.focus();
		}

		// Moz support
		else if (msgfield.selectionStart || msgfield.selectionStart == '0')
		{
			var startPos = msgfield.selectionStart;
			var endPos = msgfield.selectionEnd;

			msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
			msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
			msgfield.focus();
		}

		// Fallback support for other browsers
		else
		{
			msgfield.value += open + close;
			msgfield.focus();
		}

		return;
	}
-->
</script>

<?PHP
$newsletter = $data['newsletter'];
$item = $data['newsletter_item'];
?>

<form action="newsletter_write.php" method="POST" enctype="multipart/form-data" name="theForm" id="theForm">
<input type="hidden" name="target" value="newsletter_item">
<input type="hidden" name="data[action]" value="save">
<input type="hidden" name="data[task]" id="task" value="<?=$data['task']?>">
<input type="hidden" name="data[item][id]" value="<?=$item->id?>">
<input type="hidden" name="data[item][newsletter_id]" value="<?=$newsletter->id?>">

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
	<td><? if (!empty($item->id)) : ?><a href="newsletter_write.php?nid=<?=$newsletter->id?>&task=content"  class="default fg_black txt_medium"><?=gettext('nieuw item')?></a><? endif; ?></td>
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
	<td nowrap align="right"><?=gettext('titel')?>:</td>
	<td colspan="2">
	<input type="text" name="data[item][title]" id="title" value="<?=$canvas->edit_filter($item->title)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('tekst')?>:
	</td>
	<td colspan="2">
	<textarea name="data[item][content]" id="content" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($item->content)?></textarea>
<!-- text manipulation tools -->
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="60"><img src="<?=IMAGES?>spacer.gif" width="60" height="1"></td>
		<td width="60"><img src="<?=IMAGES?>spacer.gif" width="60" height="1"></td>
		<td width="90"><img src="<?=IMAGES?>spacer.gif" width="90" height="1"></td>
		<td width="90"><img src="<?=IMAGES?>spacer.gif" width="90" height="1"></td>
		<td><img src="<?=IMAGES?>spacer.gif" width="1" height="1"></td>
	</tr>
	<tr>
<? $text_tool = 'content'; ?>
		<td><a href="javascript:insert_text('[b]','[/b]');" class="normal"><img src="<?=IMAGES?>btn_bold.gif" alt="vetgedrukt" title="vetgedrukt" width="15" height="15" border="0" align="absmiddle"> <?=gettext('bold')?></a></td>
		<td><a href="javascript:insert_text('[i]','[/i]');" class="normal"><img src="<?=IMAGES?>btn_italic.gif" alt="schuin" title="schuin" width="15" height="15" border="0" align="absmiddle"> <?=gettext('italic')?></a></td>
		<td><a href="javascript:externalLink('<?=$text_tool?>','email');" class="normal"><img src="<?=IMAGES?>btn_link_extern.gif" alt="e-mail link" title="e-mail link" width="15" height="15" border="0" align="absmiddle"> <?=gettext('e-mail link')?></a></td>
		<td><a href="javascript:externalLink('<?=$text_tool?>','elink');" class="normal"><img src="<?=IMAGES?>btn_link_extern.gif" alt="externe link" title="e-mail link" width="15" height="15" border="0" align="absmiddle"> <?=gettext('externe link')?></a></td>
		<td width="2000">&nbsp;</td>
	</tr>
	</table>
<!-- text manipulation tools -->
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('link')?>:</td>
	<td colspan="2">
	<input type="text" name="data[item][link]" id="link" value="<?=$canvas->edit_filter($item->link)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('image')?>:</td>
	<td colspan="2">
<? if ($item->image->isEmpty()) : /* item has no image */ ?>
	<input type="file" id="image" name="files[image]" size="20">
	<span class="fg_grey txt_mediumsmall">(<?=sprintf(gettext('maximale afmetingen: %s pixels bij %s pixels'), $data['newsletter_item_width'], $data['newsletter_item_height'])?>)</span>
<? else : ?>
	<?=$item->image->name?> <a href="javascript:deleteItemFile(<?=$item->id?>,<?=$item->image->id?>);"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"></a>
<? endif; /* end item image */ ?>
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
	<td>&nbsp;</td>
	<td>
	<input type="submit" value="<?=(empty($item->id) ? _('voeg toe') : _('wijzig'))?>">
	<? if (empty($item->id)) : ?>
	<input type="reset" value="reset">
	<? endif; ?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</form>
<tr>
	<td width="120" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td width="100">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><?=gettext('Huidige items')?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? $textToolkit = $system->getModule('TextToolkit'); ?>
<? foreach ($newsletter->items as $list_item) : ?>
<tr>
	<td align="right">
<? if (!$list_item->image->isEmpty()) : /* show image */ ?>
	<img src="<?=$list_item->image->path.$list_item->image->sysname.'?v='.rand()?>" width="80">
<? endif; /* end show image */ ?>
	&nbsp;</td>
	<td colspan="2" valign="top">
	<b><?=$list_item->title?></b><br>
	<?=$textToolkit->parseText($list_item->content)?>
	</td>
	<td>
	<a href="newsletter_write.php?nid=<?=$newsletter->id?>&iid=<?=$list_item->id?>&task=content"><img src="<?=IMAGES?>btn_edit.gif" width="17" height="20" border="0"></a><br>
	<a href="javascript:deleteItem(<?=$list_item->id?>);"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0"></a><br>
<? if ($list_item != $newsletter->items[0]) : /* not first item */ ?>
	<a href="javascript:moveItem(<?=$list_item->id?>,-1);"><img src="<?=IMAGES?>btn_move_up_black.gif" width="9" height="7" border="0" style="padding: 2px 0px 2px 0px;"></a><br>
<? else : ?>
	<img src="<?=IMAGES?>btn_move_up_gray.gif" width="9" height="7" border="0" style="padding: 2px 0px 2px 0px;"><br>
<? endif; /* end not first item */ ?>
<? if ($list_item != end($newsletter->items)) : /* not last item */ ?>
	<a href="javascript:moveItem(<?=$list_item->id?>,1);"><img src="<?=IMAGES?>btn_move_down_black.gif" width="9" height="7" border="0"></a><br>
<? else : ?>
	<img src="<?=IMAGES?>btn_move_down_gray.gif" width="9" height="7" border="0"><br>
<? endif; /* end not last item */ ?>
	</td>
</tr>
<? endforeach; ?>
</table>

<form action="newsletter_write.php" method="post" enctype="application/x-www-form-urlencoded" name="moveForm" id="moveForm">
<input type="hidden" name="target" value="newsletter_item">
<input type="hidden" name="data[action]" value="move">
<input type="hidden" name="data[redirect]" value="nid=<?=$newsletter->id?>&task=content">
<input type="hidden" name="data[id]" id="item_id" value="">
<input type="hidden" name="data[direction]" id="direction" value="">
</form>

<form action="newsletter_write.php" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm" id="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit item wilt verwijderen?')?>" id="message">
<input type="hidden" name="target" value="newsletter_item">
<input type="hidden" name="data[action]" value="delete">
<input type="hidden" name="data[redirect]" value="nid=<?=$newsletter->id?>&task=content">
<input type="hidden" name="data[id]" id="delete_id" value="">
</form>

<form action="newsletter_write.php" method="post" enctype="application/x-www-form-urlencoded" name="fileDeleteForm" id="fileDeleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit bestand wilt verwijderen?')?>" id="message">
<input type="hidden" name="target" value="newsletter_item">
<input type="hidden" name="data[action]" value="delete_file">
<input type="hidden" name="data[redirect]" value="nid=<?=$newsletter->id?>&iid=<?=$item->id?>&task=content">
<input type="hidden" name="data[id]" id="filedelete_item_id" value="">
<input type="hidden" name="data[file_id]" id="filedelete_file_id" value="">
</form>
