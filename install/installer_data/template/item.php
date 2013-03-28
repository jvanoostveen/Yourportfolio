<tr>
<td valign="top" witdth="<?=$IMAGE->width?>">
<? if (!$IMAGE->isEmpty()) : ?><img src="<?=$CONTENT_PATH?><?=$IMAGE->sysname?>" width="<?=$IMAGE->width?>" height="<?=$IMAGE->height?>" alt="<?=$IMAGE->alt?>" style="padding-bottom: 2px;"><? endif; ?>
</td>
<td width="15"> </td>
<td valign="top">
<span class="title"><?=f($ITEM->title)?></span>
<span class="content"><?=f($ITEM->content)?></span>
</td>
</tr>
<tr>
<td  colspan="3" height="40" class="separator"> </td>
</tr>