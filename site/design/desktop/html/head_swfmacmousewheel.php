<?php 
$swfmacmousewheel = ($yourportfolio->site['flash']['version'] < 9 ? 'swfmacmousewheel' : 'swfmacmousewheel2');
?>
	<script type="text/javascript" src="<?php echo Path::script($swfmacmousewheel.'.js'); ?>"></script>
