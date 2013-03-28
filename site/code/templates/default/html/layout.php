<div id="header">
	<div id="title"><h1><?=$canvas->filter($yourportfolio->title)?></h1>
	<p><?=$canvas->filter($yourportfolio->preferences['description'])?></p>
	</div>
	<div id="address" class="txt_normal">
<? if (!empty($yourportfolio->phone)) : ?>
	telefoon: <?=$canvas->filter($yourportfolio->phone)?><br />
<? endif; ?>
<? if (!empty($yourportfolio->fax)) : ?>
	fax: <?=$canvas->filter($yourportfolio->fax)?><br />
<? endif; ?>
<? if (!empty($yourportfolio->email)) : ?>
	e-mail: <a href="mailto:<?=$yourportfolio->email?>" class="default txt_normal"><?=$canvas->filter($yourportfolio->email)?></a>
<? endif; ?>
	</div>
</div>

<? if (YP_MULTILINGUAL) : ?>
<div id="language">
<? foreach ($GLOBALS['YP_LANGUAGES'] as $language_key => $language_name) : ?>
<a href="<?=$canvas->url($album, $section, $item, $language_key)?>" class="default"><?=$language_name?></a> &nbsp;
<? endforeach; ?>
</div>
<? endif; ?>

<div id="content">
	<div id="menu">
	<? require($canvas->templatePath('menu')); ?>
	</div>
	<div id="submenu">
	<? require($canvas->templatePath('submenu')); ?>
	</div>
	<div id="sectionmenu">
	<? require($canvas->templatePath('sectionmenu')); ?>
	</div>
	<div id="item">
	<? require($canvas->templatePath($canvas->inner_template)); ?>
	</div>
</div>

<div>built with Yourportfolio technology, <a href="http://www.yourportfolio.nl" target="_blank" class="default">www.yourportfolio.nl</a></div>