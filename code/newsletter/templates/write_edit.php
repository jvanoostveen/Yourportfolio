<?PHP
/*
 * Project: yourportfolio
 *
 * @created Nov 14, 2006
 * @author Christiaan Ottow
 * @copyright Christiaan Ottow
 */
 
if( isset( $data)  )
{
	$edition = $data['letter']['edition'];
	$sender = $data['letter']['sender'];
	$title = $data['letter']['pagetitle'];
	$subject = $data['letter']['subject'];
	$id = $data['letter']['letter_id'];
	
} else {
	$subject = '';
	$title='';
	$edition='';
	$sender='';
	$id = '';
}

 if (!empty($data['templates']))
 {
	foreach( $data['templates'] as $t )
	{
		if($t['template_id'] == $data['letter']['template_id'])
		{
			$template = $t;
		}
	}
} 
?>


<br>
<div style="position: relative; text-align: center; width: 100%; height: 30px;">
	<div style="margin: auto; width: 29em">
		<ul class="switchbox" id="menu_list">
			<li class="active"><a id="template_link" href="#" onclick="switchView($('template_div'), this, true)">1. Template</a></li>
			<li class=""><a id="content_link" href="#" onclick="switchView($('content_div'), this, true)">2. Inhoud</a></li>
			<li class=""><a id="addr_link" href="#" onclick="switchView($('addr_div'), this, true)">3. Groepen</a></li>
		</ul>
	</div>
</div>

<br>

<div id="container_div" style="position: relative; text-align: center; width: 100%; height: 100%;">

	<form name="form" action="newsletter_write.php" method="post">
		<input type="hidden" name="case" value="save">
		<input type="hidden" name="action" value="save" id="action">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="hidden" name="template_id" id="template_id" value="<?=$data['letter']['template_id']?>">
	
		<div id="template_div" style="position: absolute; top: 0; left: 100px; margin: auto; z-index: 10; background-color: #ffffff;">
			<p>
				<? require('components/write_template_config.php'); ?>
			</p>
			
			<p>
				<? require('components/write_template_select.php'); ?>
			</p>
				
		</div>
		
		<div id="addr_div" style="position: absolute; top: 0; left: 100px;margin: auto; z-index: 0 background-color: #ffffff;">
			<?=_('Groepen waarnaar deze nieuwsbrief gestuurd moet worden')?>:
			<p>
			<?PHP
			if( is_array($data['groups']) && count($data['groups']) > 0 )
			{
				foreach( $data['groups'] as $g )
				{
					if( isset( $data['data']) && is_array( $data['data']) && count( $data['data']) > 0 )
					{
						$checked = (count( $data['data']['recipients']) > 0 && in_array( $g['group_id'], $data['data']['recipients'])) ? ' checked="checked"' : '';
					} else {
						$checked = '';
					}
					
					?><input type="checkbox" name="group_<?=$g['group_id']?>"<?=$checked?>><?=$g['name']?><br />
					<?PHP
				}
			} else {
				echo _('Er zijn nog geen groepen gedefini&euml;erd');
			}
			?>
			</p>
			
		</div>
	</form>

	<div id="content_div" style="position: absolute; top: 0; left: 100px;margin: auto; z-index: 0; background-color: #ffffff;">
		<? require('components/add_item_form.php'); ?>
		<hr>
		<?PHP
		if( isset($data['items']) && is_array($data['items']) && count($data['items']) > 0 )
		{
			foreach( $data['items'] as $i )
			{
				require('components/show_item.php');
			}
		}
		?>
	</div>
		
	
</div>

<div style="visibility: hidden;">

	<form name="delItemForm" method="post" action="newsletter_write.php">
		<input type="hidden" name="case" value="delItem">
		<input type="hidden" name="item_id" value="" id="del_id">
	</form>
	
	<form name="moveForm" method="post" action="newsletter_write.php">
		<input type="hidden" name="case" value="move">
		<input type="hidden" name="item_id" value="" id="move_id">
		<input type="hidden" name="direction" value="" id="move_direction">
	</form>
	
</div>