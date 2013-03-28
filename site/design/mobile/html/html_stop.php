<?php
if ($yourportfolio->site['google_analytics']['enabled'])
{
	$googleAnalyticsImageUrl = googleAnalyticsGetImageUrl($GA_ACCOUNT);
	echo '<img src="' . $googleAnalyticsImageUrl . '" />';
}
?>
</body>
</html>