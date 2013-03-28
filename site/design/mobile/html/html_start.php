<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="content-Type" content="text/html; charset=utf-8">
	<title><?php echo $canvas->f($title); ?></title>
	
	<script type="text/javascript" src="<?php echo Path::script('jquery.js'); ?>"></script>
	
	<link rel="stylesheet" href="<?php echo Path::css('base.css'); ?>" media="screen" />
<?php if(Path::cssExists('style.css')) : ?>
	<link rel="stylesheet" href="<?php echo Path::css('style.css'); ?>" media="screen" />
<?php endif; ?>

<?php
$meta = array(
		array('rel' => 'apple-touch-startup-image', 'href' => 'apple-touch-startup-image.png'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon.png'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed.png'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon-72x72.png', 'sizes' => '72x72'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed-72x72.png', 'sizes' => '72x72'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon-114x114.png', 'sizes' => '114x114'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed-114x114.png', 'sizes' => '114x114')
	);
?>
<?php foreach ($meta as $link) : ?>
<?php if (!Path::imageExists($link['href'])) continue; ?>
	<?php echo generateTag('link', $link).PHP_EOL; ?>
<?php endforeach; ?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;">
	<script type="text/javascript">
	/mobile/i.test(navigator.userAgent) && !window.location.hash && setTimeout(
	function () {
		if (window.scrollY == 0)
		{
			window.scrollTo(0, 1);
			window.scrollTo(0, 0);
		}
	}, 1000);
	</script>	
</head>
<body>