<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * section edit template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
 ?>
<?=$canvas->filter($yourportfolio->feedback)?>
<form action="<?=$system->thisFile()?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="section">

<input type="hidden" name="sectionForm[action]" value="section_save">

<input type="hidden" name="sectionForm[section][id]" value="<?=$section->id?>" id="theId">
<input type="hidden" name="sectionForm[section][old_album_id]" value="<?=$album->id?>">
<input type="hidden" name="sectionForm[section][album_id]" value="<?=$album->id?>">
<input type="hidden" name="sectionForm[section][template]" value="<?=(!is_null($section->template)) ? $section->template : $yourportfolio->templates[$album->template]['section']?>">
<? if (!$yourportfolio->preferences['section_selection']) : /* has no section selection (default settings) */ ?>
<input type="hidden" name="sectionForm[section][is_selection]" value="<?=$section->is_selection?>">
<? endif; /* end has section selection */ ?>
<? if (empty($yourportfolio->labels['section_type'])) : /* will not have type selector */ ?>
<input type="hidden" name="sectionForm[section][type]" value="<?=$section->type?>">
<? endif; /* end type selector */ ?>

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
	<input type="hidden" name="sectionForm[section][online]" value="N">
	<input type="checkbox" name="sectionForm[section][online]" id="online" value="Y"<?=($section->online == 'Y') ? ' checked' :''?> accesskey="o"><label for="online"> <?=gettext('zichtbaar')?></label>
	</td>
	<td align="right">
	<? if ($section->id > 0) : /* existing section */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium" accesskey="d"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> <?=gettext('verwijder sectie')?></a><? endif; /* end delete section link */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<?php if ($yourportfolio->settings['mobile'] || $yourportfolio->settings['tablet']) : ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="sectionForm[section][online_mobile]" value="N">
	<input type="checkbox" name="sectionForm[section][online_mobile]" id="online_mobile" value="Y"<?=($section->online_mobile == 'Y') ? ' checked' :''?>><label for="online_mobile"> <?=gettext('zichtbaar in mobiele site')?></label>
	</td>
	<td align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<?php endif; ?>
<? if ($yourportfolio->settings['text_nodes'] && $section->template != 'newsitem') : ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="sectionForm[section][text_node]" value="N">
	<input type="checkbox" name="sectionForm[section][text_node]" id="text_node" value="Y"<?=($section->text_node == 'Y') ? ' checked' :''?> accesskey="t"><label for="text_node"> <?=gettext('tekst sectie')?></label>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; ?>
<? if ($yourportfolio->preferences['section_selection']) : /* has any of these options */ ?>
<tr>
	<td>&nbsp;</td>
	<td>
<? if ($yourportfolio->preferences['section_selection'] && $section->template != 'newsitem') : /* has section selection */ ?>
	<input type="hidden" name="sectionForm[section][is_selection]" value="N">
	<input type="checkbox" name="sectionForm[section][is_selection]" id="is_selection" value="Y"<?=($section->is_selection == 'Y') ? ' checked' :''?>><label for="is_selection"> <?=$canvas->filter($yourportfolio->preferences['section_selection_name'])?></label>
<? else : /* alt section selection */ ?>
	<input type="hidden" name="sectionForm[section][is_selection]" value="<?=$section->is_selection?>">
<? endif; /* end has section selection */ ?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end show row with options */ ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<?php include('section_metadata.php'); ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="right"><?=gettext('album')?>:</td>
	<td>
	<input type="hidden" name="sectionForm[section][old_album_id]" value="<?=$album->id?>">
	<select id="album_id" name="sectionForm[section][album_id]">
<? foreach($yourportfolio->menu_albums as $l_album) : /* loop through available albums */ ?>
<? if (empty($l_album['name']))
{ 
	$tmp_album = new Album();
	$tmp_album->id = $l_album['id'];
	$tmp_album->load();
	
	if (!empty($tmp_album->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_album->strings['name']));
		$l_album['name'] = $tmp_album->strings['name'][$first_language]['string_parsed'];
	} else {
		$l_album['name'] = _('geen naam');
	}
}
?>
<? if ($album->template == 'album') : /* current album is of type album */ ?>
<? if ($l_album['template'] == 'album') : /* make sure it's an album */ ?>
		<option value="<?=$l_album['id']?>" <?=($l_album['id'] == $album->id) ? 'selected' : ''?>><?=$canvas->filter($l_album['name'])?><?=($l_album['online'] != 'Y') ? ' '._('(niet zichtbaar)') : '' ?></option>
<? endif; /* end is album */ ?>
<? endif; /* end current album is album */ ?>

<? if ($album->template == 'news') : /* current album is of type news */ ?>
<? if ($l_album['template'] == 'news') : /* make sure it's news */ ?>
		<option value="<?=$l_album['id']?>" <?=($l_album['id'] == $album->id) ? 'selected' : ''?>><?=$canvas->filter($l_album['name'])?><?=($l_album['online'] != 'Y') ? ' '._('(niet zichtbaar)') : '' ?></option>
<? endif; /* end is news */ ?>
<? endif; /* end current album is news */ ?>
<? endforeach; /* end loop albums */ ?>
<? if (!empty($yourportfolio->menu_restricted_albums)) : ?>
<? foreach($yourportfolio->menu_restricted_albums as $l_album) : /* loop through available albums */ ?>
<? if ($album->template == 'album') : /* current album is of type album */ ?>
<? if ($l_album['template'] == 'album') : /* make sure it's an album */ ?>
		<option value="<?=$l_album['id']?>" <?=($l_album['id'] == $album->id) ? 'selected' : ''?>><?=$canvas->filter($l_album['name'])?><?=($l_album['online'] != 'Y') ? ' '._('(niet zichtbaar)') : '' ?></option>
<? endif; /* end is album */ ?>
<? endif; /* end current album is album */ ?>

<? if ($album->template == 'news') : /* current album is of type news */ ?>
<? if ($l_album['template'] == 'news') : /* make sure it's news */ ?>
		<option value="<?=$l_album['id']?>" <?=($l_album['id'] == $album->id) ? 'selected' : ''?>><?=$canvas->filter($l_album['name'])?><?=($l_album['online'] != 'Y') ? ' '._('(niet zichtbaar)') : '' ?></option>
<? endif; /* end is news */ ?>
<? endif; /* end current album is news */ ?>
<? endforeach; /* end loop albums */ ?>
<? endif; ?>
	</select>
	</td>
	<td align="right">
	</td>
	<td>&nbsp;</td>
</tr>
<? if (!empty($yourportfolio->labels['section_type'])) : /* labels for section_type not empty */ ?>
<tr>
	<td nowrap align="right"><?=gettext('type')?>:</td>
	<td valign="top">
<? if ( $yourportfolio->settings['can_edit_types'] ) : /* can edit types */ ?>
<? if ( !empty($yourportfolio->labels['section_type']) ) : /* has item type labels */ ?>
	<select id="type" name="sectionForm[section][type]">
<? foreach ( $yourportfolio->labels['section_type'] as $value => $label ) : /* start section type label loop */ ?>
		<option value="<?=$value?>" <?=($value == $section->type) ? 'selected' : '';?>><?=$canvas->filter($label)?></option>
<? endforeach; /* end section type label loop */ ?>
	</select>
<? else : /* no section type labels defined */ ?>
	<input type="hidden" name="sectionForm[section][type]" value="<?=$section->type?>">
<? endif; /* end has section type labels */ ?>
<? else : /* can't edit types */ ?>
	<input type="hidden" name="sectionForm[section][type]" value="<?=$section->type?>">
<? endif; /* end can edit types */ ?>
	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* labels for section_type not empty */ ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top" style="padding-top: 7px;"><?=gettext('datum')?>:</td>
	<td valign="top">
	<input type="hidden" name="sectionForm[section][section_time]" value="<?=$canvas->unix2dutch($section->section_date, 'H:i:s')?>">
	<input type="text" name="sectionForm[section][section_date]" id="section_date" value="<?=$canvas->unix2dutch($section->section_date, 'd-m-Y')?>" size="12" onClick="scwShow(this,this);">
	</td>
	<td align="right" valign="top">
	<?=gettext('positie')?>: 
	<input type="hidden" name="sectionForm[section][old_position]" id="old_position" value="<?=$section->position?>">
	<input type="text" name="sectionForm[section][position]" id="position" value="<?=$section->position?>" size="3"><? if ($section->id == 0) : /* new section */ ?><br><span class="fg_grey txt_mediumsmall"><?=gettext('(niets invullen is achteraan)')?><? endif; /* end is new section */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? if (YP_MULTILINGUAL) : /* show multilingual edit */ ?>
<? require(HTML.'section_multilanguage_edit.php'); ?>
<? else : /* show normal edit */ ?>
<tr>
	<td nowrap align="right"><?=gettext('titel')?>:</td>
	<td colspan="2">
	<input type="text" name="sectionForm[section][name]" id="title" value="<?=$canvas->edit_filter($section->name)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<? if ($yourportfolio->settings['sections_have_subname']) : /* design supports a subtitle */ ?>
<tr>
	<td nowrap align="right"><?=gettext('subtitel')?>:</td>
	<td colspan="2">
	<input type="text" name="sectionForm[section][subname]" id="subtitle" value="<?=$canvas->edit_filter($section->subname)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end subtitle */ ?>
<? if ($yourportfolio->settings['has_custom_fields']) : /* items have a custom setup */ ?>
<? $yourportfolio->parseCustomFields(); ?>
<? foreach($yourportfolio->custom_fields as $custom_field) : /* loop thru custom fields */ ?>
<?
if (!isset($custom_field['owner']) || $custom_field['owner'] != 'section')
	continue;

if (isset($custom_field['type']) && $custom_field['type'] != $section->album->id)
	continue;
?>
<tr>
	<td nowrap align="right"><?=$canvas->filter($custom_field['label'])?>:</td>
	<td colspan="2">
	<input type="text" name="sectionForm[section][custom_data][<?=$custom_field['key']?>]" id="<?=$custom_field['key']?>" value="<?=$canvas->edit_filter($section->getCustomData($custom_field['key']))?>" size="<?=$custom_field['length']?>">
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
	<textarea name="sectionForm[section][text_original]" id="text" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($section->text_original)?></textarea>
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
	<td><img src="<?=IMAGES?>spacer.gif" width="1" height="15"/></td>
</tr>
</table>

<? if (!empty($section->files_settings)) : /* show file uploads */ ?>
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
<? foreach($section->files_settings as $file_id => $settings) : /* loop through file settings */ ?>
<? if (isset($settings['hidden']) && $settings['hidden'] == true) : continue; endif; /* skip these file upload when settings indicate it is hidden */ ?>
<? $file = $section->getFile($file_id); ?>
<input type="hidden" name="allowed_extensions_<?=$file_id?>" id="allowed_extensions_<?=$file_id?>" value="<?=$settings['extension']?>">
<input type="hidden" name="sectionForm[section][files_properties][<?=$file_id?>][online_old]" value="<?=$file->online?>">
<input type="hidden" name="sectionForm[section][files_properties][<?=$file_id?>][online]" value="<?=$file->online?>" id="<?=$file_id?>-online">
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
	<td colspan="2" class="bg_lightgrey upload_text">
<? if ($file->id == 0) : /* no file yet */ ?>
	<input type="file" id="<?=$file_id?>" name="sectionFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
<? else : /* file is present */ ?>
	<a href="download.php?fid=<?=$file->id?>&obj=section" class="fg_black default"><?=$canvas->filter($file->name)?></a>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'section',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end file or not */ ?>
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'section',<?=$section->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>" align="absmiddle" style="padding-left: 5px;"></a>
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
	<td width="125"><img src="<?=IMAGES?>spacer.gif" width="125" height="1"></td>
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
	<td width="125" class="bg_lightgrey" rowspan="2">
<?
if (!file_exists(CACHE_UPLOAD_DIR.$file->sysname))
	$file->buildCacheFile($section->files_settings[$file_id]['naming']);
?>
	<img src="<?=CACHE_UPLOAD_DIR.$file->sysname?>">
<? else : /* is not an image */ ?>
	<td width="125" class="bg_lightgrey upload_text" rowspan="2" valign="top">
	<?=$canvas->filter($file->name, 15)?>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'section',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end preview */ ?>
<? else : /* is empty entry */ ?>
	<td width="125" class="bg_lightgrey" rowspan="2">&nbsp;
<? endif; /* end not empty entry */ ?>
	</td>
	<td width="18" class="bg_lightgrey" rowspan="2" valign="top" style="padding-top: 3px;">
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'section',<?=$section->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>"></a>
<? endif; ?>
	</td>
	<td class="bg_lightgrey txt_small upload_text" colspan="2"><?=$settings['description']?></td>
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
	<input type="file" id="<?=$file_id?>" name="sectionFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
<? endif; /* end is not an empty entry */ ?>
	</td>
	<td class="bg_lightgrey upload_text" align="right" valign="bottom">&nbsp;<? if (!empty($file->id)) : ?><a href="javascript:deleteFile(<?=$file->id?>);" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"></a> <? endif; ?></td>
	<td width="75">&nbsp;</td>
</tr>
</table>
</div>
<? endforeach; /* end loop through file settings */ ?>
<? endif; /* end show file uploads */ ?>

<?php
if ($yourportfolio->settings['tags'])
	include('section_tags.php');
?>
</form>

<form action="album.php?aid=<?=$album->id?>" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u deze sectie en alle onderliggende items wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="section_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$section->id?>" id="deleteID">
</form>

<form action="section.php?aid=<?=$album->id?>&sid=<?=$section->id?>&mode=edit" method="post" enctype="application/x-www-form-urlencoded" name="deleteFileForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit bestand wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="section_file_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$section->id?>">
<input type="hidden" name="deleteForm[delete][file_id]" value="" id="file_id">
</form>

