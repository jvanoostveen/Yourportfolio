<div id="centercontent">

<table align="center">
<tr>
	<td colspan="3"><h3><?=gettext('Sinds laatste bezoek')?></h3></td>
</tr><tr>
	<td><a href="newsletter_edit.php?f=<?=AddressStatus::BOUNCED()?>" class="default fg_black"><?=gettext('bounces')?></a></td><td><img src="<?=IMAGES?>spacer.gif" width="30" height="1"/></td><td><span id="bounces">...</span></td>
</tr><tr>
	<td><a href="newsletter_edit.php?f=<?=AddressStatus::UNSUBSCRIBED()?>" class="default fg_black"><?=gettext('afmeldingen')?></a></td><td></td><td><span id="unsubscribes">...</span></td>
</tr><tr>
	<td>&nbsp;</a></td><td></td><td>&nbsp;</td>
</tr><tr>
	<td><?=gettext('nog te verwerken')?></a></td><td></td><td><span id="more">...</span></td>
</tr><tr>
	<td><img src="<?=IMAGES?>spacer.gif" height="20"></td>
</tr><tr>
	<td colspan="3"><h3><?=gettext('Totalen')?></h3></td>
</tr><tr>
	<td><?=gettext('e-mail adressen')?></td><td></td><td><?=$data['num_addresses']?></td>
</tr><tr>
	<td><?=gettext('groepen')?></td><td></td><td><?=$data['num_groups']?></td>
</tr><tr>
	<td><?=gettext('nieuwsbrieven verzonden')?></td><td></td><td><?=$data['num_sent']?></td>
</tr>
</table>

</div>