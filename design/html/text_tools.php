<!-- text manipulation tools -->
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td width="60"><img src="<?=IMAGES?>spacer.gif" width="60" height="1"></td>
		<td width="60"><img src="<?=IMAGES?>spacer.gif" width="60" height="1"></td>
		<td width="90"><img src="<?=IMAGES?>spacer.gif" width="90" height="1"></td>
		<td width="90"><img src="<?=IMAGES?>spacer.gif" width="90" height="1"></td>
<? if ($yourportfolio->settings['internal_links']) : ?>
		<td width="90"><img src="<?=IMAGES?>spacer.gif" width="90" height="1"></td>
<? endif; ?>
		<td><img src="<?=IMAGES?>spacer.gif" width="1" height="1"></td>
	</tr>
	<tr>
		<td><a href="javascript:htmlTag('<?=$text_tool?>','b');" class="normal"><img src="<?=IMAGES?>btn_bold.gif" alt="vetgedrukt" title="vetgedrukt" width="15" height="15" border="0" align="absmiddle"> <?=gettext('bold')?></a></td>
		<td><a href="javascript:htmlTag('<?=$text_tool?>','i');" class="normal"><img src="<?=IMAGES?>btn_italic.gif" alt="schuin" title="schuin" width="15" height="15" border="0" align="absmiddle"> <?=gettext('italic')?></a></td>
		<td><a href="javascript:externalLink('<?=$text_tool?>','email');" class="normal"><img src="<?=IMAGES?>btn_link_extern.gif" alt="e-mail link" title="e-mail link" width="15" height="15" border="0" align="absmiddle"> <?=gettext('e-mail link')?></a></td>
		<td><a href="javascript:externalLink('<?=$text_tool?>','elink');" class="normal"><img src="<?=IMAGES?>btn_link_extern.gif" alt="externe link" title="e-mail link" width="15" height="15" border="0" align="absmiddle"> <?=gettext('externe link')?></a></td>
<? if ($yourportfolio->settings['internal_links']) : ?>
		<td><a href="javascript:externalLink('<?=$text_tool?>','link');" class="normal"><img src="<?=IMAGES?>btn_link_intern.gif" alt="interne link" title="e-mail link" width="15" height="15" border="0" align="absmiddle"> <?=gettext('interne link')?></a></td>
<? endif; ?>
		<td width="2000">&nbsp;</td>
	</tr>
	</table>
<!-- text manipulation tools -->
