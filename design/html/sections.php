<div class="grid" id="sortable">
<?php $ids = array(); ?>
<? if (!empty($album->sections)) : /* has sections to show */ ?>
<? foreach($album->sections as $section) : /* section loop */ ?>
<?
if (empty($section['name']))
{
	$tmp_section = new Section();
	$tmp_section->id = $section['id'];
	$tmp_section->load();
	
	if (!empty($tmp_section->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_section->strings['name']));
		$section['name'] = $tmp_section->strings['name'][$first_language]['string_parsed'];
	} else {
		$section['name'] = '';
	}
}
?>
<?php $ids[] = $section['id']; ?>
<div class="griditem" id="<?php echo $section['id']; ?>">
<a name="section-<?=$section['id']?>"></a>
<?php 
$image = '';
if (file_exists(YOURPORTFOLIO_DIR.'section-'.$section['id'].'.jpg')) : /* has image */
	$image = YOURPORTFOLIO_DIR.'section-'.$section['id'].'.jpg';
else : /* has no image */
	$image = $canvas->showIcon('section_overview');
endif; /* end has image */
$image .= '?m='.filectime($image);
?>
	<div style="background-image: url('<?php echo $image; ?>')" class="griditem-image">
		<a href="section.php?aid=<?=$album->id?>&sid=<?=$section['id']?>"><img src="<?=IMAGES?>spacer.gif" width="120" height="90"></a>
	</div>
	<div class="griditem-label">
		<a href="<?=$system->file?>?aid=<?=$album->id?>&switch=<?=$section['id']?>"><img src="<?=IMAGES?>photo_<?=($section['online'] == 'Y') ? 'online' : 'offline'?>.gif" width="20" height="20"></a>
		<div><a href="section.php?aid=<?=$album->id?>&sid=<?=$section['id']?>" class="default fg_white txt_mediumsmall"><?=$canvas->filter($section['name'])?></a></div>
	</div>
</div>
<? endforeach; /* end section loop */ ?>
<? endif; /* end has sections to show */ ?>
</div>


<form action="<?php echo $system->thisUrl(); ?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="album">
<input type="hidden" name="albumForm[action]" value="sections_position_save">
<input type="hidden" name="albumForm[id]" value="<?php echo $album->id; ?>">
<input type="hidden" id="ids" name="albumForm[ids]" value="<?php echo implode(',', $ids); ?>">
</form>

<script>
$(function() {
	$('#sortable').sortable(
			{
				distance: 10, 
				update: function (event, ui)
				{
					var ids = [];
					$('.griditem').each(function (index)
							{
								ids.push($(this).attr('id'));
							}
						);
					$('#ids').attr('value', ids.join(','));
				}
			});
	$('#sortable').disableSelection();
});
</script>
