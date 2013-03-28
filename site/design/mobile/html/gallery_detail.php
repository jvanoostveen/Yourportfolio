<?php
$image = Files::get(Files::PREVIEW, $node);
$movie = Files::get(Files::MOVIE, $node);

$next = $node->getNext();
$previous = $node->getPrevious();
?>

<?php //include('header.php'); ?>
<div class="gallery_holder">


<?php if ($movie) : ?>
<?php
$f = $movie->width / $movie->height;
$width = 320;
$height = round($width / $f);
?>
	<div class="video-js-box">
		<video id="example_video_1" class="video-js" width="<?php echo $width; ?>" height="<?php echo $height; ?>" <?php echo ($image ? 'poster="' . $image->path . '"' : 'autoplay="autoplay"'); ?> controls  preload>
			<source src="<?php echo $movie->path; ?>" type='video/mp4'>
		</video>
	</div>
	<script type="text/javascript" charset="utf-8">
		VideoJS.setupAllWhenReady();
	</script>
<!--
	<ul class="metadata">
		<li><span class="label">title:</span>Blue Motion</li>
		<li><span class="label">product:</span>Volkswagen</li>
		<li><span class="label">agency:</span>DDB</li>
		<li><span class="label">creatives:</span>Joris Kuijpers & Dylan de Backer</li>
		<li><span class="label">editor:</span>Ben Isaacs</li>
		<li><span class="label">d.o.p.::</span>DDB</li>
	</ul>
-->
<?php elseif($image) : ?>
	<img src="<?php echo $image->getPath(array('width' => 320, 'height' => 340, 'crop' => false)); ?>" />
<?php endif; ?>
<?php include('gallery_detail_navigation.php'); ?>
</div><!-- gallery_holder -->







