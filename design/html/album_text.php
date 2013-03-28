<?=$canvas->filter($yourportfolio->feedback)?>
<form action="<?=$system->thisFile()?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="album">

<input type="hidden" name="albumForm[action]" value="album_save">

<input type="hidden" name="albumForm[album][id]" value="<?=$album->id?>">

<input type="hidden" name="albumForm[album][template]" value="<?=$album->template?>">
<input type="hidden" name="albumForm[album][type]" value="<?=$album->type?>">
<input type="hidden" name="albumForm[album][online]" value="<?=$album->online?>">
<input type="hidden" name="albumForm[album][old_position]" value="<?=$album->position?>">
<input type="hidden" name="albumForm[album][position]" value="<?=$album->position?>">
<input type="hidden" name="albumForm[album][locked]" value="<?=$album->locked?>">
<input type="hidden" name="albumForm[album][restricted]" value="<?=$album->restricted?>">
<input type="hidden" name="albumForm[album][user_id]" value="<?=$album->user_id?>">

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td width="100">&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? if (YP_MULTILINGUAL) : /* show multilingual edit */ ?>
<? $textarea_rows = 20; ?>
<? require(HTML.'album_multilanguage_edit.php'); ?>
<? else : /* show normal edit */ ?>
<tr>
	<td nowrap align="right"><?=gettext('titel')?>:</td>
	<td>
	<input type="text" name="albumForm[album][name]" id="title" value="<?=$canvas->edit_filter($album->name)?>" size="40">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top"><?=gettext('beschrijving')?>:</td>
	<td>
	<textarea name="albumForm[album][text_original]" id="text" rows="20" cols="60" class="fullsize"><?=$canvas->edit_filter($album->text_original)?></textarea>
<?
# <!-- text manipulation tools -->
$text_tool = 'text';
require(HTML.'text_tools.php');
# <!-- text manipulation tools -->
?>
	</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end multilingual / normal edit */ ?>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>
