<?PHP
$lang = (isset($_GET['l']) ? $_GET['l'] : 'nl_NL');

define('LOCALE', '../../locale/');

@putenv("LANG=$lang");
if (!setlocale(LC_ALL, $lang))
{
	trigger_error('Locale '.$lang.' not found');
}

// language domains
bindtextdomain('backend', LOCALE);
bindtextdomain('newsletter', LOCALE);

// current domain
textdomain('newsletter');

?>
var totaltime = 0;
var sent = 0;
var doSend = true;
var errors = 0;

var control_stop = '<a href="#" onclick="stopSend()" class="upload"><?=_("stop")?></a>';
var control_resume = '<a href="#" onclick="resumeSend()" class="upload"><?=_("hervat")?></a>';

function init() {}

function sendQueue()
{
	if( confirm('<?=addcslashes(_('Weet u zeker dat u deze berichten wilt versturen?\n\nWanneer u op OK hebt geklikt, zal het versturen beginnen. Het kan enige tijd duren voor u weer een pagina te zien krijgt.'), "'")?>'))
	{
		var content = $('buffer').innerHTML;
		$('buffer').parentNode.removeChild($('buffer'));
		$('output').innerHTML = content;	
		$('controlButton').innerHTML = control_stop;		
		startBatch();
	}
}

function deleteQueue(id)
{
	if( confirm('<?=addcslashes(_("Weet u zeker dat u de onverzonden mails van deze zending wilt annuleren?"), "'")?>') )
	{
		$('del_id').value = id;
		document.deleteForm.submit();
	}
}

function stopSend()
{
	if( doSend )
	{
		doSend = false;
		$('controlButton').innerHTML = control_resume;	
	}
}

function resumeSend()
{
	if( !doSend )
	{
		doSend = true;
		$('controlButton').innerHTML = control_stop;
		startBatch();
	}
}

function startBatch()
{

	var params = 'case=send';

	if( doSend )
	{
		new Ajax.Request( 'newsletter_queue.php', {
			method		: 'post',
			postBody	: params,
			onSuccess	: batchComplete,
			onFailure	: batchError
		});
	}		
}

function batchComplete( response )
{
	var resp = response.responseText;
	var values = resp.split(':');
	if( values.length == 2 )
	{
		// fatal error
		var message = '<?=addcslashes(_('Er is een fatale fout opgetreden. Probeer het verzenden later opnieuw.'), "'")?>';
		
		if( values[0] == 'connect' )
		{
			message = '<?=addcslashes(_('Er kon geen verbinding worden gemaakt met de mail server voor verzending. Probeer het later opnieuw.'), "'")?>';
		}
		
		alert( message );

		window.location.reload();
				
		return;

	}
	
	var unsent = values[1];
	errors 		+= Number(values[2]);
	totaltime 	+= Number(values[3].replace(/,/,'.'));
	sent 		+= Number(values[0]);
	
	var timepermail = totaltime / sent;
	var timeleft = unsent * timepermail;
	var minutes = Math.floor((timeleft / 60));
	var seconds = Math.round(timeleft - (minutes * 60 ));

	$('sent').innerHTML = sent;
	$('unsent').innerHTML = unsent;
	$('errors').innerHTML = errors;
	$('timeleft').innerHTML = minutes+' min '+seconds+' sec';
	
	if( unsent == 0 || unsent == '')
	{
		doSend = false;
		var minutes = Math.floor((totaltime / 60));
		var seconds = Math.round(totaltime - (minutes * 60 ));
		alert('<?=addcslashes(_("Het versturen is voltooid"), "'")?>');
		document.location = 'newsletter_start.php';
	} else {
		startBatch();
	}
	
}

function batchError( response )
{
	alert('<?=addcslashes(_("Er is een fout opgetreden bij het versturen van de mail queue:"), "'")?> '+response.responseText);
}