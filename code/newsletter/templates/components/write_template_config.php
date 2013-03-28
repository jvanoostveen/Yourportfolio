<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Dec 12, 2006
 */
?>
<table align="center">
	<tr>
		<td align="right"><b><?=gettext('Onderwerp')?>:</b></td>
		<td><input type="text" name="subject" value="<?=$subject?>"></td>
	</tr><tr>
		<td align="right"><b><?=gettext('Nieuwsbrief titel')?>:</b></td>
		<td><input type="text" name="title" value="<?=$title?>"></td>
	</tr><tr>
		<td align="right"><b><?=gettext('Afzender naam')?>:</b></td>
		<td><input type="text" name="sender" value="<?=$sender?>"></td>
	</tr><tr>
		<td align="right"><b><?=gettext('Editie / datum')?>:</b></td>
		<td><input type="text" name="edition" value="<?=$edition?>"></td>
	</tr>
</table>