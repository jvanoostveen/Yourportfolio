<!DOCTYPE html> 
<html lang="en"> 
<head>
	<title><?=$canvas->f($title)?></title>
	<link rel="stylesheet" href="<?php echo Path::css('base.css'); ?>" media="screen" />
<?php if(Path::cssExists('style.css')) : ?>
	<link rel="stylesheet" href="<?php echo Path::css('style.css'); ?>" media="screen" />
<?php endif; ?>
<?php
	if ($yourportfolio->site['google_analytics']['enabled']) : 
		include('head_google_analytics.php');
	endif;
?>
	<meta http-equiv="content-Type" content="text/html; charset=utf-8">
<?php
	include('head_apple_touch_links.php');
?>
	<link rel="shortcut icon" href="<?php echo Path::image('favicon.ico'); ?>" />
</head>
<body<?php echo (!$node ? ' class="home"' : ''); ?>>
	
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
				