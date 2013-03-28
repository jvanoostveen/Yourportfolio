<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * program startup page WITH authentication
 * starts output buffering, includes all the code
 * creates the objects
 *
 * @package yourportfolio
 * @subpackage Pages
 */

ini_set('log_errors_max_len', 0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (defined('CUSTOM_LOG_PATH'))
{
	ini_set('error_log', CUSTOM_LOG_PATH);
}

// handle the database connection exception
function noDBConnection($msg) 
{
	ob_end_clean();
	trigger_error('Could not connect to the database', E_USER_WARNING);
	require(CODE.'pages/db_offline.php');
	exit();
}

/**
 * start output buffering
 */
ob_start();

/**
 * scripts needs to be completed or we could get strange and half saved data
 */
ignore_user_abort(true);

/**
 * calculate script start time
 */
$start_time = array_sum(explode(' ', microtime())); 

/**
 * turn error reporting to correct settings
 */
if (function_exists('error_reporting'))
	error_reporting(E_ALL);

/**
 * Set default timezone.
 */
if (function_exists('date_default_timezone_set'))
	date_default_timezone_set('UTC');

/**
 * set the defines
 */
require(CODE.'program/static.php');

/**
 * boot up the system
 */
require(CODE.'system/SiteSystem.php');
$system = new SiteSystem();
global $system;

/**
 * debug session reset
 */
if (isset($_GET['flush']))
{
	$system->flushSession();
}

/**
 * display class
 * for your viewing pleasures
 */
require(CODE.'system/Canvas.php');
$canvas = new Canvas();
global $canvas;

/**
 * database handler object
 */
require(CODE.'system/DatabaseToolkit.php');

/**
 * create new database connection
 */
$db = new DatabaseToolkit();
global $db;

/**
 * user authentication object
 */
require(CODE.'system/Shield.php');
$shield = new Shield();

/**
 * the custom classes always needed
 * classes specificly needed in some documents can be loaded in those documents
 */
require(CODE.'classes/Yourportfolio.php');
require(CODE.'classes/Node.php');
require(CODE.'classes/Album.php');
require(CODE.'classes/Section.php');
require(CODE.'classes/Item.php');
require(CODE.'classes/FileObject.php');
require(CODE.'classes/MessageQueue.php');

/**
 * program startup
 * start session
 */
ini_set('session.use_only_cookies', 1);
if (isset($_POST['PHPSESSID']))
	session_id($_POST['PHPSESSID']);
session_start();

/**
 * handle authentication log out
 */
if (isset($_GET['logout']))
{
	$shield->logOut();
	$system->relocate('index.php');
}

/**
 * handle authentication log in
 * normally forms should be handled by the form input handler and
 * be redirected to the correct object
 */
if (isset($_POST['targetObj']) && $_POST['targetObj'] == 'shield')
{
	$shield->handleInput($_POST[$_POST['targetObj'].'Form']);
	
	// remove data from _POST
	unset($_POST);
}

/**
 * Create message queue.
 */
$messages = new MessageQueue();
global $messages;

/**
 * create the custom object
 * yourportfolio framework
 */
$yourportfolio = new Yourportfolio();
global $yourportfolio;

/**
 * check if authentication is correct and yourportfolio can be shown
 * otherwise show login page
 */
$shield->checkAuth();
if (!$shield->access)
{
	session_regenerate_id();
	
	$challenge = $shield->createChallenge();
	
	// not logged in.. display login form
	$canvas->template = 'login';
	$canvas->addStyle('common');
	$canvas->addStyle('page_normal_css');
	$canvas->addScript('md5');
	$canvas->addScript('login');

	$yourportfolio->title = 'login';
	$yourportfolio->loadPhotographerName();
	$yourportfolio->advancedSettingsLoad(true);

	// initiate shutdown sequence
	require(CODE.'program/shutdown.php');
}

/**
 * preform some checkup and database corrections if needed
 * but only after when user is logged in
 */
if ($shield->loggedIn)
{
	$shield->onLogin();
}

/**
 * load the advanced settings
 */
$yourportfolio->advancedSettingsLoad();

/**
 * include some files based on the advanced settings loaded in yourportfolio
 */
if ($yourportfolio->settings['restricted_albums'])
{
	require(CODE.'classes/ClientUser.php');
}

if ($yourportfolio->settings['subusers'])
{
	require(CODE.'classes/SubUser.php');
}

/**
 * For newsletter: check if ordering of a list has changed and save in session
 */
if (isset( $_GET['o_f']))
{
	$order_field = $_GET['o_f'];
	$order_list = $_GET['o_l'];
} else if (isset($_POST['o_f']))
{
	$order_field = $_POST['o_f'];
	$order_list = $_POST['o_l'];
}

if (isset($order_field))
{
	if (isset($_SESSION['order_'.$order_list]))
	{
		if ($_SESSION['order_'.$order_list]['field'] == $order_field)
		{
			$_SESSION['order_'.$order_list]['direction'] = ($_SESSION['order_'.$order_list]['direction'] == 'ASC' ? 'DESC' : 'ASC' );
		} else {
			$_SESSION['order_'.$order_list]['field'] = $order_field;
			$_SESSION['order_'.$order_list]['direction'] = 'ASC';
		}
	} else {
		$_SESSION['order_'.$order_list]['field'] = $order_field;
		$_SESSION['order_'.$order_list]['direction'] = 'ASC';
	}
}		

/**
 * For newsletter: set pagination values
 */
if (isset($_GET['app']) || isset($_POST['app']))
{
	if (isset($_GET['app']))
	{
		$app = (int) $_GET['app'];
	} else {
		$app = (int) $_POST['app'];
	}
	
	setcookie('app', $app, time() + 60 * 60 * 24 * 30);
	$_COOKIE['app'] = $app;
	
} else if (!isset($_COOKIE['app']) || $_COOKIE['app'] == 0)
{
	setcookie('app', 100, time() + 60 * 60 * 24 * 30);
	$_COOKIE['app'] = 100;
}

$yourportfolio->validateInstallation();

/**
 * end program startup
 */

/**
 * clean program startup
 */
?>