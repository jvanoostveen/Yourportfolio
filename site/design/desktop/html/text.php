<h1><?php echo $canvas->f($node->getTitle()); ?></h1>
<?php 
$file = Files::get(Files::PREVIEW, $node);
if ($file) : 
?>
<img src="<?php echo $file->getPath(); ?>" />
<?php endif ; ?>
<p><?php echo $canvas->f($node->getText()) ?></p>

