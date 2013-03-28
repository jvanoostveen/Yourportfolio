var flash = 0;

function detectFlash()
{
	if ((navigator.userAgent.indexOf('MSIE') != -1)
		&& (navigator.userAgent.indexOf('Win') != -1))
	{
		document.writeln('<script language="VBscript">');
		document.writeln('Private i, x, ControlVersion');
		document.writeln('ON ERROR RESUME NEXT');
		document.writeln('x = null');
		document.writeln('ControlVersion = 0');
		document.writeln('var Flashmode');
		document.writeln('FlashMode = False');
		document.writeln('For i = 9 To 1 Step -1');
		document.writeln('	Set x = CreateObject("ShockwaveFlash.ShockwaveFlash." & i)');
		document.writeln('	ControlInstalled = IsObject(x)');
		document.writeln('	If ControlInstalled Then');
		document.writeln('		flash = CStr(i)');
		document.writeln('		Exit For');
		document.writeln('	End If');
		document.writeln('Next');
		document.writeln('</scr' + 'ipt>');
	} else {
		if (navigator.plugins && navigator.plugins.length > 0)
		{
			if (navigator.plugins["Shockwave Flash"])
			{
				flash = navigator.plugins["Shockwave Flash"].description;
				flash = flash.substring(flash.indexOf(" ") + 1);
				flash = flash.substring(flash.indexOf(" ") + 1);
			} 
		}
	}
	return parseInt(flash);
}

function testVersion(flash, flash_min)
{
	if (flash >= flash_min)
	{
		return true;
	} else {
		return false;
	}
}

var ns4 = (document.layers);
var ie4 = (document.all && !document.getElementById);
var ie5 = (document.all && document.getElementById);
var ns6 = (!document.all && document.getElementById);

function get_obj(id)
{
	if(ns4)
		var tmp_obj = document.layers[id];
	else if(ie4)
		var tmp_obj = document.all[id];
	else if(ie5 || ns6)
		var tmp_obj = document.getElementById(id);

	return(tmp_obj);
}

function setFlashWidth(divid, newW)
{
	document.getElementById(divid).style.width = newW+"px";
}

function setFlashHeight(divid, newH)
{
	document.getElementById(divid).style.height = newH+"px";		
}

function setFlashSize(divid, newW, newH)
{
	if (newW < minW)
	{
		newW = minW;
	}
	
	if (newH < minH)
	{
		newH = minH;
	}
	
	setFlashWidth(divid, newW);
	setFlashHeight(divid, newH);
}

function canResizeFlash()
{
	var ua = navigator.userAgent.toLowerCase();
	var opera = ua.indexOf("opera");
	if( document.getElementById ){
		if(opera == -1) return true;
		else if(parseInt(ua.substr(opera+6, 1)) >= 7) return true;
	}
	return false;
}

var minW = null;
var minH = null;

function setMinimumSize(nw, nh)
{
	minW = nw;
	minH = nh;
}

function windowResized()
{
	if (ns4 || ns6)
	{
		setFlashSize('flashid', window.innerWidth, window.innerHeight);
	} else if (ie4 || ie5) {
		setFlashSize('flashid', window.top.document.body.clientWidth, window.top.document.body.clientHeight);
	}
}

function embedflash(movie, movievar, width, height, version, bgcolor, flashvars, base)
{
	 flashPlugin('',version, base,'',width,height,'middle','',movie+'.swf' + movievar,flashvars,'','high','',bgcolor,"true",'');
 }
 
function flashPlugin(schema, version, base, id, width, height, align, scroll, movie, flashvars, menu, quality, wmode, bgcolor, swlive, script)
{
	var schema = 'http';
    var classid = "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000";
    var codebase = schema + "://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=" + version;

    var output = (
        "<object" 
        + (id ? attribute("id", id) + attribute("name", id) : "")
        + attribute("classid", classid)
        + attribute("codebase", codebase)
        + attribute("width", width)
        + attribute("height", height)
        + attribute("align", align)
        + (scroll ? attribute("scroll", scroll) : "")
        + ">"
        + param("movie", movie)
        + (flashvars ? param("flashvars", flashvars) : "")
        + param("quality", quality)
        + (base ? param("base", base) : "")
        + (menu ? param("menu", menu) : "")
        + (wmode ? param("wmode", wmode) : "")
        + (bgcolor ? param("bgcolor", bgcolor) : "")
        + "<embed"
        + attribute("src", movie)
        + (id ? attribute("name", id) : "")
        + (flashvars ? attribute("flashvars", flashvars) : "")
        + attribute("quality", quality)
        + attribute("width", width)
        + attribute("height", height)
        + attribute("align", align)
        + attribute("type", "application/x-shockwave-flash")
        + attribute("pluginspace", schema + "://www.macromedia.com/go/getflashplayer")
        + (base ? attribute("base", base) : "")
        + (menu ? attribute("menu", menu) : "")
        + (wmode ? attribute("wmode", wmode) : "")
        + (bgcolor ? attribute("bgcolor", bgcolor) : "")
        + (swlive ? attribute("swliveconnect", swlive) : "")
        + (script ? attribute("mayscript", script) : "")
        + "</embed>"
        + "</object>"
    );
	document.write(output);
}

function attribute(name, value){
    return " " + name + '="' + value + '"';
}

function param(name, value){
    return "<PARAM " + attribute("name", name) + attribute("VALUE", value) + ">";
}