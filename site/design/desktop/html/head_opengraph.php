	<meta property="og:title" content="<?php echo $og_title; ?>" />
	<meta property="og:site_name" content="<?php echo $og_site_name; ?>" />
	<meta property="og:description" content="<?php echo $og_description; ?>" />
<?php if (!empty($og_url)) : ?>
	<meta property="og:url" content="<?php echo $og_site_url.$og_url; ?>" />
<?php endif; ?>
<?php if ($og_image_url) : ?>
	<meta property="og:image" content="<?php echo $og_image_url; ?>" />
<?php endif; ?>
<?php if ($og_video_url) : ?>
	<meta property="og:video" content="<?php echo $og_video_url; ?>" />
	<meta property="og:video:width" content="<?php echo $og_video_width; ?>" />
	<meta property="og:video:height" content="<?php echo $og_video_height; ?>" />
	<meta property="og:video:type" content="application/x-shockwave-flash" />
<?php endif; ?>
<?php if (!empty($og_fb_admins)) : ?>
	<meta property="fb:admins" content="<?php echo $og_fb_admins; ?>" />
<?php endif; ?>
<?php if (!empty($og_fb_app_id)) : ?>
	<meta property="fb:app_id" content="<?php echo $og_fb_app_id; ?>" />
<?php endif; ?>
<?php
