<a href="<?php echo $home_url; ?>" id="logo">
<?php if( Path::imageExists('logo.png') ) : ?>
	<img src="<? echo Path::image('logo.png'); ?>" />
<?php else : ?>
	<?php echo $title; ?>
<?php endif; ?>
</a>