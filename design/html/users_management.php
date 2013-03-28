<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name: rel_2-5-23 $
 */
 
/**
 * subuser edit template
 *
 * @package yourportfolio
 * @subpackage HTML
 */
 ?>

<br>
<br>
<br>
<center>
<table width="570" border="0" cellpadding="0" cellspacing="0" class="border_grey">
<tr>
	<td>

<form action="<?=$system->thisFile()?>" method="POST" enctype="multipart/form-data" name="theForm">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="user">

<input type="hidden" name="userForm[action]" value="user_save">

<input type="hidden" name="userForm[user][id]" value="<?=$user->id?>" id="theId">
<input type="hidden" name="userForm[user][site_user_id]" value="<?=$yourportfolio->user_id?>">
<input type="hidden" name="userForm[user][last_login]" value="<?=$user->last_login?>" id="last_login">

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="100" nowrap align="right">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td width="100">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>
	<input type="hidden" name="userForm[user][online]" value="N">
	<input type="checkbox" name="userForm[user][online]" id="online" value="Y"<?=($user->online == 'Y') ? ' checked' :''?>><label for="online"> <?=gettext('actief')?></label>
	</td>
	<td align="right">
	<? if ($user->id > 0) : /* existing section */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> verwijder gebruiker</a><? endif; /* end delete user link */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('naam')?>:</td>
	<td colspan="2">
	<input type="text" name="userForm[user][name]" id="name" value="<?=$canvas->edit_filter($user->name)?>" size="50">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('login')?>:</td>
	<td colspan="2">
	<input type="text" name="userForm[user][login]" id="login" value="<?=$canvas->edit_filter($user->login)?>" size="25">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('wachtwoord')?>:</td>
	<td colspan="2">
	<input type="text" name="userForm[user][password]" id="password" value="<?=$canvas->edit_filter($user->password)?>" size="25">
	</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top"><?=gettext('albums')?>:</td>
	<td colspan="2"><?=gettext('Geef hier aan tot welke albums de gebruiker toegang heeft.')?><br />
	<br />
<? if (!empty($yourportfolio->albums)) : /* has albums to loop */ ?>
<? foreach($yourportfolio->albums as $s_album) : /* loop albums */ ?>
<?
if (empty($s_album->name) && YP_MULTILINGUAL)
{
	if (!empty($s_album->strings['name']))
	{
		$first_language = array_shift(array_keys($tmp_album->strings['name']));
		$s_album->name = $s_album->strings['name'][$first_language]['string_parsed'];
	} else {
		$a_album->name = '';
	}
}
?>
	<input type="checkbox" name="userForm[user][album_ids][]" value="<?=$s_album->id?>" <?=(in_array($s_album->id, $user->album_ids)) ? 'checked' : ''?>><?=$canvas->filter($s_album->name)?><?=($s_album->online != 'Y') ? ' '._('(niet zichtbaar)') : '' ?><br />
<? endforeach; /* end loop albums */ ?>
	<br />
<? endif; /* end has albums */ ?>
	</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>
	<br>
	</td>
</tr>
<tr>
	<td>
	
	<table width="570" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td height="20" width="35" class="bg_black">&nbsp;</td>
		<td width="280" class="bg_black fg_white txt_medium"><?=gettext('naam gebruiker')?></td>
		<td width="225" class="bg_black fg_white txt_medium"><?=gettext('ingelogd op')?></td>
		<td width="30" class="bg_black">&nbsp;</td>
	</tr>
<? foreach($yourportfolio->subusers as $subuser) : /* loop client users */ ?>
	<tr <?=($user->id == $subuser->id) ? 'class="bg_grey"' : ''?>>
		<td align="center"><a href="users.php?uid=<?=$subuser->id?>"><img src="<?=$canvas->showIcon(($subuser->online == 'Y') ? 'user_on' : 'user_off')?>" border="0"></a></td>
		<td><a href="users.php?uid=<?=$subuser->id?>" class="default fg_black"><?=$canvas->filter($subuser->name)?></a> <?=($subuser->countAssignedAlbums() == 0) ? '<span class="fg_red txt_small">'._('(geen albums toegewezen)').'</span>' : ''?></td>
		<td><?=$canvas->readableDate($subuser->last_login)?></td>
		<td><a href="users.php?uid=<?=$subuser->id?>"><img src="<?=IMAGES?>btn_edit.gif" border="0"></a></td>
	</tr>
<? endforeach; /* end loop client users */ ?>
	<tr <?=($user->id == 0) ? 'class="bg_grey"' : ''?>>
		<td align="center"><a href="users.php?uid=0"><img src="<?=IMAGES?>btn_new_album.gif" border="0"></a></td>
		<td><a href="users.php?uid=0" class="default fg_black txt_mediumsmall block italic"><?=gettext('nieuwe gebruiker...')?></a></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</center>

<form action="users.php" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u deze gebruiker wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="user_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$user->id?>" id="deleteID">
</form>