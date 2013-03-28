<?php include('logo.php'); ?>

<ul class="mainmenu">
<?php foreach ($dataprovider->getNodes() as $rootNode) : ?>
	<li><a href="<?php echo $rootNode->url(); ?>"><?php echo $canvas->f($rootNode->getTitle()); ?></a></li>
<?php endforeach; ?>
</ul>
