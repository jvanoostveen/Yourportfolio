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
textdomain('backend');

?>
<!--
var ns4 = (document.layers);
var ie4 = (document.all && !document.getElementById);
var ie5 = (document.all && document.getElementById);
var ns6 = (!document.all && document.getElementById);

function get_obj(id)
{
	var tmp_obj = null;
	if (ns4)
	{
		tmp_obj = document.layers[id];
	} else if (ie4)
	{
		tmp_obj = document.all[id];
	} else if (ie5 || ns6)
	{
		tmp_obj = document.getElementById(id);
	}
	
	return(tmp_obj);
}

function windowResized()
{
	var menu_holder = get_obj("menu_holder");
	var menu_content = get_obj("menu_content");
	
	menu_holder.style['max-height'] = window.innerHeight - 80;
	menu_holder.style['height'] = window.innerHeight - 80;
	
	if (menu_content.offsetHeight < menu_holder.offsetHeight)
	{
		menu_holder.style['max-height'] = menu_content.offsetHeight;
		menu_holder.style['height'] = menu_content.offsetHeight;
	}
}

function copyItem()
{
	var album_section_list = get_obj("album_section");
	var album_section_id = get_obj("copy_album_section");
	
	var album_section = album_section_list[album_section_list.selectedIndex].value;
	
	album_section_id.value = album_section;
	
	document.copyForm.submit();
}


function deleteThis() {
	msg = '\n' + document.deleteForm.message.value + '\n';

	result = confirm(msg);

	if (result)
		document.deleteForm.submit();
}

function sectionInput()
{
	var section			= get_obj('section');
	var selectedItem		= section.selectedIndex;
	var selectedItemValue	= section[selectedItem].value;

	var input_cell = get_obj('input_cell');
	
	alert("test: " + input_cell.style.visibility);
	
	if (selectedItemValue == 0) // show
	{
		input_cell.visibility = 'block';
	} else { // hide
		input_cell.visibility = 'visible';
	}
}

var refreshAllowed = false;

function openParser(album_id, section_id)
{
	refreshAllowed = true;
	
	url = 'parser.php?aid=' + album_id + '&sid=' + section_id;
	width = 360;
	height = 230;
	if (screen)
	{
		y = Math.floor((screen.availHeight - height)/2);
		x = Math.floor((screen.availWidth - width)/2);
		
		if (screen.availWidth > 1800)
		{	x = ((screen.availWidth/2) - width)/2; }
	} else {
		x = 100;
		y = 100;
	}
	
	parserWindow = window.open(url,'parserWindow','width=' + width + ',height=' + height + ',screenX=' + x + ',screenY=' + y + ',top=' + y + ',left=' + x + ',scrollbars=no,resizable=no');
}

/**
 * open image crop tool
 */
function openImageCrop(file_id, owner, oid)
{
	url = 'crop.php?fid=' + file_id + '&owner=' + owner + '&oid=' + oid + '&rnd=' + Math.random();
	/* Hoe groot gaat het venster worden (meesturen??) */
	width = 813;
	height = 820;
	if (screen)
	{
		y = Math.floor((screen.availHeight - height)/2);
		x = Math.floor((screen.availWidth - width)/2);
		
		if (screen.availWidth > 1800)
		{	x = ((screen.availWidth/2) - width)/2; }
	} else {
		x = 100;
		y = 100;
	}
	
	cropWindow = window.open(url,'parserWindow','width=' + width + ',height=' + height + ',screenX=' + x + ',screenY=' + y + ',top=' + y + ',left=' + x + ',scrollbars=no,resizable=no');
	
	if (cropWindow.opener == null)
		cropWindow.opener = self;
}

/**
 * preload the uploading image
 */
var progress_image = new Image();
progress_image.src = 'design/img/sync-white.gif';

/**
 * starts playback of animations
 * calls the submitForm after a slight delay
 */
var cansave = true;
function save()
{
	if (!cansave)
	{
		alert("Bezig met gegevens bewaren.");
		return;
	}
	cansave = false;
	
	var progress1 = get_obj('progress1');
	progress1.src = 'design/img/sync-white.gif';
	var progress2 = get_obj('progress2');
	progress2.src = 'design/img/sync-white.gif';
	
	setTimeout('submitForm()', 10); // add this delay otherwise the image won't be loaded (Safari, ...?)
	//document.theForm.submit();
}

/**
 * called by save(), submits the general form
 */
function submitForm()
{
	document.theForm.submit();
}

/**
 * sets the correct values and submits the delete file form
 * @param file_id:Number
 */
function deleteFile(file_id)
{
	msg = '\n' + document.deleteFileForm.message.value + '\n';
	
	document.deleteFileForm.file_id.value = file_id;
	
	result = confirm(msg);

	if (result)
		document.deleteFileForm.submit();
}

function changeFileStatus(value, file_id, property)
{
	var target = get_obj(file_id + '-' + property);
	var field1 = get_obj(file_id + '-' + property + '-div1');
	var field2 = get_obj(file_id + '-' + property + '-div2');
	
	target.value = (value) ? 'Y' : 'N';
	
	// add check to see fields are checkboxes or something else...
	field1.checked = value;
	field2.checked = value;
	
	//alert(file_id + "-" + property + ": " + target.value);
}

function checkFileUpload(file_id, media, input)
{
	var allowed_extensions = get_obj("allowed_extensions_" + file_id).value.toLowerCase();
	var extension = getExtension(input.value).toLowerCase();
	
	if (allowed_extensions == "*")
	{
		// all extensions are valid
		return;
	}
	
	var validExtension = false;
	var extensions = allowed_extensions.split(",");
	var extsListing = "";
	for (var i = 0; i < extensions.length; i++)
	{
		var ext = extensions[i];
		if (extension == ext)
		{
			// valid extension found
			return;
		}
		
		if (extensions.length > 1 && i > 0)
		{
			if (i + 1 == extensions.length)
			{
				extsListing += " <?=gettext('of')?> ";
			} else {
				extsListing += ", ";
			}
		}
		
		extsListing += "." + ext;
	}
	
	if (!validExtension)
	{
		alert("\n<?=sprintf(gettext('U kunt voor %s alleen bestanden met de extensie %s uploaden.\nAndere bestanden zullen worden genegeerd.'), '\'" + media + "\'', '" + extsListing + "')?>\n");
		// simulate click after delay?
		//input.click();
	}
}

function getExtension(value)
{
	return value.substring(value.lastIndexOf('.') + 1,value.length);
}

/**
 * 
 * @param target:String
 * @param show:Boolean
 */
function uploadCollapse(target, open)
{
	var summary = get_obj(target + "-summary");
	var detail = get_obj(target + "-detail");
	
	if (open) // show the upload detail
	{
		summary.style.display = 'none';
		detail.style.display = 'block';
	} else {
		summary.style.display = 'block';
		detail.style.display = 'none';
	}
}

function playMovie(id, obj, width, height)
{
	url = 'play.php?fid=' + id + '&obj=' + obj + '&type=video';
	if (width == 0)
	{
		width = 320;
	}
	if (height == 0)
	{
		height = 280;
	} else {
		height += 40;
	}
	if (screen)
	{
		y = Math.floor((screen.availHeight - height)/2);
		x = Math.floor((screen.availWidth - width)/2);
		
		if (screen.availWidth > 1800)
		{	x = ((screen.availWidth/2) - width)/2; }
	} else {
		x = 100;
		y = 100;
	}
	
	myMoviePlayer = window.open(url,'myMoviePlayer','width=' + width + ',height=' + height + ',screenX=' + x + ',screenY=' + y + ',top=' + y + ',left=' + x + ',scrollbars=no,resizable=no');
}

function playMusic(id, obj)
{
	url = 'play.php?fid=' + id + '&obj=' + obj + '&type=audio';
	width = 320;
	height = 120;
	if (screen)
	{
		y = Math.floor((screen.availHeight - height)/2);
		x = Math.floor((screen.availWidth - width)/2);
		
		if (screen.availWidth > 1800)
		{	x = ((screen.availWidth/2) - width)/2; }
	} else {
		x = 100;
		y = 100;
	}
	
	myMusicPlayer = window.open(url,'myMusicPlayer','width=' + width + ',height=' + height + ',screenX=' + x + ',screenY=' + y + ',top=' + y + ',left=' + x + ',scrollbars=no,resizable=no');
}

var currentLanguage = null;

function switchLanguage(lang)
{
	if (currentLanguage != null)
	{
		var currentMenu = get_obj('lang_' + currentLanguage);
		currentMenu.className = '';
		
		var currentEdit = get_obj('edit_' + currentLanguage);
		currentEdit.style.display = 'none';
	}
		
	var newMenu = get_obj('lang_' + lang);
	newMenu.className = 'active';
	
	var newEdit = get_obj('edit_' + lang);
	newEdit.style.display = 'block';
	
	currentLanguage = lang;
}
// -->