<script type="text/javascript">

	jQuery(window).load(function(){
	
		jQuery('#cropbox').Jcrop({
			onChange: showPreview,
			onSelect: showPreview,
			minSize: [<?=$crop_width?>,<?=$crop_height?>],
			aspectRatio: <?=number_format(($crop_width/$crop_height),1,".","")?>,
			boxWidth: 750,
			boxHeight: 750

		});
	
	});
	function updateCoords(c)
		{
			$('#x').val(c.x);
			$('#y').val(c.y);
			$('#w').val(c.w);
			$('#h').val(c.h);
		};
	function showPreview(coords)
	{
		updateCoords(coords);
		/*
if (parseInt(coords.w) > 0)
		{
			var rx = <?//=$crop_width?> / coords.w;
			var ry = <?//=$crop_height?> / coords.h;
	
			jQuery('#preview').css({
				width: Math.round(rx * <?//=$original_image_size[0]?>) + 'px',
				height: Math.round(ry * <?//=$original_image_size[1]?>) + 'px',
				marginLeft: '-' + Math.round(rx * coords.x) + 'px',
				marginTop: '-' + Math.round(ry * coords.y) + 'px'
			});
		}
*/
	}
	
	function checkCoords()
	{
		if (parseInt($('#w').val())) return true;
		alert('Please select a crop region then press submit.');
		return false;
	};

</script>

<form action="<?=$system->thisFile()?>" method="post" onsubmit="return checkCoords();">
	<!-- Submit Stuff -->
	<input type="hidden" name="action" value="crop_image">
	<input type="hidden" id="file_id" name="file_id" value="<?=$file->id?>" />
	<input type="hidden" id="owner_type" name="owner_type" value="<?=$owner_type?>" />
	<input type="hidden" id="owner_id" name="owner_id" value="<?=$owner_id?>" />
	<!-- Coords -->
	<input type="hidden" id="x" name="x" />
	<input type="hidden" id="y" name="y" />
	<input type="hidden" id="w" name="w" />
	<input type="hidden" id="h" name="h" />
	
	<div id="crop_container"><img src="<?=ORIGINALS_DIR.$file->sysname?>" id="cropbox" /></div>
	<div id="crop_footer"><input type="submit" value="<?=gettext('Afbeelding bijsnijden')?>" /></div>
</form>

<!--<div style="width:<?//=$crop_width?>px;height:<?//=$crop_height?>px;overflow:hidden;">
	<img src="<?//=ORIGINALS_DIR.$file->sysname?>" id="preview" />
</div>-->


