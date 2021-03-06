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
<? foreach ($rss_contents as $rss_content) : /* loop contents */ ?>
<?PHP
if (is_a($rss_content, 'Item'))
{
	$item = $rss_content;
	
	if (YP_MULTILINGUAL)
	{
		$item->loadLanguageStrings();
		
		if ($item->isEmpty())
		{
			continue;
		}
	}
	
	if (isset($albums[$item->album_id]))
	{
		$album = $albums[$item->album_id];
	} else {
		$album = new Album();
		$album->id = $item->album_id;
		$album->load();
		
		$albums[$album->id] = $album;
	}
	
	if ($yourportfolio->site['frontend']['filter_bracket_album'])
	{
		if ($album->name{0} == '[' && $album->name{strlen($album->name) - 1} == ']')
			continue;
	}
	
	if (isset($sections[$item->section_id]))
	{
		$section = $sections[$item->section_id];
	} else {
		$section = new Section();
		$section->id = $item->section_id;
		$section->load();
		
		$sections[$item->section_id] = $section;
	}
	
	$item->loadCustomData();
	$item->loadFiles();
	
	$custom_data = '';
	if (!empty($yourportfolio->custom_fields))
	{
		foreach ($yourportfolio->custom_fields as $custom_field) /* loop thru custom fields */
		{
			$item_custom_data = $item->getCustomData($custom_field['key']);
			if (!empty($item_custom_data))
			{
				$custom_data .= $canvas->filter($custom_field['label']).': <b>'.$canvas->filter($item_custom_data).'</b><br />';
			}
		}
		
		if (!empty($custom_data))
		{
			$custom_data .= '<br />';
		}
	}
?>
<?PHP require('rss_item_node.php'); ?>
<?PHP
} else if (is_a($rss_content, 'Section'))
{
	$section = $rss_content;
	
	if (YP_MULTILINGUAL)
	{
		$section->loadLanguageStrings();
		
		if ($section->isEmpty())
		{
			continue;
		}
	}
	
	if (isset($albums[$section->album_id]))
	{
		$album = $albums[$section->album_id];
	} else {
		$album = new Album();
		$album->id = $section->album_id;
		$album->load();
		
		$albums[$album->id] = $album;
	}
?>
<item>
	<title><![CDATA[<?=$canvas->filter($section->getName())?>]]></title>
	<description><![CDATA[<?=$canvas->filter($section->getText())?>]]></description>
	<link><![CDATA[http://<?=DOMAIN.$canvas->url($album, $section, null, $language)?>]]></link>
	<pubDate><?=date("r", $section->modified)?></pubDate>
</item>
<?PHP } ?>
<? endforeach; /* end rss contents loop */ ?>
</channel>
</rss>