      	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="12">
		<img src="<?=IMAGES?>spacer.gif" width="12" height="12" border="0">
		</td>
	</tr>
	<tr>
		<td class="unselected">
		<a href="index.php" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('album')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('site beheer')?></a>
		</td>
	</tr>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>

	<?PHP
	if( strpos($page_name,'/') != false )
	{
		list( $active_menu, $active_item ) = explode('/', $page_name );
	} else {
		$active_menu = $page_name;
		$active_item = '';
	}
	
	foreach( array_keys( $menu ) as $item )
	{
		if( $menu[$item]['name'] == $active_menu)
		{
			$class = "selected";
		} else {
			$class = "unselected";
		}
		
		$link = $menu[$item]['href'];
				
		if( isset( $menu[$item]['id'] ) )
		{
			$id = 'id="'.$menu[$item]['id'].'" ';
		} else {
			$id = '';
		}
		
		if( isset($menu[$item]['icon']) )
		{
			$icon = 'design/' . $menu[$item]['icon'];
		} else {
			$icon = 'design/img/preferences.gif';
		}
		?>
		<tr>
			<td class="<?=$class?>" colspan="2">
			<a <?=$id?>href="newsletter_<?=$link?>.php" class="default fg_black txt_medium block">
				<img src="<?=$icon?>" width="28" height="20" border="0" align="absmiddle"><?=$item?>
			</a>
			</td>
		</tr>
		<?PHP
		
		
		if( isset( $submenu ) && $active_menu == $menu[$item]['name'])
		{ 
			?> <!-- submenu --><?PHP
			
			foreach( $submenu as $subitem)
			{
				$class = 'unselected';
				if( $active_item == $subitem['name'] || (isset($subitem['active']) && $subitem['active']))
				{
					$class = 'selected';
				}				
				
				if( isset( $subitem['id'] ) )
				{
					$id = 'id="'.$subitem['id'].'" ';
				} else {
					$id = '';
				}
				
				if( isset( $subitem['type'] ) && $subitem['type'] == 'event' )
				{
					$event = 'onclick="'.$subitem['href'].'" ';
					$subitem['href'] = 'javascript:void(0)';
				} else {
					$event = '';
				}	
				?>
				<tr class="<?=$class?>">
					<td>
						<span style="width: 20px; float: left;">&nbsp;</span>
						<a style="float: left;" <?=$id?>href="<?=$subitem['href']?>" <?=$event?>class="default fg_black txt_medium block">
							<img src="design/<?=$subitem['icon']?>" border="0" align="absmiddle"><?=$canvas->filter($subitem['name'], 20)?>
						</a><?PHP
						if( isset($subitem['popup']) )
						{
								?><span style="float: right;"><a href="#" onclick="<?=$subitem['popup']?>"><img border="0" alt="edit" src="design/img/btn_edit.gif"></a></span><?PHP
						}?></td>
				</tr>
				<?PHP
			}			
			
		}
		
	}
	?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
	<tr>
		<td class="unselected">
		<a href="logout.php" class="default fg_black txt_medium block" accesskey="q"><img src="<?=IMAGES?>log_out.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('log uit')?></a>
		</td>
	</tr>
	</table>

	