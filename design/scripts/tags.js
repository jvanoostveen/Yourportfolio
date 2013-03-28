var currently_open = 0;
var open_type = '';

function tags_init()
{
}

function hideTagKeyEdit()
{
	if( currently_open != 0 )
	{
		var close_span = document.getElementById( 'edit_'+open_type+'_'+currently_open );
		close_span.style.visibility = 'hidden';
	}
}

function tag_edit( id, tag )
{
	hideTagKeyEdit();	
	var edit_span = document.getElementById( 'edit_tag_'+id );
	edit_span.style.visibility = 'visible';
	currently_open = id;
	open_type = 'tag';
}

function key_edit( id, key )
{
	hideTagKeyEdit();	
	var edit_span = document.getElementById( 'edit_key_'+id );
	edit_span.style.visibility = 'visible';
	currently_open = id;
	open_type = 'key';
}

function showEdit( gid )
{
	hideTagKeyEdit();
	var span = document.getElementById( 'group_'+gid );
	span.style.visibility = 'visible';
}

function hideEdit( gid )
{
	var span = document.getElementById( 'group_'+gid );
	span.style.visibility = 'hidden';
}

function move_tag( tag_id, group_id )
{
	var form = document.getElementById( 'tagsForm' );
	var action = document.getElementById( 'tagsAction' );
	action.value = 'tag_move';
	var tid_input = document.getElementById( 'tagsParam1' );
	var gid_input = document.getElementById( 'tagsParam2' );
	tid_input.setAttribute('name', 'tagsForm[tag_id]');
	tid_input.value = tag_id;
	gid_input.setAttribute('name', 'tagsForm[group_id]');
	gid_input.value = group_id;
	form.submit();
}

function delete_tag( tag_id )
{
	var form = document.getElementById( 'tagsForm' );
	var action = document.getElementById( 'tagsAction' );
	action.value = 'tag_delete';
	var input1 = document.getElementById( 'tagsParam1' );
	input1.setAttribute('name', 'tagsForm[tag_id]');
	input1.value = tag_id;
	form.submit();
}

function rename_tag( tag_id, tag )
{
	var new_tag = prompt("Enter a new value:",tag);
	if( new_tag != tag && new_tag != null )
	{
		var form = document.getElementById( 'tagsForm' );
		var action = document.getElementById( 'tagsAction' );
		action.value = 'tag_rename';
		var input1 = document.getElementById( 'tagsParam1' );
		var input2 = document.getElementById( 'tagsParam2' );
		input1.setAttribute('name', 'tagsForm[tag_id]');
		input1.value = tag_id
		input2.setAttribute('name', 'tagsForm[tag_value]');
		input2.value = new_tag;
		form.submit();
	}
}

function add_tag( group_id )
{
	var form = document.getElementById( 'tagsForm' );
	var action = document.getElementById( 'tagsAction' );
	action.value = 'tag_add';
	var input1 = document.getElementById( 'tagsParam1' );
	var input2 = document.getElementById( 'tagsParam2' );
	input1.setAttribute('name', 'tagsForm[tag_value]');
	input1.value = document.getElementById('newTag_'+group_id).value;
	input2.setAttribute('name', 'tagsForm[group_id]');
	input2.value = group_id;
	form.submit();
}

function deleteGroup( group_id )
{
	if( confirm( 'Weet u zeker dat u deze groep met alle tags daarin weg wilt gooien?' ) )
	{
		var form = document.getElementById( 'tagsForm' );
		var action = document.getElementById( 'tagsAction' );
		action.value = 'group_delete';
		var input1 = document.getElementById( 'tagsParam1' );
		input1.setAttribute('name', 'tagsForm[group_id]');
		input1.value = group_id;
		form.submit();
	}
}

function renameGroup( group_id, old_name )
{
	new_name = prompt( "Nieuwe naam voor deze groep:", old_name );
	if( new_name != old_name && new_name != null)
	{
		var form = document.getElementById( 'tagsForm' );
		var action = document.getElementById( 'tagsAction' );
		action.value = 'group_rename';
		var input1 = document.getElementById( 'tagsParam1' );
		input1.setAttribute('name', 'tagsForm[group_id]');
		input1.value = group_id;
		var input2 = document.getElementById( 'tagsParam2' );
		input2.setAttribute('name', 'tagsForm[new_name]');
		input2.value = new_name;
		form.submit();
	}
}

function check_add_group( key )
{
	var code;
	
	if (!e)
	{
		var e = window.event;
	}
	
	if (e.keyCode)
	{
		code = e.keyCode;
	} else if (e.which) {
		code = e.which;
	}

	if( code == 13 )
	{
		add_group();
	}
}

function add_group( )
{
	var inp = document.getElementById('newGroupInput');
	var name = inp.value;
	var form = document.getElementById( 'tagsForm' );
	var action = document.getElementById( 'tagsAction' );
	action.value = 'group_add';
	var input = document.getElementById( 'tagsParam1' );
	input.setAttribute('name', 'tagsForm[name]');
	input.value = name;
	form.submit();
}

function check_add_key( key )
{
	var code;
	
	if (!e)
	{
		var e = window.event;
	}
	
	if (e.keyCode)
	{
		code = e.keyCode;
	} else if (e.which) {
		code = e.which;
	}

	if( code == 13 )
	{
		add_key();
	}
}

function add_key( )
{
	var inp = document.getElementById( 'newKeyInput' );
	var keyval = inp.value;
	var form = document.getElementById( 'keysForm' );
	var action = document.getElementById( 'keysAction' );
	action.value = 'key_add';
	var input = document.getElementById( 'keysParam1' );
	input.setAttribute('name', 'keysForm[keyval]');
	input.value = keyval;
	form.submit();
}

function rename_key( key_id, keyval )
{
	var new_val = prompt( "Geef een nieuwe waarde voor dit sleutelwoord op:", keyval );
	if( new_val != keyval && new_val != null)
	{
		var form = document.getElementById( 'keysForm' );
		var action = document.getElementById( 'keysAction' );
		action.value = 'key_rename';
		var input = document.getElementById( 'keysParam1' );
		input.setAttribute('name', 'keysForm[key_id]');
		input.value = key_id;
		var input = document.getElementById( 'keysParam2' );
		input.setAttribute('name', 'keysForm[keyval]');
		input.value = new_val;
		form.submit();
	}
}

function delete_key( key_id )
{
	var form = document.getElementById( 'keysForm' );
	var action = document.getElementById( 'keysAction' );
	action.value = 'key_delete';
	var input = document.getElementById( 'keysParam1' );
	input.setAttribute('name', 'keysForm[key_id]');
	input.value = key_id;
	form.submit();
}
