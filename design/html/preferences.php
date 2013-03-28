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
 * preferences template
 *
 * @package yourportfolio
 * @subpackage PageHtml
 * @version $Revision: 1.1.2.7 $
 * @date $Date: 2005/02/10 13:56:42 $
 */
?>
<?=$canvas->filter($yourportfolio->feedback)?>

<form action="<?=$system->thisFile()?>" method="POST" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="prefs">

<input type="hidden" name="prefsForm[action]" value="preferences_save">


<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="150" nowrap align="right">&nbsp;</td>
	<td width="100%">&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<b><?=gettext('site voorkeuren')?></b>
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('titel')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[title]" id="title" value="<?=$canvas->edit_filter($yourportfolio->preferences['title'])?>" size="40">
	</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('beschrijving')?>:
	</td>
	<td>
	<textarea name="prefsForm[description]" id="description" cols="40" rows="5"><?=$canvas->edit_filter($yourportfolio->preferences['description'])?></textarea>
	</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('keywords')?>:
	</td>
	<td>
	<textarea name="prefsForm[keywords]" id="keywords" cols="40" rows="5"><?=$canvas->edit_filter($yourportfolio->preferences['keywords'])?></textarea>
	</td>
</tr>
<tr>
	<td nowrap align="right" valign="top">
	<?=gettext('copyright')?>:
	</td>
	<td>
	<textarea name="prefsForm[copyright]" id="copyright" cols="40" rows="5"><?=$canvas->edit_filter($yourportfolio->preferences['copyright'])?></textarea>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<b><?=gettext('contactgegevens')?></b>
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('voornaam')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[firstname]" id="firstname" value="<?=$canvas->edit_filter($yourportfolio->preferences['firstname'])?>" size="40">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('achternaam')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[lastname]" id="lastname" value="<?=$canvas->edit_filter($yourportfolio->preferences['lastname'])?>" size="40">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('e-mail')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[email]" id="email" value="<?=$canvas->edit_filter($yourportfolio->preferences['email'])?>" size="30">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('telefoon')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[phone]" id="phone" value="<?=$canvas->edit_filter($yourportfolio->preferences['phone'])?>" size="20">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('mobiel')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[mobile]" id="mobile" value="<?=$canvas->edit_filter($yourportfolio->preferences['mobile'])?>" size="20">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('fax')?>:
	</td>
	<td>
	<input type="text" name="prefsForm[fax]" id="fax" value="<?=$canvas->edit_filter($yourportfolio->preferences['fax'])?>" size="20">
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<b><?=gettext('inloggegevens')?></b>
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('login')?>:
	</td>
	<td>
	<input type="hidden" name="prefsForm[old_login]" value="<?=$yourportfolio->preferences['login']?>">
	<input type="text" name="prefsForm[login]" id="login" value="<?=$yourportfolio->preferences['login']?>" size="30" disabled><!-- (a-z, 0-9, _) -->
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('wachtwoord')?>:
	</td>
	<td>
	<input type="hidden" name="prefsForm[old_password]" value="<?=$yourportfolio->preferences['password']?>">
	<input type="password" name="prefsForm[password_1]" id="password_1" value="" size="30">
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?=gettext('controle wachtwoord')?>:
	</td>
	<td>
	<input type="password" name="prefsForm[password_2]" id="password_2" value="" size="30">
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>
	<b><?php echo _('beheer voorkeuren'); ?></b>
	</td>
</tr>
<tr>
	<td nowrap align="right">
	<?php echo _('taal'); ?>:
	</td>
	<td>
		<select id="language" name="prefsForm[language]" <?php echo ($yourportfolio->config_db ? '' : 'disabled'); ?>>
			<option value="nl_NL"><?php echo _('Nederlands')?></option>
			<option value="en_GB"><?php echo _('Engels')?></option>
<?php if ($yourportfolio->session['master']) : ?>
			<option value="de_DE"><?php echo _('Duits')?></option>
			<option value="fr_FR"><?php echo _('Frans')?></option>
<?php endif; ?>
		</select>
		<script type="text/javascript">
		$(function() {
			var language = '<?php echo $yourportfolio->display_language; ?>';
			$('#language option[value=' + language + ']').attr('selected', true);
		});
		</script>
	</td>
</tr>
<tr>
	<td nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>