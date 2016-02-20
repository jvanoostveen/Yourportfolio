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

header('Content-Type: application/javascript');
?>
var error_mark = '-';
var do_mail_check = true;

// voor wanneer er meer bounces/unsubs zijn dan er in 1 php executietijd gaan
var total_bounces = 0;
var total_unsubscribes = 0;

var pageUnloaded = false;

function pageUnloadHandler()
{
	pageUnloaded = true;
}

function init()
{
	window.onresize = resize;
	
	// check ingekomen mail
	if( do_mail_check )
	{
		new Ajax.Request(
			'newsletter_start.php?case=check',
				{
					method: 'post',
					parameters: '',
					onComplete: showResults
				}
		);
	}
	
	resize();
}

function showResults( rsp )
{
	if (pageUnloaded)
	{
		return;
	}
	
	var text = rsp.responseText;
	
	if (text.length == 0)
	{
		alert('<?=addcslashes(sprintf(_('Er is een fout opgetreden tijdens de verwerking van de e-mails. Neem contact op met %s.'), 'support@furthermore.nl'), "'")?>');
		return;
	}
	
	if( text.length < 5 || text.substr(0, 5) == 'ERROR')
	{
		var msg = text;
		if( text.substr(0,5) == 'ERROR')
		{
			var msg = text.substr(6, text.length - 6);
		}
		alert(msg);
		
		if ($('bounces').innerText)
		{
			$('bounces').innerText = error_mark;
			$('unsubscribes').innerText = error_mark;
			$('more').innerText = '?';
		} else {
			$('bounces').textContent = error_mark;
			$('unsubscribes').textContent = error_mark;
			$('more').textContent = '?';
		}
		return;
	}
	
	var parts = text.split(';');
	
	// bounces
	var parts2 = parts[0].split(':');
	var bounces = parts2[1];
	
	parts2 = parts[1].split(':');
	var unsubscribes = parts2[1];
	
	parts2 = parts[2].split(':');
	var more = parts2[1];

	total_bounces += parseInt(bounces);
	total_unsubscribes += parseInt(unsubscribes);
	
	var suffix = '';
	if( more > 0 )
	{
		suffix = '...';
	}
	
	if ($('bounces').innerText)
	{
		$('bounces').innerText = total_bounces + suffix;
		$('unsubscribes').innerText = total_unsubscribes + suffix;
		$('more').innerText = more;
	} else {
		$('bounces').textContent = total_bounces + suffix;
		$('unsubscribes').textContent = total_unsubscribes + suffix;
		$('more').textContent = more;
	}
	
	if( more > 0 )
	{
		init();
	} else {
		resize();
	}
}
