<?PHP
define('NL_CODE', CODE.'newsletter/code/');

require(CODE.'program/startup.php');

require(NL_CODE.'classes/Newsletter.php');
require(NL_CODE.'classes/NewsletterTemplate.php');
require(NL_CODE.'classes/NewsletterView.php');

if ( ($newsletter_id = (isset($_GET['nid'])) ? (int) $_GET['nid'] : false) !== false )
{
	$newsletter = new Newsletter();
	$newsletter->id = $newsletter_id;
	
	if (!$newsletter->load())
	{
		exit('no newsletter found');
	}
	$newsletter->loadItems();
	
	$template = $newsletter->getTemplate();
	$template->loadDesign();
	
	$view = new NewsletterView();
	$view->newsletter = $newsletter;
	$view->template = $template;
	
	$view->build();
	
	echo ($view->html);
	exit();
} else {
	exit('no newsletter id');
}

?>
