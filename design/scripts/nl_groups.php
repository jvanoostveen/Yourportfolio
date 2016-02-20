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
// globale variabelen
var overlay;
var popupVisible = false;
var progress_image = document.createElement('img');
progress_image.src = 'design/img/sync-white.gif';
var checkboxes = new Array();

// voor sorteren
var base_url = 'newsletter_groups.php';
var listid = 'group';

// voor selectie
var selected = new Array();
var dataset = new Array();
var lastSelected = -1;
var debugWin;
var commandkey = false;
var mark_color = '#c7d3ed';
var all_selected = true;

// settings
var layout = 1;
var do_overlay = true;
var overlay_opacity = 5;
var popup;

// voor zoeken
var search_url = 'newsletter_groups.php?case=search';
var group = true;

function windowResized()
{
	setOverlaySize();

	if( !popupVisible )
	{
		popup = $('newGroupDiv');
		
		if (popup != null)
		{
			popup.style.left = (document.body.clientWidth / 2 ) - 150;
			popup.style.top = 200;
		}
	}
	resize();
}

// overlay settings
function init()
{

	if( tablepage && $('numpages') )
	{
		var thediv = $('numpages');
		thediv.style.top = 0;
	}

	window.onresize = windowResized;
	
	overlay    				= document.createElement('div');
	overlay.style.position 	= 'absolute';
	
	overlay.style.top    	= 0;
	overlay.style.left   	= 0;	
	overlay.style.backgroundColor = '#aaaaaa';
	overlay.visibility 		= 'hidden';
	overlay.style.display	= 'none';
	overlay.style.zIndex 	= -100;
	overlay.id 				= 'greyOverlay';
	overlay.onclick 		= showPopup;
	setOverlaySize();	
	setOpacity(overlay, overlay_opacity);
	$('progress').style.visibility = 'hidden';
	
	popup = $('newGroupDiv');
	
	document.body.appendChild(overlay);
	
	if( $('results') )
	{
		setBusy(true);
		loadData();
	}
	
}

function loadData()
{
	var group_only = $('group_only').value;
	
	new Ajax.Request( 'newsletter_groups.php?case=load&group='+groupId+'&page='+page + '&group_only=' + group_only,
		{
			method: 	'post',
			postBody:		'',
			onSuccess:	dataReceived,
			onFailure:	loadFailed
		}
	);
}

function dataReceived( response )
{
	if( response.responseText == 'ERROR:<?=addcslashes(_("page out of reach"), "'")?>' )
	{
		window.location = 'newsletter_groups.php?case=show&page=1&group='+groupId+'&group_only='+$F('group_only');
	} else if( (!response.responseText || response.responseText == '') && currentView == 'group') {
		switchView('all');
	} else if( (!response.responseText || response.responseText == '') && currentView != 'group') {
		if( $('msg').innerText )
		{
			$('msg').innerText = '<?=addcslashes(_('Er zijn geen adressen'), "'")?>';
		} else {
			$('msg').textContent = '<?=addcslashes(_('Er zijn geen adressen'), "'")?>';
		}
	} else {

		$('msg').parentNode.removeChild($('msg'));
		$('results').innerHTML = response.responseText;
	}
	
	parseRows(true);
	setBusy(false);
	resize();
}

function loadFailed( response )
{
	alert( '<?=addcslashes(_("There was an error while loading the data:"), "'")?> '+response.responseText );
	setBusy(false);
}

function parseRows( init )
{
	// handlers voor rows
	if( $('addresses') )
	{
		var container = $('addresses');
		var rows = container.getElementsByTagName('tr');
		
		for(var i=1; i < rows.length;i++)
		{
			var cells = rows[i].getElementsByTagName('td');
			for(var j=1;j < cells.length;j++)
			{
				cells[j].onclick = 	cellClicked;
			}
			
			var inputs = rows[i].getElementsByTagName('input');
			if( init )
			{
				checkboxes.push( inputs[0] );
			}

			if( inputs[0].checked )
			{
					selectRow(rows[i]);
			}
		
			inputs[0].onclick = function() {
				selectCheckRow( this );
			}
			
			
		}
		
		enableDocumentMouseDown(false);
	}
}

function toggleAll()
{
	var rows = $('addresses').getElementsByTagName('tr');
	all_selected = !all_selected;
	
	for( var i=0; i < rows.length; i++ )
	{
		if(all_selected)
		{
			deselectRow(rows[i]);
		} else {
			selectRow(rows[i]);
		}
	}
		
}

function selectCheckRow( elem )
{
	var id = elem.id.substr(5);
	
	if( contains(selected, id) )
	{
		deselectRow(elem.parentNode.parentNode);
	} else {
		selectRow(elem.parentNode.parentNode);
	}
}

function cellClicked(e)
{
	// de-selecteer geselecteerde tekst
	if (window.getSelection) 
	{
		var sel = window.getSelection();
		if (sel.rangeCount != undefined)
		{
			sel.removeAllRanges();
		}
	} else if (document.selection) {
		document.selection.empty(); 
	}

	var code;
	if (!e) var e = window.event;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	var character = String.fromCharCode(code);
	
	e = (e) ? e : ((window.event) ? window.event : "")
	var element = e.target || e.srcElement;
	var row = element.parentNode;
	
	// vind huidig item
	allRows = row.parentNode.getElementsByTagName('tr');
	for(var i=0; i < allRows.length; i++)
	{
		if( allRows[i] == row )
		{
			var pressed = i;
		}
	}
	
	// meerdere items selecteren?
	if( e.shiftKey && (lastSelected >= 0 ) )
	{
		
		// start en end
		if( pressed < lastSelected )
		{
			var start = pressed;
			var end = lastSelected;
		} else {
			var start = lastSelected;
			var end = pressed;
		}
		
		// selecteren
		for( var i=start; i<=end;i++ )
		{
			selectRow(allRows[i]);
		}
		
	} else {
		if( contains( selected, row.id.substr(2) ) )
		{
			deselectRow(row);
		} else {
			lastSelected = pressed;
			selectRow(row);
		}
	
	}
}

function selectRow(row)
{
	colorCellsOfRow(row, mark_color);

	if(!array_contains(row.id.substr(2), selected ) )
	{
		selected.push( row.id.substr(2) );
	}
	//$('debug').innerText = selected.join(',');
	// select checkbox
	var inputs = row.getElementsByTagName('input');
	inputs[0].checked = true;

}

function deselectRow(row)
{
	colorCellsOfRow(row, '#ffffff');
	// select checkbox
	selected = filterOut( selected, row.id.substr(2) );
	//$('debug').innerText = selected.join(',');
	var inputs = row.getElementsByTagName('input');
	inputs[0].checked = false;
}

function filterOut(ar, elem)
{
	var a = new Array();
	for( var i=0; i < ar.length; i++ )
	{
		if(ar[i] != elem) 
		{
			a.push( ar[i] );
		}
	}
	return a;
}

function deselectAll()
{
	for(var i=0; i < selected.length; i++)
	{
		deselectRow( $('tr'+selected[i]) );
	}
	selected.length = 0;
}

function colorCellsOfRow(row, color)
{
	var tds = row.getElementsByTagName('td');
	for(var i=0; i < tds.length; i++)
	{
		tds[i].style.backgroundColor = color;
	}
}

function selectAll( val )
{
	for( n=0; n < checkboxes.length; n++ )
	{
		checkboxes[n].checked = val;
	}
}

// zoek functies

function checkFocus( type )
{
}

function checkBlur( type )
{
}

function addResults (text)
{
	setBusy( false );
	$('results').innerHTML = text;
	parseRows(false);
}

function searchError( response )
{
	setBusy( false );
	alert('<?=addcslashes(_("search error"), "'")?>');
	
}

function saveData()
{
	setBusy(true);	
	$('serialized_members').value = selected.join(',');
	
	// fill the dataset with the current addresses being displayed
	var thetable = $('addresses');
	var rows = thetable.getElementsByTagName('tr');
	dataset.length = 0;
	for( var i=1; i < rows.length; i++ )
	{
		dataset.push(rows[i].id.substr(2));
	}
	
	$('serialized_dataset').value = dataset.join(',');

	document.editForm.submit();
}

function setPagination()
{
	var sel = $('results_per_page');
	var num_pages = sel.options[sel.selectedIndex].value;
	$('app').value = num_pages;
	saveData();
}

function showPage(page, params)
{
	$('goto_page').value = page;
	saveData();
}

function checkFocus( type )
{
	switch (type)
	{
		case ('search'):
			enableDocumentMouseDown(true);
			elem = $('search_param');
			if (elem.value != '')
			{
				elem.value = '';
				doSearch();
			}
	}
	
	enableDocumentMouseDown(true);
}

function checkBlur( type )
{
	switch (type)
	{
		case ('search'):
			enableDocumentMouseDown(false);
			break;
	}
	
	enableDocumentMouseDown(false);
}

function switchView( view )
{
	if( view == 'all' )
	{
		groupOnly = 0;
	} else {
		groupOnly = 1;
	}
	
	window.location = 'newsletter_groups.php?group='+groupId+'&group_only='+groupOnly+'&case=show';
}
