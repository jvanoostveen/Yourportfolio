var pxextra = 0;

// voor IE: resize de tabel met content
// de andere browsers doen dit automatisch omdat ze DIVS hebben, geen tabel
function resize()
{
	if( tablepage )
	{
		var height = document.body.offsetHeight;
		$('contentcell').style.height = (height-90-pxextra);
		var width = document.body.offsetWidth;
		$('contentcell').style.width = (width-260);
	}
	
	var menu_holder = $('menu_holder');
	var menu_content = $('menu_content');
	
	menu_holder.style['max-height'] = window.innerHeight - 80;
	menu_holder.style['height'] = window.innerHeight - 80;
	
	if (menu_content.offsetHeight < menu_holder.offsetHeight)
	{
		menu_holder.style['max-height'] = menu_content.offsetHeight;
		menu_holder.style['height'] = menu_content.offsetHeight;
	}
}

function setBusy( busy )
{
	if( busy )
	{
		var val = 'visible';
	} else {
		var val = 'hidden';
	}

	if( $('saving1') )
		$('saving1').style.visibility = val;
	if( $('saving2') )
		$('saving2').style.visibility = val;
}

function groupExists( group )
{
	for( var i in groups)
	{
		if( groups[i] == group )
		{
			return true;
		}
	}
	
	return false;
}

function array_contains( elem, array )
{
	for( var i=0; i<array.length;i++ )
	{
		if( array[i] == elem )
		{
			return true;
		}
	}
	return false;
}

function sortDispatcher( field, list, extra )
{
	if( !in_search)
	{
		sortBy( field, list, extra );
	} else {
		sort_field = field;
		sort_list = list;
		doSearch();
	}
}

function sortBy( field, list, extra )
{
	var param = '&o_f='+field+'&o_l='+list+extra;
	new Ajax.Request( base_url, 
		{
			method: 'post',
			parameters: param,
			onComplete: sortWrapper,
			onFailure: searchError
		}
	);
}

function sortWrapper( response )
{
	addResults(response.responseText );
}

function contains(ar, elem)
{
	for( var i=0; i<ar.length; i++ )
	{
		if( ar[i] == elem )
		{
			return true;
		}
	}
	
	return false;
}

