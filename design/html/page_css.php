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
 * main page used for mozilla 5 browsers
 *
 * @package yourportfolio
 * @subpackage PageHtml
 */
?>
<DIV id="menu">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('menu_header')?>" width="30" height="28" class="special"></td>
		<td class="namebar" valign="middle">
		<!-- photographer name -->
		<a href="http://<?=DOMAIN?><?=(SUB_DOMAIN) ? '/'.SUB_DOMAIN : ''?>" target="_blank" class="fg_white txt_no_underline"><?=$canvas->filter($yourportfolio->photographer_name)?></a>
		<!-- end photographer name -->
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid black;"><div id="menu_holder"><div id="menu_content">
	<!-- menu -->
<? if (!$yourportfolio->session['limited']) : /* show normal menu */ ?>
<? require(HTML.'menu.php'); ?>
<? else : /* show limited menu */ ?>
<? require(HTML.'menu_subuser.php'); ?>
<? endif; /* end show limited menu */ ?>
	<!-- end menu -->
	</div></div>

<? if (!$system->isIE()) : ?>
<script>
windowResized();
</script>
<? endif; ?>
	
	</td>
</tr>
<tr>
	<td width="1" height="1" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
</tr>
</table>
<? if ($yourportfolio->session['master']) : /* master account is logged in */ ?>
<div width="100%" style="text-align: right; padding: 2px 4px 0px 0px;">
<span style="color: gray; font-size=-2;">
<?=gettext('versie')?>: <?=$yourportfolio->version();?>
</span>
</div>
<? endif; /* end master account options */ ?>
</DIV>


<DIV id="canvas_top">
	<? require(HTML.'page_top.php'); ?>
</DIV>


<DIV id="canvas_div">
		<!-- current view -->
<? require(HTML.$canvas->inner_template.'.php'); ?>
		<!-- end current view name -->
</DIV>


<DIV id="canvas_bottom">
<? require(HTML.'page_bottom.php'); ?>
</DIV>
