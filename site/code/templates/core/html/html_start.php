<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * opens the html page, loads the stylesheets and javascript files.
 *
 * @package yourportfolio
 * @subpackage SiteHTML
 */
?>
<html>
<head>
	<title><?=$canvas->filter($canvas->title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<? foreach($canvas->meta_http as $type => $content) : /* http-equiv meta headers */ ?>
	<meta http-equiv="<?=$type?>" content="<?=$content?>">
<? endforeach; /* end http-equiv */ ?>
<? foreach($canvas->meta as $type => $content) : /* http-equiv meta headers */ ?>
	<meta name="<?=$type?>" content="<?=$content?>">
<? endforeach; /* end http-equiv */ ?>
<? foreach($canvas->stylesheets as $stylesheet) : /* page needs stylesheet files */ ?>
	<link href="<?=$system->base_url?><?=CORE_CSS?><?=$stylesheet?>.css?c=<?=filectime(CORE_CSS.$stylesheet.'.css')?>" rel="stylesheet" type="text/css">
<? endforeach; /* end load stylesheet files */ ?>
<? foreach($canvas->template_stylesheets as $stylesheet) : /* page needs stylesheet files */ ?>
	<link href="<?=$system->base_url?><?=CUSTOM_CSS?><?=$stylesheet?>.css?c=<?=filectime(CUSTOM_CSS.$stylesheet.'.css')?>" rel="stylesheet" type="text/css">
<? endforeach; /* end load stylesheet files */ ?>
<? if ($yourportfolio->site['google_analytics']['enabled']) : /* Google Analytics is enabled */ ?>
<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '<?=$yourportfolio->site['google_analytics']['account']?>']);
<? if (!$yourportfolio->site['swfobject']['address']) : /* makes no use of SWFAddress */ ?>
	_gaq.push(['_trackPageview']);
<? endif; /* end makes no use of SWFAddress */ ?>

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>
<? endif; /* end google analytics */ ?>
<? foreach($canvas->scripts as $script) : /* page needs script files */ ?>
	<script language="<?=$script['lang']?>" type="<?=$script['type']?>" src="<?=$system->base_url?><?=CORE_SCRIPTS.$script['file'].$script['ext']?><? if ($script['nocache']) : ?>?c=<?=filectime(CORE_SCRIPTS.$script['file'].$script['ext'])?><? endif; ?><? if (!empty($script['query'])) : /* has script query */ ?>?<?=$script['query']?><? endif; /* end has script query */ ?>"></script>
<? endforeach; /* end load script files */ ?>
<? foreach($canvas->raw_scripts as $script) : /* page script */ ?>
	<script language="<?=$script['lang']?>" type="<?=$script['type']?>"><?=$script['script']?></script>
<? endforeach; /* end script */ ?>
<? if ($yourportfolio->site['rss']['show']) : ?>
<? if (!$yourportfolio->settings['rss_news_only']) : /* news rss only - skip regular rss */ ?>
	<link rel="alternate" type="application/rss+xml" title="<?=$canvas->filter($yourportfolio->title)?> RSS" href="<?=$canvas->rssUrl(null, null, null, $language)?>" />
<? endif; /* end news rss only */ ?>
<? if ($yourportfolio->settings['news_templates']) : /* has news templates */ ?>
<? $rss_albums = $yourportfolio->fetchNewsAlbums(); ?>
<? foreach ($rss_albums as $rss_album) : ?>
	<link rel="alternate" type="application/rss+xml" title="<?=$canvas->filter($yourportfolio->title.' | '.$rss_album->getName())?> RSS" href="<?=$canvas->rssUrl($rss_album, null, null, $language)?>" />
<? endforeach; ?>
<? endif; /* end has news templates */ ?>
<? endif; ?>
<? if (file_exists('favicon.ico')) : /* has own favion */ ?>
	<link rel="shortcut icon" href="<?=$system->base_url?>favicon.ico" />
<? else : /* load yp favion */ ?>
	<link rel="shortcut icon" href="<?=$system->base_url?><?=CORE_IMAGES?>favicon.ico" />
<? endif; /* end favicon */ ?>
</head>
<? if ($canvas->showBody) : /* show body tag */ ?>
<body <?=$canvas->generateBodyTags()?>>
<? endif; /* end show body tag */ ?>
