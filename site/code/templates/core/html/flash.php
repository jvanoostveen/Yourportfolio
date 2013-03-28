<div id="container">
<div id="yp_content_div">
<? $canvas->skipHeaders = true; ?>
<? require(CODE.'pages/noflash.php');?>
<? $canvas->skipHeaders = false; ?>
</div>
</div>

<script type="text/javascript">
// show flash needed page.
if (!swfobject.hasFlashPlayerVersion("6.0.65"))
{
	window.location = "<?=$system->base_url?>error.php?error=flash";
}

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
	swfobject.createCSS("object", "outline: none;");
	swfobject.createCSS("#container", "top:0px; left:0px; margin:0; width:100%; height:100%; min-width:<?=$yourportfolio->site['resize_div']['min_width']?>px; min-height:<?=$yourportfolio->site['resize_div']['min_height']?>px;");
	window.onresize = function()
	{
		var el = document.getElementById("container");
		var size = getViewportSize();
		el.style.width = size[0] < <?=$yourportfolio->site['resize_div']['min_width']?> ? "<?=$yourportfolio->site['resize_div']['min_width']?>px" : "100%";
		el.style.height = size[1] < <?=$yourportfolio->site['resize_div']['min_height']?> ? "<?=$yourportfolio->site['resize_div']['min_height']?>px" : "100%";
	};
	window.onresize();
	
<? if ($yourportfolio->site['swfobject']['mousewheel']) : /* has mac mousewheel support */ ?>
<? if ($yourportfolio->site['flash']['version'] < 9) : /* AS2 website */ ?>
	var macmousewheel = new SWFMacMouseWheel(document.getElementById("yourportfolio_swf"));
<? else : /* AS3 website */ ?>
	if (swfmacmousewheel != null)
	{
		swfmacmousewheel.registerObject("yourportfolio_swf");
	}
<? endif; /* end flash version */ ?>
<? endif; /* end has mac mousewheel support */ ?>
}

var flashvars = {<?=join(', ', $flashvars)?>};
var params = {
	base: "<?=$system->base_url?>",
	seamlesstabbing: false,
	allowFullScreen: true,
	wmode: "direct",
	bgcolor: "#<?=$yourportfolio->prefs['bg_colour']?>"
};
var attributes = {
	id: "yourportfolio_swf",
	name: "yourportfolio_swf"
};

<?PHP
$swf = file_exists('yourportfolio_loader.swf') ? 'yourportfolio_loader' : 'yourportfolio';
?>
swfobject.embedSWF("<?=$system->base_url.$swf?>" + ".swf?m=<?=@filectime('yourportfolio.swf')?>", "yp_content_div", "100%", "100%", "<?=$yourportfolio->site['flash']['version']?>", "<?=$system->base_url?>design/swf/ExpressInstall.swf", flashvars, params, attributes);
if (swfobject.hasFlashPlayerVersion("6.0.65"))
{
	swfobject.addDomLoadEvent(onDOMLoaded);
}
</script>

<? if (file_exists(CUSTOM_HTML.'custom.php')) : /* has custom php file */ ?>
<? include(CUSTOM_HTML.'custom.php'); ?>
<? endif; ?>