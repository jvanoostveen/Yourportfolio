<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * sets the defines like paths and debug mode
 *
 * @package yourportfolio
 * @subpackage Config
 */

// defines and inits
define('HTML', 'design/html/');
define('CSS', 'design/css/');
define('SCRIPTS', 'design/scripts/');
define('IMAGES', 'design/img/');
define('ICONS', 'design/iconsets/');
define('CUSTOM_ICONS', 'icons/');
define('SWFS', 'design/swf/');
define('LOCALE', BASE.'locale');

define('XML', CODE.'xml/');

// error contact
define('WEBDEBUGGER', 'bug-report@webdebugger.nl');

// directories
define('DATA_DIR', '../data/');
define('SITEROOT_DIR', '../');
define('YOURPORTFOLIO_DIR', '../assets/yourportfolio/');
define('ORIGINALS_DIR', '../assets/original/');
define('CACHE_UPLOAD_DIR', '../assets/cache_upload/');

define('VENDOR', CODE.'vendor/');

define('THUMBS_DIR', '../assets/thumbs/');
define('PREVIEW_DIR', '../assets/preview/');
define('MUSIC_DIR', '../assets/music/');
define('MOVIES_DIR', '../assets/movies/');
define('DOWNLOADS_DIR', '../assets/downloads/');
define('CACHE_DIR', '../assets/cache/');

// code directories
define('MODULES', CODE.'modules/');
define('CORE_SETTINGS', CODE.'settings/');

// other
define('FRONTEND', 0);

// upload stuff
define('UPLOAD_MAX_SIZE', min(ini_get('upload_max_filesize'), ini_get('post_max_size')));

if (!defined('MASTER_DOMAIN'))
{
	define('MASTER_DOMAIN', 'www.yourportfolio.nl');
}

/**
 * setting debug mode based upon a debug file in the settings base directory.
 */
if (!defined('SETTINGS'))
{
	trigger_error('SETTINGS constant should be defined!', E_USER_ERROR);
}

define('DEBUG', (file_exists(SETTINGS.'../yourportfolio_debug')));
define('DOMAIN', strtolower($_SERVER['HTTP_HOST']));

if ( strcmp(DOMAIN, MASTER_DOMAIN) == 0 )
{
	// site is running on the master domain
	$pos1  = strpos(strtolower($_SERVER['SCRIPT_FILENAME']), MASTER_DOMAIN.'/');
	$pos1 += strlen(MASTER_DOMAIN.'/');

	$pos2  = strpos(strtolower($_SERVER['SCRIPT_FILENAME']), '/', $pos1);
	define('SUB_DOMAIN', substr(strtolower($_SERVER['SCRIPT_FILENAME']), $pos1, $pos2 - $pos1));
	
	unset($pos1, $pos2);
} else {
	define('SUB_DOMAIN', false);
}
?>