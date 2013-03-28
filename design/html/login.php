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
 * login template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
?>
<table width="98%" height="98%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="50%">
		&nbsp;
		</td>
		<td>

<table width="325" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td width="1"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
	<td>
	<table width="309" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td width="30"><img src="<?=$canvas->showIcon('menu_header')?>" width="30" height="28" class="special"></td>
		<td class="namebar" valign="middle">login</td>
	</tr>
	</table>
	</td>
	<td width="10" bgcolor="black"><img src="<?=IMAGES?>black_spacer.gif" width="10" height="28" class="special"></td>
	<td width="4" valign="top"><img src="<?=IMAGES?>round_right.gif" width="4" height="28" class="special"></td>
	<td width="1" valign="top"><img src="<?=IMAGES?>round_row.gif" width="1" height="28" class="special"></td>
</tr>
<tr>
	<td width="1" class="verticalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
	<td valign="top" bgcolor="#FFFFFF">


		<form action="<?=$system->url?>" method="post" enctype="application/x-www-form-urlencoded" name="loginForm" onsubmit="return submitLogin();">
		<input type="hidden" name="shieldForm[login][challenge]" value="<?=$challenge['id']?>">
		<input type="hidden" id="challenge" value="<?=$challenge['string']?>">
		<input type="hidden" id="password_hash" name="shieldForm[login][password]">
		<table width="100%" border="0" cellpadding="6" cellspacing="0">
		<tr>
			<td>
			<?=gettext('Voer uw login en wachtwoord in om toegang te krijgen tot het systeem.')?>
			<br>
<? if (!empty($shield->feedback)) : /* has some feedback */ ?>
			<br><span class="fg_red bold"><?=$shield->feedback?></span>
<? endif; /* end show feedback */ ?>
			<input type="hidden" name="targetObj" value="shield">
			<input type="hidden" name="formName" value="shield">
			
			<input type="hidden" name="shieldForm[action]" value="login">
			<table width="250" border="0" cellpadding="3" cellspacing="0" align="center">
			<tr>
				<td nowrap class="edittext" width="10%" align="right"><?=gettext('login')?>:</td>
				<td><input type="text" id="login" name="shieldForm[login][login]" size="20"></td>
			</tr>
			<tr>
				<td nowrap class="edittext" width="10%" align="right"><?=gettext('wachtwoord')?>:</td>
				<td><input type="password" id="password" size="20"></td>
			</tr>
			<tr>
				<td nowrap class="edittext" width="10%" align="right">&nbsp;</td>
				<td>
				<a href="#" onClick="submitLogin(event);" class="save_black"><?=gettext('log in')?></a>
  				<input type="image" src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0">
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</form>
		<script type="text/javascript" language="javascript">
		<!--
		document.loginForm.login.focus();
		//-->
		</script>


	</td>
	<td width="10" bgcolor="#FFFFFF"><img src="<?=IMAGES?>spacer.gif" width="10" height="28" class="special" class="special"></td>
	<td width="4" valign="top" bgcolor="#FFFFFF"><img src="<?=IMAGES?>spacer.gif" width="4" height="28" class="special"></td>
	<td width="1" class="verticalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
</tr>
<tr>
	<td width="1" height="1" class="dotline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
	<td width="1" height="1" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" border="0" class="special"></td>
	<td width="10" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="10" height="1" class="special" class="special"></td>
	<td width="4" valign="top" class="horizontalline"><img src="<?=IMAGES?>spacer.gif" width="4" height="1" class="special"></td>
	<td width="1" class="dotline"><img src="<?=IMAGES?>spacer.gif" width="1" height="1" class="special"></td>
</tr>
</table>

<noscript><span class='fg_red bold'><?php echo _('Javascript is vereist om te kunnen inloggen...'); ?></span></noscript>

		</td>
		<td width="50%">
		&nbsp;
		</td>
	</tr>
</table>
