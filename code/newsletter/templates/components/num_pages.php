<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Feb 2, 2007
 */
 
$s = $data['selected_app'];
$values = array(
	'100' => array('selected' => '', 'display' => '100'),
	'200' => array('selected' => '', 'display' => '200'),
	'500' => array('selected' => '', 'display' => '500'),
	'1000' => array('selected' => '', 'display' => '1000'),
	'-1'   => array('selected' => '', 'display' => _('Alles'))
);

$values[$s]['selected'] = ' selected';

$filter = isset($data['filter']) ? $data['filter'] : 0;

?>
<div id="numpages">
<?=_('Adressen per pagina')?>:&nbsp;&nbsp;<select name="results_per_page" id="results_per_page" onchange="setPagination(<?=$filter?>);">
<?PHP
	foreach( array_keys($values) as $val )
	{
		?><option value="<?=$val?>"<?=$values[$val]['selected']?>><?=$values[$val]['display']?></option><?PHP
	} 
?>
</select>
</div>
