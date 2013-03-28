	<div class="navigation">
		<div class="left">
		<?php if ($node->parent) : ?>
			<a href="<?php echo $node->parent->parent->url(); ?>"><img src="<? echo Path::image('arrow_up.png'); ?>" alt="back" width="30" height="30" /></a>
		<?php endif; ?>

			<a href="<?php echo $node->parentUrl(); ?>"><img src="<? echo Path::image('thumbs.png'); ?>" alt="thumbs" width="30" height="30" /></a>
		</div>
		<div class="right">
		<?php if ($previous) : ?>
			<a href="<?php echo $previous->url(); ?>" class="prev"><img src="<? echo Path::image('arrow_left.png'); ?>" alt="previous" width="30" height="30" /></a>
		<?php endif; ?>
	
		<?php if ($next) : ?>
			<a href="<?php echo $next->url(); ?>"><img src="<? echo Path::image('arrow_right.png'); ?>" alt="next" width="30" height="30" /></a>
		<?php endif; ?>	
		</div>
	</div>