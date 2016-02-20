<?PHP
$lang = (isset($_GET['l']) ? $_GET['l'] : 'nl_NL');

define('LOCALE', '../../locale/');

@putenv("LANG=$lang");
if (!setlocale(LC_ALL, $lang))
{
	trigger_error('Locale '.$lang.' not found');
}

// language domains
bindtextdomain('backend', LOCALE);
bindtextdomain('newsletter', LOCALE);

// current domain
textdomain('newsletter');

header('Content-Type: application/javascript');
?>
var search_threshold = 3;
var showing_results = false;
var saved_sort_name, saved_sort_address;
var in_search = false;
var sort_field = '';
var sort_list = '';
var saved_center_header = '-';
var count_received = true;

function doSearch( )
{

	in_search = true;
	
	if( $F('search_param').length < search_threshold && $F('search_param').length > 0 )
	{
		return;
	} else if( $F('search_param').length == 0 ) {
		in_search = false;
		if( saved_center_header != '-' )
		{
			$('total_count').innerHTML = saved_center_header;
			saved_center_header = '-';
			count_received = false;
		}
		showPagination();
		var param = '';
	} else {
		count_received = true;
		if( saved_center_header == '-')
		{
			saved_center_header = $('total_count').innerHTML;
			$('total_count').innerHTML = '';
		}

		hidePagination();
		var param = $F('search_param');
	}
	
	
	var filter = $('search_filter').value;
	
	setBusy(true);	
	
	/* build up parameters */
	var param = 'param=' + param + '&filter=' + filter;
	
	if( group )
	{
		param += '&gid='+groupId+'&group_only='+$F('group_only');
	}
	
	if( page )
	{
		param += '&page='+page;
	}
	
	if( sort_field != '' && sort_list != '' )
	{
		param += '&o_l='+sort_list+'&o_f='+sort_field;
		sort_field = '';
		sort_list = '';
	}
	
	new Ajax.Request( 
		search_url,
		{
			method: 'post',
			parameters: param,
			contentType: 'application/x-www-form-urlencoded',
			onComplete: responseWrapper,
			onFailure: searchError
		}
	);
	
}

function responseWrapper( response )
{
	var text = response.responseText;
	var parts = text.split('|');
	
	if(parts.length > 1 )
	{
		if( count_received )
		{
			$('total_count').innerHTML = parts[0]+' <?=addcslashes(_("adressen"), "'")?>';
		}
		addResults(parts[1]);
	} else {
		addResults(text);
	}
	
}

function showPagination()
{
	$('paginator').style.visibility = 'visible';
	$('numpages').style.visibility = 'visible';
	
}

function hidePagination()
{
	$('numpages').style.visibility = 'hidden';
	$('paginator').style.visibility = 'hidden';
	
}

function showSearch()
{
	$('search').style.visibility = 'visible';
}

function hideSearch()
{
	$('search').style.visibility = 'hidden';
}

function showCount()
{
	$('total_count').style.visibility = 'visible';
}

function hideCount()
{
	$('total_count').style.visibility = 'hidden';
}
