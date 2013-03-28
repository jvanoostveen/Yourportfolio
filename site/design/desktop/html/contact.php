<h1><?=$canvas->f($node->getTitle())?></h1>

<p><?=$canvas->f($node->getText())?></p>

<form action="<?=$node->url()?>" method="post" name="contactForm">
<input type="hidden" name="targetObj" value="Contact"/>
<input type="hidden" name="formName" value="contact"/>

<input type="hidden" name="contactForm[action]" id="action" value="contact"/>
<input type="hidden" name="contactForm[contact][language]" value="<?php echo $GLOBALS['YP_CURRENT_LANGUAGE']; ?>" />

<ul class="contact">
	<li>
		<input type="text" name="contactForm[contact][name]" id="ctname" onfocus="this.value= (this.value=='<?php echo _('Naam'); ?>') ? '' : this.value" onblur="this.value= (this.value=='') ? '<?php echo _('Naam'); ?>' : this.value" value="<?php echo _('Naam'); ?>" required>
	</li>
	<li><input type="email" name="contactForm[contact][email]" id="ctemail" onfocus="this.value= (this.value=='<?php echo _('E-mail'); ?>') ? '' : this.value" onblur="this.value= (this.value=='') ? '<?php echo _('E-mail'); ?>' : this.value" value="<?php echo _('E-mail'); ?>" required></li>
	<li><textarea name="contactForm[contact][message]" id="ctmessage" cols="24" rows="13"><?=$canvas->edit_filter( (isset($GLOBALS['contact']) ? $GLOBALS['contact']['message'] : '') )?></textarea></li>
	<li><input type="submit" value="<?php echo _('Verstuur'); ?>" class="submit"></li>
</ul>
<?php if (isset($GLOBALS['contact']) && isset($GLOBALS['contact']['success'])) echo "<p>" . $GLOBALS['contact']['feedback'] . "</p>" ; ?>
