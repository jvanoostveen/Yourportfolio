<?php include('header.php'); ?>
<ul>
<?php foreach ($node->getChildren() as $child) : ?>
	<li><a href="<?php echo $child->url(); ?>"><?php echo $canvas->f($child->getTitle()); ?></a></li>
<?php endforeach; ?>
</ul>