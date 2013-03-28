<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created May 8, 2007
 */
 
$page_name = 'settings';
$page_title = _('Settings');

require(CODE.'newsletter/code/startup.php');

// check of admin is ingelogd
if( !$yourportfolio->session['master'] )
{
	ob_end_clean();
	header("Location: newsletter_start.php");
	exit();
}

$canvas->addScript('nl_common');
$canvas->addScript('nl_settings');

$case = 'list';

if( isset($_POST['case']) )
{
	$case = $_POST['case'];
} else if( isset($_GET['case']) ) {
	$case = $_GET['case'];
}

switch( $case )
{
	case 'list':
		show();
		break;
	case 'save':
		save();
		break;
	default:
		show();
		break;
}

function show()
{
	global $data, $templates, $yourportfolio, $db, $components, $settings;
	$components['bottomBar'][] = 'save_link.php';
	$components['topBar'][] = 'save_link.php';
	
	// kopieer $settings in $data['settings']
	foreach( array_keys($settings) as $key )
	{
		$data['settings'][$key] = array('value' => $settings[$key], 'type' => '');
	}
	
	$query = sprintf("SELECT `name`, `value`, `type` FROM `%s`", $yourportfolio->_table['nl_settings']);
	$db_settings = array();
	$db->doQuery($query, $db_settings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	
	foreach( $db_settings as $row )
	{
		$data['settings'][$row['name']] = array( 'value' => $row['value'], 'type' => $row['type']);	
	}
	
	$templates[] = 'settings_show.php';
	
}

function save()
{
	global $db, $yourportfolio;
	
	foreach(array_keys($_POST['settings']) as $key)
	{
		$key = $db->filter($key);
		$val = $db->filter($_POST['settings'][$key]);
		
		$query = sprintf("SELECT EXISTS( SELECT * FROM `%s` WHERE `name`='$key')", $yourportfolio->_table['nl_settings']);
		$exists = 0;
		$db->doQuery($query, $exists, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		if( $exists == 1 )
		{
			$query = sprintf("UPDATE `%s` SET `value`='$val' WHERE `name`='$key'", $yourportfolio->_table['nl_settings']);
			$result = null;
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		} else {
			$query = sprintf("INSERT INTO `%s` SET `value`='$val', `name`='$key', `type`='string'", $yourportfolio->_table['nl_settings']);
			$result = null;
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
	}
	
	show();
	log_message( 'event', "Settings updated", debug_backtrace() );
}

do_output();
require(CODE.'newsletter/code/shutdown.php');

?>
