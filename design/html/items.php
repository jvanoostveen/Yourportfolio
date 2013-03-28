<div class="grid" id="sortable">
<?php $ids = array(); ?>
<? if (!empty($section->items)) : /* has items to show */ ?>
<? foreach($section->items as $loop_item) : /* item loop */ ?>
<? if (empty($loop_item['name'])) :
	$tmp_item = new Item();
	$tmp_item->id = $loop_item['id'];
	$tmp_item->load();
	
	if (!empty($tmp_item->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_item->strings['name']));
		$loop_item['name'] = $tmp_item->strings['name'][$first_language]['string_parsed'];
	} else {
		$loop_item['name'] = '';
	}
endif;
?>
<?php $ids[] = $loop_item['id']; ?>
<div class="griditem" id="<?php echo $loop_item['id']; ?>">
<a name="item-<?=$loop_item['id']?>"></a>
<?php 
$image = '';
if (file_exists(YOURPORTFOLIO_DIR.$loop_item['id'].'.jpg')) : /* has image */
	$image = YOURPORTFOLIO_DIR.$loop_item['id'].'.jpg';
elseif (file_exists(YOURPORTFOLIO_DIR.'item-'.$loop_item['id'].'.jpg')) : /* has image, but other name */ 
	$image = YOURPORTFOLIO_DIR.'item-'.$loop_item['id'].'.jpg';
else : /* has no image */ 
	$image = $canvas->showIcon('item_overview');
endif; /* end has image */
$image .= '?m='.filectime($image);
?>
	<div style="background-image: url('<?php echo $image; ?>')" class="griditem-image">
		<a href="item.php?aid=<?=$album->id?>&sid=<?=$section->id?>&iid=<?=$loop_item['id']?>"><img src="<?=IMAGES?>mediatype_<?=$loop_item['type']?>.gif" width="120" height="90" border="0"></a>
	</div>
	<div class="griditem-label">
		<a href="<?=$system->file?>?aid=<?=$album->id?>&sid=<?=$section->id?>&switch=<?=$loop_item['id']?>"><img src="<?=IMAGES?>photo_<?=($loop_item['online'] == 'Y') ? 'online' : 'offline'?>.gif" width="20" height="20"></a>
		<div><a href="item.php?aid=<?=$album->id?>&sid=<?=$section->id?>&iid=<?=$loop_item['id']?>" class="default fg_white txt_mediumsmall"><?=$canvas->filter($loop_item['name'])?></a></div>
	</div>
</div>
<? endforeach; /* end items loop */ ?>
<? endif; /* end has items to show */ ?>
</div>

<form action="<?php echo $system->thisUrl(); ?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="section">
<input type="hidden" name="sectionForm[action]" value="items_position_save">
<input type="hidden" name="sectionForm[id]" value="<?php echo $section->id; ?>">
<input type="hidden" id="ids" name="sectionForm[ids]" value="<?php echo implode(',', $ids); ?>">
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
