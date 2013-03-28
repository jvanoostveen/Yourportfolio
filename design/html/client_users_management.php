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
 * section edit template
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

<input type="hidden" name="userForm[action]" value="client_user_save">

<input type="hidden" name="userForm[user][id]" value="<?=$user->id?>" id="theId">
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
	<? if ($user->id > 0) : /* existing section */ ?> <a href="javascript:deleteThis();" class="default fg_black txt_medium"><img src="<?=IMAGES?>btn_trash.gif" width="16" height="16" border="0" align="absbottom"> <?=gettext('verwijder gebruiker')?></a><? endif; /* end delete user link */ ?>
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
	<td nowrap align="right">&nbsp;</td>
	<td colspan="2"><a href="<?=$yourportfolio->save_url?>" class="txt_mediumlarge bold fg_black"><?=gettext('bewaar')?></a></td>
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
<? foreach($yourportfolio->client_users as $client_user) : /* loop client users */ ?>
	<tr <?=($user->id == $client_user->id) ? 'class="bg_grey"' : ''?>>
		<td align="center"><a href="client_users.php?uid=<?=$client_user->id?>"><img src="<?=$canvas->showIcon(($client_user->online == 'Y') ? 'user_on' : 'user_off')?>" border="0"></a></td>
		<td><a href="client_users.php?uid=<?=$client_user->id?>" class="default fg_black"><?=$canvas->filter($client_user->name)?></a> <?=($client_user->countAssignedAlbums() == 0) ? '<span class="fg_red txt_small">'._('(geen albums toegewezen)').'</span>' : ''?></td>
		<td><?=$canvas->readableDate($client_user->last_login)?></td>
		<td><a href="client_users.php?uid=<?=$client_user->id?>"><img src="<?=IMAGES?>btn_edit.gif" border="0"></a></td>
	</tr>
<? endforeach; /* end loop client users */ ?>
	<tr <?=($user->id == 0) ? 'class="bg_grey"' : ''?>>
		<td align="center"><a href="client_users.php?uid=0"><img src="<?=IMAGES?>btn_new_album.gif" border="0"></a></td>
		<td><a href="client_users.php?uid=0" class="default fg_black txt_mediumsmall block italic"><?=gettext('nieuwe gebruiker...')?></a></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</center>

<form action="client_users.php" method="post" enctype="application/x-www-form-urlencoded" name="deleteForm">
<input type="hidden" name="message" value="<?=gettext('Weet u zeker dat u deze gebruiker wilt verwijderen?')?>" id="message">
<input type="hidden" name="targetObj" value="yourportfolio">
<input type="hidden" name="formName" value="delete">
<input type="hidden" name="deleteForm[action]" value="client_user_delete">
<input type="hidden" name="deleteForm[delete][id]" value="<?=$user->id?>" id="deleteID">
</form>