	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td height="12">
		<img src="<?=IMAGES?>spacer.gif" width="12" height="12" border="0">
		</td>
	</tr>
<? if (!empty($yourportfolio->menu_albums)) : /* albums to show */ ?>
	<tr>
		<td>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
<? foreach($yourportfolio->menu_albums as $menu_album) : /* loop albums */ ?>
		<tr class="<?=($canvas->open_album == $menu_album['id'])?'':'un'?>selected">
			<td width="200">
			<a href="album.php?aid=<?=$menu_album['id']?>" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon(($menu_album['online'] == 'N') ? $menu_album['template'].'_off' : $menu_album['template'], $menu_album['type'])?>" width="28" height="20" border="0" align="absmiddle"><?=$canvas->filter($menu_album['name'])?></a>
			<td width="17" align="right">
<? if ($menu_album['locked'] == 'N' || $yourportfolio->session['master']) : /* can be edited or master is logged in */ ?>
			<a href="album.php?aid=<?=$menu_album['id']?>&mode=edit"><img src="<?=IMAGES?>btn_edit.gif" width="17" height="20" border="0"></a>
<? else : /* is locked */ ?>
			<img src="<?=IMAGES?>spacer.gif" width="17" height="20">
<? endif; /* end edit/lock album */ ?>
			</td>
		</tr>
<? if ($canvas->open_album == $menu_album['id']) : /* is active */ ?>
<? if ($menu_album['template'] == 'album') : /* can have sub items */ ?>
		<tr>
			<td colspan="2">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
<? if (!empty($album->menu_sections)) : /* has sections to show */ ?>
<? if ($album->_previous_more) : /* show dots */ ?>
			<tr>
				<td width="20">
				<img src="<?=IMAGES?>spacer.gif" width="20" height="10">
				</td>
				<td width="200">
				<img src="<?=IMAGES?>spacer.gif" width="28" height="10" border="0" align="absmiddle">. . .
				</td>
				<td width="17" align="right">
				&nbsp;
				</td>
			</tr>
<? endif; /* end show dots */ ?>
<? foreach($album->menu_sections as $menu_section) : /* loop sections */ ?>
			<tr class="<?=($canvas->open_section == $menu_section['id'])?'':'un'?>selected">
				<td width="20">
				<img src="<?=IMAGES?>spacer.gif" width="20" height="24">
				</td>
				<td width="200">
				<a href="section.php?aid=<?=$menu_album['id']?>&sid=<?=$menu_section['id']?>" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon(($menu_section['online'] == 'N') ? $menu_section['template'].'_off' : $menu_section['template'])?>" width="24" height="20" border="0" align="absmiddle"><?=$canvas->filter($menu_section['name'], 20)?></a>
				</td>
				<td width="17" align="right">
				<a href="section.php?aid=<?=$menu_album['id']?>&sid=<?=$menu_section['id']?>&mode=edit"><img src="<?=IMAGES?>btn_edit.gif" width="17" height="20" border="0"></a>
				</td>
			</tr>
<? endforeach; /* end loop sections */ ?>
<? if ($album->_next_more) : /* show dots */ ?>
			<tr>
				<td width="20">
				<img src="<?=IMAGES?>spacer.gif" width="20" height="10">
				</td>
				<td width="200">
				<img src="<?=IMAGES?>spacer.gif" width="28" height="10" border="0" align="absmiddle">. . .
				</td>
				<td width="17" align="right">
				&nbsp;
				</td>
			</tr>
<? endif; /* end show dots */ ?>
<? endif; /* end has sections to show */ ?>
			<tr class="<?=($canvas->open_section === 0)?'':'un'?>selected">
				<td width="22">
				&nbsp;
				</td>
				<td>
				<a href="section.php?aid=<?=$menu_album['id']?>&sid=0" class="default fg_black txt_mediumsmall block"><img src="<?=IMAGES?>btn_new_section.gif" width="24" height="20" border="0" align="absmiddle"><i><?=gettext('nieuwe sectie...')?></i></a>
				</td>
				<td width="10">
				&nbsp;
				</td>
			</tr>
			</table>
			</td>
		</tr>
<? endif; /* end sub items */ ?>
<? endif; /* is active */ ?>
<? endforeach; /* end loop albums */ ?>
		</table>
		</td>
	</tr>
<? endif; /* end albums to show */ ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
<? if ( !$yourportfolio->settings['autopublish'] ) : /* has a manual publish */ ?>
	<tr>
		<td class="<?=($canvas->menu_item == 'publish')?'':'un'?>selected">
		<a href="<?=$system->setParameter($system->url, 'publish', '1')?>" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('publish')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('publish')?></a>
		</td>
	</tr>
<? endif; /* end has publish */ ?>
	<tr>
		<td class="unselected">
		<a href="logout.php" class="default fg_black txt_medium block"><img src="<?=IMAGES?>log_out.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('log uit')?></a>
		</td>
	</tr>
	</table>
