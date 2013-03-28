<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Feb 19, 2007
 */
?>

<div style="float: right; margin-right: 10px; margin-left: 10px; margin-top: 4px;">
<?PHP
if( $data['group_only'] == 1)
{
	?><a href="javascript:void(0)" onclick="switchView('all')" class="save"><?=gettext('alle adressen')?></a><?PHP
} else {
	?><a href="javascript:void(0)" onclick="switchView('members')" class="save"><?=gettext('alleen leden')?></a><?PHP
}	
?>
</div>	