<?PHP
/**
 * Startup voor de newsletter component
 *
 * Project: Yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 */


define('NL_TEMPLATES', CODE . 'newsletter/templates/');
define('NL_CODE', CODE.'newsletter/code/');
define('ROOT_URL', dirname($_SERVER['SCRIPT_NAME']));

require(CODE . 'program/startup.php');
textdomain('newsletter');

//set_include_path('.'.PATH_SEPARATOR.CODE.'vendor/');
set_include_path(CODE.'vendor/PEAR'.PATH_SEPARATOR.get_include_path());

require(NL_CODE.'classes/AddressStatus.php');
require(NL_CODE.'classes/Newsletter.php');
require(NL_CODE.'classes/NewsletterTemplate.php');
require(NL_CODE.'classes/NewsletterSender.php');
require(NL_CODE.'classes/Group.php');

$components = array( 'topBar' => array(), 'bottomBar' => array() );

$settings = array (
	'page_title' => _('Nieuwsbrief'),
	'page_icon' => 'design/iconsets/default/album_white.gif',
	'save_link' => 'saveData()',
	'send_link' => 'send()',
	'error_threshold' => 4,
	'mbox_user' => '',
	'mbox_pass' => '',
	'mbox_method' => 'APOP',
	'mbox_host' => '',
	'mbox_port' => 110,
	'batch_size' => 20,
	'unsubscribe_mode' => 'groep',
	'debug'	=> false
	);

$query = sprintf("SELECT name, value, type FROM `%s`", $yourportfolio->_table['nl_settings']);
$config_t = array();
$db->doQuery( $query, $config_t, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);

if (!empty($config_t))
{
	foreach( $config_t as $row )
	{
		if( $row['value'] == 'true' )
		{
			$settings[$row['name']] = true;
		} else if( $row['value'] == 'false') {
			$settings[$row['name']] = false;
		} else {
			$settings[$row['name']] = $row['value'];
		}
	}
}



function do_ordering($in, $element )
{
	global $_SESSION, $db;

	$field = 'created';
	$direction = 'DESC';
	
	// set per-element defaults
	switch( $element )
	{
		case 'letter_group':
			$field = 'created';
			$direction = 'DESC';
			break;
	}
	
	// session data
	if( isset($_SESSION['ordering'][$element]) )
	{
		$field = $_SESSION['ordering'][$element]['field'];
		$direction = $_SESSION['ordering'][$element]['direction'];
	}
	
	// now input parsing
	if( isset($in['order_field']) )
	{
		if( $in['order_field'] == $field )
		{
			if( $direction == 'DESC')
			{
				$direction = 'ASC';
			} else {
				$direction = 'DESC';
			}
		} else {
			$field = $db->filter($in['order_field']);
			$_SESSION['ordering'][$element]['field'] = $field;
		}
	}
	if( isset($in['order_direction']) )
	{
		if( $in['order_direction'] == $direction )
		{
			if( $direction == 'DESC')
			{
				$direction = 'ASC';
			} else {
				$direction = 'DESC';
			}
		} else {
			$direction = $db->filter($in['order_direction']);
			$_SESSION['ordering'][$element]['direction'] = $direction;
		}
	}
	
	if( $direction == 'ASC')
	{
		$other = 'DESC';
	} else {
		$other = 'ASC';
	}
	
	return array($field,$direction,$other);
		
}

function do_output()
{
	global 	$pre_templates, 
		$templates, 
		$custom_out, 
		$page_name, 
		$menu, 
		$submenu, 
		$filter, 
		$canvas, 
		$data, 
		$yourportfolio, 
		$system,
		$settings,
		$components,
		$ordering;
			
	if( isset( $pre_templates ) )
	{
		foreach( $pre_templates as $t )
		{
			require(NL_TEMPLATES . $t );
		}
	}

	require( BASE . 'design/html/html_start.php');
	
	if( $system->browser == 5 )
	{
		require(NL_TEMPLATES . 'page_5.php');
	} else {
		require(NL_TEMPLATES . 'page_4.php');
	}
	
	
	if( isset( $custom_out) && isset( $custom_out['pre_templates'] ) )
	{
		echo $custom_out['pre_templates'];
	}
	
	if( isset( $templates ) && is_array( $templates) )
	{
		foreach( $templates as $t )
		{
			require(NL_TEMPLATES . $t);
		}
	}
	
	if( isset( $custom_out) && isset( $custom_out['post_templates'] ) )
	{
		echo $custom_out['post_templates'];
	}
	
}

/* build menu */

$menu = array (
	_('Startpagina')             => array(
		'name'		=> 'start',
		'href'		=> 'start',
		'icon'		=> 'iconsets/default/start.gif'
	),
	
	_('Adressen')      => array (
		'name'		=> 'addresses',
		'href'		=> 'edit',
		'icon'		=> 'iconsets/default/users_filled.gif'
	),
	
	_('Groepen')		=> array (
		'name'		=> 'groups',
		'href'		=> 'groups',
		'icon'		=> 'iconsets/default/groups.gif'
	),
	
	_('Nieuwsbrieven')   		  => array (
		'name'		=> 'newsletters',
		'href'		=> 'write',
		'icon'		=> 'iconsets/default/newsletters.gif'
	),
	
	_('Opt-in status')	=> array(
		'name'		=> 'optin',
		'href'		=> 'optin',
		'icon'		=> 'iconsets/default/groups.gif'
	)
);

if( $yourportfolio->session['master'] )
{
	$menu['Templates'] = array(
		'name'	=> 'templates',
		'href'	=> 'templates',
		'icon'	=> 'iconsets/default/newsletters.gif'
	);
	
	$menu['Settings'] = array(
		'name'	=> 'settings',
		'href'	=> 'settings',
		'icon'	=> 'iconsets/default/newsletters.gif'
	);
}
	
$query = sprintf("SELECT EXISTS( SELECT * FROM `%s` )", $yourportfolio->_table['nl_queue']);
$num = 0;
$db->doQuery($query, $num,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
if( $num == 1)
{
	
	$menu[_('Te verzenden')] = array (
		'name'	=> 'queue',
		'href'		=> 'queue',
		'icon'		=> 'iconsets/default/outgoing.gif'
	);
}
/* template instellingen */

if ($system->browser == 5)
{
	$canvas->template = 'page_css';
	$canvas->addStyle('page_css2');
} else {
	$canvas->template = 'page_4';
	$canvas->addStyle('page_normal_css');
}
$canvas->addStyle('common');
$canvas->addStyle('complex');
$canvas->addStyle('newsletter');

#$canvas->addScript('text_manipulation');
#$canvas->addScript('common');
$canvas->addScript('prototype');
$canvas->addScript('scriptaculous');
//$canvas->addScript('newsletter');


$yourportfolio->title = _('nieuwsbrief');
$canvas->addBodyTag('onload', 'init()');


function group_exists( $group_name )
{
	global $yourportfolio, $db;
	
	$query = sprintf("SELECT EXISTS ( SELECT * FROM `%s` WHERE name='$group_name')", $yourportfolio->_table['nl_groups']);
	$result = null;
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	
	return ( $result == 1 );
}

function group_id_exists( $id )
{
	global $yourportfolio, $db;
	
	$query = sprintf("SELECT EXISTS ( SELECT * FROM `%s` WHERE group_id='$id')", $yourportfolio->_table['nl_groups']);
	$result = null;
	$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	
	return ( $result == 1 );
}

function log_message( $type, $msg, $debug )
{
	global $yourportfolio, $db;
	
	$line = print_r( $debug, true );
	//trigger_error( $line );
	$keys = array_keys( $debug[0] );
	if( in_array( 'class', $keys ) )
	{
		$class = $debug[0]['class'];
	} else {
		$class = 'none';
	}
	
	$query = sprintf( "INSERT INTO %s SET `date`=NOW(), `type`='%s', `message`='%s', `file`='%s', `line`='%d', `function`='%s', `class`='%s'", $yourportfolio->_table['nl_log'], $db->filter($type), $db->filter($msg), $debug[0]['file'], $debug[0]['line'], $debug[0]['function'], $class );
	$result = null;
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
}

function include_message( $template, $params )
{
	$current_buffer = ob_get_contents();
	ob_end_clean();
	
	ob_start();
	require(NL_TEMPLATES."messages/$template.php");
	$msg_buffer = ob_get_contents();
	ob_end_clean();
	
	echo $current_buffer;
	
	return $msg_buffer;
}

?>