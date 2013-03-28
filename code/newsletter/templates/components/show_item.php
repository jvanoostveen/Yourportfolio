<?PHP
/*
 * Project: yourportfolio / newsletter
 *
 * @author Christiaan Ottow
 * @created Dec 12, 2006
 */
?>

<table>
<tr><td>

<table width="500" align="center" style="border: 1px solid #888888;" cellpadding="3" cellspacing="0">
	<tr>
		<td style="background-color: #eeeeee;" colspan="2"><b><a href="<?=$i['link']?>"><?=$i['title']?></a></b></td>
	</tr>
		<td><?=$i['content']?></td>
		<td></td>
	</tr>
</table>

</td><td valign="top">
<a href="#" onclick="moveItem(<?=$i['item_id']?>,'up')"><?=_('up')?></a><br>
<a href="#" onclick="moveItem(<?=$i['item_id']?>,'down')"><?=_('down')?></a><br>
<a href="#" onclick="delItem(<?=$i['item_id']?>)"><?=_('delete')?></a><br>
<a href="newsletter_write.php?case=edit&a=<?=$id?>&item=<?=$i['item_id']?>"><?=_('edit')?></a><br>

</td></tr>
</table>
<br>