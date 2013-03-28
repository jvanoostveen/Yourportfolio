<?PHP
define('NL_CODE', CODE.'newsletter/code/');

require(CODE.'program/startup.php');

require(NL_CODE.'classes/Newsletter.php');
require(NL_CODE.'classes/NewsletterTemplate.php');
require(NL_CODE.'classes/NewsletterView.php');

if ( ($address = (isset($_GET['aid'])) ? $_GET['aid'] : false) !== false )
{
	$address = $db->filter( $address );
	$result = null;
	$query = sprintf("SELECT `address_id` FROM %s WHERE `address`='%s'", $db->_table['nl_addresses'], $address );
	$db->doQuery( $query, $address_id,__FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	if( $address_id != false )
	{
		$query = sprintf("INSERT INTO %s SET `logstamp`=NOW(), `address`='%s', `address_id`=%d, `remoteip`='%s', `useragent`='%s', `method`='email'",
			$db->_table['nl_optinlog'],
			$address,
			$address_id,
			$_SERVER['REMOTE_ADDR'],
			$_SERVER['HTTP_USER_AGENT']
		);
		$db->doQuery( $query, $result,__FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
			
		$query = sprintf("UPDATE %s SET `verified`=1 WHERE `address_id`=%d", $db->_table['nl_addresses'], $address_id );
		
		$db->doQuery( $query, $result,__FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
		
	}
}
// we always display this message to give the visitor no information about the contents of the database, in error or success cases
echo "Bedankt voor uw aanmelding. U blijft de nieuwsbrief ontvangen.";

?>