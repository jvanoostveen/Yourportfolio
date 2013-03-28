<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * item multilanguage (part) edit template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
?>
<tr>
	<td>&nbsp;</td>
	<td colspan="2">
<div id="content_language_switch" style="display: none;">
<?php foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) : ?>
	<input type="radio" id="content_<?php echo $language_key; ?>" name="content_language"/><label for="content_<?php echo $language_key; ?>"><?php echo $language?></label>
<?php endforeach; ?>
</div>
<script type="text/javascript">
$(function () {
	var content = $('#content_language_switch');
	content.buttonset().disableSelection().show();
	content.find(':first').attr('checked', true).button('refresh');

	content.children('input').change(function () {
		$('#content_language_fields > div').hide();
		$('#content_language_fields > div[id=' + $(this).attr('id') + ']').show();
	});
});
</script>
	</td>
	<td>&nbsp;</td>
</tr>
</table>
<div id="content_language_fields">
<? foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) : ?>
<div id="content_<?php echo $language_key; ?>" style="display: none;">
<? if ($language_key == $GLOBALS['YP_DEFAULT_LANGUAGE']) : ?>
<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right"><?=gettext('titel')?>:</td>
	<td>
		<input type="text" name="itemForm[item][name]" id="title" value="<?=$canvas->edit_filter($item->name)?>" size="50">
	</td>
	<td width="75">&nbsp;</td>
</tr>
<? if ($yourportfolio->settings['items_have_subname']) : /* design supports a subtitle */ ?>
<tr>
	<td nowrap align="right"><?=gettext('subtitel')?>:</td>
	<td>
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
	<td>
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
	<td>
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
</table>
<? else : ?>
<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right"><?=gettext('titel')?>:</td>
	<td>
	<input type="text" name="itemForm[item][strings][name][<?=$language_key?>]" id="title_<?=$language_key?>" value="<?=$canvas->edit_filter($item->getText('name', $language_key))?>" size="50">
	</td>
	<td width="75">&nbsp;</td>
</tr>
<? if ($yourportfolio->settings['items_have_subname']) : /* design supports a subtitle */ ?>
<tr>
	<td nowrap align="right"><?=gettext('subtitel')?>:</td>
	<td>
	<input type="text" name="itemForm[item][strings][subname][<?=$language_key?>]" id="subtitle_<?=$language_key?>" value="<?=$canvas->edit_filter($item->getText('subname', $language_key))?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<? endif; /* end subtitle */ ?>
<? if ($yourportfolio->settings['has_custom_fields']) : /* items have a custom setup */ ?>
<? foreach($yourportfolio->custom_fields as $custom_field) : /* loop thru custom fields */ ?>
<?
if (isset($custom_field['owner']) && $custom_field['owner'] != 'item')
	continue;

if (isset($custom_field['type']) && $custom_field['type'] != $item->album_id)
	continue;
?>
<tr>
	<td nowrap align="right"><?=$canvas->filter($custom_field['label'])?>:</td>
	<td>
	<input type="text" name="itemForm[item][custom_data][<?=$custom_field['key']?>]" id="<?=$custom_field['key']?>" value="<?=$canvas->edit_filter($item->getCustomData($custom_field['key']))?>" size="<?=$custom_field['length']?>" disabled>
	</td>
	<td>&nbsp;</td>
</tr>
<? endforeach; /* end loop thru custom fields */ ?>
<? endif; /* end custom fields */ ?>
<tr>
	<td nowrap align="right" valign="top"><?=gettext('beschrijving')?>:</td>
	<td>
	<textarea name="itemForm[item][strings][text_original][<?=$language_key?>]" id="text_<?=$language_key?>" rows="10" cols="50" class="fullsize"><?=$canvas->edit_filter($item->getText('text_original', $language_key))?></textarea>
<?
# <!-- text manipulation tools -->
$text_tool = 'text_'.$language_key;
require(HTML.'text_tools.php');
# <!-- text manipulation tools -->
?>
	</td>
	<td>&nbsp;</td>
</tr>
</table>
<? endif; ?>
</div>
<? endforeach; ?>
</div>

<script type="text/javascript">
$(function () {
	$('#content_language_fields > div').hide().first().show();
});
</script>

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td width="75">&nbsp;</td>
</tr>
	