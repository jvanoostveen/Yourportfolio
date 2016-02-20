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
var groupEditing = '';

function deleteGroup()
{
	if( confirm('<?=addcslashes(_("Weet u zeker dat u deze groep wilt verwijderen?"), "'")?>') )
	{
		if( $('delete_contents_check').checked )
		{
			$('deleteFormDelContents').value = 'yes';
		} else {
			$('deleteFormDelContents').value = 'no';
		}
		
		$('deleteFormGroupId').value = $F('editGroupId');
		document.deleteForm.submit();
	}
}

function groupMade( response )
{
	$('groupField').value = '';

	if( response.responseText != 'OK' )
	{
		alert(response.responseText);
		$('progress').style.visibility = 'hidden';
	} else {
		// reload page
		window.location.reload(true);
	}
}	

function makeGroup()
{
	// set progress image
	var p = $('progress');
	p.style.visibility = 'visible';
	p.style.zIndex = 300;
	
	// ajax request
	var req = new Ajax.Request( 
		'newsletter_groups.php?case=new',
		{
			method: 'post',
			parameters: 'name='+$F('groupField')+'&visible='+$('groupVisible').checked,
			onComplete: groupMade
		}
	);
	
}

// popup toggle
function newGroup()
{
	popup = $('newGroupDiv');
	popup.style.left = (document.body.clientWidth / 2 ) - 150;
	popup.style.top = 200;	
	showPopup();
}

function editGroup(id, name, visibility)
{
	var visible;
	
	if( visibility == 'Y' )
	{
		visible = true;
	} else {
		visible = false;
	}
	
	groupEditing = name;
	
	popup = $('editGroupDiv');
	popup.style.left = (document.body.clientWidth / 2 ) - 150;
	popup.style.top = 200;
	$('editGroupField').value = name;
	$('editGroupId').value = id;
	
	// erase groupVisibleDiv children
	var div = $('groupVisibleDiv');
	for(var i=0; i < div.childNodes.length; i++)
	{
		div.removeChild(div.childNodes[i]);
	}
	
	if( div.innerText )
	{
		div.innerText = '';
	} else {
		div.textContent = '';
	}

	var input = document.createElement('input');
	input.type = 'checkbox';
	input.id = 'groupVisibleCheck';
	div.appendChild(input);
	div.appendChild(document.createTextNode(' <?=addcslashes(_("Groep is zichtbaar op de website"), "'")?>'));
	input.checked = visible;
	
	showPopup();
}

function saveGroupMeta()
{
	var groupID = $F('editGroupId');
	var groupName = $F('editGroupField');
	
	var visible = $('groupVisibleCheck').checked;
	
	if( groupName == '' )
	{
		alert('<?=addcslashes(_("De groep naam mag niet leeg zijn"), "'")?>');
		return;
	} else if( groupExists(groupName) && groupName != groupEditing) {
		alert('<?=addcslashes(_("Er bestaat al een groep met deze naam"), "'")?>');
		return;
	}
	
	groupEditing = '';
	
	// set progress image
	var p = $('progress2');
	p.style.visibility = 'visible';
	p.style.zIndex = 300;
	
	// save everything
	$('saveFormGroupId').value = groupID;
	$('saveFormGroupName').value = groupName;
	$('saveFormGroupVisible').value = visible;
	
	document.saveForm.submit();
	
}

function setOpacity(testObj, value)
{
	testObj.style.opacity = value/10;
	testObj.style.filter = 'alpha(opacity=' + value*10 + ')';
}

function handleKeyPress(e) {
	var key=e.keyCode || e.which;
	
	if (key==13){
		if( popup == $('newGroupDiv')) {
			makeGroup();
		} else {
			saveGroupMeta();
		}
	}
}

// array met x en y t.o.v. body van een element
function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

function showPopup()
{
	var link = $('newGroupLink');

	if( popupVisible )
	{
		enableDocumentMouseDown(false);
		popup.style.visibility = 'hidden';
		popupVisible = false;

		if( do_overlay )
		{
			overlay.visibility = 'hidden';
			overlay.style.zIndex = -100;		
			overlay.style.display = 'none';
		}
		
		input = $('groupField');
		input.value = '';
		return;
	}

	enableDocumentMouseDown(true);
	
	if( do_overlay )
	{
		setOverlaySize();
		overlay.style.zIndex = 10;
		overlay.style.visibility = 'visible';
		overlay.style.display = 'block';
	}
			
	var pos = findPos(link);
	var top = pos[1];
	var diff = findPos(popup.parentNode);
	var vOffset = 24;
	
	popup.style.width = 300;
	popup.style.height = 160;
	popup.style.zIndex = 20;
	popup.style.visibility = 'visible';
	popupVisible = true;
}

function setOverlaySize()
{
	if (!overlay)
		return;
	
	var x,y;
	var test1 = document.body.scrollHeight;
	var test2 = document.body.offsetHeight
	if (test1 > test2) // all but Explorer Mac
	{
		x = document.body.scrollWidth;
		y = document.body.scrollHeight;
	}
	else // Explorer Mac;
	     //would also work in Explorer 6 Strict, Mozilla and Safari
	{
		x = document.body.offsetWidth;
		y = document.body.offsetHeight;
	}
	
	overlay.style.width  	= x;
	overlay.style.height 	= y;
}


function enableDocumentMouseDown(value)
{
	if( $('addresses') )
	{
		if (value)
		{
			$('addresses').onmousedown = null;
		} else {
			$('addresses').onmousedown = function() { return false; };
		}
	}
}
