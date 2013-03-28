<?php if( Path::imageExists('logo.png') ) : ?>
	<div class="image_holder" style="background-image: url('<? echo Path::image('logo.png'); ?>'); height: 100px;"></div>
<?php else : ?>
	<h1><?php echo $title; ?></h1>
<?php endif; ?>