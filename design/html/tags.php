<div style="width: 770px; overflow: auto;">
<script type="text/javascript">
var groups = Array();
var tags = Array();
</script>

<div style="width: 350px; margin-left: 10px; float: left; background-color: #eeeeee;">
<center><h3>Tags</h3></center>
</div>
<div style="width: 370px; margin-left: 22px; clear: right; float: left;">
<center><h3>&nbsp;</h3></center>
</div>
<div style="margin: 20px 10px 2px 10px; padding: 0px; width: 350px; border: 1px solid #c0c0c0; float: left;">
<div style="background-color: #f9f9f9; padding: 10px;">
	Nieuwe groep toevoegen: <br><img src="<?=IMAGES?>spacer.gif" width="15" height="1"/><input type="text" onkeyup="javascript:check_add_group();" id="newGroupInput">
	<a href="javascript:add_group();"><img src="<?=IMAGES?>btn_new.gif" border="0"></a><br>
</div>
<div style="margin: 0px; padding: 10px 10px 0px 10px; float: left;">
<?
	$yourportfolio->loadTags();
?>

<?
	foreach( $yourportfolio->tags as $group ) :
?>
		<div onmouseover="showEdit( <?=$group['id']?> );" onmouseout="hideEdit( <?=$group['id']?> );">
		<b><?=$group['name']?></b>
		<span style="visibility: hidden;" id="group_<?=$group['id']?>">
			<img src="<?=IMAGES?>spacer.gif" width="15" height="1"/>
			<a href="javascript:renameGroup(<?=$group['id']?>, '<?=$group['name']?>');">
				<img valign="bottom" border="0" src="<?=IMAGES?>btn_edit.gif"></a>
			<a href="javascript:deleteGroup(<?=$group['id']?>);">
				<img valign="middle" border="0" src="<?=IMAGES?>btn_trash.gif"/>
			</a>
		</span>
		</div>
		<div style="margin-left: 15px;">
		<? if( !empty( $group['tags'] ) ) : ?>
		<?
		foreach( $group['tags'] as $tag ) :
			?>
			<span onmouseover="javascript:tag_edit(<?=$tag['id']?>,'<?=addslashes($tag['tag'])?>');" id="span_<?=$tag['id']?>"><?=$tag['tag']?><img src="<?=IMAGES?>" height="10" width="1"/></span>
			<span style="display: inline; visibility: hidden;" id="edit_tag_<?=$tag['id']?>">
				<img src="<?=IMAGES?>spacer.gif" width="15" height="1"/>
				<a href="javascript:rename_tag(<?=$tag['id']?>,'<?=addslashes($tag['tag'])?>');"><img border="0" valign="bottom" src="<?=IMAGES?>btn_edit.gif"></a>
				<a href="javascript:delete_tag(<?=$tag['id']?>);"><img border="0" valign="middle" src="<?=IMAGES?>btn_trash.gif"></a>
				<select name="groups" onchange="move_tag(<?=$tag['id']?>, this.options[this.selectedIndex].value)">
				<? foreach( $yourportfolio->tags as $g ) : ?>
					<option value="<?=$g['id']?>"<? if( $tag['group_id'] == $g['id'] ) : echo ' selected'; endif;?>><?=$g['name']?></option>
				<? endforeach; ?>
				</select>
			</span><br/>
			<?
		endforeach;
		endif;
		?>
		<input type="text" name="newTag" id="newTag_<?=$group['id']?>" onchange="javascript:add_tag(<?=$group['id']?>)" />
			<a href="javascript:add_tag(<?=$group['id']?>);"><img src="<?=IMAGES?>btn_new.gif" border="0"></a>
		</div>
		<br/>
		<?
	endforeach;
	?>
</div>
</div>

<div style="visibility: hidden; display: none;">
<form action="<?=$system->thisFile()?>" method="POST" enctype="multipart/form-data" name="tagsForm" id="tagsForm">
	<input type="hidden" name="targetObj" value="yourportfolio" />
	<input type="hidden" name="formName" value="tags" />
	<input type="hidden" name="tagsForm[action]" id="tagsAction" value="" />
	<input type="hidden" name="tagsForm[param1]" value="" id="tagsParam1" />
	<input type="hidden" name="tagsForm[param2]" value="" id="tagsParam2" />
</form>
</div>
</div>