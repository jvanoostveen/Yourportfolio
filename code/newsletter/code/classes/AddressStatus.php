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
	
	function getStatusName( $status )
	{
		switch( $status )
		{
			case 1:
				return _("OK");
				break;
			case 2:
				return _("Error bij verzenden");
				break;
			case 4:
				return _("Max. aantal errors");
				break;
			case 8:
				return _("Bounced");
				break;
			case 16:
				return _("Uitgeschreven");
				break;
		}	
	}
}
?>