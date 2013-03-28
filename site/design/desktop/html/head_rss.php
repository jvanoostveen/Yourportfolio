<?php
if ($yourportfolio->site['rss']['show']) :
	if (!$yourportfolio->settings['rss_news_only']) : /* news rss only - skip regular rss */
?>
	<link rel="alternate" type="application/rss+xml" title="<?=$canvas->filter($yourportfolio->title)?> RSS" href="<?=$canvas->rssUrl(null, null, null, $GLOBALS['YP_CURRENT_LANGUAGE'])?>" />
<?php
	endif; /* end news rss only */
	if ($yourportfolio->settings['news_templates']) : /* has news templates */
		$rss_albums = $yourportfolio->fetchNewsAlbums();
		foreach ($rss_albums as $rss_album) :
?>
	<link rel="alternate" type="application/rss+xml" title="<?=$canvas->filter($yourportfolio->title.' | '.$rss_album->getName())?> RSS" href="<?=$canvas->rssUrl($rss_album, null, null, $GLOBALS['YP_CURRENT_LANGUAGE'])?>" />
<?php
		endforeach;
	endif; /* end has news templates */
endif;
