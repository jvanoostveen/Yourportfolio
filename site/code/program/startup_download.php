<?PHP
/**
 * Project:			yourportfolio
 * File:			$RCSfile: startup_download.php,v $
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name: rel_2-5-23 $
 */

/**
 * program startup page for use with file downloads
 * minimal set is included
 *
 * @package yourportfolio
 * @subpackage Pages
 * @version $Revision: 1.2 $
 * @date $Date: 2005/03/16 13:34:31 $
 */

define('STARTUP_DONE', 1);

/**
 * startup script settings
 * ### should also set a time limit, but since that is not available with safe mode on... ###
 */
ignore_user_abort(false);

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
 * we don't need a real object, we'll be calling a method with ::
 */
require(CODE_BACKEND.'system/SiteSystem.php');
$system = new SiteSystem();
global $system;

/**
 * display class
 * set to null, because other classes are still linking to it, but it is not used in the download script (yet)
 * could be being used for filtering the filename
 */
#require(CODE.'system/Canvas.php');
$canvas = null;
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
require(CODE.'classes/Yourportfolio.php');
require(CODE.'classes/Album.php');
require(CODE.'classes/Section.php');
require(CODE.'classes/Item.php');
require(CODE.'classes/FileObject.php');

/**
 * program startup
 */

/**
 * create the yourportfolio object
 * yourportfolio framework
 * Yourportfolio class is needed because it sets the correct tables used
 */
$yourportfolio = new Yourportfolio();

/**
 * end program startup
 */

/**
 * clean program startup
 */
?>