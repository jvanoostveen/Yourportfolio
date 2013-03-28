<?php
$query_string = $_SERVER['QUERY_STRING'];

// store original query string in session
$_SESSION['deeplink'] = $query_string;

if (YP_MULTILINGUAL)
{
	$query = explode('/', $query_string);
	
	// last entry is always nonsense (because of trailing slash)
	if (substr($query_string, -1, 1) == '/')
	{
		array_pop($query);
	}
	
	if (in_array($query[0], array_keys($GLOBALS['YP_LANGUAGES'])))
	{
		$language = $query[0];
		$GLOBALS['YP_CURRENT_LANGUAGE'] = $language;
	}
}

$base = (substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')));
if (empty($base))
	$base = '/';

exit('location.replace("'.$base.'#/'.$query_string.'")');
