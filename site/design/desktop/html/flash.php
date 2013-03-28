<?php
global $system;
global $yourportfolio;

$swf = 'yourportfolio';
$mtime = @filemtime($swf.'.swf');
if (file_exists('yourportfolio_loader.swf'))
{
	$swf = 'yourportfolio_loader';
	$mtime = max($mtime, @filemtime($swf.'.swf'));
}
?>

<script type="text/javascript">

function getViewportSize()
{
	var size = [0, 0];
	if (typeof window.innerWidth != "undefined")
	{
		size = [window.innerWidth, window.innerHeight];
	} else if (typeof document.documentElement != "undefined" && typeof document.documentElement.clientWidth != "undefined" && document.documentElement.clientWidth != 0)
	{
		size = [document.documentElement.clientWidth, document.documentElement.clientHeight];
	} else {
		size = [document.getElementsByTagName("body")[0].clientWidth, document.getElementsByTagName("body")[0].clientHeight];
	}
	return size;
}

function onDOMLoaded()
{
	swfobject.createCSS("html", "height: 100%;");
	swfobject.createCSS("body", "height: 100%; overflow: auto;");
	swfobject.createCSS("object", "outline: none; width: 100%; height: 100%;");
	swfobject.createCSS("#container", "width: 100%; height: 100%;");
	swfobject.createCSS("#swf_container", "top: 0px; left: 0px; margin: 0px; width: 100%; height: 100%; min-width: <?=$yourportfolio->site['resize_div']['min_width']?>px; min-height: <?=$yourportfolio->site['resize_div']['min_height']?>px;");
	window.onresize = function()
	{
		var el = document.getElementById("swf_container");
		var size = getViewportSize();
		el.style.width = size[0] < <?=$yourportfolio->site['resize_div']['min_width']?> ? "<?=$yourportfolio->site['resize_div']['min_width']?>px" : "100%";
		el.style.height = size[1] < <?=$yourportfolio->site['resize_div']['min_height']?> ? "<?=$yourportfolio->site['resize_div']['min_height']?>px" : "100%";
	};
	window.onresize();
	
<? if ($yourportfolio->site['swfobject']['mousewheel']) : /* has mac mousewheel support */ ?>
<? if ($yourportfolio->site['flash']['version'] < 9) : /* AS2 website */ ?>
	var macmousewheel = new SWFMacMouseWheel(document.getElementById("yourportfolio_swf"));
<? else : /* AS3 website */ ?>
	if (typeof(swfmacmousewheel) != 'undefined')
		swfmacmousewheel.registerObject("yourportfolio_swf");
<? endif; /* end flash version */ ?>
<? endif; /* end has mac mousewheel support */ ?>
}


// flashvars: l (language), aid (album id), sid (section id), iid (item id), d (domain tld)
var flashvars = {
	
};
var params = {
	base: "<?php echo $system->base_url; ?>",
	seamlesstabbing: false,
	allowFullScreen: true,
	wmode: "direct",
	bgcolor: "#<?php echo $yourportfolio->prefs['bg_colour']; ?>"
};
var attributes = {
	id: "yourportfolio_swf",
	name: "yourportfolio_swf"
};

swfobject.embedSWF("<?php echo $system->base_url.$swf; ?>" + ".swf?m=<?php echo $mtime; ?>", "container", "100%", "100%", "<?php echo $yourportfolio->site['flash']['version']; ?>", "<?php echo Path::image('expressinstall.swf'); ?>", flashvars, params, attributes);
if (swfobject.hasFlashPlayerVersion("6.0.65"))
{
	swfobject.addDomLoadEvent(onDOMLoaded);
}

</script>