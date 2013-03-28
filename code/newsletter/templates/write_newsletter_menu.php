<tr>
	<td colspan="3" style="padding-left: 50px;">
<div id="language-switch">
<ul id="menu_list">
<? foreach ($data['tasks'] as $task => $label) : /* task loop */ ?>
	<li><a href="javascript:switchTo('<?=$task?>');" <?=($data['task'] == $task ? 'class="active"' : '')?>><?=$label?></a></li>
<? endforeach; /* end task loop */ ?>
</ul>
</div>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
