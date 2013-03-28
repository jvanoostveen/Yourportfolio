<?PHP

require(CODE.'newsletter/code/startup.php');

if ( ($newsletter_id = (isset($_GET['nid'])) ? (int) $_GET['nid'] : false) !== false )
{
	require_once(NL_CODE.'classes/NewsletterView.php');
	
	$newsletter = new Newsletter();
	$newsletter->id = $newsletter_id;
	
	$newsletter->load();
	$newsletter->loadItems();
	
	$template = $newsletter->getTemplate();
	$template->loadDesign();
	
	$view = new NewsletterView();
	$view->newsletter = $newsletter;
	$view->template = $template;
	
	$view->build();
	
	if (isset($_GET['mode']) && $_GET['mode'] == 'text')
	{
		$view->buildText();
		
		echo ('<pre>'.$view->text.'</pre>');
	} else {
		echo ($view->html);
	}
	
} else {
	exit('no newsletter id');
}

?>
