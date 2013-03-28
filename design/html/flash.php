<script type="text/javascript" language="javascript">
<!--
if (flash8_pass)
{
	embedflash("<?=SWFS.$player?>","?m=<?=filectime(SWFS.$player.'.swf')?>","100%","100%",8,"<?=$background?>","path=<?=$file->path.$file->sysname?>");
} else {
	alert("<?=gettext('Macromedia Flash Player 8 of hoger vereist...')?>");
}
-->
</script>
<noscript>
	<?=gettext('Javascript en Flash Player 8 vereist...')?>
</noscript>
