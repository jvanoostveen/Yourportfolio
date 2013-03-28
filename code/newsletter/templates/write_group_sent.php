<?PHP
/*
 * Project: yourportfolio
 *
 * @created Nov 14, 2006
 * @author Christiaan Ottow
 * @copyright Christiaan Ottow
 */

$subject_img = '';
$created_img = '';
$datesent_img = '';

$varname = $ordering['field'] . '_img';
$$varname = '&nbsp;<img border="0" alt="sort" src="design/img/btn_sort_'.strtolower($ordering['dir']).'.gif"/>';

if (is_array( $data['letters']) && count($data['letters']) > 0 ) : ?>
<table class="listing" cellspacing="0" cellpadding="0" width="100%" border="0">
<tr>
	<th width="20"><img src="<?=IMAGES?>spacer.gif" width="20" height="20"></th>
	<th width="50%"><a class="black" href="?case=group&g=sent&o_f=subject&o_l=sent"><?=_('onderwerp')?><?=$subject_img?></a></th>
	<th width="150" nowrap class="black"><?=_('geadresseerden')?></th>
	<th width="150" nowrap><a class="black" href="?case=group&g=sent&o_f=datesent&o_l=sent"><?=_('datum verstuurd')?><?=$datesent_img?></a></th>
	<th width="150" nowrap><a class="black" href="?case=group&g=sent&o_f=created&o_l=sent"><?=_('datum toegevoegd')?><?=$created_img?></a></th>
	<th width="20">&nbsp;</th>
</tr>
<?PHP foreach( $data['letters'] as $letter ) : /* loop newsletters */ ?>
<tr>
<td>&nbsp;</td>
<td>
	<a href="newsletter_view.php?nid=<?=$letter['letter_id']?>" class="default fg_black txt_medium" target="_blank"><?=$letter['subject']?></a>
</td>
<td><?=(isset($data['stats'][$letter['letter_id']]) ? $data['stats'][$letter['letter_id']]['addressees'] : '-')?></td>
<td><?=$canvas->readableDate($letter['modified'], true)?></td>
<td><?=$canvas->readableDate($letter['created'], true)?></td>
<td>
	<a href="#" onclick="duplicateLetter(<?=$letter['letter_id']?>)" class="default">
		<img src="design/img/btn_link_extern.gif" width="15" height="15" border="0" align="absmiddle">
	</a>
</td>
</tr>
<?PHP endforeach; /* end loop newsletters */ ?>
</table>
<form name="newsletterForm" action="newsletter_write.php" method="post">
<input type="hidden" name="target" value="newsletter">
<input type="hidden" name="data[action]" value="duplicate">
<input type="hidden" name="data[id]" id="newsletter_id" value="">
</form>

<?PHP else : ?>
	<b><?=_('Er zijn geen nieuwsbrieven in deze map')?></b>
<?PHP endif; ?>
