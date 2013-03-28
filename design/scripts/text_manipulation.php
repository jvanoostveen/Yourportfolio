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
var createLinkId = null;

function createLink(id, title, myTitle)
{
	var myLink;
	mySitemap.close();
	
	if (myTitle == null)
		return;
	if (myTitle != '')
		title = myTitle;
	
	myLink = '[link=' + id + ']' + title + '[/link]';
	
	var field = get_obj(createLinkId);
	field.value += myLink;
	createLinkId = null;
}

function externalLink(fieldName, tag)
{
	var myLink = "";
	var myUrl;
	var myText;
	
	switch (tag)
	{
		case ('elink'):
			msg  = "<?=addcslashes(gettext('Wat is het adres van de link?\n(inclusief http:// of ander protocol)'), '"')?>";
			def  = "http://";
			msg2 = "<?=addcslashes(gettext('Wat is de tekst die u wilt tonen in plaats van het adres (optioneel)'), '"')?>";
			break;
		case ('email'):
			msg = "<?=addcslashes(gettext('Wat is het e-mailadres?'), '"')?>";
			def = "";
			msg2 = "<?=addcslashes(gettext('Wat is de tekst die u wilt tonen in plaats van het e-mailadres (optioneel)'), '"')?>";
			break;
		case ('link'):
			msg = "<?=addcslashes(gettext('Geef de link op van album, sectie en item\n(bv: 1,2,3 of album/section/item)'), '"')?>";
			def = "";
			msg2 = "<?=addcslashes(gettext('Wat is de tekst die u wilt tonen als link?'), '"')?>";
			break;
		default:
			msg = "<?=addcslashes(gettext('Wat is de waarde?'), '"')?>";
			def = "";
			msg2 = "<?=addcslashes(gettext('Wat is de tekst die u wilt tonen in plaats de waarde? (optioneel)'), '"')?>";
	}
	
	myUrl = prompt(msg, def);
	if (myUrl == null || myUrl == '')
		return;
	
	myText = prompt(msg2, myUrl);
	if (myText == null)
		return;
	if (myText == '')
		myText = myUrl;
	
	myLink += '[' + tag + '=' + myUrl + ']' + myText + '[/' + tag + ']';
	
	var field = get_obj(fieldName);
	field.value += myLink;
}

/**
 * creates custom tags for conversion into html tags
 * when used in Internet Explorer on Windows, it retrieves the current selected text
 *
 * @param string $fieldName
 * @param string $tag
 */
function htmlTag(fieldName, tag)
{
	var myTag = '';
	
	var field = get_obj(fieldName);
	
	if (!insert_tag(field, tag))
	{
		if (tag == 'b')
			msg = "<?=addcslashes(gettext('Welke tekst moet vetgedrukt worden?'), '"')?>";
		else if (tag == 'i')
			msg = "<?=addcslashes(gettext('Welke tekst moet schuin worden?'), '"')?>";
		else
			msg = "<?=sprintf(addcslashes(gettext('Welke tekst moet tussen [%s] en [/%s] komen te staan'), '"'), '" + tag + "', '" + tag + "')?>"; 
		
		text = prompt(msg,'');
		
		if (text == null)
			return;
		
		myTag += '[' + tag + ']';
		myTag += text;
		myTag += '[/' + tag + ']';
		
		field.value += myTag;
	}
}

function insert_tag(msgfield, tag)
{
	var open = '[' + tag + ']';
	var close = '[/' + tag + ']';
	
	// IE support
	if (document.selection && document.selection.createRange)
	{
		msgfield.focus();
		sel = document.selection.createRange();
		sel.text = open + sel.text + close;
		msgfield.focus();
	}
	
	// Moz support
	else if (msgfield.selectionStart || msgfield.selectionStart == '0')
	{
		var startPos = msgfield.selectionStart;
		var endPos = msgfield.selectionEnd;
		
		msgfield.value = msgfield.value.substring(0, startPos) + open + msgfield.value.substring(startPos, endPos) + close + msgfield.value.substring(endPos, msgfield.value.length);
		msgfield.selectionStart = msgfield.selectionEnd = endPos + open.length + close.length;
		msgfield.focus();
	}
	
	// Fallback support for other browsers
	else
	{
		return false;
	}
	
	return true;
}
// -->