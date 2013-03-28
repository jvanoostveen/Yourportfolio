<?PHP
/**
 * Beheer van namen en adressen
 *
 * Project: Yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 */

$page_name = 'addresses/edit';

if( !isset( $_GET['case']) )
{
	$case = 1;
} else {
	$case = $_GET['case'];
}

if( isset($_POST['case'] ) )
{
	$case = $_POST['case'];
}
$email_regexp = '[A-Za-z0-9._-]+\@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}';


require(CODE.'newsletter/code/startup.php');

// filters: filter op adres status (bounced, afgemeld)
$filter = (isset($_GET['f']) ? (int) $_GET['f'] : 0);
$filter = (isset($_POST['f']) ? (int)$_POST['f'] : $filter );
$filter = (isset($_POST['filter']) ? (int) $_POST['filter'] : $filter);
$data['filter'] = $filter;

$canvas->addScript('nl_common');
$canvas->addScript('nl_search');
$canvas->addScript('nl_addresses');
$canvas->addScript('nl_groups_popups');

$submenu = array(
	array(
			'name'	=> _('bounces'),
			'icon'	=> 'iconsets/default/users_filled.gif',
			'href'	=> 'newsletter_edit.php?f='.AddressStatus::BOUNCED(),
			'id'	=> 'addresses_bounced',
			'active' => ($filter == AddressStatus::BOUNCED()) ? true : false,
		),
	array(
			'name'	=> _('afgemeld'),
			'icon'	=> 'iconsets/default/users_filled.gif',
			'href'	=> 'newsletter_edit.php?f='.AddressStatus::UNSUBSCRIBED(),
			'id'	=> 'addresses_unsubscribed',
			'active' => ($filter == AddressStatus::UNSUBSCRIBED()) ? true : false,
		),
	array(
			'name'	=> _('nieuwe aanmeldingen'),
			'icon' 	=> 'iconsets/default/users_filled.gif',
			'href'	=> 'newsletter_edit.php?case=new',
			'id'	=> 'addresses_new',
			'active' => ($case == 'new') ? true : false,
		),
	array(
			'name'	=> _('niet in groep'),
			'icon'	=> 'iconsets/default/users_filled.gif',
			'href'	=> 'newsletter_edit.php?case=unused',
			'id'	=> 'addresses_unused',
			'active' => ($case == 'unused' ? true : false),
		),
/* nu nog niet nodig
	array(
			'name'	=> 'problemen',
			'icon'	=> 'iconsets/default/users_filled.gif',
			'href'	=> 'newsletter_edit.php?f='.AddressStatus::ERROR(),
			'id'	=> 'addresses_unsubscribed',
			'active' => ($filter == AddressStatus::ERROR()) ? true : false,
		)*/
	);


$components['bottomBar'][] = 'download_link.php';
$components['bottomBar'][] = 'delete_selection.php';

$ordering = '';
if( isset($_SESSION['order_addr'] ) )
{
	$ordering['field'] = $db->filter( $_SESSION['order_addr']['field'] );
	$ordering['dir'] = $db->filter( $_SESSION['order_addr']['direction'] );
} else {
	$ordering['field'] = 'name';
	$ordering['dir'] = 'ASC';
}

$data['error_threshold'] = $settings['error_threshold'];

switch( $case )
{
	case 'ajax_save_item':
		ajax_save_item();
		break;
	case 'delete_item':
		delete_item();
		break;
	case 'add_item':
		add_item();
		break;
	case 'search':
		search();
		break;
	case 'load':
		load();
		break;
	case 'loadnew':
		load(true);
		break;
	case 'loadunused':
		load(false, true);
		break;
	case 'mass':
		mass_add();
		break;
	case 'new':
		show_addresses(true);
		break;
	case 'unused':
		show_addresses(false, true);
		break;
	case 'delete_selection':
		delete_selection();
		break;
	default:
		show_addresses();
		break;
}

function show_addresses($show_new = false, $unused_only = false)
{
	global $yourportfolio, $db, $data, $templates, $settings, $components;
	$components['topBar'][] = 'search_bar.php';
	$components['pre_bottomBar'][] = 'add_address.php';
	
	// pagination
	$components['bottomBar'][] = 'num_pages.php';
	$components['bottomBar'][] = 'paginator.php';
		
	if( isset($_GET['page']) )
	{
		$data['page'] = (int)$_GET['page'];
	} else {
		$data['page'] = 1;
	}
	$data['selected_app'] = $_COOKIE['app'];
	if( isset($_GET['f']) )
	{
		$data['params'] = 'f='.(int)$_GET['f'];
	}

	$data['show_new'] = $show_new;
	$data['unused_only'] = $unused_only;
	if( $show_new || $unused_only )
	{
		if( !empty($data['params']) )
		{
			$data['params'] .= '&';
		} else {
			$data['params'] = '';
		}
		
		if ($show_new)
			$data['params'] .= 'case=new';
		
		if ($unused_only)
			$data['params'] .= 'case=unused';
	}
	
	// status filter
	$query = "SELECT COUNT(*) FROM `".$yourportfolio->_table['nl_addresses']."` WHERE ";

	if( $show_new )
	{
		$query .= "DATEDIFF( NOW(), created) <= 30 AND ";
	}
	
	if (!isset($_GET['f']) || $_GET['f'] == 0)
	{
		$query .= "status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")";
	} else {
		$query .= "status = '".$db->filter($_GET['f'])."'";
	}
	
	if ($unused_only)
	{
		$query .= "AND address_id NOT IN (SELECT DISTINCT address_id FROM `".$yourportfolio->_table['nl_address_group']."`)";
	}
	
	$total = 0;
	$db->doQuery( $query, $total, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	$settings['page_center_title'] = $total.' '._('adressen');
	$data['num_pages'] =  ceil( $total / $_COOKIE['app']);
	// einde pagination
	
	$query = "SELECT `group_id`, `name` FROM `".$yourportfolio->_table['nl_groups']."` ORDER BY `name`";
	$db->doQuery( $query, $data['groups'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );

	$data['error_threshold'] = $settings['error_threshold'];
	$templates[] = 'edit_list.php';
	if( isset( $_SESSION['message'] ) && !empty( $_SESSION['message'] ) )
	{
		$data['message'] = $_SESSION['message'];
		unset( $_SESSION['message'] );
	}
}

function load($show_new = false, $unused_only = false)
{
	global $db, $data, $yourportfolio, $ordering, $settings, $components, $canvas, $filter;

	if( isset($_GET['page']) )
	{
		$page = (int)$_GET['page'];
	} else {
		$page = 1;
	}
	
	if( isset($_COOKIE['app']) )
	{
		$app = $_COOKIE['app'];
	} else {
		$app = 0;
	}
	
	$data['show_new'] = $show_new;
	$data['unused_only'] = $unused_only;
		
	$query = "SELECT `address_id`, `address`, `name`, `status`, `status_param`, `created` FROM `".$yourportfolio->_table['nl_addresses']."` WHERE";

	// show new subscriptions
	if( $show_new )
	{
		$query .= " DATEDIFF( NOW(), created) <= 30 AND ";
	}
	
	// status filter
	if ($filter == 0)
	{
		$query .= " status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")";
	} else {
		$query .= " status = '".$db->filter($filter)."'";
	}
	
	// show only unused (address not in a group)
	if ($unused_only)
	{
		$query .= "AND address_id NOT IN (SELECT DISTINCT address_id FROM `".$yourportfolio->_table['nl_address_group']."`)";
	}
	
	// ordering
	$query .= sprintf(" ORDER BY %s %s", $ordering['field'], $ordering['dir']);
	
	// pagination
	if( $app > 0 )
	{
		$from = ($page-1) * $app;
		$query .= " LIMIT $from, $app";
	}
	$db->doQuery( $query, $data['addresses'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	
	header("Content-Type: text/html; charset=ISO-8859-1");
	// parse results
	if( is_array($data['addresses']) && count($data['addresses']) > 0 )
	{
		require(NL_TEMPLATES.'components/address_start.php');
		foreach( $data['addresses'] as $row )
		{
			require(NL_TEMPLATES.'components/address_row.php');
		}
		require(NL_TEMPLATES.'components/address_end.php');
	}
		
	exit();
}

function ajax_save_item()
{	
	global $yourportfolio, $db;
	
	ob_end_clean();
	ob_start();
	
	$value = $db->filter( utf8_decode($_POST['value']) );
	$value = trim($value);
	$id = (int) $_POST['id'];
	$type = $db->filter( $_POST['type'] );
	
	if( $type == 'addr' )
	{
		$value = strtolower($value);
		$field = 'address';
		if( !valid_email($value) )
		{
			echo 'E:'.$value._(' is geen geldig e-mail adres');
			exit();
		}
	} else if ($type == 'name' )
	{
		$field = 'name';
	}
	
	$result = null;
	$query = sprintf( "UPDATE `%s` SET `%s` = '%s', modified = NOW(), status=IF(status=".AddressStatus::BOUNCED().", ".AddressStatus::OK().", status), status_param=0 WHERE `address_id` = '%d'", $yourportfolio->_table['nl_addresses'], $field, $value, $id );
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
	
	if( !$result )
	{
		echo "E:error in query";
	} else {
		echo "S:success";
	}
	
	log_message( 'event', "Address info for addr $id updated through Ajax call", debug_backtrace() );
	
	ob_flush();
	exit();

}

function add_item()
{
	global $db, $yourportfolio, $templates;
	
	$name = $db->filter($_POST['name']);
	$addr = strtolower($db->filter($_POST['addr']));
	$addr = trim($addr);
	$group_id = $db->filter($_POST['group_id']);
	
	if ($name == 'Naam')
	{
		$name = $addr;
	}
	
	// check if emailaddress is new.
	$result = null;
	$query = sprintf( "SELECT address_id, status FROM `".$yourportfolio->_table['nl_addresses']."` WHERE address='%s' LIMIT 1", $addr);
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'resource', false );
	
	$address_id = 0;
		
	if (mysql_num_rows($result) == 1)
	{
		// emailadress already exists
		// check if status is unsubscribed, in which case it will be reset to OK
		list($address_id, $status) = mysql_fetch_row($result);
		if( $status == AddressStatus::UNSUBSCRIBED() )
		{
			$query = sprintf( "UPDATE `%s` SET `status`='%d' WHERE `address_id`='%d'", $yourportfolio->_table['nl_addresses'], AddressStatus::OK(), $address_id );
			$res = null;
			$db->doQuery($query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
		}
		if( $status == AddressStatus::BOUNCED() )
		{
			$_SESSION['message'] = include_message('address_error', array('msg' => _('Het adres staat al in de lijst (bij bounces) en is niet nog een keer toegevoegd.') ) );
		} else {
			$_SESSION['message'] = include_message('address_error', array('msg' => _('Het adres staat al in de lijst en is niet nog een keer toegevoegd.') ) );
		}
	} else {
		$query = sprintf( "INSERT INTO `%s` SET `name` = '%s', `address` = '%s', `status` = '%d', `created` = NOW(), `modified` = NOW()", $yourportfolio->_table['nl_addresses'], $name, $addr, AddressStatus::OK() );
		$db->doQuery( $query, $address_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
	}
	
	if (!empty($group_id) && $group_id != 0 )
	{
		$result = null;
		$query = sprintf("INSERT INTO `%s` SET `address_id` = '%d', `group_id` = '%d'", $yourportfolio->_table['nl_bindings'], $address_id, $group_id );
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
	}
	
	log_message( 'event', "Address $addr added", debug_backtrace() );
	show_addresses();
}

function delete_item($filter = 0)
{
	global $db, $yourportfolio;
	
	$item = (int) $_POST['id'];
	
	$result = null;

	$query = sprintf( "SELECT `address` FROM `%s` WHERE `address_id`='%s'", $yourportfolio->_table['nl_addresses'], $item );
	$addr = '';
	$db->doQuery( $query, $addr, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	
	$query = sprintf( "DELETE FROM `%s` WHERE `address_id` = '%s'", $yourportfolio->_table['nl_addresses'], $item );
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
	$query = sprintf("DELETE FROM `%s` WHERE `address_id`='%s'", $yourportfolio->_table['nl_bindings'], $item);
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
	
	log_message( 'event', "Address $addr(id=$item) deleted from database", debug_backtrace() );
	
	show_addresses();
}

function delete_selection()
{
	global $db, $yourportfolio;
	
	$list = explode(',', $_POST['ids']);
	$ids = '(';
	foreach($list as $id)
	{
		$ids .= (int)$id.',';
	}
	$ids = substr($ids, 0, strlen($ids)-1).')';
	
	$query = sprintf("DELETE FROM `%s` WHERE `address_id` IN $ids", $yourportfolio->_table['nl_addresses']);
	$db->doQuery( $query, $list, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );	
	$query = sprintf("DELETE FROM `%s` WHERE `address_id` IN $ids", $yourportfolio->_table['nl_bindings']);
	$db->doQuery( $query, $list, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
	
	log_message( 'event', "Selection of addresses $ids deleted", debug_backtrace() );
	show_addresses();
	 
}

function search()
{
	global $yourportfolio, $db, $canvas, $settings, $filter, $ordering;
	
	if( isset( $_POST['param'] ) )
	{
		$param = $_POST['param'];
	} else {
		exit();
	}
	
	if( isset($_GET['page']) )
	{
		$page = (int)$_GET['page'];
	} else if( isset($_POST['page']) ) {
		$page = (int) $_POST['page'];
	} else {
		$page = 1;
	}
	
	if( isset($_COOKIE['app']) )
	{
		$app = $_COOKIE['app'];
	} else {
		$app = 0;
	}
		
	$regex = '%'.$db->filter($param).'%';
	if( $regex == '' )
	{
		$regex = '.*';
	}
	
	$query = "SELECT `address_id`, `address`, `name`, `status`, `created`, `status`, `status_param` FROM `".$yourportfolio->_table['nl_addresses']."` WHERE ";
	if ($filter == 0)
	{
		$query .= "status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")";
	} else {
		$query .= "status = '".$db->filter($filter)."'";
	} 
	$query .= sprintf(" AND (`address` LIKE '%s' OR `name` LIKE '%s') ORDER BY `%s` %s", utf8_decode($regex), utf8_decode($regex), $ordering['field'], $ordering['dir']);
	$result = array();
	
//	$imquery = "SHOW VARIABLES LIKE 'c%'";
//	$r = null;
//	$db->doQuery($imquery, $r, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
//	var_dump($r);
//	die();
	
	// pagination: alleen pagineren als zoekterm leeg is
	if( $app > 0 && $param == '')
	{
		$from = ($page-1) * $app;
		$query .= " LIMIT $from, $app";
	}
	
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	if ($result === false)
	{
		$result = array();
	}
	
	header("Content-Type: text/html; charset=ISO-8859-1");
	
	print(count($result).'|');
	
	if (count($result) > 0)
	{
		require(NL_TEMPLATES . 'components/address_start.php');
		$data['error_threshold'] = $settings['error_threshold'];
		
		foreach( $result as $row )
		{
			require(NL_TEMPLATES . 'components/address_row.php');
		}
		require(NL_TEMPLATES . 'components/address_end.php');
	}	
	exit();
}

function mass_add()
{
	global $db, $yourportfolio, $email_regexp, $system, $settings;
	
	$data = $db->filter($_POST['addresses']);
	$group_form = array();
	if( isset($_POST['massAdd']['groups']) )
	{
		$groups_form = $_POST['massAdd']['groups'];
	}
	
	$gname = $db->filter($_POST['newGroupName']);
	
	$groups = array();
	$emails = array();
	$current_emails = array();
	$group_only = array();
	$update_status = array();
	
	// haal alle huidige adressen. dit is sneller dan voor alle input een query doen om te checken
	$query = "SELECT address_id AS id, address, status FROM `".$yourportfolio->_table['nl_addresses']."`";
	$db->doQuery($query, $current_emails, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array', false, array('index_key' => 'address') );

	if( !$current_emails )
	{
		$current_emails = array();
	}
	
	// adressen filteren
	$data = str_replace("\r", '', $data);
	$lines = explode('\n',$data);
	
	$regex1 = '/(.+)\s\<?('.$email_regexp.')\>?/';
	$regex2 = '/\<?('.$email_regexp.')\>?/';
	
	$emails_temp = array_keys($current_emails);
	
	$skipped = array();
	$invalid = array();
	$addcnt  = 0;
	
	foreach( $lines as $line )
	{
		$line = trim($line);
		$matches = array();
		if( preg_match( $regex1, $line, $matches ) == 1 )
		{
			$addr = $matches[2];
			$name = $matches[1];
		} else if( preg_match( $regex2, $line, $matches) == 1) {
			$addr = $matches[1];
			$name = $matches[1];
		} else {
			if( !empty( $line ) )
			{
				$invalid[] = $line;
			}
			continue;
		}
		
		if( $addr == '' )
		{
			$addr = $name;
		}
		
		$addr = trim($addr);
		
		// clean name from tabs and excess whitespace
		$name = str_replace(array("\t", "\r", "\n"), ' ', $name);
		$name = eregi_replace(" +", " ", $name);
		
		if ( !cis_contains($addr, $emails_temp) && valid_email($addr))
		{
			// is a new emailaddress
			$emails[] = array('id' => 0, 'name' => $name, 'mail' => strtolower($addr));
			$emails_temp[] = $addr;
			$addcnt++;
		} else {
			// address is not in de current list, but is in the emails_temp list (duplicate add entries)
			if (!isset($current_emails[$addr]))
			{
				continue;
			}
			
			if( $current_emails[$addr]['status'] == AddressStatus::UNSUBSCRIBED() )
			{
				// status updaten
				if( $settings['debug'] == true)
				{
					trigger_error("Added to 'update status' queue: ".$current_emails[$addr]['address']);
				}
				$update_status[] = $current_emails[$addr]['id'];
			} else {
				if( !empty( $addr ) )
				{
					$skipped[] = $addr;
				}
			}
			
			// emailaddress already exists, add to group list when it isn't in the array yet.
			if (!in_array($current_emails[$addr]['id'], $group_only))
			{
				$group_only[] = $current_emails[$addr]['id'];
			}
		}
	}
	
	unset($emails_temp, $lines);
	
	// add new group on the fly
	if( in_array(0, $groups_form) && !empty($gname))
	{
		$query = sprintf("INSERT INTO `%s` SET `name`='".$gname."'", $yourportfolio->_table['nl_groups']);
		$db->doQuery($query, $groups[], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false ); 
	}
	
	// check for existence of groups supplied
	foreach ($groups_form as $group_id)
	{
		$group_id = (int) $group_id;
		if ($group_id == 0)
		{
			continue;
		}
		
		if (group_id_exists($group_id))
		{
			$groups[] = $group_id;
		}
	}
	
	// loop through emails to add
	if (!empty($emails))
	{
		foreach ($emails as $key => $email)
		{
			$query = sprintf("INSERT INTO `%s` SET `name`='".$email['name']."', `address`='".$email['mail']."', `created`=NOW(), `status`=1, `recv_count`=0", $yourportfolio->_table['nl_addresses']);
			$db->doQuery($query, $email['id'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
			
			foreach ($groups as $group_id)
			{
				$result = null;
				$query = sprintf("INSERT INTO `%s` SET `group_id`='".$group_id."', `address_id`='".$email['id']."'", $yourportfolio->_table['nl_bindings']);
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
			}
		}
	}
	
	// loop through unsubscribed addresses to re-activate
	if( !empty( $update_status ) )
	{
		$list = '';
		foreach( $update_status as $addr )
		{
			$list .= "'$addr', ";
		}
		$list = substr($list, 0, strlen($list)-2);
		
		$query = sprintf( "UPDATE %s SET `status`='%d' WHERE `address_id` IN (%s)", $yourportfolio->_table['nl_addresses'], AddressStatus::OK(), $list );
		$void = null;
		$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
	}
	
	// loop through email ids for group bindings
	if (!empty($group_only))
	{
		$group_bindings = array();
		$query = "SELECT group_id, address_id FROM `".$yourportfolio->_table['nl_bindings']."`";
		$db->doQuery($query, $group_bindings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_value', false, array('index_key' => 'address_id', 'value' => 'group_id') );
		
		foreach ($group_only as $email_id)
		{
			foreach ($groups as $group_id)
			{
				// email id is already in group?
				if (isset($group_bindings[$email_id]) && in_array($group_id, $group_bindings[$email_id]))
				{
					continue;
				}
				
				$result = null;
				$query = sprintf("INSERT INTO `%s` SET `group_id`='".$group_id."', `address_id`='".$email_id."'", $yourportfolio->_table['nl_bindings']);
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
			}
		}
	}
	
	log_message( 'event', 'Done with mass add of '.count($emails).' addresses', debug_backtrace() );
	$_SESSION['message'] = include_message( 'massadd_report', array( 'invalid' => $invalid, 'skipped' => $skipped, 'addcnt' => $addcnt ) );
	$system->relocate('newsletter_edit.php');
}

function valid_email( $addr )
{
	global $email_regexp;
	return preg_match('/^'.$email_regexp.'$/', $addr);
}

// case-insensitive in_array
function cis_contains($needle, $haystack)
{
	foreach($haystack as $elem )
	{
		if( strtolower($elem) == strtolower($needle) )
		{
			return true;
		}
	}
	
	return false;
}

do_output();
require(NL_CODE . 'shutdown.php');

?>
	
	