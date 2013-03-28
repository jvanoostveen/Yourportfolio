<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Opens the html page, loads the stylesheets and javascript files.
 *
 * @package yourportfolio
 * @subpackage Pages
 */
?>
<html>
<head>
	<title><?=$canvas->filter($yourportfolio->photographer_name)?> beheer :: <?=$canvas->filter($yourportfolio->title)?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	<meta http-equiv="X-UA-Compatible" content="IE=9" />
<? foreach($canvas->stylesheets as $stylesheet) : /* page needs stylesheet files */ ?>
	<link href="<?=CSS?><?=$stylesheet?>.css?c=<?=filectime(CSS.$stylesheet.'.css')?>" rel="stylesheet" type="text/css">
<? endforeach; /* end load stylesheet files */ ?>
<? foreach($canvas->scripts as $script) : /* page needs script files */ ?>
	<script language="<?=$script['lang']?>" type="<?=$script['type']?>" src="<?=SCRIPTS.$script['file'].$script['ext']?>?c=<?=filectime(SCRIPTS.$script['file'].$script['ext'])?><?=(!empty($script['l']) ? '&l='.$yourportfolio->display_language : '')?>"></script>
<? endforeach; /* end load script files */ ?>
<? foreach($canvas->raw_scripts as $script) : /* page script */ ?>
	<script language="<?=$script['lang']?>" type="<?=$script['type']?>"><?=$script['script']?></script>
<? endforeach; /* end script */ ?>
<? if (file_exists('../favicon.ico')) : /* has own favion */ ?>
	<link rel="shortcut icon" href="../favicon.ico" />
<? else : /* load yp favion */ ?>
	<link rel="shortcut icon" href="../<?=IMAGES?>favicon.ico" />
<? endif; /* end favicon */ ?>
</head>
<body <?=$canvas->generateBodyTags()?> bgcolor="#FFFFFF" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginheight="0" marginwidth="0">
<? if ($canvas->showCalendar) : ?>
<script language="javascript" type="text/javascript" src="<?=SCRIPTS?>scw.js?c=<?=filectime(SCRIPTS.'scw.js')?>"></script>
<? endif; ?>
<? if (!empty($messages->messages)) : /* has feedback messages */ ?>
<div id="MessageQueue" class="popupDiv">
	<table width="100%" style="padding: 0; margin: 0; " cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td class="heading" width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
		<td class="heading" width="30" valign="top"><img src="<?php echo $canvas->showIcon($messages->getIcon()); ?>" class="special"></td>
		<td class="namebar" width="100%"><?php echo $messages->getTitle(); ?></td>
		<td class="namebar" align="right">&nbsp;</td>
		<td class="heading" width="27" valign="top"><a href="javascript:void(0)" onclick="showPopup()"><img src="design/iconsets/default/close.gif" width="27" height="28" class="special" border="0"></a></td>
		<td class="heading" width="1" valign="top"><img src="design/img/round_row.gif" width="1" height="28" class="special"></td>
	</tr>
	<tr>
		<td colspan="6">
			<div width="100%" class="popupInnerDiv">
				<div style="position: absolute; top: 20px; left: 20px; font-weight: normal;"><?=nl2br(implode("\n", $messages->messages))?></div> 
				<div class="button" style="position: absolute; bottom: 15px; right: 10px;"><a class="upload" href="javascript:void(0)" onclick="showPopup()"><?=gettext('OK')?></a></div>
			</div>
		</td>
	</tr>
	</table>
</div>
<script language="javascript">
var overlay;

overlay = document.createElement('div');
overlay.style.position 	= 'absolute';

overlay.style.top = 0;
overlay.style.left = 0;	
overlay.style.backgroundColor = '#aaaaaa';
overlay.visibility = 'hidden';
overlay.style.display = 'none';
overlay.style.zIndex = -100;
overlay.id = 'greyOverlay';
overlay.onclick = showPopup;
setOverlaySize();

setOpacity(overlay, 5);

document.body.appendChild(overlay);

popup = get_obj('MessageQueue');
popup.style.left = (document.body.clientWidth / 2 ) - 150;
popup.style.top = 200;

var popupVisible = false;

showPopup();

function showPopup()
{
	if( popupVisible )
	{
		popup.style.visibility = 'hidden';
		popupVisible = false;

		overlay.visibility = 'hidden';
		overlay.style.zIndex = -100;
		overlay.style.display = 'none';
		
		return;
	}

	setOverlaySize();
	overlay.style.zIndex = 10;
	overlay.style.visibility = 'visible';
	overlay.style.display = 'block';
	
	popup.style.width = 300;
	popup.style.height = 160;
	popup.style.zIndex = 20;
	popup.style.visibility = 'visible';
	popupVisible = true;
}

function setOverlaySize()
{
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
	
	overlay.style.width = x;
	overlay.style.height = y;
}

function setOpacity(testObj, value)
{
	testObj.style.opacity = value/10;
	testObj.style.filter = 'alpha(opacity=' + value*10 + ')';
}
</script>
<? endif; /* end has feedback messages */ ?>
