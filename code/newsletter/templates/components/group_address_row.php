<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Jan 13, 2007
 */
?>
<tr id="tr<?=$u['address_id']?>">
<td width="25"><input type="checkbox" style="margin-left: 10px;" id="check<?=$u['address_id']?>" name="users[<?=$u['address_id']?>]"<?=$checked?>></td> 
<td><?=$u['name']?></td>
<td><?=$u['address']?></td>
</tr>
