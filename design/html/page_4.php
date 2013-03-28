<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * main page used for mozilla != 5 browsers
 *
 * @package yourportfolio
 * @subpackage HTML
 * @version $Revision$
 * @date $ReleaseDate$
 */
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="12" height="12"><img src="<?=IMAGES?>spacer.gif" width="12" height="12"></td>
	<td width="176" height="12"><img src="<?=IMAGES?>spacer.gif" width="176" height="12"></td>
	<td height="12"><img src="<?=IMAGES?>spacer.gif" width="1" height="12"></td>
	<td width="12" height="12"><img src="<?=IMAGES?>spacer.gif" width="12" height="12"></td>
</tr>
<tr>
	<td width="12" height="1"><img src="<?=IMAGES?>spacer.gif" width="12" height="1"></td>
	<td width="200" valign="top">

<table width="200" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="1"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	<td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('menu_header')?>" width="30" height="28" class="special"></td>
		<td class="bg_black fg_white" valign="middle">
		<!-- photographer name -->
		<a href="http://<?=DOMAIN?><?=(SUB_DOMAIN) ? '/'.SUB_DOMAIN : ''?>" target="_blank" class="fg_white txt_no_underline"><?=$canvas->filter($yourportfolio->photographer_name)?></a>
		<!-- end photographer name -->
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td width="1" class="verticalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
	<td valign="top">
	<!-- menu -->
<? if (!$yourportfolio->session['limited']) : /* show normal menu */ ?>
<? require(HTML.'menu.php'); ?>
<? else : /* show limited menu */ ?>
<? require(HTML.'menu_subuser.php'); ?>
<? endif; /* end show limited menu */ ?>
	<!-- end menu -->
	</td>
</tr>
<tr>
	<td width="1" height="1" class="horizontalline" colspan="2"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
</tr>
</table>

</td>
<td valign="top">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="canvas_div">
<tr>
	<td><? require(HTML.'page_top.php'); ?></td>
</tr>
<tr>
	<td class="canvas_content" valign="top">
	<img src="<?=IMAGES?>spacer.gif" width="1" height="650" align="left">
		<!-- current view -->
<? require(HTML.$canvas->inner_template.".php"); ?>
		<!-- end current view name -->
	</td>
</tr>
<tr>
	<td>
<? require(HTML.'page_bottom.php'); ?>
	</td>
</tr>
</table>


	</td>
	<td width="12" height="1"><img src="<?=IMAGES?>spacer.gif" width="12" height="1"></td>
</tr>
</table>
<br>
<br>