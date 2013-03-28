<?PHP
class AddressStatus
{
	function OK()
	{
		return 1;
	}
	
	function ERROR()
	{
		return 2;
	}
	
	function ERROR_MAX()
	{
		return 4;
	}
	
	function BOUNCED()
	{
		return 8;
	}
	
	function UNSUBSCRIBED()
	{
		return 16;
	}
}
?>