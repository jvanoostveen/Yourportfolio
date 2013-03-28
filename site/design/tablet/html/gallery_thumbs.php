<h1><?php echo $canvas->f($node->getTitle()); ?></h1>
<pre><?php //print_r($node->children); ?></pre>
<div class="gallery_thumbs">
<?php 
foreach ($node->getChildren() as $child) : 
	$file = Files::get(Files::THUMBNAIL, $child);
	if(!$file)
		continue;
?>
	
		<a href="<?php echo $child->url(); ?>"><img src="<?php echo $file->getPath(array('width' => 70, 'height' => 70)); ?>" width="<?php echo $file->width; ?>" height="<?php echo $file->height; ?>" /></a>
	<?php endforeach; ?>

</div>