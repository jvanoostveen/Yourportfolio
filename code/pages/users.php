<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page handling section call
 *
 * @package yourportfolio
 * @subpackage Page
 */

// start the program
require(CODE.'program/startup.php');

// handle any possible form
require(CODE.'handlers/formhandler.php');
//

// program code
if ($yourportfolio->session['limited']) // limited user shouldn't be here
{
	$system->relocate('album.php');
}

$yourportfolio->title = _('Gebruikers beheer');
$yourportfolio->upload_link = false;

$canvas->inner_template = 'users_management';
$canvas->menu_item = 'users';
$canvas->icon = 'users_white';

$user_id = false;
if ( ($user_id =   (isset($_GET['uid'])) ? intval($_GET['uid']) : false) !== false )
{
	$user = new SubUser();
	$user->id = $user_id;
	$user->load();
} else {
	$user = new SubUser();
	$user->init();
}

$yourportfolio->loadSubUsers();

$yourportfolio->loadAlbums();

/**
 * load menu data after all the things done
 */
$yourportfolio->loadMenu();

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

$canvas->addScript('common');

// end program code
require(CODE.'program/shutdown.php');
?>