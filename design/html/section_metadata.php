<tr>
	<td nowrap align="right" valign="top"><?php echo _('metadata'); ?>:</td>
	<td>
	
	<a href="#" class="metadata_toggle" id="metadata_seo_toggle"><?php echo _('Search engines'); ?></a>
	<div id="metadata_seo" style="display: none;">
		<br>
<?php if (YP_MULTILINGUAL) : ?>
		<div id="metadata_seo_language">
<?php
$first_language_key = null;
foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) :
if (!$first_language_key)
{
	$first_language_key = $language_key;
} 
?>
			<input type="radio" id="seo_<?php echo $language_key; ?>" name="seo_language"/><label for="seo_<?php echo $language_key; ?>"><?php echo $language; ?></label>
<?php endforeach; ?>
		</div>
		<div id="metadata_seo_fields" style="position: relative;">
<?php foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) : ?>
			<div id="metadata_seo_<?php echo $language_key; ?>">
			<label for="seo_description_<?php echo $language_key; ?>" class="floating_label"><?php echo _('beschrijving'); ?>:</label>
			<textarea name="sectionForm[section][metadata][seo_description][<?php echo $language_key; ?>]" id="seo_description_<?php echo $language_key; ?>" rows="4" cols="75"><?=$canvas->edit_filter($section->getMetadata('seo_description', $language_key));?></textarea><br>
			<label for="seo_keywords_<?php echo $language_key; ?>" class="floating_label"><?php echo _('keywords'); ?>:</label>
			<textarea name="sectionForm[section][metadata][seo_keywords][<?php echo $language_key; ?>]" id="seo_keywords_<?php echo $language_key; ?>" rows="3" cols="75"><?=$canvas->edit_filter($section->getMetadata('seo_keywords', $language_key)); ?></textarea>
			</div>
<?php endforeach; ?>
		</div>
<script type="text/javascript">
$(function() {
		$('#metadata_seo_fields > div').hide().first().show();
		
		var seo = $('#metadata_seo_language');
		seo.buttonset().disableSelection();
		seo.find(':first').attr('checked', true).button('refresh');

		seo.children('input').change(function () {
			$('#metadata_seo_fields > div').hide();
			$('#metadata_seo_fields > div[id=metadata_' + $(this).attr('id') + ']').show();
		});
	});
</script>
<?php else : ?>
		<div id="metadata_seo_fields">
			<div id="metadata_seo_default" style="position: relative;">
			<label for="seo_description_default" class="floating_label"><?php echo _('beschrijving'); ?>:</label>
			<textarea name="sectionForm[section][metadata][seo_description][default]" id="seo_description_default" rows="4" cols="75"><?=$canvas->edit_filter($section->getMetadata('seo_description', 'default'));?></textarea><br>
			<label for="seo_keywords_default" class="floating_label"><?php echo _('keywords'); ?>:</label>
			<textarea name="sectionForm[section][metadata][seo_keywords][default]" id="seo_keywords_default" rows="3" cols="75"><?=$canvas->edit_filter($section->getMetadata('seo_keywords', 'default')); ?></textarea><br>
			</div>
		</div>
<?php endif; ?>
	</div>
	<br>
	<a href="#" class="metadata_toggle" id="metadata_og_toggle"><?php echo _('Facebook'); ?></a>
	<div id="metadata_og" style="display: none;">
		<br>
<?php if (YP_MULTILINGUAL) : ?>
		<div id="metadata_og_language">
<?php
$first_language_key = null;
foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) :
if (!$first_language_key)
{
	$first_language_key = $language_key;
} 
?>
			<input type="radio" id="og_<?php echo $language_key; ?>" name="og_language"/><label for="og_<?php echo $language_key; ?>"><?php echo $language; ?></label>
<?php endforeach; ?>
		</div>
		<div id="metadata_og_fields" style="position: relative;">
<?php foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language) : ?>
			<div id="metadata_og_<?php echo $language_key; ?>">
			<label for="og_title_<?php echo $language_key; ?>" class="floating_label"><?php echo _('titel'); ?>:</label>
			<input type="text" name="sectionForm[section][metadata][og_title][<?php echo $language_key; ?>]" id="og_title_<?php echo $language_key; ?>" size="50" value="<?php echo $canvas->filter($section->getMetadata('og_title', $language_key)); ?>"><br>
			<label for="og_description_<?php echo $language_key; ?>" class="floating_label"><?php echo _('beschrijving'); ?>:</label>
			<textarea name="sectionForm[section][metadata][og_description][<?php echo $language_key; ?>]" id="og_description_<?php echo $language_key; ?>" rows="4" cols="75"><?=$canvas->edit_filter($section->getMetadata('og_description', $language_key));?></textarea>
			</div>
<?php endforeach; ?>
		</div>
<script type="text/javascript">
$(function() {
		$('#metadata_og_fields > div').hide().first().show();
		
		var og = $('#metadata_og_language');
		og.buttonset().disableSelection();
		og.find(':first').attr('checked', true).button('refresh');

		og.children('input').change(function () {
			$('#metadata_og_fields > div').hide();
			$('#metadata_og_fields > div[id=metadata_' + $(this).attr('id') + ']').show();
		});
	});
</script>
<?php else : ?>
		<div id="metadata_og_fields">
			<label for="og_title_default" class="floating_label"><?php echo _('titel'); ?>:</label>
			<input type="text" name="sectionForm[section][metadata][og_title][default]" id="og_title_default" size="50" value="<?php echo $canvas->filter($section->getMetadata('og_title', 'default')); ?>"><br>
			<label for="og_description_default" class="floating_label"><?php echo _('beschrijving'); ?>:</label>
			<textarea name="sectionForm[section][metadata][og_description][default]" id="og_description_default" rows="4" cols="75"><?=$canvas->edit_filter($section->getMetadata('og_description', 'default'));?></textarea>
		</div>
<?php endif; ?>
	</div>
<script type="text/javascript">
$(function () {
	$('#metadata_seo_toggle').disableSelection().click(function ()
			{
				$('#metadata_seo').toggle('fast');
			});
	
	$('#metadata_og_toggle').disableSelection().click(function ()
			{
				$('#metadata_og').toggle('fast');
			});
});
</script>	

	</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>