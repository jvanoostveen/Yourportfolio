<?PHP
/**
 * Admin beginpagina
 *
 * Project: Yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 */

$page_name = 'start/start';

require(CODE . 'newsletter/code/startup.php');
require('Net/POP3.php');

$canvas->addScript('nl_common');
$canvas->addScript('nl_start');
$canvas->addBodyTag('onBeforeUnload', 'pageUnloadHandler()');

if( isset($_GET['case']) )
{
	$case = $_GET['case'];
} else {
	$case = '';
}

switch($case)
{
	case 'check':
		getMail();
		break;
	default:
		index();
		break;
}


function index()
{
	global $templates, $data, $yourportfolio, $db;
	$templates[] = 'main.php';
	
	//get total number of addresses
	$query = sprintf("SELECT COUNT(*) FROM `%s`", $yourportfolio->_table['nl_addresses']);
	$db->doQuery($query, $data['num_addresses'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	// get total number of groups
	$query = sprintf("SELECT COUNT(*) FROM `%s`", $yourportfolio->_table['nl_groups']);
	$db->doQuery($query, $data['num_groups'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	// get number of sent newsletters
	$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE status='sent'", $yourportfolio->_table['nl_letters']);
	$db->doQuery($query, $data['num_sent'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	// purge newsletter log for entries older then 60 days
	$result = null;
	$query = "DELETE FROM `".$yourportfolio->_table['nl_log']."` WHERE DATEDIFF(CURRENT_DATE(), `date`) > 60";
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	
	// purge newsletter incoming entries older then 60 days
	$query = "DELETE FROM `".$yourportfolio->_table['nl_incoming']."` WHERE DATEDIFF(CURRENT_DATE(), `date_inserted`) > 60";
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	
	// optimize tables where rows are deleted
	$query = "OPTIMIZE TABLE `".implode("`,`", array($yourportfolio->_table['nl_log'], $yourportfolio->_table['nl_incoming']))."`";
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
}

/**
 * Geef een afzender adres
 */
 
function get_sender($headers)
{
	$from = $headers['From'];
	
	if( preg_match("/.*<(.*)>.*/", $from, $matches) )
	{
		return $matches[1];
	} else {
		return $from;
	}
}

/**
 * Tests if a message is a bounce message
 * @param array $headers the message headers
 * @param string $body the message body
 */
function is_error_bounce( $headers, $body )
{
	$test1 = ( isset($headers['Return-Path']) && $headers['Return-Path'] == '<>' );
	$test3 = ( isset($headers['Return-Path']) && preg_match("/MAILER-DAEMON@web\d\.roosit\.nl/", $headers['Return-Path']) == 1);
	$test4 = ( isset($headers['Return-Path']) && preg_match("/MAILER-DAEMON@(mail|web)\d+\.roosit\.eu/", $headers['Return-Path']) == 1);
	
	$test2 = ( isset($headers['Return-path']) && $headers['Return-path'] == '<>' );
	
	return ( $test1 || $test2 || $test3 || $test4 );
}

/**
 * Check of een mailtje een unsubscribe is
 * @return mixed het ID van de nieuwsbrief, of 0 als die niet gegeven is
 */
function is_unsubscribe( $headers, $body )
{
	$matches = array();
	if( isset($headers['Subject']) && ( preg_match("/unsubscribe\s(\d+)/", $headers['Subject'], $matches )) )
	{
		return $matches[1];
	} else if( isset($headers['Subject']) && ( preg_match("/unsubscribe/", $headers['Subject'] ) ) ) {
		return true;
	}
		
	return false;
}

/**
 * Filters the error code out of a bounce message
 * @param $body string the message body
 * @return int the error code
 */
function get_error_code( $body )
{
	if( preg_match( "/\D(55\d)\D/", $body, $matches ) > 0 )
	{
		return $matches[0];
	} else {
		return 0;
	}
}

/**
 * Filters the user for which the error report is out of a message body
 * @param $body string the message body
 * @return string the email address
 */
function get_error_recipient( $body )
{
	global $settings;
	
	$expr = "/<([A-Z0-9._-]+@[A-Z0-9.-]+\.[A-Z]{2,4})>/i";
	$num = preg_match_all( $expr, $body, $matches );
	if( $num > 0 && is_array($matches[1]) && count($matches[1]) > 0 )
	{
		for($i=0; $i<$num; $i++)
		{
			if( $matches[1][$i] != $settings['mbox_address'] )
			{
				return $matches[1][$i];
			}
		}
	}
	return '';
}

/**
 * Splits a message in headers and data
 * @param string $message the mail data
 * @return array with first an associative headers array, then a body string
 */
function split_message( $message )
{
	$have_header = false;
	$lines = explode("\r\n", $message);
	$last_key = '';
	$header = array();
	$body = '';
	foreach( $lines as $line )
	{
		if( !$have_header && $line != '' )	////// header line
		{
			if( strchr( $line, ": " ) )
			{
				list($name, $value) = explode(": ", $line );

				if( is_array( $value ) )
				{
					$value = implode(": ", $value);
				}
				
				$last_key = $name;
				
				if( isset( $header[$name] ) )
				{
					$header[$name] .= rtrim($value);
				} else {
					$header[$name] = rtrim($value);
				}
				
			} else if( $last_key != '' ) {
				$header[$last_key] .= $line;
			}

		} else if( $line == '' ) {		/////// header-body division line

			$have_header = true;

		} else if( $have_header ) {		/////// body line

			$body .= $line;

		}
	}
	
	return array( $header, $body );
}

/**
 * Haal en verwerk email
 */
function getMail()
{
	global $templates, $data, $settings, $db, $yourportfolio;
	$bounces = 0;
	$unsubscribes = 0;
	
	$bounce_addresses = array();
	$unsubscribe_addresses = array();
	
	if ( empty($settings['mbox_host']) )
	{
		echo 'ERROR:'._('Geen host adres opgegeven');
		exit();
	}
	
	$ph = new Net_POP3();
	if( ! $ph->connect( $settings['mbox_host'], $settings['mbox_port'] ) )
	{
		echo 'ERROR:'.sprintf(_('Er kon geen verbinding worden gemaakt met de POP3 mail server %s. Probeer het later nog eens.'), $settings['mbox_host']);
		exit();
	}
	
	if (empty($settings['mbox_method']))
	{
		$settings['mbox_method'] = 'APOP';
	} else {
		$settings['mbox_method'] = strtoupper($settings['mbox_method']);
	}
	
	$var = $ph->login( $settings['mbox_user'], $settings['mbox_pass'], $settings['mbox_method'] ); 
	if( PEAR::isError($var) )
	{
		echo 'ERROR:'.sprintf(_('Het inloggen op de POP3 mail server als gebruiker %s is mislukt. Controleer de instellingen en probeer het opnieuw.'), $settings['mbox_user']);
		exit();
	}
	
	// bounce adressen verzamelen
	$list = $ph->getListing();
	$handled = 0;
	$void = null;
	if( is_array($list) && count($list) > 0 )
	{
		// niet over executie-tijd heen lopen
		$process_max = 5;
		foreach( $list as $p )
		{
			$mdata = $ph->getMsg( $p['msg_id'] );
			list( $headers, $body ) = split_message( $mdata );
			$subject = $headers['Subject'];

			if( is_error_bounce( $headers, $body ) )
			{
				$bounces++;
				$addr = get_error_recipient( $body );
				$bounce_addresses[] = $addr;
				$type = 'bounce';
				log_message( 'info', "Determined mail from $addr with subject $subject to be a bounce mail", debug_backtrace() );
			} else if( ($id = is_unsubscribe($headers, $body ) ) !== false ) {
				$unsubscribes++;
				$addr = get_sender( $headers );
				$unsubscribe_addresses[] = array('address' => $addr, 'id' => $id);
				$type = 'unsubscribe';
				log_message( 'info', "Determined mail from $addr with subject $subject to be an unsubscribe mail", debug_backtrace() );
			} else {
				$addr = get_sender( $headers );
				$type = 'unknown';
				log_message( 'info', "Determined mail from $addr with subject $subject to be a unknown mail", debug_backtrace() );
			}
			
			$h = serialize( $headers );
			$query = sprintf( "INSERT INTO `%s` SET `date_inserted`=NOW(), `subject`='%s', `address`='%s', `type`='%s', `headers`='%s', `body`='%s'", $yourportfolio->_table['nl_incoming'], $db->filter( $subject ), $db->filter( $addr ), $db->filter( $type ), $db->filter( $h ), $db->filter( $body ) );
			$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
			$ph->deleteMsg( $p['msg_id'] );
			$handled++;
			if ($handled >= $process_max)
			{
				break;
			}
		}
	} else {
		echo 'BOUNCES:0;UNSUBSCRIBES:0;MORE:0';
		exit();
	}
	
	$ph->disconnect();
	
	// bounces verwerken
	foreach( $bounce_addresses as $addr )
	{
		$void = '';
		$query = sprintf( "UPDATE `%s` SET status=".AddressStatus::BOUNCED().", status_param=status_param + 1, modified=NOW() WHERE address='".$addr."' LIMIT 1", $yourportfolio->_table['nl_addresses']);
		$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
	}
	$max = $settings['error_threshold'];
	if( $max <= 0 )		// sanity check
	{
		$max = 10;
	}
	
	$query = sprintf( "UPDATE `%s` SET status = '%d', modified=NOW() WHERE (status = '%d' OR status = '%d') AND status_param >= '%d'", $yourportfolio->_table['nl_addresses'], AddressStatus::ERROR_MAX(), AddressStatus::ERROR(), AddressStatus::BOUNCED(), $max);
	$db->doQuery($query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );

	// unsubscribes verwerken
	foreach( $unsubscribe_addresses as $addr )
	{
		// address ID vinden
		$addr_id = null;
		$query = sprintf("SELECT `address_id` FROM `%s` WHERE `address`='%s'", $yourportfolio->_table['nl_addresses'], $addr['address']);
		$db->doQuery($query, $addr_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );

		// ongeldig adres
		if( !$addr_id)
		{
			continue;
		}
		
		if( $settings['debug'] == true)
		{
			trigger_error("Unsubscribing ".$addr['address']. " from ".$addr['id']);
		}
		
		$query = sprintf( "INSERT INTO `%s` SET `letter_id`=%d, `unsubscribes`='%d' ON DUPLICATE KEY UPDATE `unsubscribes`=`unsubscribes`+1", $yourportfolio->_table['nl_letter_stats'], $addr['id'], 1 );
		$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
		
		if( $settings['unsubscribe_mode'] == 'systeem' )
		{
			// unsubscribe uit het systeem
			$query = sprintf( "UPDATE `%s` SET `status`='%d', modified=NOW() WHERE `address`='%s' LIMIT 1", $yourportfolio->_table['nl_addresses'], AddressStatus::UNSUBSCRIBED(), $addr['address'] );
			$db->doQuery($query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
			
			// weg uit groepen
			$query = sprintf( "DELETE FROM `%s` WHERE `address_id`='%d'", $yourportfolio->_table['nl_bindings'], $addr_id);
			$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
			
		} else {
			// unsubscribe uit bepaalde groep
			
			// geen geldig newsletter ID gegeven, maar wel een unsubscribe:
			if( $addr['id'] === true )
			{
				// vind alle groepen waar deze user lid van is
				$query = sprintf("SELECT DISTINCT `group_id` FROM `%s` WHERE `address_id`=%d", $yourportfolio->_table['nl_bindings'], $addr_id);
				$groups = array();
				$db->doQuery($query, $groups, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false );
				
				// vind de laatst gestuurde nieuwsbrief naar een van deze groepen

				$query = sprintf(
					"SELECT l.`letter_id` FROM `%s` l WHERE l.`status`='sent' AND EXISTS (".												 
						"SELECT * FROM `%s` r WHERE r.`letter_id`=l.`letter_id` AND EXISTS (".
							"SELECT * FROM `%s` g WHERE g.`group_id`=r.`group_id` AND EXISTS (".
								"SELECT * FROM `%s` b WHERE b.`group_id`=g.`group_id` AND b.`address_id`=%d".
							")".
						")".
					") ORDER BY `datesent` DESC LIMIT 1",
					$yourportfolio->_table['nl_letters'],
					$yourportfolio->_table['nl_recipients'],
					$yourportfolio->_table['nl_groups'],
					$yourportfolio->_table['nl_bindings'],
					$addr_id
				);
				$db->doQuery( $query, $addr['id'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
				if( $settings['debug'] == true)
				{
					trigger_error( "Newsletter gevonden: ".$addr['id'] );
				}
			}
			
			// groep voor deze nieuwsbrief vinden waar de user in zit
			$query = sprintf( 
				"SELECT `group_id` FROM `%s` b WHERE b.`address_id`='%d' AND EXISTS (".
					"SELECT * FROM `%s` r WHERE r.`letter_id`='%d' AND r.`group_id`=b.`group_id`".
				") LIMIT 1", 
				$yourportfolio->_table['nl_bindings'], 
				$addr_id, 
				$yourportfolio->_table['nl_recipients'], 
				$addr['id'] );
			$group_id = null;
			$db->doQuery( $query, $group_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
			if( $settings['debug'] == true)
			{
				trigger_error("Found group: ".$group_id);
			}
			
			// geen groep kunnen vinden, ongeldig nieuwsbrief ID
			if( !$group_id )
			{
				if( $settings['debug'] == true)
				{
					trigger_error( "Lopen vast hier, geen ID");
				}
				continue;
			}
			
			// uit groep gooien
			if( $group_id != null )
			{
				$query = sprintf( "DELETE FROM `%s` WHERE address_id=%d AND group_id=%d", $yourportfolio->_table['nl_bindings'], $addr_id, $group_id );
				$db->doQuery( $query, $addr_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false ); 
			}
		}
		
		// delete from queue
		$query = sprintf( "DELETE FROM `%s` WHERE `addr_email` = '%s'", $yourportfolio->_table['nl_queue'], $addr['address'] );
		$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		
	}	
	
	if( $handled < count($list) )
	{
		$leftover = count($list) - $handled;
		if( $settings['debug'] == true)
		{
			trigger_error("Sending leftover $leftover");
		}
		echo 'BOUNCES:'.$bounces.';UNSUBSCRIBES:'.$unsubscribes.';MORE:'.$leftover;
	} else {
		echo 'BOUNCES:'.$bounces.';UNSUBSCRIBES:'.$unsubscribes.';MORE:0';
	}
	exit();
}


do_output();
require(NL_CODE . 'shutdown.php');

?>
