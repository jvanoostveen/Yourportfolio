<?php
$links =
	array(
		array('rel' => 'apple-touch-startup-image', 'href' => 'apple-touch-startup-image.png'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon.png'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed.png'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon-72x72.png', 'sizes' => '72x72'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed-72x72.png', 'sizes' => '72x72'),
		array('rel' => 'apple-touch-icon', 'href' => 'apple-touch-icon-114x114.png', 'sizes' => '114x114'),
		array('rel' => 'apple-touch-icon-precomposed', 'href' => 'apple-touch-icon-precomposed-114x114.png', 'sizes' => '114x114')
	);

foreach ($links as $link) :
	if (!Path::imageExists($link['href']))
		continue;
	echo '	'.generateTag('link', $link).PHP_EOL;
endforeach;
