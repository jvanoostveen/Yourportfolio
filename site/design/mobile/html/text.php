<?php include('header.php'); ?>
<?php $file = Files::get(Files::PREVIEW, $node); ?>
<?php if ($file) : ?>
<img src="<?php echo $file->getPath(array('width' => 300)); ?>"  width="<?php echo $file->width; ?>" height="<?php echo $file->height; ?>" />
<?php endif; ?>
<p><?php echo $canvas->f($node->getText()) ?></p>
