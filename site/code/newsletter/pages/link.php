<?PHP
/*
 * Project: yptrunk
 *
 * API voor de frontend
 * Geeft een lijst van groepen weer of subscribed iemand (evt in een groep)
 * @author Christiaan Ottow
 * @created Mar 3, 2007
 */
 
define('NL_CODE', CODE.'newsletter/code/');

require(CODE.'program/startup.php');
require(NL_CODE.'classes/AddressStatus.php');

// $db en $yourportfolio zijn beschikbaar
if( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) )
{
	$link_id = (int) $_GET['id'];

	$query = sprintf( "SELECT `link`, `clicks` FROM `%s` WHERE `id`=%d", $yourportfolio->_table['nl_links'], $link_id );
	$res = '';
	$db->doQuery($query,$res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	$row = $res[0];
	
	if( !empty( $row['link'] ) )
	{
		// increase counter
		$query = sprintf( "UPDATE `%s` SET `clicks`=%d WHERE `id`=%d", $yourportfolio->_table['nl_links'], $row['clicks']+1, $link_id );
		$db->doQuery($query,$null, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		header( "Location: ".$row['link'] );
		exit();
	}
}

?>
<html>
<body>
<script language="JavaScript">
	history.back();
</script>
</body>
</html>
