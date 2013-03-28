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
define('CORE_HTML', CODE.'templates/core/html/');
define('CORE_CSS', 'design/css/');
define('CORE_SCRIPTS', 'design/scripts/');
define('CORE_IMAGES', 'design/img/');

define('SITE_HTML', CODE.'templates/default/html/');
define('SITE_CSS', 'templates/css/');
define('SITE_SCRIPTS', 'templates/scripts/');
define('SITE_IMAGES', 'templates/images/');
define('SITE_MAIL', CODE.'templates/default/mail/default.txt');
define('DEFAULT_RSS', CODE.'templates/default/rss/');

/* @since 2.10.0 */
define('SHARED', 'design/shared/');
define('DESKTOP', 'design/desktop/');
define('TABLET', 'design/tablet/');
define('MOBILE', 'design/mobile/');

// easier mapping for use in html only templates
define('FRAMEWORK', CODE.'framework/');
define('SITE_CODE', 'templates/site/');
define('TEMPLATES', 'templates/templates/');
define('HTML', 'templates/html/');
define('SITE', 'templates/');

define('DOWNLOADS_DIR', 'assets/downloads/');
define('YOURPORTFOLIO_DIR', 'assets/yourportfolio/');
define('ORIGINAL', 'assets/original/');
define('CACHE', 'assets/cache/');

define('CODE_BACKEND', realpath(BASE.'../code').'/');
define('LOCALE', realpath(BASE.'../locale'));

define('XML', CODE_BACKEND.'xml/');
define('DATA_DIR', 'data/');

// code directories
define('MODULES', CODE_BACKEND.'modules/');
define('CORE_SETTINGS', CODE_BACKEND.'settings/');
define('VENDOR', CODE_BACKEND.'vendor/');

// make the API aware of the fact that we're running the frontend (database independance)
define('FRONTEND', 1);

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
