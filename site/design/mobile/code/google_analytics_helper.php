<?php
// Copyright 2009 Google Inc. All Rights Reserved.
function googleAnalyticsGetImageUrl($account)
{
	$GA_PIXEL = '/ga.php';
	
	$url = '';
	$url .= $GA_PIXEL . '?';
	$url .= 'utmac=' . $account;
	$url .= '&utmn=' . rand(0, 0x7fffffff);
	
	$referer = '-';
	if (!empty($_SERVER['HTTP_REFERER']))
		$referer = $_SERVER['HTTP_REFERER'];
	$query = $_SERVER['QUERY_STRING'];
	$path = $_SERVER['REQUEST_URI'];
	$url .= '&utmr=' . urlencode($referer);
	if (!empty($path)) {
		$url .= '&utmp=' . urlencode($path);
	}
	$url .= '&guid=ON';
	return str_replace('&', '&amp;', $url);
}
