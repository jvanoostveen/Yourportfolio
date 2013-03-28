function submitLogin(e)
{
	var form = document.loginForm;
	
	if (form.login.value.length == 0)
	{
		form.login.focus();
		return false;
	}
	
	if (form.password.value.length == 0)
	{
		form.password.focus();
		return false;
	}
	
	form.password_hash.value = hex_md5(hex_md5(form.password.value) + "" + form.challenge.value);
	
	var code;
	if (!e) var e = window.event;
	
	if (!e.shiftKey && !e.altKey)
	{
		form.challenge.value = "";
		form.password.value = "";
	}
	
	form.submit();
	return false;
}

function windowResized()
{
	//
}