<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created Mar 26, 2007
 */


require(CODE.'newsletter/code/startup.php');

/***********************
 * initialization
 * ********************/
 

$filename = _('adressen');

if( isset($_SESSION['order_group'] ) )
{
	$ordering['field'] = $db->filter( $_SESSION['order_group']['field'] );
	$ordering['dir'] = $db->filter( $_SESSION['order_group']['direction'] );
} else {
	$ordering['field'] = 'name';
	$ordering['dir'] = 'ASC';
}

if( isset($_COOKIE['app']) )
{
	$app = $_COOKIE['app'];
} else {
	$app = 0;
}
	
/***************************
 * parse input variables
 * ************************/

if( !isset($_GET['page']))
{
	ob_end_clean();
	header("Location: newsletter_start.php");
	exit();
}

$page = (int) $_GET['page'];
$filter_where = '';

if( isset($_GET['filter'] ) && $_GET['filter'] != 0)
{
	$filter = (int) $_GET['filter'];
	$filter_where .= "u.status = $filter";	
} else {
	$filter_where .= "u.status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")";
}

/*************************
 * query building
 * **********************/
 
if( isset($_GET['group']) )
{
	$group = (int) $_GET['group'];
	$g = new Group();
	$filename .= '_'.preg_replace("/\s/","_", $g->getName($group));
	$query = sprintf("SELECT u.address_id, u.name, u.address, u.status, u.created, u.recv_count FROM `%s` u, `%s` g WHERE %s AND g.group_id = '%d' AND g.address_id = u.address_id ORDER BY u.%s %s", $yourportfolio->_table['nl_addresses'], $yourportfolio->_table['nl_bindings'], $filter_where, $group, $ordering['field'], $ordering['dir']);
} else {
	$query = sprintf("SELECT u.address_id, u.name, u.address, u.status, u.created, u.recv_count FROM `%s` u WHERE %s ORDER BY u.%s %s", $yourportfolio->_table['nl_addresses'], $filter_where, $ordering['field'], $ordering['dir']);
}

if( $app > 0 )
{
	$from = ($page-1) * $app;
	$query .= " LIMIT $from, $app";
	$to = $from+$app;
	$filename .= "_${from}-${to}";
}

/*********************************
 * data retrieval en parsing
 * *******************************/
$result = array();
$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);

$headers = explode(', ', _("Naam, Adres, Status, Datum aangemaakt, Ontvangen nieuwsbrieven\n"));

define('DELIMITER', ';');

$outfile = implode(DELIMITER, $headers);

if( count($result) > 0 )
{
	foreach( $result as $row )
	{
		$outfile .= $row['name'].DELIMITER.$row['address'].DELIMITER.AddressStatus::getStatusName($row['status']).DELIMITER. $row['created'].DELIMITER. $row['recv_count']. "\n";
	}
}

header("Content-type: text/comma-separated-value");
header("Content-Disposition: attachment; filename=\"${filename}.csv\"");
echo $outfile; 


?>
