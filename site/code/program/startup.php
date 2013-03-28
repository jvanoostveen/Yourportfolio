<?PHP
/**
 * Project:			yourportfolio
 * File:			$RCSfile: startup.php,v $
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name: rel_2-5-23 $
 */

/**
 * program startup page WITH authentication for 'restricted albums'
 * starts output buffering, includes all the code
 * creates the objects
 *
 * @package yourportfolio
 * @subpackage Pages
 * @version $Revision: 1.8 $
 * @date $Date: 2005/03/16 13:34:31 $
 */

ini_set('log_errors_max_len', 0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
if (defined('CUSTOM_LOG_PATH'))
{
	ini_set('error_log', CUSTOM_LOG_PATH);
}

define('STARTUP_DONE', 1);

/**
 * start output buffering
 */
ob_start();
ignore_user_abort(true); // scripts needs to be completed or we could get strange and half saved data

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
 * set the defines
 */
require(CODE.'program/static.php');

/**
 * boot up the system
 */
require(CODE_BACKEND.'system/SiteSystem.php');
$system = new SiteSystem();
global $system;

/**
 * display class
 * for your viewing pleasures
 */
require(CODE_BACKEND.'system/Canvas.php');
$canvas = new Canvas();
global $canvas;

/**
 * database handler object
 */
require(CODE_BACKEND.'system/DatabaseToolkit.php');

/**
 * create new database connection
 */
$db = new DatabaseToolkit();
global $db;

/**
 * the custom classes always needed
 * classes specificly needed in some documents can be loaded in those documents
 */
require(CODE.'utils/utils.php');
require(CODE.'classes/Yourportfolio.php');
require(CODE.'classes/Album.php');
require(CODE.'classes/Section.php');
require(CODE.'classes/Item.php');
require(CODE.'classes/FileObject.php');
require(FRAMEWORK.'utils/Browser.php');

/**
 * program startup
 */

/**
 * create the yourportfolio object
 * yourportfolio framework and load contact info
 */
$yourportfolio = new Yourportfolio();
$yourportfolio->getContactInfo();

if ($yourportfolio->settings['restricted_albums'] && !empty($_POST))
{
	$data = $_POST;
	$target = (isset($data['target'])) ? $data['target'] : false;
	
	switch ($target)
	{
		case ('ClientLogin'):
			
			require(CODE.'classes/ClientUser.php');
			
			$client = ClientUser::handle($data);
			$feedback = $client->generateXML();
			
			exit($feedback);
			break;
	}
}

/**
 * end program startup
 */

/**
 * clean program startup
 */

?>