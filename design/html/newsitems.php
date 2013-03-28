<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name: rel_2-5-23 $
 */

/**
 * shows sections as newsitems
 *
 * @package yourportfolio
 * @subpackage HTML
 */
?>
&nbsp;
<br>

<? if (!empty($album->sections)) : /* has sections to show */ ?>
<? foreach($album->sections as $section) : /* section loop */ ?>
<? if (empty($section['name'])) :
	$tmp_section = new Section();
	$tmp_section->id = $section['id'];
	$tmp_section->load();
	
	if (!empty($tmp_section->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_section->strings['name']));
		$section['name'] = $tmp_section->strings['name'][$first_language]['string_parsed'];
	} else {
		$section['name'] = _('geen titel');
	}

endif;
?>

<table width="400" height="20" border="0" cellpadding="0" cellspacing="0">
<tr class="bg_black">
	<td width="250" height="20">
	<a href="<?=$system->file?>?aid=<?=$album->id?>&switch=<?=$section['id']?>"><img src="<?=IMAGES?>photo_<?=($section['online'] == 'Y') ? 'online' : 'offline'?>.gif" width="20" height="20" align="absmiddle" border="0"></a><a href="section.php?aid=<?=$album->id?>&sid=<?=$section['id']?>&mode=edit" class="txt_medium fg_white default"><?=$canvas->filter($section['name'], 40)?></a>
	</td>
	<td class="txt_medium fg_white" align="right">
	<?=$canvas->readableDate($section['section_date'], true, false)?>
	</td>
</tr>
</table>
<br>
<? endforeach; /* end section loop */ ?>
<? endif; /* end has sections to show */ ?>
