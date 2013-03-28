<?php
$image = Files::get(Files::PREVIEW, $node);
$movie = Files::get(Files::MOVIE, $node);
?>

<div class="gallery_holder">
<?php if ($movie) : ?>
	<script type="text/javascript" src="<? echo Path::script('ac_quicktime.js'); ?>"></script>
	<script>
		function jumpToTime(timeInSecs)
		{
			try
			{
				var vid = document.getElementById("movie_video");
				if ( vid && ('VIDEO' == vid.tagName) && vid.currentTime )
				{
					// video tag, use it
					vid.currentTime = timeInSecs;
					return;
				}
			
				// browser apparently doesn't support video, look for embed then for an object
				vid = document.getElementById("movie_embed");
				if ( !vid ) 
					vid = document.getElementById("movie_obj");
				if ( vid && vid.GetTimeScale ) 
				{
					// time in QuickTime is in timescale units per second, convert from param in seconds
					var timeScale = vid.GetTimeScale();
					vid.SetTime(timeInSecs * timeScale);
				}
			}
			catch(e) {} 
		}
	// 
	</script>
	<?php $image = Files::get(Files::PREVIEW, $node); ?>
	<video id="movie_video" <?php echo ($image ? 'poster="' . $image->path . '"' : 'autoplay="autoplay"'); ?> controls >
		<source src="<?php echo $movie->path; ?>" type="video/mp4">
		<script>
			QT_WriteOBJECT('<?php echo $movie->path; ?>',
			'<?php echo $movie->width; ?>px', '<?php echo $movie->height; ?>px',
			'',
			'scale', 'tofit',
			'emb#id', 'movie_embed',
			'obj#id', 'movie_obj');
		</script> 
	</video>
<?php elseif($image) : ?>
	<img src="<?php echo $image->getPath(array('width' => 690, 'height' => 1020, 'crop' => false)); ?>" />
<?php endif; ?>

<?php 
$next = $node->getNext();
$previous = $node->getPrevious();
?>
<?php include('gallery_detail_navigation.php'); ?>
</div>
