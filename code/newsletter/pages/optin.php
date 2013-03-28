<?PHP
/**
 * Admin beginpagina
 *
 * Project: Yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2009
 * @author Christiaan Ottow <chris@6core.net>
 */

$page_name = 'optin';

require(CODE . 'newsletter/code/startup.php');

if( isset($_GET['case']) )
{
	$case = $_GET['case'];
} else {
	$case = '';
}

switch($case)
{
	case 'purge':
		purge();
		break;
	default:
		index();
		break;
}

function index()
{
	global $templates, $data, $yourportfolio, $db;
	$templates[] = 'optin.php';
	
	//get total number of addresses
	$query = sprintf("SELECT COUNT(*) FROM `%s`", $yourportfolio->_table['nl_addresses']);
	$db->doQuery($query, $data['num_addresses'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	// get number of verified addresses
	$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE `verified`=1", $yourportfolio->_table['nl_addresses']);
	$db->doQuery($query, $data['num_verified'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	// get number of unverified addresses
	$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE `verified`=0", $yourportfolio->_table['nl_addresses']);
	$db->doQuery($query, $data['num_unverified'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
}

function purge()
{
	// delete all unverified addresses
	global $db, $yourportfolio;
	
	$null = 0;
	$query = sprintf("DELETE FROM %s WHERE `verified` = 0", $yourportfolio->_table['nl_addresses'] );
	$db->doQuery($query, $null, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	
	header("Location: newsletter_optin.php");
}

do_output();
require(NL_CODE . 'shutdown.php');

?>
