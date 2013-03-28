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
var active_view;
var fade_duration = 0.4;
var selected_template;
var entry = 0;
var layers;
var links;
var progress_image = document.createElement('img');
progress_image.src = 'design/img/sync-white.gif';

function init()
{
	window.onresize = resize;
	resize();	
}

function switchTo(task)
{
	var task_input = $('task');
	
	task_input.value = task;
	setBusy(true);
	
	document.theForm.submit();
}

function selectTemplate( id, obj )
{
	$('template_id').value = id;
	
	if( selected_template )
	{
		selected_template.className = 'templatePreview';
	}
	
	obj.className = 'templateActive';
	
	selected_template = obj;

}

function setView( pane, doFade )
{
	pane.className = 'active';
	if (doFade)
	{
		new Effect.Appear(pane, {duration: fade_duration, from: 0.0, to: 1.0, queue: 'end'});
	} else {
		new Effect.Appear(pane, {duration: 0, from: 0.0, to: 1.0, queue: 'end'});
	} 
	active_view = pane;
}

function switchView( pane, link, doFade )
{
	if( pane != active_view )
	{
		if( active_view )
		{
			if( doFade )
			{
				new Effect.Fade(active_view, {duration: fade_duration, from: 1.0, to: 0.0});
			} else {
				new Effect.Fade(active_view, {duration: 0, from: 1.0, to: 0.0});
			}
			
			active_view.className = '';
			setView( pane, doFade );
		}
	}
	setActive(link);
	
}

function setActive( link )
{
	var list = $('menu_list').getElementsByTagName('li');
	for( var i=0; i < list.length; i++ )
	{
		var cur = list[i].firstChild;
		if( cur == link )
		{
			cur.className = 'active';
		} else {
			cur.className = '';
		}
	}
}

function saveData()
{
	switchTo(active_view);
}

function send()
{
	if( confirm('<?=addcslashes(_("Weet u zeker dat u deze nieuwsbrief wilt versturen?"), "'")?>') )
	{
		$('action').value = 'send';
		saveData();
	}
}

function deleteLetter( id )
{
	if( confirm('<?=addcslashes(_("Weet u zeker dat u deze nieuwsbrief wilt verwijderen?"), "'")?>') )
	{
		$('del_id').value = id;
		document.deleteForm.submit();
	}
}

function duplicateLetter( id )
{
	$('newsletter_id').value = id;
	document.newsletterForm.submit();
}

function moveItem(item, direction)
{
	$('item_id').value = item;
	$('direction').value = direction;
	document.moveForm.submit();
}

function deleteItem(item)
{
	msg = '\n' + document.deleteForm.message.value + '\n';
	
	result = confirm(msg);

	if (result)
	{
		$('delete_id').value = item;
		$('deleteForm').submit();
	}
}

function deleteItemFile(item_id, file_id)
{
	msg = '\n' + document.fileDeleteForm.message.value + '\n';
	
	result = confirm(msg);

	if (result)
	{
		$('filedelete_item_id').value = item_id;
		$('filedelete_file_id').value = file_id;
		$('fileDeleteForm').submit();
	}
}
