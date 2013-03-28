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
<? foreach ($items as $item) : /* loop sections */ ?>
<?PHP
if (YP_MULTILINGUAL)
{
	$item->loadLanguageStrings();
	
	if ($item->isEmpty())
	{
		continue;
	}
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
<? endforeach; /* end section loop */ ?>
</channel>
</rss>