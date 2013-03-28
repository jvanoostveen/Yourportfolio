<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Dec 12, 2006
 */
?>


<table class="templateSelect" align="center">
<tr><td>
<form enctype="multipart/form-data" name="adddItemForm" method="post" action="newsletter_write.php">
<input type="hidden" name="case" value="addItem">
<input type="hidden" name="letter_id" value="<?=$data['letter']['letter_id']?>">
<table>
	<tr>
		<td><b><?=_('Titel')?>:</b></td>
		<td><input type="text" name="title" size="25"></td>
	
	</tr><tr>
		<td><b><?=_('Link')?>:</b></td>
		<td><input type="text" name="link" size="25" value="http://"></td>
	</tr><tr>
		<td valign="top"><b><?=_('Inhoud')?>:</b></td>
		<td>
			<textarea name="content" cols="50" rows="10"><?=_('Inhoud')?></textarea>
		</td>
	</tr><tr>
		<td><b><?=_('Afbeelding')?> </b><br>(<?=_('optioneel')?>):</td>
		<td><input type="file" name="image"/></td>
	</tr><tr>
		<td colspan="2">
			<input type="submit" value="<?=_('Toevoegen')?>">
		</td>
	</tr>
</table>
</form>
</td></tr></table>