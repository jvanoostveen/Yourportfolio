<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page handling site administration for master accounts
 *
 * @package yourportfolio
 * @subpackage Page
 */

// start the program
require(CODE.'program/startup.php');

if (!$yourportfolio->session['master'])
	$system->relocate('index.php');

// handle any possible form
require(CODE.'handlers/formhandler.php');
//

// program code

#$yourportfolio->preferencesLoad();

$yourportfolio->upload_link = false;
$yourportfolio->title = _('site beheer');
$canvas->inner_template = 'administration';

$canvas->menu_item = 'admin';
$canvas->icon = 'preferences_white';

if ($system->browser == 5)
{
	$canvas->template = 'page_css';
	$canvas->addStyle('page_css2');
} else {
	$canvas->template = 'page_4';
	$canvas->addStyle('page_normal_css');
}
$canvas->addStyle('input');
$canvas->addStyle('common');
$canvas->addStyle('complex');

$canvas->addScript('common');


/**
 * load menu data after all the things done
 */
$yourportfolio->loadMenu();

// end program code
require(CODE.'program/shutdown.php');
?>