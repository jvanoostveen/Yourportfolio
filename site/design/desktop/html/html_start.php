<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title><?=$canvas->f($title)?></title>
	<link rel="stylesheet" href="<?php echo Path::css('base.css'); ?>" media="screen" />
<?php if (Path::cssExists('style.css')) : ?>
	<link rel="stylesheet" href="<?php echo Path::css('style.css'); ?>" media="screen" />
<?php endif; ?>
<?php
	# Google Analytics
	if ($yourportfolio->site['google_analytics']['enabled']) : 
		include('head_google_analytics.php');
	endif;
?>
<?php
	# SWFAddress Optimizer
	if (isset($_GET['q'])) :
		include('head_swfaddress-optimizer.php');
	endif;
?>
	<script type="text/javascript" src="<?php echo Path::script('swfobject.js'); ?>"></script>
<?php
	# MacMousewheel
	if ($yourportfolio->site['swfobject']['mousewheel']) :
		include('head_swfmacmousewheel.php');
	endif;
?>
	<script type="text/javascript" src="<?php echo Path::script('swfaddress.js'); ?>"></script>
	<meta http-equiv="content-Type" content="text/html; charset=utf-8">
<?php 
	# SEO metadata
	include('head_seo_meta.php');
?>
<?php
	# Google Site Verification
	if (!empty($yourportfolio->prefs['google_site_verification'])) :
		include('head_google_site_verification.php');
	endif; 
?>
<?php
	# Apple Touch icons and startup screen
	include('head_apple_touch_links.php');
?>
<?php
	# RSS feeds
	include('head_rss.php');
?>
<?php 
	# Open Graph / Facebook
	include('head_opengraph.php');
?>
	<link rel="shortcut icon" href="<?php echo Path::image('favicon.ico'); ?>" />
</head>
<body<?php echo (!$node ? ' class="home"' : ''); ?>>
<div id="swf_container">	
	<div id="container">
		<?php include('languageselect.php'); ?>
		<div id="header">
			<?php include('logo.php'); ?>
		</div>
		<div id="body">
			<div id="navigation">
				<?php include('menu.php'); ?>
			</div>
			<div id="content">
				