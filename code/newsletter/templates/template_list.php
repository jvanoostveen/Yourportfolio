<?PHP
if( is_array( $data['templates'] ) && count( $data['templates'] ) > 0 )
{
	foreach( $data['templates'] as $template )
	{
		?>
		<table width="120" height="120" border="0" cellpadding="0" cellspacing="0" align="left" class="phototable">
		<tr>
			<td height="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="10" border="0" class="special"></td>
			<td height="10" width="120"><img src="<?=IMAGES?>spacer.gif" width="120" height="1" border="0" class="special"></td>
		</tr>
		<tr>
			<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="90"></td>
			<td class="bg_grey" align="center" valign="middle" width="120">
			<a href="newsletter_templates.php?case=edit&id=<?=$template->id?>"><img src="design/iconsets/default/section_overview.gif" width="76" height="82" border="0"></a>
			</td>
		</tr>
		<tr>
			<td width="10"><img src="<?=IMAGES?>spacer.gif" width="10" height="1"></td>
			<td height="1" width="120" class="bg_black"><img src="design/img/black_spacer.gif" width="120" height="1"></td>
		</tr>
		<tr>
			<td><img src="<?=IMAGES?>spacer.gif" width="1" height="20"></td>
			<td height="20" width="120" class="bg_black">
			<a href="newsletter_templates.php?case=show&template=<?=$template->id?>"><img src="design/img/photo_<?=($template->online=='Y'?'online':'offline')?>.gif" width="20" height="20" align="absmiddle" border="0"></a><a href="newsletter_templates.php?case=edit&id=<?=$template->id?>" class="default fg_white txt_mediumsmall"><?=$canvas->filter($template->name, 15)?></a>
			</td>
		</tr>
		</table>
		<?PHP
	}
} else {
	?><div id="form_content"><?=sprintf(_('Er zijn momenteel geen templates. Klik %s om een template aan te maken.'), sprintf('<a href="newsletter_templates.php?case=edit">%s</a>', _('hier')))?></div><?PHP
}
?>
