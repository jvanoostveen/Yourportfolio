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
// voor fancy editing
var editing = false;
var current_input;
var current_type;
var current_id;
var num_received = 0;

// voor selectie
var selected = new Array();
var lastSelected = -1;
var debugWin;
var commandkey = false;
var mark_color = '#c7d3ed';
var multi_selection = false;

// voor zoeken
var search_url = 'newsletter_edit.php?case=search';
var group = false;

var base_url='newsletter_edit.php';
// voor nieuw adres toevoegen
var check_enabled = true;

// voor mass add
var overlay;
var overlayVisible = false;
// de groepen, voor in mass add form
var groups;
// de html van de lijst adressen
var listContent;
// de html van het mass-add formulier
var massContent;
// hierin wordt bij switchen de invoer opgeslagen
var massData;
// content van quickadd div
var quickadd_html;

// laad adressen bij onLoad
var load_immediately = false;

function init()
{
	
	if( tablepage )
	{
		var thediv = $('numpages');
		thediv.style.top = 0;
	}
	
	if( show_new )
	{
		base_url += '?case=new';
		hideSearch();
	}
	
	setBusy(false);
	window.onresize = resize;

	hideCount();
	hideSearch();
	hidePagination();
	
	if( load_immediately )
	{
		loadInitial();
	}
}

function loadInitial()
{
	loadData();
	setSelectEnabled(false);
	showCount();
	showPagination();
	showSearch();
	setBusy( true );
}

function setSelectEnabled(val)
{
	document.onselectstart = function(){ return val;};
	
}
function loadData()
{
	var url;
	
	if( show_new )
	{
		url = 'newsletter_edit.php?case=loadnew&page='+page;
	} else if ( unused_only )
	{
		url = 'newsletter_edit.php?case=loadunused&page='+page;
	} else {
		url = 'newsletter_edit.php?case=load&f='+filter+'&page='+page;
	}
	new Ajax.Request( url, 
	{
		method: 	'post',
		postBody:	'',
		onComplete:	dataReceived,
		onFailure: 	ajaxError
	});
}

function dataReceived( response )
{
	setBusy(false);
	if( response.responseText != null && response.responseText.length > 0 )
	{
		$('msg').parentNode.removeChild($('msg'));
		$('results').innerHTML = response.responseText;
	} else {
		if( $('msg').innerText )
		{
			$('msg').innerText = '<?=addcslashes(_('Er zijn geen adressen'), "'")?>';
		} else {
			$('msg').textContent = '<?=addcslashes(_('Er zijn geen adressen'), "'")?>';
		}
	}
	
	
	makeRowsClickable();
	makeMassAddForm();
	enableDocumentMouseDown(true);
	
	massData = { addresses: '', selected: '', name: '', groups: [] };
	resize();
}

function ajaxError( response )
{
	alert("<?=addcslashes(_('There was an error loading the data:'), '"')?> "+response.responseText);
}

function makeMassAddForm()
{
	massContent = '<div style="padding-left: 10px;"><form name="massAddForm" id="massAddForm" action="newsletter_edit.php?case=mass" method="post">';
	massContent += '<h3><?=addcslashes(_("Meerdere adressen toevoegen"), "'")?></h3><textarea name="addresses" id="massInput" cols="60" rows="20"></textarea><br>';
	massContent += '<div class="fg_darkgrey" style="padding-left: 2px;"><?=addcslashes(_("formaat"), "'")?>: `<?=addcslashes(_("naam"), "'")?> &lt;<?=addcslashes(_("e-mailadres"), "'")?>&gt;`, `<?=addcslashes(_("naam"), "'")?> <?=addcslashes(_("e-mailadres"), "'")?>` <?=addcslashes(_("of"), "'")?> `<?=addcslashes(_("e-mailadres"), "'")?>`</div>';
	massContent += '<div class="fg_darkgrey" style="padding-left: 2px;"><?=sprintf(_("Bij problemen met importeren, plaats maximaal %s adressen per keer."), 1000)?></div><br>';
	massContent += '<?=addcslashes(_("Adressen toevoegen aan:"), "'")?> <br><div id="groupList">';
	
	for (i in groups)
	{
		massContent += '<input type="checkbox" name="massAdd[groups][]" value="' + i + '"> ' + groups[i] + '<br>';
	}
	
	massContent += '</div><input type="checkbox" name="massAdd[groups][]" value="0" id="new_group"><input type="text" name="newGroupName" id="newGroupName" onKeyUP="editNewGroup();" onChange="editNewGroup();" onBlur="editNewGroup();"><br><br>';
	massContent += '<div class="button"><a class="upload" href="#" onclick="submitMassAdd()"><?=_("voeg toe")?></a></div></div>';
	
	listContent = $('results').innerHTML;
}

function makeRowsClickable()
{
	if( $('addresses') )
	{
		var theTable = $('addresses');
		var trList = theTable.getElementsByTagName('tr');
		
		var num = trList.length;
		
		for(var i=1; i < num; i++)
		{
			var tds = trList[i].getElementsByTagName('td');
			for( var n=0; n < tds.length; n++ )
			{
				tds[n].onclick= function(e) {
					cellClicked(e);
				}
			}
		}
	}
}

function enableDocumentMouseDown(value)
{
}

function cellClicked(e)
{
	var tagName;
	if( window.event && window.event.srcElement )
	{
		tagName = window.event.srcElement.tagName;
	} else {
		tagName = e.target.nodeName;
	}
	
	if( tagName != 'TD' )
	{
		return;
	}
	
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
	var tag = element.tagName ? element.tagName.toLowerCase() : null;
	var row = element.parentNode;
	
	// vind huidig item
	allRows = row.parentNode.getElementsByTagName('tr');
	for(var i=0; i<allRows.length; i++)
	{
		if( allRows[i] == row )
		{
			var pressed = i;
		}
	}
	
	
	// meerdere items selecteren?
	if( e.shiftKey && (lastSelected >= 0 ) )
	{
		// show "delete selection" link
		if( !multi_selection) 
		{
			multi_selection = true;
			$('deleteSelection').style.visibility = 'visible';
		}
		
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
			colorCellsOfRow(allRows[i], mark_color);
			selected.push(allRows[i]);
		}
	} else {
		// hide "delete selection" link
		if( multi_selection && !e.metaKey && !e.ctrlKey)
		{
			multi_selection = false;
			$('deleteSelection').style.visibility = 'hidden';
		}
		
		if( contains( selected, row) )
		{
			if( !e.metaKey && !e.ctrlKey )
			{
				deselectAll();
				selected.push(row);
				colorCellsOfRow(row, mark_color);
				lastSelected = pressed;
			} else {
				selected = filterOut(selected, row);
				colorCellsOfRow(row, '#ffffff');
			}
		} else {
			if( !e.metaKey && !e.ctrlKey )
			{
				deselectAll();
			} else if( !multi_selection ) {
				multi_selection = true;
				$('deleteSelection').style.visibility = 'visible';
			}
			selected.push(row);
			lastSelected = pressed;
			colorCellsOfRow(row, mark_color);
		}
	
	}
}

function filterOut(ar, elem)
{
	var a = new Array();
	for( var i = 0; i < ar.length; i++ )
	{
		if(ar[i] != elem) 
		{
			a.push( ar[i] );
		}
	}
	return a;
}

function contains(ar, elem)
{
	for( var i = 0; i < ar.length; i++ )
	{
		if( ar[i] == elem )
		{
			return true;
		}
	}
	
	return false;
}

function deselectAll()
{
	for(var i = 0; i < selected.length; i++)
	{
		colorCellsOfRow(selected[i], '#ffffff');
	}
	selected = new Array();
}

function colorCellsOfRow(row, color)
{
	var tds = row.getElementsByTagName('td');
	for(var i=0; i < tds.length; i++)
	{
		tds[i].style.backgroundColor = color;
	}
}

function addResults( text )
{
	num_received++;
	setBusy(false);
	$('results').innerHTML = text;
	$('results').style.visibility = 'visible';
	makeRowsClickable();
}

function searchError( response )
{
	alert('<?=addcslashes(_("search error"), "'")?>');
}

function checkSearch()
{
	if( $F('search_param') != '' )
	{
		doSearch();
	}
}

// makeEdit
function e( type, id )
{
	if( editing )
	{
		// trying to edit same field?
		if ( id == current_id && type == current_type )
		{
			return;
		}
		
		saveField();
		if ( editing )
		{
			return;
		}
	}
	
	enableDocumentMouseDown(true);
	
	var field = $( type + id);
	
	var input = document.createElement('INPUT');
	input.type 		= 'text';
	input.className	= 'wide';
	input.name 		= 'edit_'+type+id;
	input.id		= 'edit_'+type+id;
	input.onblur	= onBlurCallback;
	input.onchange	= onChangeCallback;
	input.value		= field.childNodes[0].nodeValue;
	
	field.replaceChild( input, field.childNodes[0] );
	
	input.focus();
	
	editing = true;
	
	current_input = input;
	current_type  = type;
	current_id    = id;
}

function onBlurCallback()
{
//	alert('on blur');
	saveField();
}

function onChangeCallback()
{
//	alert('on change');
	saveField();
}

function saveField()
{
	if( !editing )
	{
		return;
	}
	
	var type  = current_type;
	var id 	  = current_id;
	var value = current_input.value;
	
	var field = document.getElementById( type + id );
	
	
	if( validate( type, value) )
	{
		field.innerHTML = value;
		
		editing = false;
		enableDocumentMouseDown(false);
		
		new Ajax.Request( root_url + '/newsletter_edit.php', 
			{
				method 		: 'post',
				postBody	: 'case=ajax_save_item&value='+value+'&id='+id+'&type='+type,
				onSuccess	: saved,
				onFailure	: error
			});
			
	} else {
		var inputfield = document.getElementById('edit_'+type+id);
		inputfield.focus();
	}
}

function validate( type, value )
{
	if( type == 'addr' )
	{
		if( value.search(/@/) == -1 )
		{
			check_enabled = false;		
			alert('<?=addcslashes(_("Een email adres dient een @ te bevatten"), "'")?>');
			return false;
		} else {
			{
				var parts = value.split('@');
				if( parts.length != 2 )
				{
					check_enabled = false;
					alert('<?=addcslashes(_("Een email adres mag niet meer dan een @ bevatten"), "'")?>');
					return false;
				}
			
				if ( parts[1].search(/\./) == -1 )
				{
					check_enabled = false;
					alert('<?=addcslashes(_("Een email adres dient een punt in de domeinnaam te bevatten"), "'")?>');
					return false;
				}
			}
		}
				
	}
	
	return true;
}

function saved( request )
{
	var text = request.responseText;
	
	var parts = text.split(':');
	
	if( parts[0] == 'E' )
	{
		error( request, false );
	}
	
}

function error( request, direct)
{
	var msg = '<?=addcslashes(_("Er was een fout in uw invoer"), "'")?>';
	if( !direct)
	{
		msg += ':\n\n' + request.responseText;
	}
	
	alert(msg);

	document.location = unescape(window.location.pathname);	
}

function formSubmit()
{
	var addr = $F('new_addr');
	var name = $F('new_name');
	var g_idx = $('group_id_select').selectedIndex;
	var group_id = $('group_id_select').options[g_idx].value;
	if( !validate( 'addr', addr ) )
	{
		return;
	}
	
	$('f_new_addr').value = addr;
	$('f_new_name').value = name;
	$('f_group_id').value = group_id;
	document.newform.submit();
}

// doDelete
function d( id )
{
	if( confirm('<?=addcslashes(_("Weet u zeker dat u dit adres uit de database wilt verwijderen?"), "'")?>') )
	{
		$('del_id').value = id;
		document.delform.submit();
	}
}

function checkFocus( type )
{
	switch (type)
	{
		case ('name'):
			elem = $('new_name');
			if( elem.value == '<?=addcslashes(_("Naam"), "'")?>' )
			{
				elem.value = '';
			}
			break;
		case ('addr'):
			elem = $('new_addr');
			if( elem.value == '<?=addcslashes(_("E-mail adres"), "'")?>' )
			{
				elem.value = '';
			}
			break;
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
		case ('name'):
			elem = $('new_name');
			if( elem.value == '' )
			{
				elem.value = '<?=addcslashes(_("Naam"), "'")?>';
			}
			break;
		case ('addr'):
			elem = $('new_addr');
			if( elem.value == '' )
			{
				elem.value = '<?=addcslashes(_("E-mail adres"), "'")?>';
			}
			break;
		case ('search'):
			enableDocumentMouseDown(false);
			break;
	}
	
	enableDocumentMouseDown(false);
}

function checkSubmitNew( key )
{
	elem = $('new_addr');
	var code;
	
	if (!e)
	{
		var e = window.event;
	}
	
	if (e.keyCode)
	{
		code = e.keyCode;
	} else if (e.which) {
		code = e.which;
	}

	if( code == 13 )
	{
		if( !check_enabled )
		{
			check_enabled = true;
		} else {

			formSubmit();
		}
	}
}

function massAdd()
{
	if( overlayVisible )
	{
		massData['addresses'] = $F('massInput');
		massData['name'] = $F('newGroupName');
		massData['groups'] = [];
		var inputList = $('groupList').getElementsByTagName('input');
		for(var i=0; i < inputList.length; i++)
		{
			massData['groups'].push(inputList[i].checked);
		}
		$('results').innerHTML = listContent;
		showPagination();
		showSearch();
		$('quickadd_link_div').innerHTML = quickadd_html;
		$('quickadd_innercontent').style.visibility = 'visible';		
	} else {
		$('quickadd_innercontent').style.visibility = 'hidden';
		quickadd_html = $('quickadd_link_div').innerHTML;
		$('quickadd_link_div').innerHTML = $('quickadd_2_div').innerHTML;
		$('results').innerHTML = massContent;
		$('massInput').value = massData['addresses'];
		$('newGroupName').value = massData['name'];
		if( massData['name'] != '' )
		{
			$('new_group').checked = true;
		}
		var inputList = $('groupList').getElementsByTagName('input');
		for(var i=0; i < inputList.length; i++)
		{
			inputList[i].checked = massData['groups'][i];
		}
		hidePagination();
		hideSearch();
		
	}	
	
	overlayVisible = !overlayVisible;
}

function editNewGroup()
{
	if ($F('newGroupName') != '')
	{
		$('new_group').checked = true;
	} else {
		$('new_group').checked = false;
	}
}

function submitMassAdd()
{
	if( $F('newGroupName') == '' && $('new_group').checked )
	{
		alert('<?=addcslashes(_("U heeft geen groep naam ingeuld voor de nieuwe groep."), "'")?>');
	} else if( $F('massInput') == '' ) {
		alert('<?=addcslashes(_("U heeft geen adressen ingevuld."), "'")?>');
	} else if( $('new_group').checked && groupExists( $F('newGroupName') )) {
		alert('<?=addcslashes(_("U heeft de naam van een bestaande groep opgegeven."), "'")?>');
		$('newGroupName').focus();
	} else if( confirm( '<?=addcslashes(_("Weet u zeker dat u al deze adressen wil toevoegen?"), "'")?>' ) ) {
		$('massAddForm').submit();
	}
}

function setPagination(filter)
{
	var sel = $('results_per_page');
	var num_pages = sel.options[sel.selectedIndex].value;
	var url = '?app='+num_pages;
	if (filter > 0)
	{
		url += '&f=' + filter;
	}
	
	window.location = url
}

function deleteSelection()
{
	var list = new Array();
	var stringlist = '';
	
	for(var i=0; i < selected.length;i++)
	{
		var row = selected[i];
		var id = row.id.substr(2);
		if( !contains(list, id) )
		{
			list.push(id);
			stringlist += id+',';
		}
	}
	
	stringlist = stringlist.substr(0,stringlist.length-1);
	$('deleteSelectionFormIds').value = stringlist;
	var message = '<?=addcslashes(sprintf(_("%s adressen zullen worden verwijderd, weet u het zeker?"), 'SELECTED_AMOUNT'), "'")?>';
	message = message.replace(/SELECTED_AMOUNT/, list.length);
	if( confirm( message ))
	{
		$('deleteSelectionForm').submit();
	}
}

