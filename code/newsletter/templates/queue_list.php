<?PHP
/*
 * Project: yourportfolio
 *
 * @created Nov 21, 2006
 * @author Christiaan Ottow
 * @copyright Christiaan Ottow
 */
?>
<p>

<form name="deleteForm" method="post" action="newsletter_queue.php">
<input type="hidden" name="case" value="delete">
<input type="hidden" id="del_id" name="id" value="">
</form>

<div id="output" style="text-align: center; position: relative;">
<?PHP
if( is_array( $data['queue']) && count($data['queue']) > 0 )
{
	?>
	<table class="listing" cellspacing="0" cellpadding="3" align="center">
	<tr>
		<th><?=gettext('Onderwerp')?></th>
		<th><?=gettext('Reeds verzonden')?></th>
		<th><?=gettext('Nog te verzenden')?></th>
		<td class="blank">&nbsp;</td>
	</tr>
	
	<?PHP

	foreach( $data['queue'] as $letter )
	{
			?><tr>
				<td>
					<?=$letter['subject']?>
				</td>
				<td><?=$letter['sent']?></td>
				<td><?=$letter['unsent']?></td>
				<td class="blank">
					<a href="#" onclick="deleteQueue(<?=$letter['id']?>)">
						<img src="design/img/btn_trash.gif" border="">
					</a>
				</td>
			</tr><?PHP
	}

	?>
	</table>
	<br>
	<div class="button_left" style="margin: auto;">
		<a class="upload" href="#" onclick="sendQueue()">
			<?=gettext('Versturen')?>
		</a>
	</div>
	
	<?PHP

} else {
	?>
	<b><?=gettext('Er zijn geen onverzonden berichten.')?></b>
	<?PHP
}
?>
</div>

<div id="buffer" style="visibility: hidden;">

<div style="margin-left: 40px; margin-top: 40px;">
	<h3><?=gettext('De berichten worden verzonden')?></h3>
	
	<table align="center">
		<tr>
			<td><?=gettext('Verzonden')?>: </td><td><span id="sent">0</span></td>
		</tr><tr>
			<td><?=gettext('Onverzonden')?>:</td><td><span id="unsent"><?=$data['unsent']?></span></td>
		</tr><tr>
			<td><?=gettext('Foutmeldingen')?>:</td><td><span id="errors">0</span></td>
		</tr><tr>
			<td><?=gettext('Resterende tijd')?>:</td><td><span id="timeleft">...</span></td>
		</tr>
	</table>
	<br /><br />	
	
	<div class="button_left" id="controlButton" style="margin: auto;">
		
	</div>
	
	<br /><br />
	<div style="width: 350px; margin: auto;">
	<p>
	<b><?=gettext('Let op:')?></b> <?=gettext("Als u deze pagina verlaat, wordt het versturen verstopt. Wanneer u het versturen wilt onderbreken kunt u op 'stop' klikken. Het versturen stopt dan niet onmiddelijk, maar op het eerst mogelijke punt.")?>
	</p>
	</div>
</div>	
</div>
