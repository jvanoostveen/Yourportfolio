<item>
	<title><![CDATA[<?=$canvas->filter($item->getName())?>]]></title>
	<description><![CDATA[
<?PHP if (file_exists(YOURPORTFOLIO_DIR.$item->id.'.jpg')) : /* item has yp preview file */ ?>
	<img src="http://<?=DOMAIN?><?=$system->base_url?>assets/yourportfolio/<?=$item->id?>.jpg" border="0" align="left" style="padding-right: 8px; padding-bottom: 5px;">
<?PHP endif; /* end item has yp preview file */ ?>
<?PHP if (file_exists(YOURPORTFOLIO_DIR.'item-'.$item->id.'.jpg')) : /* item has yp preview file */ ?>
	<img src="http://<?=DOMAIN?><?=$system->base_url?>assets/yourportfolio/item-<?=$item->id?>.jpg" border="0" align="left" style="padding-right: 8px; padding-bottom: 5px;">
<?PHP endif; /* end item has yp preview file */ ?>
	<?=$canvas->filter($custom_data.$item->getText())?>]]></description>
	<link><![CDATA[http://<?=DOMAIN.$canvas->url($album, $section, $item, $language)?>]]></link>
	<pubDate><?=date("r", $item->modified)?></pubDate>
</item>
