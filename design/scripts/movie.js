<!--
function playMovie(id, obj)
{
	url = 'play.php?iid=' + id + '&obj=' + obj;
	width = 320;
	height = 240;
	if (screen)
	{
	/*
		if (screen.availHeight < (height + 43) )
		{	height = screen.availHeight - 42; }
		
		if (screen.availWidth < (width + 10) )
		{	width = screen.availWidth - 10; }
	*/
		y = Math.floor((screen.availHeight - height)/2);
		x = Math.floor((screen.availWidth - width)/2);
		
		if (screen.availWidth > 1800)
		{	x = ((screen.availWidth/2) - width)/2; }
	} else {
		x = 100;
		y = 100;
	}
	
	myMoviePlayer = window.open(url,'myMoviePlayer','width=' + width + ',height=' + height + ',screenX=' + x + ',screenY=' + y + ',top=' + y + ',left=' + x + ',scrollbars=no,resizable=no');
}
// -->