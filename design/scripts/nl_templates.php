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
function init()
{
}

function saveData()
{
	$('editForm').submit();
}

function deleteTemplate()
{
	if( confirm("<?=addcslashes(gettext('Weet u zeker dat u deze template wil verwijderen?'), '"')?>") )
	{
		$('deleteForm').submit();
	}
}

function deleteImage(img)
{
	if( confirm('<?=addcslashes(gettext("Weet u zeker dat u deze afbeelding wil verwijderen?"), "'")?>') )
	{
		$('deleteImgName').value = img;
		$('case').value = 'img_delete';
		$('editForm').submit();
	}
}

function uploadImage()
{
	$('case').value = 'img_upload';
	$('editForm').submit();
}
