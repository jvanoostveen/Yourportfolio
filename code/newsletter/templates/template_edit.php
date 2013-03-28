<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created May 4, 2007
 */
 
if( isset($data['template']) )
{
	$new = false;
	$t = $data['template'];
	$y_selected = ( $t->online == 'Y' ? ' selected' : '' );
	$n_selected = ( $t->online == 'N' ? ' selected' : '' );
} else {
	$new = true;
	$y_selected = ' selected';
	$n_selected = '';
}

?>
<div style="padding: 10px;">
<?PHP
if( isset($data['errors']) && isset($data['errors']['general']) && !empty($data['errors']['general']) )
{
	?><h3 class="error"><?=$data['errors']['general']?></h3><?PHP
}
?>

<form name="editForm" enctype="multipart/form-data" id="editForm" action="newsletter_templates.php" method="post">
<?PHP
if( !$new )
{
	?><input type="hidden" name="template[id]" value="<?=$t->id?>" /><?PHP
}
?>
<input type="hidden" id="case" name="case" value="save" />
<input type="hidden" name="deleteImgName" id="deleteImgName" value="">

<table>

	<tr>
		<td valign="top">
			<fieldset>
			<legend><?=gettext('Template gegevens')?></legend>
			<div style="height: 150px; width: 300px; overflow: auto; border: 0;">
			<table cellspacing=3>
				<tr>
					<td><?=gettext('Naam')?></td><td><input type="text" name="template[name]" value="<?=($new?'':$t->name)?>"></td>
				</tr>
				<tr>
					<td><?=gettext('Default titel')?></td><td><input type="text" name="template[default_title]" value="<?=($new?'':$t->default_title)?>"></td>
				</tr>
				<tr>
					<td><?=gettext('Afbeelding (W x H)')?></td><td><input type="text" name="template[itemimage_width]" size="3" value="<?=($new?'':$t->itemimage_width)?>">&nbsp;x&nbsp;<input type="text" name="template[itemimage_height]" size="3" value="<?=($new?'':$t->itemimage_height)?>"></td>
				</tr>
				<tr>
					<td><?=gettext('Preview')?></td><td><?=(file_exists(SETTINGS.'newsletter/template/preview_'.$t->id.'.jpg') ? 'preview_'.$t->id.'.jpg' : '<font color="#ff0000">preview_'.$t->id.'.jpg')?></td>
				</tr>
				<tr>
					<td><?=gettext('Zichtbaar')?></td><td><select name="template[online]"><option value="Y"<?=$y_selected?>><?=gettext('Ja')?></option><option value="N"<?=$n_selected?>><?=gettext('Nee')?></option></select></td>
				</tr>
			</table>
			<p>
				<div class="button button_3" style="float: right; margin-top: 1px;"><a class="upload"  href="javascript:void(0)" onclick="javascript:deleteTemplate()"><?=gettext('template verwijderen')?></a></div>
			</p>
			</div>
			</fieldset>
		</td>
		<td width="30">&nbsp;</td>
		<td valign="top">
			<fieldset>
			<legend><?=gettext('Afbeeldingen')?></legend>
			<div style="width: 300px; height: 150px; border: 0">
				<div style="width: 300px; height: 125px; overflow: auto; border: 0; padding: 0; margin: 0; margin-bottom: 5px;">
					<table width="100%" cellpadding=2 cellspacing=1>
					<?PHP
						if( count($data['images']) > 0 )
						{
							foreach($data['images'] as $im )
							{
								?><tr>
									<td valign="top" style="border-bottom: 1px solid #E1E1E1;"><?=$im?></td>
									<td valign="top" style="border-bottom: 1px solid #E1E1E1;" align="right">
										<a href="javascript:void(0)" onclick="javascript:deleteImage('<?=$im?>')"><img src="<?=IMAGES?>/btn_trash.gif" border="0" alt="delete"></a>
									</td>
								</tr>
								<?PHP
							}
						} else {
							echo _('Er zijn geen afbeeldingen beschikbaar');
						}
					?>
					</table>
				</div>
				<input type="file" name="imageFile" /> <input type="button" onclick="javascript:uploadImage()" value="Upload">
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<fieldset>
			<legend><?=gettext('Opmaak')?></legend>
			<div style="width: 660px; margin: 0; padding: 0; border: 0;">
				<table width="100%">
				<tr>
					<td><?=gettext('header')?><br />
						<textarea class="template_text" name="template[header]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->header)?></textarea>
					</td>
					
					<td width="30">&nbsp;</td>
					
					<td><?=gettext('header_text')?><br />
						<textarea class="template_text" name="template[header_text]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->header_text)?></textarea>
					</td>
				</tr>
				<tr>
					<td><?=gettext('item')?><br />
						<textarea class="template_text" name="template[item]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->item)?></textarea>
					</td>
					
					<td width="30">&nbsp;</td>
					
					<td><?=gettext('item_text')?><br />
						<textarea class="template_text" name="template[item_text]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->item_text)?></textarea>
					</td>
				</tr>
				<tr>
					<td><?=gettext('footer')?><br />
					<textarea class="template_text" name="template[footer]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->footer)?></textarea>
					</td>
					<td width="30">&nbsp;</td>
					<td><?=gettext('footer_text')?><br />
					<textarea class="template_text" name="template[footer_text]" cols="47" rows="12"><?=$canvas->edit_filter($new?'':$t->footer_text)?></textarea>
					</td>
				</tr>
				</table>
			</div>
			</fieldset>
		</td>
	</tr>
</table>
</form>
</div>

<style>
	#legend {
		padding-left: 12px;
		width: 500px;
	}
	#legend fieldset {
		margin-bottom: 10px;
	}
	
	dt {
		font-weight: bold;
	}
</style>

<div id="legend">
	<fieldset>
		<legend><?php echo _('Functies'); ?></legend>
		<dl>
			<dt>f()</dt>
			<dd><?php echo _('Filter functie voor HTML content. Plaats dit om alle tekstuele content.'); ?></dd>
			<dt>f_t()</dt>
			<dd><?php echo _('Filter functie voor tekst content.'); ?></dd>
		</dl>
	</fieldset>
	<fieldset>
		<legend><?php echo _('Algemeen'); ?></legend>
		<dl>
			<dt>$PREVIEW_URL</dt>
			<dd><?php echo _('Complete URL naar HTML online preview.'); ?></dd>
			<dt>$IN_MAIL</dt>
			<dd><?php echo _('Boolean of de opbouw voor e-mail is of online preview.'); ?></dd>
			<dt>$UNSUBSCRIBE_LINK</dt>
			<dd><?php echo _('Complete mailto unsubscribe link. Te plaatsen in een href.'); ?></dd>
			<dt>$TEMPLATE_PATH</dt>
			<dd><?php echo _('Absolute URL naar template bestanden.'); ?></dd>
			<dt>$CONTENT_PATH</dt>
			<dd><?php echo _('Absolute URL naar content per nieuwsbrief, te gebruiken i.c.m. $IMAGE.'); ?></dd>
		</dl>
	</fieldset>
	<fieldset>
		<legend><?php echo _('Content'); ?></legend>
		<dl>
			<dt>$newsletter</dt>
			<dd>$newsletter->subject, $newsletter->pagetitle, $newsletter->introduction, $newsletter->edition.</dd>
		</dl>
	</fieldset>
	<fieldset>
		<legend><?php echo _('Item'); ?></legend>
		<dl>
			<dt>$ITEM</dt>
			<dd>$ITEM->title, $ITEM->content.</dd>
			<dt>$IMAGE</dt>
			<dd>$IMAGE->isEmpty(), $IMAGE->width, $IMAGE->height, $IMAGE->cache_name, $IMAGE->sysname.</dd>
		</dl>
	</fieldset>
</div>

<br>
<br>

<div style="visibility: hidden; display: none;">
	<form id="deleteForm" name="deleteForm" action="newsletter_templates.php" method="post">
		<input type="hidden" name="template[id]" value="<?=$t->id?>">
		<input type="hidden" name="case" value="delete">
	</form>
</div>
