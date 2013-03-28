<?PHP
/*
 * Project: Yourportfolio / newsletter
 *
 * @createdOct 23, 2006
 * @author Christiaan Ottow
 * @copyright Christiaan Ottow
 */
?>
<script type="text/javascript">
var groupId = <?=$data['group'][0]['group_id']?>;
var page = <?=$data['page']?>;
var currentView = '<?=$data['currentView']?>';
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

<form name="editForm" method="post" action="newsletter_groups.php?case=saveMembers" style="margin: 0; padding: 0;">
<input type="hidden" name="app" id="app" value="<?=$data['selected_app']?>">
<input type="hidden" name="id" value="<?=$data['group'][0]['group_id']?>">
<input type="hidden" name="goto_page" id="goto_page" value="">
<input type="hidden" name="serialized_members" id="serialized_members" value="">
<input type="hidden" name="serialized_dataset" id="serialized_dataset" value="">

<input type="hidden" name="group_only" id="group_only" value="<?=(isset($_POST['group_only'])) ? $_POST['group_only'] : ( isset($_GET['group_only']) ? $_GET['group_only'] : 1 )?>">
</form>

<div id="results">
	
</div>
<div id="msg">
<?=gettext('De adressen worden geladen ...')?>
</div>

<div id="debug">
</div>