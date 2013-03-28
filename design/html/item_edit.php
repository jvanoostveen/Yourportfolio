<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * item edit template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
?>
<?=$canvas->filter($yourportfolio->feedback)?>
<form action="<?=$system->file?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="item">

<input type="hidden" name="itemForm[action]" value="item_save">

<input type="hidden" name="itemForm[item][id]" value="<?=$item->id?>">
<? if (empty($yourportfolio->labels['item_type'])) : /* labels for item_type empty */ ?>
<input type="hidden" name="itemForm[item][label_type]" value="<?=$item->label_type?>">
<? endif; /* end labels item_type */ ?>

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td width="75">&nbsp;</td>
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
	<input type="hidden" name="itemForm[item][online]" value="N">
	<input type="checkbox" name="itemForm[item][online]" id="online" value="Y"<?=($item->online == 'Y') ? ' checked' :''?> accesskey="o"><label for="online"> <?=gettext('zichtbaar')?></label>
	</td>
	<td align="right">
	<? if ($item->id > 0) : /* existing image */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium" accesskey="d"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> <?=gettext('verwijder item')?></a><? endif; /* end delete image link */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<? if ($yourportfolio->settings['text_nodes']) : ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="itemForm[item][text_node]" value="N">
	<input type="checkbox" name="itemForm[item][text_node]" id="text_node" value="Y"<?=($item->text_node == 'Y') ? ' checked' :''?> accesskey="t"><label for="text_node"> <?=gettext('tekst object')?></label>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<?php include('item_metadata.php'); ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('sectie')?>:</td>
	<td valign="top">
	
	<input type="hidden" name="itemForm[item][old_album_id]" value="<?=$album->id?>">
	<input type="hidden" name="itemForm[item][old_section_id]" value="<?=$section->id?>">
	
	<select id="album_section" name="itemForm[item][album_id__section_id]">
<? if (!empty($yourportfolio->albums)) : /* has albums to loop */ ?>
<? foreach($yourportfolio->albums as $s_album) : /* loop albums */ ?>
<? if (empty($s_album->name))
{
	if (!empty($s_album->strings['name']))
	{
		$first_language = array_shift(array_keys($s_album->strings['name']));
		$s_album->name = $s_album->strings['name'][$first_language]['string_parsed'];
	} else {
		$s_album->name = _('geen naam');
	}
}
?>
<? if ($album->template == 'album') : /* current album is of type album */ ?>
<? if ($s_album->template == 'album') : /* make sure it's an album */ ?>
	<optgroup label="<?=$canvas->filter($s_album->name)?><?=($s_album->online != 'Y') ? ' '._('(niet zichtbaar)') : '' ?>">
<? if (!empty($s_album->sections)) : /* has sections */ ?>
<? foreach($s_album->sections as $s_section) : /* loop sections */ ?>
<? if (empty($s_section['name']))
{
	$tmp_section = new Section();
	$tmp_section->id = $s_section['id'];
	$tmp_section->load();
	
	if (!empty($tmp_section->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_section->strings['name']));
		$s_section['name'] = $tmp_section->strings['name'][$first_language]['string_parsed'];
	} else {
		$s_section['name'] = _('geen naam');
	}
}
?>
<? if ($s_section['text_node'] == 'Y') : continue; /* section cannot be a text section */ endif; ?>
		<option value="<?=$s_album->id.'__'.$s_section['id']?>" <?=($s_album->id == $album->id && $s_section['id'] == $section->id) ? 'selected' : ''?>><?=$canvas->filter($s_section['name'])?><?=($s_section['online'] != 'Y') ? ' '._('(niet zichtbaar)') : ''?></option>
<? endforeach; /* end loop sections */ ?>
<? endif; /* end has sections */ ?>
	</optgroup>
<? endif; /* end is album */ ?>
<? endif; /* end current album is album */ ?>
<? endforeach; /* end loop albums */ ?>
<? endif; /* end has albums */ ?>
<? if (!empty($yourportfolio->restricted_albums)) : /* has albums to loop */ ?>
<? foreach($yourportfolio->restricted_albums as $s_album) : /* loop albums */ ?>
<? if ($album->template == 'album') : /* current album is of type album */ ?>
<? if ($s_album->template == 'album') : /* make sure it's an album */ ?>
	<optgroup label="<?=$canvas->filter($s_album->name)?><?=($s_album->online != 'Y') ? ' '._('(niet zichtbaar)') : '' ?>">
<? if (!empty($s_album->sections)) : /* has sections */ ?>
<? foreach($s_album->sections as $s_section) : /* loop sections */ ?>
<? if (empty($s_section['name']))
{
	$tmp_section = new Section();
	$tmp_section->id = $s_section['id'];
	$tmp_section->load();
	
	if (!empty($tmp_section->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_section->strings['name']));
		$s_section['name'] = $tmp_section->strings['name'][$first_language]['string_parsed'];
	} else {
		$s_section['name'] = _('geen naam');
	}
}
?>
		<option value="<?=$s_album->id.'__'.$s_section['id']?>" <?=($s_album->id == $album->id && $s_section['id'] == $section->id) ? 'selected' : ''?>><?=$canvas->filter($s_section['name'])?><?=($s_section['online'] != 'Y') ? ' '._('(niet zichtbaar)') : ''?></option>
<? endforeach; /* end loop sections */ ?>
<? endif; /* end has sections */ ?>
	</optgroup>
<? endif; /* end is album */ ?>
<? endif; /* end current album is album */ ?>
<? endforeach; /* end loop albums */ ?>
<? endif; /* end has albums */ ?>
	</select>
	<? if ($item->id > 0) : /* existing item */ ?>&nbsp;&nbsp;<a href="javascript:copyItem();" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_duplicate.gif" width="16" height="16" border="0" align="absmiddle"> <?=gettext('kopieer item')?></a><? endif; /* end is existing item */ ?>
	<input type="hidden" name="itemForm[item][new_section]" value="">
	</td>
	<td align="right" valign="top">
	<?=gettext('positie')?>: 
	<input type="hidden" name="itemForm[item][old_position]" id="old_position" value="<?=$item->position?>">
	<input type="text" name="itemForm[item][position]" id="position" value="<?=$item->position?>" size="3">
	</td>
	<td>&nbsp;</td>
</tr>
<? if (!empty($yourportfolio->labels['item_type'])) : /* labels for item_type not empty */ ?>
<tr>
	<td nowrap align="right"><?=gettext('type')?>:</td>
	<td valign="top">
<? if ( $yourportfolio->settings['can_edit_types'] ) : /* can edit types */ ?>
<? if ( !empty($yourportfolio->labels['item_type']) ) : /* has item type labels */ ?>
	<select id="type" name="itemForm[item][label_type]">
<? foreach ( $yourportfolio->labels['item_type'] as $value => $label ) : /* start item type label loop */ ?>
		<option value="<?=$value?>" <?=($value == $item->label_type) ? 'selected' : '';?>><?=$canvas->filter($label)?></option>
<? endforeach; /* end item type label loop */ ?>
	</select>
<? else : /* no item type labels defined */ ?>
	<input type="hidden" name="itemForm[item][label_type]" value="<?=$item->label_type?>">
<? endif; /* end has item type labels */ ?>
<? else : /* can't edit types */ ?>
	<input type="hidden" name="itemForm[item][label_type]" value="<?=$item->label_type?>">
<? endif; /* end can edit types */ ?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* labels for item_type not empty */ ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? if (YP_MULTILINGUAL) : /* show multilingual edit */ ?>
<? require(HTML.'item_multilanguage_edit.php'); ?>
<? else : /* show normal edit */ ?>
<tr>
	<td nowrap align="right"><?=gettext('titel')?>:</td>
	<td colspan="2">
	<input type="text" name="itemForm[item][name]" id="title" value="<?=$canvas->edit_filter($item->name)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<? if ($yourportfolio->settings['items_have_subname']) : /* design supports a subtitle */ ?>
<tr>
	<td nowrap align="right"><?=gettext('subtitel')?>:</td>
	<td colspan="2">
	<input type="text" name="itemForm[item][subname]" id="subtitle" value="<?=$canvas->edit_filter($item->subname)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end subtitle */ ?>
<? if ($yourportfolio->settings['has_custom_fields']) : /* items have a custom setup */ ?>
<? $yourportfolio->parseCustomFields(); ?>
<? foreach($yourportfolio->custom_fields as $custom_field) : /* loop thru custom fields */ ?>
<?
if (isset($custom_field['owner']) && $custom_field['owner'] != 'item')
	continue;

if (isset($custom_field['type']) && $custom_field['type'] != $item->album_id)
	continue;
?>
<tr>
	<td nowrap align="right"><?=$canvas->filter($custom_field['label'])?>:</td>
	<td colspan="2">
	<input type="text" name="itemForm[item][custom_data][<?=$custom_field['key']?>]" id="<?=$custom_field['key']?>" value="<?=$canvas->edit_filter($item->getCustomData($custom_field['key']))?>" size="<?=$custom_field['length']?>">
	</td>
	<td>&nbsp;</td>
</tr>
<? endforeach; /* end loop thru custom fields */ ?>
<? endif; /* end custom fields */ ?>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('beschrijving')?>:
	</td>
	<td colspan="2">
	<textarea name="itemForm[item][text_original]" id="text" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($item->text_original)?></textarea>
<?
# <!-- text manipulation tools -->
$text_tool = 'text';
require(HTML.'text_tools.php');
# <!-- text manipulation tools -->
?>
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

<table>
<? endif; /* end show normal / multilingual edit */ ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>

<table width="98%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td width="100"><img src="<?=IMAGES?>spacer.gif" width="100" height="1"></td>
	<td width="20"><img src="<?=IMAGES?>spacer.gif" width="15" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
	<td width="50"><img src="<?=IMAGES?>spacer.gif" width="50" height="1"></td>
	<td width="120"><img src="<?=IMAGES?>spacer.gif" width="120" height="1"></td>
	<td width="18"><img src="<?=IMAGES?>spacer.gif" width="18" height="1"></td>
	<td><img src="<?=IMAGES?>spacer.gif" width="1" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
</tr>
<tr>
	<td width="100" align="right" class="txt_small fg_darkgrey upload_text"><?=gettext('zichtbaar')?>:</td>
	<td width="15">&nbsp;</td>
	<td width="75" class="txt_small fg_darkgrey upload_text"><?=gettext('media')?>:</td>
	<td width="50" class="txt_small fg_darkgrey upload_text"><?=gettext('formaat')?>:</td>
	<td width="120" class="txt_small fg_darkgrey upload_text"><?=gettext('bestand')?>:</td>
	<td width="18" class="fg_darkgrey"></td>
	<td class="txt_small fg_darkgrey upload_text"><?=gettext('instructies')?>:</td>
	<td width="75">&nbsp;</td>
</tr>
</table>
<? foreach($item->files_settings as $file_id => $settings) : /* loop through file settings */ ?>
<? if (isset($settings['hidden']) && $settings['hidden'] == true) : continue; endif; /* skip these file upload when settings indicate it is hidden */ ?>
<? $file = $item->getFile($file_id); ?>
<input type="hidden" name="allowed_extensions_<?=$file_id?>" id="allowed_extensions_<?=$file_id?>" value="<?=$settings['extension']?>">
<input type="hidden" name="itemForm[item][files_properties][<?=$file_id?>][online_old]" value="<?=$file->online?>">
<input type="hidden" name="itemForm[item][files_properties][<?=$file_id?>][online]" value="<?=$file->online?>" id="<?=$file_id?>-online">
<div id="<?=$file_id?>-summary">
<table width="98%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td width="100"><img src="<?=IMAGES?>spacer.gif" width="100" height="1"></td>
	<td width="15"><img src="<?=IMAGES?>spacer.gif" width="15" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
	<td width="50"><img src="<?=IMAGES?>spacer.gif" width="50" height="1"></td>
	<td width="120"><img src="<?=IMAGES?>spacer.gif" width="120" height="1"></td>
	<td><img src="<?=IMAGES?>spacer.gif" width="25" height="1"></td>
	<td width="25"><img src="<?=IMAGES?>spacer.gif" width="25" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
</tr>
<tr>
	<td width="100" align="right" valign="top" class="upload_text"><input type="checkbox" <?=($file->online == 'Y') ? 'checked' : ''?> <?=($settings['required'] == true) ? 'disabled' : ''?> onchange="changeFileStatus(this.checked,'<?=$file_id?>','online');" id="<?=$file_id?>-online-div1"></td>
	<td width="15" valign="top" class="bg_lightgrey upload_arrow" width="10" align="center"><a href="javascript:uploadCollapse('<?=$file_id?>',true);"><img src="<?=IMAGES?>btn_arrow_right.gif" width="8" height="8" border="0"></a></td>
	<td width="75" valign="top" class="bg_lightgrey bold upload_text"><a href="javascript:uploadCollapse('<?=$file_id?>',true);" class="default fg_black"><?=$settings['media']?></a></td>
	<td width="50" valign="top" class="bg_lightgrey upload_text extension_listing"><?=$canvas->extensionListing($settings['extension'])?></td>
	<td class="bg_lightgrey upload_text" colspan="2">
<? if ($file->id == 0) : /* no file yet */ ?>
	<input type="file" id="<?=$file_id?>" name="itemFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
<? else : /* file is present */ ?>
	<a href="download.php?fid=<?=$file->id?>&obj=item" class="fg_black default"><?=$canvas->filter($file->name)?></a>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'item',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? if ($settings['type'] == 'audio') : /* add play btn when music */ ?>
	<a href="javascript:playMusic(<?=$file->id?>,'item');"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end file or not */ ?>
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'item',<?=$item->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>" align="absmiddle" style="padding-left: 5px;"></a>
<? endif; ?>
	</td>
	<td class="bg_lightgrey upload_text" align="right">&nbsp;<? if (!empty($file->id)) : ?><a href="javascript:deleteFile(<?=$file->id?>);" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"></a> <? endif; ?></td>
	<td width="75">&nbsp;</td>
</tr>
</table>

</div>
<div id="<?=$file_id?>-detail" class="hidden">
<table width="98%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td width="100"><img src="<?=IMAGES?>spacer.gif" width="100" height="1"></td>
	<td width="15"><img src="<?=IMAGES?>spacer.gif" width="15" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
	<td width="50"><img src="<?=IMAGES?>spacer.gif" width="50" height="1"></td>
	<td width="122"><img src="<?=IMAGES?>spacer.gif" width="122" height="1"></td>
	<td width="18"><img src="<?=IMAGES?>spacer.gif" width="18" height="1"></td>
	<td colspan="2"><img src="<?=IMAGES?>spacer.gif" width="25" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
</tr>
<tr>
	<td width="100" align="right" valign="top" class="upload_text"><input type="checkbox" <?=($file->online == 'Y') ? 'checked' : ''?> <?=($settings['required'] == true) ? 'disabled' : ''?> onchange="changeFileStatus(this.checked,'<?=$file_id?>','online');" id="<?=$file_id?>-online-div2"></td>
	<td width="15" valign="top" class="bg_lightgrey upload_arrow" align="center"><a href="javascript:uploadCollapse('<?=$file_id?>',false);"><img src="<?=IMAGES?>btn_arrow_down.gif" width="8" height="8" border="0"></a></td>
	<td width="75" valign="top" class="bg_lightgrey bold upload_text"><a href="javascript:uploadCollapse('<?=$file_id?>',false);" class="default fg_black"><?=$settings['media']?></a></td>
	<td width="50" valign="top" class="bg_lightgrey upload_text extension_listing"><?=$canvas->extensionListing($settings['extension'])?></td>
<? if (!empty($file->id)) : /* is not an empty entry */ ?>
<? if ($settings['type'] == 'image') : /* image files can be displayed in the browser */ ?>
	<td width="122" class="bg_lightgrey" rowspan="2">
<?
if (!file_exists(CACHE_UPLOAD_DIR.$file->sysname))
	$file->buildCacheFile($item->files_settings[$file_id]['naming']);
?>
	<a href="download.php?fid=<?=$file->id?>&obj=item"><img src="<?=CACHE_UPLOAD_DIR.$file->sysname?>" border="0"></a>
<? else : /* is not an image */ ?>
	
	<td width="122" class="bg_lightgrey upload_text" rowspan="2" valign="top">
	<a href="download.php?fid=<?=$file->id?>&obj=item" class="fg_black default"><?=$canvas->filter($file->name, 15)?></a>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'item',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? if ($settings['type'] == 'audio') : /* add play btn when music */ ?>
	<a href="javascript:playMusic(<?=$file->id?>,'item');"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end preview */ ?>
<? else : /* is empty entry */ ?>
	<td width="122" class="bg_lightgrey" rowspan="2">&nbsp;
<? endif; /* end not empty entry */ ?>
	</td>
	<td width="18" class="bg_lightgrey" rowspan="2" valign="top" style="padding-top: 3px;">
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'item',<?=$item->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>"></a>
<? endif; ?>
	</td>
	<td class="bg_lightgrey txt_small upload_text" colspan="2"><?=$canvas->filter($settings['description'])?></td>
	<td width="75">&nbsp;</td>
</tr>
<tr>
	<td align="right" valign="top" class="upload_text">&nbsp;</td>
	<td class="bg_lightgrey">&nbsp;</td>
	<td class="bg_lightgrey fg_darkgrey txt_small upload_text" colspan="2" valign="bottom">
<? if (!$file->isEmpty()) : ?>
<? if ($file->width > 0) : /* display current sizes */ ?>
	<?=$file->width?> x <?=$file->height?> px<br>
<? endif; /* end display sizes */ ?>
	<?=$canvas->formatFilesize($file->size)?><br>
	<?=strftime("%H:%M %d-%m-%Y", strtotime($file->created))?>
<? endif; ?>
	</td>
	<td class="bg_lightgrey upload_text" valign="bottom">
<? if (!empty($file->id)) : /* is not an empty entry */ ?>
	<input type="file" id="<?=$file_id?>" name="itemFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
<? endif; /* end is not an empty entry */ ?>
	</td>
	<td class="bg_lightgrey upload_text" align="right" valign="bottom">&nbsp;<? if (!empty($file->id)) : ?><a href="javascript:deleteFile(<?=$file->id?>);" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"></a> <? endif; ?></td>
	<td width="75">&nbsp;</td>
</tr>
</table>
</div>
<? endforeach; /* end loop through file settings */ ?>

<?php
if ($yourportfolio->settings['tags'])
	include('item_tags.php');
?>

</form>

<form action="section.php?aid=<?=$album->id?>&sid=<?=$section->id?>" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit item wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="item_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$item->id?>" id="deleteID">
</form>

<form action="item.php?aid=<?=$album->id?>&sid=<?=$section->id?>&iid=<?=$item->id?>" method="post" enctype="application/x-www-form-urlencoded" name="deleteFileForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit bestand wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="item_file_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$item->id?>">
<input type="hidden" name="deleteForm[delete][file_id]" value="" id="file_id">
</form>

<form action="item.php" method="post" enctype="application/x-www-form-urlencoded" name="copyForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="copy">
<input type="hidden" name="copyForm[action]" value="item_copy">
<input type="hidden" name="copyForm[copy][id]" value="<?=$item->id?>">
<input type="hidden" name="copyForm[copy][album_section]" value="" id="copy_album_section">
</form>
