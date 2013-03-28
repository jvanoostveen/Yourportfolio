<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * album edit template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
?>
<?=$canvas->filter($yourportfolio->feedback)?>
<form action="<?=$system->thisFile()?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="album">
<input type="hidden" name="albumForm[action]" value="album_save">

<input type="hidden" name="albumForm[album][id]" value="<?=$album->id?>">


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
<? if ($yourportfolio->session['master']) : /* master account is logged in */ ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="albumForm[album][locked]" value="N">
	<input type="checkbox" name="albumForm[album][locked]" id="locked" value="Y"<?=($album->locked == 'Y') ? ' checked' :''?>><label for="locked"> <?=gettext('locked')?></label>
	</td>
	<td align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? else: ?>
<input type="hidden" name="albumForm[album][locked]" value="<?=$album->locked?>">
<? endif; /* end master account options */ ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="albumForm[album][online]" value="N">
	<input type="checkbox" name="albumForm[album][online]" id="online" value="Y"<?=($album->online == 'Y') ? ' checked' :''?> accesskey="o"><label for="online"> <?=gettext('zichtbaar')?></label>
	</td>
	<td align="right">
	<? if ($album->id > 0 && !$yourportfolio->session['limited']) : /* existing album */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium" accesskey="d"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> <?=gettext('verwijder album')?></a><? endif; /* end delete link */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<?php if ($yourportfolio->settings['mobile'] || $yourportfolio->settings['tablet']) : ?>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="albumForm[album][online_mobile]" value="N">
	<input type="checkbox" name="albumForm[album][online_mobile]" id="online_mobile" value="Y"<?=($album->online_mobile == 'Y') ? ' checked' :''?>><label for="online_mobile"> <?=gettext('zichtbaar in mobiele site')?></label>
	</td>
	<td align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<?php endif; ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<?php include('album_metadata.php'); ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? if (!$yourportfolio->session['limited']) : /* is not limited user */ ?>
<tr>
	<td nowrap align="right"><?=gettext('type')?>:</td>
	<td valign="top">
<? if (!empty($yourportfolio->templates)) : /* has templates */ ?>
	<select id="template" name="albumForm[album][template]">
<? foreach($yourportfolio->templates as $template) : /* loop templates */ ?>
		<option value="<?=$template['value']?>" <?=($album->template == $template['value']) ? 'selected' : '';?>><?=$template['name']?></option>
<? endforeach; /* end loop templates */ ?>
	</select>
<? endif; /* end has templates to show */ ?>
<? if ( $yourportfolio->settings['can_edit_types'] ) : /* can edit types */ ?>
<? if ( !empty($yourportfolio->labels['album_type']) ) : /* has album type labels */ ?>
	<select id="type" name="albumForm[album][type]">
<? foreach ( $yourportfolio->labels['album_type'] as $value => $label ) : /* start album type label loop */ ?>
		<option value="<?=$value?>" <?=($value == $album->type) ? 'selected' : '';?>><?=$canvas->filter($label)?></option>
<? endforeach; /* end album type label loop */ ?>
	</select>
<? else : /* no album type labels defined */ ?>
	<input type="hidden" name="albumForm[album][type]" value="<?=$album->type?>">
<? endif; /* end has album type labels */ ?>
<? else : /* can't edit types */ ?>
	<input type="hidden" name="albumForm[album][type]" value="<?=$album->type?>">
<? endif; /* end can edit types */ ?>
	</td>
	<td align="right" valign="top">
	<?=gettext('positie')?>: 
	<input type="hidden" name="albumForm[album][old_position]" id="old_position" value="<?=$album->position?>">
	<input type="text" name="albumForm[album][position]" id="position" value="<?=$album->position?>" size="4"><? if ($album->id == 0) : /* new image */ ?><br><span class="fg_grey txt_mediumsmall"><?=gettext('(niets invullen is achteraan)')?><? endif; /* end is new image */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end not limited user */ ?>
<? if ($yourportfolio->settings['restricted_albums'] && !$yourportfolio->session['limited']) : /* can have restricted albums */ ?>
<tr>
	<td nowrap align="right"><?=gettext('beveiligd')?>:</td>
	<td valign="top">
	<input type="hidden" name="albumForm[album][restricted]" id="restricted" value="N">
	<input type="checkbox" name="albumForm[album][restricted]" id="restricted" value="Y"<?=($album->restricted == 'Y') ? ' checked' :''?>><label for="restricted"></label>
	
	<select id="user" name="albumForm[album][user_id]">
		<option value="" <?=(empty($album->user_id)) ? 'selected' : '' ?>>-</option>
<? foreach ($yourportfolio->client_users as $client_user) : /* loop client users */ ?>
		<option value="<?=$client_user->id?>" <?=($album->user_id == $client_user->id) ? 'selected' : ''?>><?=$canvas->filter($client_user->name)?></option>
<? endforeach; /* end loop client users */ ?>
	</select>
	
	</td>
	<td align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end can have restricted albums */ ?>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? if (YP_MULTILINGUAL) : /* show multilingual edit */ ?>
<? require(HTML.'album_multilanguage_edit.php'); ?>
<? else : /* show normal edit */ ?>
<tr>
	<td nowrap align="right"><?=gettext('titel')?>:</td>
	<td colspan="2">
	<input type="text" name="albumForm[album][name]" id="title" value="<?=$canvas->edit_filter($album->name)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('beschrijving')?>:
	</td>
	<td colspan="2">
	<textarea name="albumForm[album][text_original]" id="text" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($album->text_original)?></textarea>
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
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>

<? if (!empty($album->files_settings)) : /* show file uploads */ ?>
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
<? foreach($album->files_settings as $file_id => $settings) : /* loop through file settings */ ?>
<? if (isset($settings['hidden']) && $settings['hidden'] == true) : continue; endif; /* skip these file upload when settings indicate it is hidden */ ?>
<? $file = $album->getFile($file_id); ?>
<input type="hidden" name="allowed_extensions_<?=$file_id?>" id="allowed_extensions_<?=$file_id?>" value="<?=$settings['extension']?>">
<input type="hidden" name="albumForm[album][files_properties][<?=$file_id?>][online_old]" value="<?=$file->online?>">
<input type="hidden" name="albumForm[album][files_properties][<?=$file_id?>][online]" value="<?=$file->online?>" id="<?=$file_id?>-online">
<div id="<?=$file_id?>-summary">
<table width="98%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td width="100"><img src="<?=IMAGES?>spacer.gif" width="100" height="1"></td>
	<td width="15"><img src="<?=IMAGES?>spacer.gif" width="15" height="1"></td>
	<td width="75"><img src="<?=IMAGES?>spacer.gif" width="75" height="1"></td>
	<td width="50"><img src="<?=IMAGES?>spacer.gif" width="50" height="1"></td>
	<td width="120"><img src="<?=IMAGES?>spacer.gif" width="120" height="1"></td>
	<td><img src="<?=IMAGES?>spacer.gif" width="1" height="1"></td>
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
	<input type="file" id="<?=$file_id?>" name="albumFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
<? else : /* file is present */ ?>
	<a href="download.php?fid=<?=$file->id?>&obj=album" class="fg_black default"><?=$canvas->filter($file->name)?></a>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'album',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end file or not */ ?>
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'album',<?=$album->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>" align="absmiddle" style="padding-left: 5px;"></a>
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
	$file->buildCacheFile($album->files_settings[$file_id]['naming']);
?>
	<img src="<?=CACHE_UPLOAD_DIR.$file->sysname?>">
<? else : /* is not an image */ ?>
	<td width="125" class="bg_lightgrey upload_text" rowspan="2" valign="top">
	<?=$canvas->filter($file->name, 15)?>
<? if ($settings['type'] == 'video') : /* add play btn when video */ ?>
	<a href="javascript:playMovie(<?=$file->id?>,'album',<?=$file->width?>,<?=$file->height?>);"><img src="<?=IMAGES?>btn_play.gif" align="absmiddle" width="24" height="15" border="0"></a>
<? endif; /* end add play button */ ?>
<? endif; /* end preview */ ?>
<? else : /* is empty entry */ ?>
	<td width="125" class="bg_lightgrey" rowspan="2">&nbsp;
<? endif; /* end not empty entry */ ?>
	</td>
	<td width="18" class="bg_lightgrey" rowspan="2" valign="top" style="padding-top: 3px;">
<? if ($file->id > 0 && $settings['type'] == 'image' && $file->canCrop($settings)) : ?>
	<a href="javascript:openImageCrop(<?=$file->id?>,'album',<?=$album->id?>)"><img src="<?=$canvas->showIcon("crop_grey")?>" width="18" height="18" border="0" alt="<?=gettext('Afbeelding bijsnijden')?>"></a>
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
	<input type="file" id="<?=$file_id?>" name="albumFiles[<?=$file_id?>]" size="20" onchange="checkFileUpload('<?=$file_id?>','<?=$settings['media']?>',this);">
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
	include('album_tags.php');
?>
</form>

<form action="<?=$system->thisFile()?>" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<? if ($album->template == 'album') : /* if album is a real album */ ?>
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit album en alle onderliggende secties en items wilt verwijderen?')?>" id="message">
<? else : /* other type of album */ ?>
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit album wilt verwijderen?')?>" id="message">
<? endif; /* end album template message check */ ?>
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="album_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$album->id?>" id="deleteID">
</form>

<form action="album.php?aid=<?=$album->id?>&mode=edit" method="post" enctype="application/x-www-form-urlencoded" name="deleteFileForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u dit bestand wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="album_file_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$album->id?>">
<input type="hidden" name="deleteForm[delete][file_id]" value="" id="file_id">
</form>