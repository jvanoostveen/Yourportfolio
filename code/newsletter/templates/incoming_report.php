<?PHP
/*
 * Project: yourportfolio
 *
 * @author Christiaan Ottow
 * @created Nov 28, 2006
 */
 
?>
<div id="form_content">
	<h3>Controleren op berichten voltooid</h3>
	
	<p>Het controleren op nieuwe berichten is voltooid. Resultaten:
	</p>
	
	<p>
	<table>
		<tr>
			<td>Aantal berichten in mailbox:</td><td width="20"></td><td><b><?=$data['num_parsed']?></b></td>
		</tr><tr>
			<td>Aantal error berichten:</td><td></td><td><b><?=$data['num_bounces']?></b></td>
		</tr><tr>
			<td>Aantal verwijderde adressen:</td><td></td><td><b><?=$data['num_deleted']?></b></td>
		</tr>
	</table>
	</p>
</div>	
	
