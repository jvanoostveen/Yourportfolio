<?PHP
/*
 * Project: yourportfolio
 *
 * @author Christiaan Ottow
 * @created Nov 28, 2006
 */
?>

<div id="form_content">

	<h3>Ingekomen mail</h3>
	
	<p>
	Wanneer u een nieuwsbrief naar veel adressen verstuurt, kan het zijn dat niet iedereen de nieuwsbrief ontvangt. Mailboxen kunnen vol zijn, adressen in ongebruik geraakt et cetera.
	Wanneer zoiets gebeurt, zal er een email bericht terug komen met een foutmelding van de mailserver. Op deze pagina kunt u controleren of zulke berichten binnengekomen zijn. Wanneer dit het geval is,
	worden de geadresseerden in de database gemarkeerd, en na <b><?=$data['max_errors']?></b> foutmeldingen wordt een adres automatisch verwijderd.
	</p>
	
	<p>
	Wanneer een nieuwsbrief wordt verstuurd, wordt er een afzender adres meegegeven waarnaar de foutmeldingen gestuurd moeten worden. Momenteel is de configuratie als volgt:
	</p>
	
	<p>
	<b>POP3 mail server: </b> <?=$data['mbox_host']?> poort <?=$data['mbox_port']?><br />
	<b>Gebruikersnaam: </b> <?=$data['mbox_user']?><br/>
	</p>
	
	<p> 
	Klik op 'Haal berichten' om te controleren of er bounces zijn binnengekomen. Afhankelijk van hoe snel de mailserver is en hoeveel berichten er zijn, kan dit even duren.
	</p>
	<input type="button" onclick="doCheckMail()" value="Haal berichten">

</div>