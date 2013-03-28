<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * XML output file for flash (xml output 2)
 *
 * @package yourportfolio
 * @subpackage XML
 */
?>
<?='<'.'?'?>xml version="1.0" encoding="ISO-8859-1" <?='?'.">\n"?>
<rss version="2.0">
<channel>
	<title><![CDATA[<?=$canvas->filter($yourportfolio->title)?>]]></title>
	<link>http://<?=DOMAIN.$canvas->url($album)?></link>
	<description><![CDATA[<?=$canvas->filter($yourportfolio->preferences['description'])?>]]></description>
	<copyright><![CDATA[<?=$canvas->filter($yourportfolio->preferences['copyright'])?>]]></copyright>
<? foreach ($album->sections as $section) : /* loop sections */ ?>
<?PHP
if ($section->isEmpty())
{
	continue;
}
?>
<item>
	<title><![CDATA[<?=$canvas->filter($section->getName())?>]]></title>
	<description><![CDATA[<?=$canvas->filter($section->getText())?>]]></description>
	<link><![CDATA[http://<?=DOMAIN.$canvas->url($album, $section, null, $language)?>]]></link>
	<pubDate><?=date("r", $section->section_date)?></pubDate>
</item>
<? endforeach; /* end section loop */ ?>
</channel>
</rss>