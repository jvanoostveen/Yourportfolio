<?PHP
/*
 * Project: yourportfolio / newsletter
 *
 * @created Nov 21, 2006
 * @author Christiaan Ottow
 * @copyright Christiaan Ottow
 */

$page_name = 'queue/';

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

require(CODE . 'newsletter/code/startup.php');
require_once(NL_CODE.'classes/NewsletterMailer.php');
$canvas->addScript('nl_common');
$canvas->addScript('nl_queue');

switch( $case )
{
	case 'delete':
		delete();
		break;
	case 'send':
		send_queue();
		break;
	default: 
		show_queue();
}

/**
 * Wis de queue voor een specifieke newsletter
 */
function delete()
{
	global $yourportfolio, $db;
	
	$id = (int) $_POST['id'];
	
	$query = sprintf("DELETE FROM `%s` WHERE letter_id='$id'", $yourportfolio->_table['nl_queue']);
	$res = '';
	$db->doQuery($query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	
	$query = sprintf("UPDATE `%s` SET status='draft' WHERE letter_id='$id'", $yourportfolio->_table['nl_letters']);
	$db->doQuery($query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	
	header("Location: newsletter_queue.php");
	exit();
}

/**
 * Queue verwerken: alle mail in de queue verzenden
 */
function send_queue()
{
	global $system, $settings;
	require_once(NL_CODE.'classes/Queue.php');
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$Tbegintime = $time;	
	
	log_message( 'event', "Sending queue", debug_backtrace() );
	$result = Queue::send();
	
	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$Tendtime = $time;
	
	$Tdiff = $Tendtime-$Tbegintime;
	if( $settings['debug'] == true)
	{
		trigger_error("Sending took ".$Tdiff." seconds");
	}
	log_message( 'event', "Queue sent in $Tdiff seconds", debug_backtrace() );
	
	$output = '';
	foreach( array_keys( $result) as $key )
	{
		$output .= $result[$key] . ':';
	}
	$output .= $Tdiff;

	print $output;
	if( DEBUG )
	{
		trigger_error($output);
	}
	
	exit();
	
}

function show_queue()
{
	global $yourportfolio, $db, $templates, $data;
	
	$query = sprintf("SELECT DISTINCT letter_id FROM `%s`", $yourportfolio->_table['nl_queue']);
	$db->doQuery( $query, $data['queue'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
	if( is_array($data['queue']) && count($data['queue']) > 0 )
	{
		for( $n=0; $n<count($data['queue']); $n++)
		{
			$id = $data['queue'][$n]['letter_id'];	
			$data['queue'][$n]['id'] = $id;
			
			$query = sprintf("SELECT subject FROM `%s` WHERE letter_id='$id'", $yourportfolio->_table['nl_letters']);
			$db->doQuery($query, $data['queue'][$n]['subject'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE letter_id='$id' AND status='sent'", $yourportfolio->_table['nl_queue']);
			$db->doQuery($query, $data['queue'][$n]['sent'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE letter_id='$id' AND status='unsent'", $yourportfolio->_table['nl_queue']);
			$db->doQuery($query, $data['queue'][$n]['unsent'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		}
	}	
	
	$unsent = 0;
	if( is_array($data['queue']) && count($data['queue']) > 0 )
	{
		foreach( $data['queue'] as $q )
		{
			$unsent += $q['unsent'];
		}
	} else {
		$unsent = 0;
	}
	
	$data['unsent'] = $unsent;
	
	$templates[] = 'queue_list.php';
}

do_output();

require(NL_CODE . 'shutdown.php');

?>

