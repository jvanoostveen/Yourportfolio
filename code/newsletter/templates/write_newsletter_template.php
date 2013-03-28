<script type="text/javascript">
active_view = 'template';
</script>

<?PHP
$newsletter = $data['newsletter'];
?>

<form action="newsletter_write.php" method="POST" enctype="multipart/form-data" name="theForm" id="theForm">
<input type="hidden" name="target" value="newsletter">
<input type="hidden" name="data[action]" value="save">
<input type="hidden" name="data[task]" id="task" value="<?=$data['task']?>">
<input type="hidden" name="data[newsletter][id]" value="<?=$newsletter->id?>">
<input type="hidden" name="data[newsletter][template_id]" id="template_id" value="<?=$newsletter->template_id?>">

<table width="98%" border="0" cellpadding="2" cellspacing="0">
<tr>
	<td width="120" nowrap align="right">&nbsp;</td>
	<td width="200">&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>

<?PHP
require('write_newsletter_menu.php');
?>

<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('onderwerp')?>:</td>
	<td nowrap width="200">
	<input type="text" name="data[newsletter][subject]" tabindex="1" id="subject" value="<?=$canvas->edit_filter($newsletter->subject)?>" size="50">
	</td>
	<td rowspan=4 align="left" valign="top">
		<div style="margin-left: 20px;">
		Introductie tekst:<br/>
		<textarea cols="60" rows="6" name="data[newsletter][introduction]" tabindex="5" id="introduction"><?=$canvas->edit_filter($newsletter->introduction)?></textarea>
		</div>
	</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('titel nieuwsbrief')?>:</td>
	<td nowrap>
	<input type="text" name="data[newsletter][pagetitle]" id="pagetitle" tabindex="2" value="<?=$canvas->edit_filter($newsletter->pagetitle)?>" size="50">
	</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('naam afzender')?>:</td>
	<td nowrap>
	<input type="text" name="data[newsletter][sender]" id="sender" tabindex="3" value="<?=$canvas->edit_filter($newsletter->sender)?>" size="50">
	</td>
</tr>
<tr>
	<td nowrap align="right"><?=gettext('editie / datum')?>:</td>
	<td nowrap>
	<input type="text" name="data[newsletter][edition]" id="edition" tabindex="4" value="<?=$canvas->edit_filter($newsletter->edition)?>" size="50">
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td nowrap align="right" valign="top"><?=gettext('opmaak')?>:</td>
	<td colspan="2">
<? if (empty($data['templates'])) : /* no templates defined */ ?>
<?=gettext('Er zijn nog geen templates aangemaakt.')?>
<? endif; /* end no templates defined */ ?>
<div width="600" style="overflow: auto;">
<table><tr><? $n = 1;?>
<?PHP foreach( $data['templates'] as $t ) : /* begin template loop */ ?>
	<td><? if( !file_exists( SETTINGS.'newsletter/template/preview_'.$t->id.'.jpg' ) ) : $image = 'preview_1.jpg'; else: $image = 'preview_'.$t->id.'.jpg'; endif;?>
	<div id="template_<?=$t->id?>" class="template<?=($t->id == $newsletter->template_id ? 'Active' : 'Preview')?>" onclick="selectTemplate(<?=$t->id?>,this)" onmouseover="this.style.cursor = 'pointer'" onmouseout="this.style.cursor = ''" >
		<img src="newsletter/template/<?=$image?>" /><br/>
		<p>
			<?=$t->name?><br/>
			<?=$canvas->readableDate($t->created, false, false)?><br/>
		</p>
	</div></td>
	<? if( $n % 4 == 0 || $n == 4 ) : ?></tr><tr><? endif; ?>
	<? $n++; ?>
<?PHP endforeach; /* end template loop */ ?>
</td></tr></table>
</div>
	<script type="text/javascript">
	selected_template = $('template_<?=$newsletter->template_id?>');
	</script>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
</form>