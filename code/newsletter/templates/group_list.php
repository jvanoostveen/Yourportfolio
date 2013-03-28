<script type="text/javascript">
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

<?php if (is_array($data['groups']) && count($data['groups']) > 0) : ?>
<div class="grid">
<?php foreach ($data['groups'] as $group) : ?>
	<div class="griditem" id="<?php echo $group['group_id']; ?>">
		<div style="background-image: url('design/iconsets/default/section_overview.gif')" class="griditem-image">
			<a href="newsletter_groups.php?case=show&group=<?=$group['group_id']?>"><img src="design/img/spacer.gif" width="120" height="90"></a>
		</div>
		<div class="griditem-label">
			<a href="newsletter_groups.php?case=show&group=<?=$group['group_id']?>"><img src="design/img/photo_online.gif" width="20" height="20"></a>
			<div><a href="newsletter_groups.php?case=show&group=<?=$group['group_id']?>" class="default fg_white txt_mediumsmall"><?=$canvas->filter($group['name'])?></a></div>
		</div>
	</div>	
<?php endforeach; ?>
</div>
<?php else : ?>
<div id="form_content"><?=sprintf(gettext('Er zijn momenteel geen groepen. Klik %shier%s om een groep aan te maken.'), '<a href="javascript:void(0)" onclick="javascript:newGroup()">', '</a>')?></div>
<?php endif; ?>
