<?PHP
$class = '';
if( $row['status'] == 2 && $row['status_param'] >= $data['error_threshold'] )
{
	$class = "for_delete";
} else if( $row['status'] == 2 ) {
	$class = "marked";
}
?>
<tr id="tr<?=$row['address_id']?>">
<td><?PHP if ( $class == 'marked') : ?><img src="design/img/error.gif"><?PHP else : ?>&nbsp;<?PHP endif; ?></td>
<td><span class="<?=$class?>" id="name<?=$row['address_id']?>" onclick="e('name','<?=$row['address_id']?>',this)"><?=$row['name']?></span></td>
<td><span class="<?=$class?>" id="addr<?=$row['address_id']?>" onclick="e('addr','<?=$row['address_id']?>',this)"><?=$row['address']?></span></td>
<td nowrap><span class="<?=$class?>"><?=$canvas->readableDate($row['created'])?></span></td>
<td><a href="#" onclick="d(<?=$row['address_id']?>)"><img src="design/img/btn_trash.gif" align="absmiddle" style="padding-left: 5px;"></a></td>
</tr>
