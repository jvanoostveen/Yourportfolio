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
<? $textarea_rows = (isset($textarea_rows)) ? $textarea_rows : 10; ?>
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
<?php foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) : ?>
<div id="content_<?php echo $language_key; ?>" style="display: none;">
<?php if ($language_key == $GLOBALS['YP_DEFAULT_LANGUAGE']) : ?>
<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right"><?php echo _('titel'); ?>:</td>
	<td>
		<input type="text" name="albumForm[album][name]" id="title" value="<?php echo $canvas->edit_filter($album->name); ?>" size="50">
	</td>
	<td width="75">&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top"><?php echo _('beschrijving'); ?>:</td>
	<td>
	<textarea name="albumForm[album][text_original]" id="text" rows="<?php echo $textarea_rows; ?>" cols="50" class="fullsize"><?php echo $canvas->edit_filter($album->text_original); ?></textarea>
<?php
# <!-- text manipulation tools -->
$text_tool = 'text';
require(HTML.'text_tools.php');
# <!-- text manipulation tools -->
?>
	</td>
	<td>&nbsp;</td>
</tr>
</table>
<?php else : ?>
<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right"><?php echo _('titel'); ?>:</td>
	<td>
	<input type="text" name="albumForm[album][strings][name][<?php echo $language_key; ?>]" id="title_<?php echo $language_key; ?>" value="<?php echo $canvas->edit_filter($album->getText('name', $language_key)); ?>" size="50">
	</td>
	<td width="75">&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top"><?php echo _('beschrijving'); ?>:</td>
	<td>
	<textarea name="albumForm[album][strings][text_original][<?php echo $language_key; ?>]" id="text_<?php echo $language_key; ?>" rows="<?php echo $textarea_rows; ?>" cols="50" class="fullsize"><?php echo $canvas->edit_filter($album->getText('text_original', $language_key)); ?></textarea>
<?php
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
	