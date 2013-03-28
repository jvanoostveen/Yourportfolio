<h2><?=_('Resultaten')?></h3>
<p>
<table>
<tr>
	<td align="left"><?=_('Toegevoegd')?>:</td><td><?=$params['addcnt']?></td>
</tr>
<tr>
	<td align="left"><?=_('Al bestaand')?>:</td><td><?=count($params['skipped'])?></td>
</tr>
<tr>
	<td align="left"><?=_('Ongeldig')?>:</td><td><?=count($params['invalid'])?></td>
</tr>
</table>
</p>
<?php
if( count( $params['skipped'] ) > 0 )
{
	?><h3><?=_('Al bestaande adressen')?></h3><ul><?php
	foreach( $params['skipped'] as $addr )
	{
		echo '<li>'.$addr . '</li>';
	}
	echo '</ul>';
}

if( count( $params['invalid'] ) > 0 )
{
	?><h3><?=_('Ongeldige adressen')?></h3><ul><?php
	foreach( $params['invalid'] as $addr )
	{
		echo '<li>'.$addr . '</li>';
	}
	echo '</ul>';
	
}
?>