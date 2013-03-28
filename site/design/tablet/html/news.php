	<?php foreach ($node->getChildren() as $child) : ?>
		<div class="newsitem">
			<?php 
			$file = Files::get(Files::PREVIEW, $child);
			if ($file) : ?>
				<img src="<?php echo $file->getPath(array('width' => 300, 'height' => 200)); ?>" class="right" />
			<?php endif; ?>
			<h2><?php echo $canvas->f($child->getTitle())?></h2>
			<span class="date"><?php echo date('j M Y', $child->date)?></span>
			
			<p><?php echo $canvas->f($child->getText())?></p>
			
			<div class="clear"></div>
		</div>
	<?php endforeach; ?>