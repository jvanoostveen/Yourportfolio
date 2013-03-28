<ul>
<?php foreach ($dataprovider->getNodes() as $rootNode) : ?>
<?php $active = (isset($node) && $rootNode == $node->root ? true : false); ?> 
	<li>
		<a href="<?php echo $rootNode->url(); ?>"<?php echo ($active ? ' class="active"' : ''); ?>><?php echo $canvas->f($rootNode->getTitle()); ?></a>
<?php if($active && $rootNode->template != NodeTemplate::NEWS) : ?>
			<ul>
<?php foreach ($rootNode->getChildren() as $childNode) : ?>
<?php $childactive = ($childNode == $node || $childNode == $node->parent ? true : false); ?>
				<li><a href="<?php echo $childNode->url(); ?>"<?php echo ($childactive ? ' class="active"' : ''); ?>><?php echo $canvas->f($childNode->getTitle()); ?></a></li>
<?php endforeach; ?>
			</ul>
<?php endif; ?>
	</li>
<?php endforeach; ?>
</ul>
