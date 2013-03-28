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
<?
if (empty($menu_album['name'])) :
	$tmp_album = new Album();
	$tmp_album->id = $menu_album['id'];
	$tmp_album->load();
	
	if (!empty($tmp_album->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_album->strings['name']));
		$menu_album['name'] = $tmp_album->strings['name'][$first_language]['string_parsed'];
	} else {
		$menu_album['name'] = '';
	}
endif;
?>
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
<? foreach($album->menu_sections as $menu_section) : /* loop sections */ ?>
<?
if (empty($menu_section['name'])) :
	$tmp_section = new Section();
	$tmp_section->id = $menu_section['id'];
	$tmp_section->load();
	
	if (!empty($tmp_section->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_section->strings['name']));
		$menu_section['name'] = $tmp_section->strings['name'][$first_language]['string_parsed'];
	} else {
		$menu_section['name'] = '';
	}
endif;
?>
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
<? if ($yourportfolio->settings['can_add_albums'] || $yourportfolio->session['master']) : /* can add albums */ ?>
	<tr>
		<td class="<?=($canvas->open_album === 0 && $album->restricted == 'N')?'':'un'?>selected">
		<a href="album.php?aid=0" class="default fg_black txt_mediumsmall block"><img src="<?=IMAGES?>btn_new_album.gif" width="28" height="20" border="0" align="absmiddle"><i><?=gettext('nieuw album...')?></i></a>
		</td>
	</tr>
<? endif; /* end can add albums */ ?>
<? if ($yourportfolio->settings['subusers']) : /* subuser management */ ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
	<tr>
		<td class="<?=($canvas->menu_item == 'users')?'':'un'?>selected">
		<a href="users.php" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('users')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('gebruikers beheer')?></a>
		</td>
	</tr>
<? endif; /* end subuser management */ ?>
<? if ($yourportfolio->settings['restricted_albums']) : /* can have restricted albums */ ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
	<tr>
		<td class="<?=($canvas->menu_item == 'client_users')?'':'un'?>selected">
		<a href="client_users.php" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('users')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('gebruikers beheer')?></a>
		</td>
	</tr>

	
<? if (!empty($yourportfolio->menu_restricted_albums)) : /* albums to show */ ?>
	<tr>
		<td>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
<? foreach($yourportfolio->menu_restricted_albums as $menu_album) : /* loop albums */ ?>
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
		<td class="<?=($canvas->open_album === 0 && $album->restricted == 'Y')?'':'un'?>selected">
		<a href="album.php?aid=0&restricted=1" class="default fg_black txt_mediumsmall block"><img src="<?=IMAGES?>btn_new_album.gif" width="28" height="20" border="0" align="absmiddle"><i><?=gettext('nieuw album...')?></i></a>
		</td>
	</tr>

	
<? endif; /* end can have restricted albums */ ?>
<?php if ($yourportfolio->settings['tags'] || $yourportfolio->settings['newsletter']) : ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
<?php endif; ?>
<?php if ($yourportfolio->settings['tags']) : ?>
	<tr>
		<td class="<?=($canvas->menu_item == 'tags')?'':'un'?>selected">
		<a href="tags.php" class="default fg_black txt_medium block"><img src="<?=IMAGES?>preferences.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('tags')?></a>
		</td>
	</tr>
<?php endif; ?>
<? if ($yourportfolio->settings['newsletter']) : /* has newsletter module */ ?>
	<tr>
		<td class="<?=($canvas->menu_item == 'newsletter')?'':'un'?>selected">
		<a href="newsletter_start.php" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('newsletters')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('nieuwsbrieven')?></a>
		</td>
	</tr>
<? endif; /* end has newsletter module */ ?>
<? if ($yourportfolio->session['master']) : /* master account is logged in */ ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
	<tr>
		<td class="<?=($canvas->menu_item == 'admin')?'':'un'?>selected">
		<a href="administration.php" class="default fg_black txt_medium block"><img src="<?=IMAGES?>preferences.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('site voorkeuren')?></a>
		</td>
	</tr>
<? endif; /* end master account options */ ?>
	<tr>
		<td class="dashedline">
		<img src="<?=IMAGES?>spacer.gif" width="1" height="7">
		</td>
	</tr>
	<tr>
		<td class="<?=($canvas->menu_item == 'prefs')?'':'un'?>selected">
		<a href="preferences.php" class="default fg_black txt_medium block"><img src="<?=IMAGES?>preferences.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('voorkeuren')?></a>
		</td>
	</tr>
<? if ( !$yourportfolio->settings['autopublish'] ) : /* has a manual publish */ ?>
	<tr>
		<td class="<?=($canvas->menu_item == 'publish')?'':'un'?>selected">
		<a href="<?=$system->setParameter($system->url, 'publish', '1')?>" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('publish')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('publish')?></a>
		</td>
	</tr>
<? endif; /* end has publish */ ?>
<? if ( $yourportfolio->checkForUpdate() ) : /* installed version is older then version in code */ ?>
	<tr>
		<td class="unselected">
		<a href="<?=$system->setParameter($system->url, 'update', '1')?>" class="default fg_black txt_medium block"><img src="<?=$canvas->showIcon('update')?>" width="28" height="20" border="0" align="absmiddle"><?=gettext('update')?></a>
		</td>
	</tr>
<? endif; /* end has update */ ?>
	<tr>
		<td class="unselected">
		<a href="logout.php" class="default fg_black txt_medium block" accesskey="q"><img src="<?=IMAGES?>log_out.gif" width="28" height="20" border="0" align="absmiddle"><?=gettext('log uit')?></a>
		</td>
	</tr>
	</table>
