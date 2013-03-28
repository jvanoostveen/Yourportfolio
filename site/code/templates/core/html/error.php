
<style type="text/css">
<!--
body {
	background-color: #000000;
}
.error_warning_title {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	font-weight: bold;
	color: #000000;
}
.error_warning_body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #000000;
	line-height: 14px;
}
.error_warning_detailed {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	font-weight: normal;
	color: #000000;
	font-style: italic;
}
.style1 {color: #FF0000}
.basic {
	font-weight: normal;
}
-->
</style>
<br>
<br>
<br>
<br>
<br>
<table width="60%"  border="0" align="center" cellpadding="15">
  <tr>
    <td background="<?=$system->base_url?><?=CORE_IMAGES?>bg_warning.gif">
    <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFCC00">
      <tr>
        <td bgcolor="#FFFFFF"><table width="100%"  border="0" cellpadding="15">
          <tr>
            <td class="error_warning_title"><img src="<?=$system->base_url?><?=CUSTOM_IMAGES?>client_logo.gif"></td>
          </tr>
          <tr>
            <td class="error_warning_body"> <p class="style1">No compatible Flash plug-in found.</p>
			<p>This website requires a more recent version of Adobe Flash Player then currently installed.</p>
<div id="flashcontent"></div>
<script type="text/javascript">
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

function showFlashInstaller()
{
	var content = get_obj('flashcontent');
	
	var embed = '';
	embed += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="214" height="137" >';
	embed += '<param name="movie" value="design/swf/redirect.swf?targetUrl=<?=$system->base_url?>" />';
	embed += '<param name="bgcolor" value="#FFFFFF" />';
	embed += '<embed src="design/swf/redirect.swf?targetUrl=<?=$system->base_url?>" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="214" height="137" bgcolor="#FFFFFF"></embed>';
	embed += '</object>';
	
	content.innerHTML = embed;
}

var so = new SWFObject("design/swf/redirect.swf", "install", "214", "137", "<?=$yourportfolio->site['flash']['version']?>", "#FFFFFF");
so.setAttribute("xiRedirectUrl", "http://<?=DOMAIN?><?=$system->base_url?>");
so.useExpressInstall("<?=$system->base_url?>design/swf/ExpressInstall.swf");
if ( !so.write("flashcontent") )
{
	showFlashInstaller();
}
</script>
			<p>or download the installer by hand.<br>
			<a href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank"><img src="<?=$system->base_url?>design/swf/get_flash_player.gif" width="88" height="31" border="0"></a></p>

<p class='basic'><br>If you think this error is incorrect, you can use this <a href="http://<?=DOMAIN?><?=$system->base_url?>?detectflash=false">link</a> to disable the Flash check and go back to the site.</p>
              </td>
          </tr>
          <tr>
            <td class="error_warning_detailed">
              If you are experiencing problems, feel free to contact our <a href="mailto:internetengineer@webdebugger.nl?subject=About <?=DOMAIN?>">Internet Engineer</a> and state the nature of your emergency.</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<br>
</body>
</html>
