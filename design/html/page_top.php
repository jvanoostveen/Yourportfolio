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
 * top bar
 *
 * @package yourportfolio
 * @subpackage PageHtml
 * @version $Revision: 1.1.2.1 $
 * @date $Date: 2005/02/23 11:45:03 $
 */
?>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1" height="28"><img src="<?=IMAGES?>white_spacer.gif" width="1" height="28"></td>
		<td class="namebar">
		<!-- current view name -->
		<img src="<?=$canvas->showIcon($canvas->icon)?>" width="31" height="28" border="0" align="absmiddle"><? if (!empty($yourportfolio->title_url)) : ?><a href="<?=$yourportfolio->title_url?>" target="_blank" class="fg_white txt_no_underline"><? endif; ?><?=$canvas->filter($yourportfolio->title)?><? if (!empty($yourportfolio->title_url)) : ?></a><? endif; ?>
		<!-- end current view name -->
		</td>
<? if ($yourportfolio->back_url) : /* can go back / cancel action */ ?>
		<td class="namebar" align="right"><a href="<?=$yourportfolio->back_url?>" class="save"><?=gettext('annuleer')?></a></td>
		<td width="30" class="namebar"><img src="<?=IMAGES?>black_spacer.gif" width="30" height="28"></td>
<? endif; /* end can go back */ ?>
<? if ($yourportfolio->save_url) : /* show save button */ ?>
		<td width="51" align="right" class="namebar"><a href="<?=$yourportfolio->save_url?>" class="save"><?=gettext('bewaar')?></a></td>
<? endif; /* end show save button */ ?>
		<td width="22" class="namebar" align="right"><img src="<?=IMAGES?>spacer.gif" width="16" height="16" id="progress1"></td>
		<td width="10"><img src="<?=IMAGES?>black_spacer.gif" width="10" height="28" class="special" class="special"></td>
		<td width="4" valign="top"><img src="<?=IMAGES?>round_right.gif" width="4" height="28" class="special"></td>
		<td width="1" valign="top"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	</tr>
	</table>
