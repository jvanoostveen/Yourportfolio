<?php
$deeplink = '';
if ($node)
	$deeplink = $node->nodeUrl();
else if (YP_MULTILINGUAL)
	$deeplink  = $GLOBALS['YP_CURRENT_LANGUAGE'];

$base = $system->base_url;
if ($base == '/')
	$base = '';
?>
	<script type="text/javascript" src="<?php echo Path::script('swfaddress-optimizer.js'); ?>?swfaddress=<?php echo $deeplink; ?>&base=<?php echo $base; ?>"></script>
