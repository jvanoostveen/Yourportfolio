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
 * bottom bar
 *
 * @package yourportfolio
 * @subpackage PageHtml
 */
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1" class="verticalline"><img src="<?=IMAGES?>black_spacer.gif" width="1" height="1"></td>
		<!-- upload photo link -->
		<td class="namebar" width="200">
<? if ($yourportfolio->upload_link) : /* has upload link */ ?>
<?php
	$upload_url = 'album.php?aid=0';
	$upload_label = _('nieuw album');
	if (isset($_GET['aid']))
	{
		$upload_url = 'section.php?aid='.$_GET['aid'].'&sid=0';
		$upload_label = _('nieuwe sectie');
	}
	if (isset($_GET['sid']))
	{
		$upload_url = 'upload.php?aid='.$_GET['aid'].'&sid='.$_GET['sid'];
		$upload_label = _('nieuw item');
	}
?>
		<a href="<?php echo $upload_url; ?>" class="upload" accesskey="n"><img src="<?=$canvas->showIcon('item_upload')?>" width="31" height="28" border="0" align="absmiddle"><?php echo $upload_label; ?></a>
<? endif; /* end has upload link */ ?>
<? if ($yourportfolio->news_link) : /* has news link */ ?>
		<a href="section.php?aid=<?=$album->id?>&sid=0" class="upload" accesskey="n"><img src="<?=$canvas->showIcon('news_upload')?>" width="31" height="28" border="0" align="absmiddle"><?=gettext('voeg nieuw nieuwsitem toe')?></a>
<? endif; /* end has upload link */ ?>
		&nbsp;
		<!-- end upload photo link -->
		</td>
<? if ($yourportfolio->upload_dir) : /* show upload dir link */ ?>
		<td class="namebar" width="200">
		<a href="javascript:openParser(<?=$album->id?>,<?=$section->id?>);" class="upload"><img src="<?=$canvas->showIcon('folder_import')?>" width="34" height="28" border="0" align="absmiddle"><?=gettext('upload meerdere items')?></a></td>
<? endif; /* end upload dir link */ ?>
		<td class="namebar">&nbsp;</td>
<? if ($yourportfolio->back_url) : /* can go back / cancel action */ ?>
		<td class="namebar" align="right"><a href="<?=$yourportfolio->back_url?>" class="save"><?=gettext('annuleer')?></a></td>
		<td width="30" class="namebar"><img src="<?=IMAGES?>black_spacer.gif" width="30" height="28"></td>
<? endif; /* end can go back */ ?>
<? if ($yourportfolio->save_url) : /* show save button */ ?>
		<td width="51" align="right" class="namebar"><a href="<?=$yourportfolio->save_url?>" class="save" accesskey="s"><?=gettext('bewaar')?></a></td>
<? endif; /* end show save button */ ?>
		<td width="22" class="namebar" align="right"><img src="<?=IMAGES?>spacer.gif" width="16" height="16" id="progress2"></td>
		<td width="10"><img src="<?=IMAGES?>black_spacer.gif" width="10" height="28" class="special"></td>
		<td width="4" valign="top"><img src="<?=IMAGES?>round_right_down.gif" width="4" height="28" class="special"></td>
		<td width="1" valign="top"><img src="<?=IMAGES?>round_row_down.gif" width="1" height="28" class="special"></td>
	</tr>
	</table>
