<script type="text/javascript">
var filter = <?=$filter?>;
var page = <?=$data['page']?>;
var show_new = <?=($data['show_new']===true? 'true' : 'false')?>;
var unused_only = <?=(isset($data['unused_only']) && $data['unused_only'] === true ? 'true' : 'false')?>;
var groups = {
<?PHP
	$groups = $data['groups'];
	$last_group = $groups[count($groups) - 1];
	
	if (!empty($groups))
	{
		foreach($groups as $g)
		{
			$id = $g['group_id'];
			$name = addslashes($g['name']);
			echo "	$id: '$name'";
			if ( $g != $last_group )
			{
				echo ",\n";
			}
		}
	}
?>

};
</script>

<div id="msg">
<?php
if( isset( $data['message'] ) && !empty( $data['message'] ) )
{
	echo $data['message'];
	?>
	<div class="button" style="margin-top: 20px;"><a href="javascript:void(0)" onclick="javascript:loadInitial();" class="upload"><?=_('verder')?></a></div>	
	<?php
} else {
	echo _('De adressen worden geladen ...');
	?><script language="Javascript">load_immediately = true;</script><?php
}
?>
</div>

<div id="results">

</div>

<div style="visibility: hidden; display: none;">
	<form name="delform" action="<?=ROOT_URL?>/newsletter_edit.php?f=<?=$data['filter']?>" method="post">
		<input type="hidden" name="case" value="delete_item">
		<input type="hidden" name="id" value="" id="del_id">
	</form>
	
	<form name="newform" method="post" action="<?=ROOT_URL?>/newsletter_edit.php" onsubmit="formSubmit()">
		<input type="hidden" name="case" value="add_item">
		<input type="hidden" name="addr" value="" id="f_new_addr">
		<input type="hidden" name="name" value="" id="f_new_name">
		<input type="hidden" name="group_id" value="" id="f_group_id">
	</form>
	
	<form name="deleteSelectionForm" id="deleteSelectionForm" action="<?=ROOT_URL?>/newsletter_edit.php" method="post">
		<input type="hidden" name="case" value="delete_selection">
		<input type="hidden" name="ids" id="deleteSelectionFormIds" value="">
	</form>
</div>

