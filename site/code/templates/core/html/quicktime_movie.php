
<style type="text/css" title="text/css">
<!--
div.center
{
	position: relative;
	top: 50px;
	left: 50px;
}
-->
</style>

<div class="center">

<script type="text/javascript">
	var myQTObject = new QTObject("<?=$file->basepath.$file->sysname?>", "player", "<?=$width?>", "<?=$height?>");
	myQTObject.addParam("autoplay", "true");
	myQTObject.addParam("controller", "true");
	myQTObject.addParam("kioskmode", "true");
	myQTObject.write();
</script>

<noscript>
<object classid="clsid:02bf25d5-8c17-4b23-bc80-d3488abddc6b" width="<?=$width?>" height="<?=$height?>" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
<param name="src" value="<?=$file->basepath.$file->sysname?>">
<param name="autoplay" value="true">
<param name="controller" value="true">
<param name="kioskmode" value="true">
<embed src="<?=$file->basepath.$file->sysname?>" width="<?=$width?>" height="<?=$height?>" autoplay="true" controller="true" kioskmode="true" pluginspage="http://www.apple.com/quicktime/download/">
</embed>
</object>
</noscript>
</div>