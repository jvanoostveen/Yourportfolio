<?php include('header.php'); ?>
<?php 
$file = Files::get(Files::PREVIEW, $node);
if ($file) : ?>
<img src="<?php echo $file->path; ?>" />
<?php endif; ?>
<p><?php echo $canvas->f($node->getText()) ?></p>
