<?php
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @copyright 2011 Axis fm
 * @author Joeri van Oostveen <joeri@axis.fm>
 */

// start session for use with swfaddress redirect
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
session_start();

// start the program
require(CODE.'program/startup.php');

// SWFAddress requests a url change by XMLHttpRequest
if ('application/x-swfaddress' == (isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 
	(isset($_SERVER['HTTP_CONTENT_TYPE']) ? $_SERVER['HTTP_CONTENT_TYPE'] : '')))
{
	require('swfaddress-redirect.php');
	exit();
}

// extra requires
// constants
require(FRAMEWORK.'NodeTemplate.php');
require(FRAMEWORK.'NodeType.php');
require(FRAMEWORK.'Node.php');
require(FRAMEWORK.'FileNode.php');
require(FRAMEWORK.'DataProvider.php');
require(FRAMEWORK.'TemplateController.php');
require(FRAMEWORK.'Template.php');

require(FRAMEWORK.'Files.php');

require(FRAMEWORK.'Path.php');

Files::parse();

$dataprovider = new DataProvider();
global $dataprovider;

// handle contact form
// TODO: Hier ajax contact request afhandelen. (JSON antwoorden en exit)
if (!empty($_POST) && !empty($_POST['targetObj']) && $_POST['targetObj'] != 'contact')
{
	require(FRAMEWORK.'ContactController.php');
	$contact = new ContactController();
	$GLOBALS['contact'] = $contact->send();
}

// adjust include path
$core_shared = array(SHARED.'code', SHARED.'templates', SHARED.'html');
$site_shared = array(SITE.'shared/code', SITE.'shared/templates', SITE.'shared/html');

$include = array();
if (Browser::isMobile())
{
	$core_mobile = array(MOBILE.'code', MOBILE.'templates', MOBILE.'html');
	$site_mobile = array(SITE.'mobile/code', SITE.'mobile/templates', SITE.'mobile/html');
	
	$include = array_merge($site_mobile, $site_shared, $core_mobile, $core_shared);
	
	Path::addCSS('design/mobile/css/');
	Path::addCSS('templates/shared/css/');
	Path::addCSS('templates/mobile/css/');

	Path::addScripts('design/shared/scripts/');
	Path::addScripts('design/mobile/scripts/');
	Path::addScripts('templates/shared/scripts/');
	Path::addScripts('templates/mobile/scripts/');
	
	Path::addAsset('design/shared/images/');
	Path::addAsset('design/mobile/images/');
	Path::addAsset('');
	Path::addAsset('templates/shared/images/');
	Path::addAsset('templates/mobile/images/');
} else if (Browser::isTablet())
{
	$core_tablet = array(TABLET.'code', TABLET.'templates', TABLET.'html');
	$site_tablet = array(SITE.'tablet/code', SITE.'tablet/templates', SITE.'tablet/html');
	
	$include = array_merge($site_tablet, $site_shared, $core_tablet, $core_shared);
	
	Path::addCSS('design/tablet/css/');
	Path::addCSS('templates/shared/css/');
	Path::addCSS('templates/tablet/css/');
	
	Path::addScripts('design/shared/scripts/');
	Path::addScripts('design/tablet/scripts/');
	Path::addScripts('templates/shared/scripts/');
	Path::addScripts('templates/tablet/scripts/');
	
	Path::addAsset('design/shared/images/');
	Path::addAsset('design/tablet/images/');
	Path::addAsset('');
	Path::addAsset('templates/shared/images/');
	Path::addAsset('templates/tablet/images/');
} else {
	$core_desktop = array(DESKTOP.'code', DESKTOP.'templates', DESKTOP.'html');
	$site_desktop = array(SITE.'desktop/code', SITE.'desktop/templates', SITE.'desktop/html');
	
	$include = array_merge($site_desktop, $site_shared, $core_desktop, $core_shared);
	
	Path::addCSS('design/desktop/css/');
	Path::addCSS('templates/shared/css/');
	Path::addCSS('templates/desktop/css/');
	
	Path::addScripts('design/shared/scripts/');
	Path::addScripts('design/desktop/scripts/');
	Path::addScripts('templates/shared/scripts/');
	Path::addScripts('templates/desktop/scripts/');
	
	Path::addAsset('design/shared/images/');
	Path::addAsset('design/desktop/images/');
	Path::addAsset('');
	Path::addAsset('templates/shared/images/');
	Path::addAsset('templates/desktop/images/');
}

set_include_path(join(PATH_SEPARATOR, $include) . PATH_SEPARATOR . get_include_path());
//

require('NavigationFilter.php');
require('TemplateRegistry.php');
require('NodeMap.php');
require('Routes.php');
require('custom.php');

NodeMap::remap($dataprovider->nodes);

$templateController = new TemplateController();
global $templateController;

TemplateRegistry::register();

$node = $dataprovider->currentNode();
$node = NavigationFilter::filter($node);

$url = '';
if (YP_MULTILINGUAL && $GLOBALS['YP_CURRENT_LANGUAGE'] != $GLOBALS['YP_DEFAULT_LANGUAGE'])
	$url = $GLOBALS['YP_CURRENT_LANGUAGE'].'/';
if ($node)
	$url = $node->nodeUrl();

if ($url != $dataprovider->getCurrentURL())
{
	header('HTTP/1.1 302 Found');
	header('Location: '.$system->base_url.$url);
	exit();
}

try
{
	$templateClass = $templateController->findTemplate($node);
} catch (Exception $e)
{
	exit($e->getMessage());
}

require($templateClass.'.php');
$template = new $templateClass();
$template->setNode($node);
echo $template->html();

// start the end of the program
require(CODE.'program/shutdown.php');

exit();
