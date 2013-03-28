<h1><?php echo $canvas->f($node->getTitle()); ?></h1>
<ul class="thumbs">
<?php foreach ($node->getChildren() as $child) : ?>
	<li>
		<?php
		$file = Files::get(Files::THUMBNAIL, $child);
		if(!$file && $child->hasChildren())
		{
			$grandchildren = $child->getChildren();
			$file = Files::get(Files::THUMBNAIL, $grandchildren[0]);
		}
		?>
		<?php if ($file) : ?>
			<img src="<?php echo $file->getPath(array('width' => 50, 'height' => 50)); ?>" />
		<?php else : ?>
			<div class="placeholder"></div>
		<?php endif; ?>
			
		
		<a href="<?php echo $child->url(); ?>"><?php echo $canvas->f($child->getTitle()); ?></a>
	</li>
<?php endforeach; ?>
</ul>