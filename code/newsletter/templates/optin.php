<div id="centercontent">

<script type="text/javascript">
	function confirm_delete()
	{
		if( confirm( "Weet u zeker dat u alle onbevestigde adressen uit de database wil verwijderen? U kan deze actie niet ongedaan maken") )
		{
			document.location = 'newsletter_optin.php?case=purge';
		}
	}
	
</script>
<table align="center">
<tr>
	<td colspan="3"><h3><?=gettext('Status van ontvangstbevestiging (opt-in)')?></h3></td>
</tr><tr>
	<td><?=gettext('Totaal')?></td><td></td><td><?=$data['num_addresses']?></td>
</tr><tr>
	<td><?=gettext('Bevestigd')?></td><td></td><td><?=$data['num_verified']?> (<?=sprintf("%2.1f", (($data['num_verified']/$data['num_addresses']) * 100))?>%)</td>
</tr><tr>
	<td><?=gettext('Onbevestigd')?></td><td></td><td><?=$data['num_unverified']?> (<?=sprintf("%2.1f", (($data['num_unverified']/$data['num_addresses']) * 100))?>%)</td>
</tr>
</table>
<br><br>
<div style="border: 1px solid #333333; background-color: #dedede; color: #000000; width: 200px; height: 25px; font-size: 16px; font-weight: bold; padding-top: 5px; margin: auto; cursor: pointer;" onclick="javascript:confirm_delete()"><?=gettext('Adressen opschonen')?></div>

</div>