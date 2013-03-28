<table width="98%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td colspan="4"><img src="<?=IMAGES?>spacer.gif" width="1" height="30"/></td>
</tr>
<tr>
	<td nowrap width="100" valign="top" align="right"><?php echo _('tags'); ?>:</td>
	<td colspan="2">
 		<a href="#" class="metadata_toggle" id="show_tags" style="padding: 0 0 0 10px; font-weight: bold;"><?php echo _('toon tags'); ?></a>
		<div id="tags" style="display: none;">
<?php
	$yourportfolio->loadTags();
	foreach ($yourportfolio->tags as $group) :
?>
<div style="line-height: 150%; float: left; margin: 0px; padding: 0 10px 10px 10px;"><b><?=$group['name']?></b><br>
<?php foreach( $group['tags'] as $tag ) : ?>
	<input type="checkbox" name="sectionForm[section][tags][<?php echo $tag['id']; ?>]" id="t_<?php echo $tag['id']; ?>" <?=( in_array( $tag['id'], array_values( $section->tags ) ) ) ? "checked=checked" : ""?>><label for="t_<?php echo $tag['id']; ?>"> <?php echo $tag['tag']; ?></label><br>
<?php endforeach; ?>
</div>
<?php endforeach; ?>
		</div>
<script type="text/javascript">
	$(function() {
		$('#show_tags').disableSelection().click(function ()
		{
			$(this).hide();
			$('#tags').show();
			$('#canvas_div').scrollTop($('#tags').position().top);
		});
	});
</script>
	</td>
	<td width="75">&nbsp;</td>
</tr>
</table>
