<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page handling tag and keywords administration
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

#$yourportfolio->preferencesLoad();

$yourportfolio->upload_link = false;
$yourportfolio->title = _('tags beheer');
$canvas->inner_template = 'tags';

$canvas->menu_item = 'tags';
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
$canvas->addScript('tags');


/**
 * load menu data after all the things done
 */
$yourportfolio->loadMenu();

// end program code
require(CODE.'program/shutdown.php');
?>