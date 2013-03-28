<?PHP
/**
 * Beheer van namen en adressen
 *
 * Project: Yourportfolio Newsletter module
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

$page_name = 'newsletters/';

require(CODE.'newsletter/code/startup.php');
$canvas->addScript('nl_common');
$canvas->addScript('nl_write');

$submenu = array (
	array(
		'name'	=> _('Verzonden'),
		'icon'	=> 'iconsets/default/section.gif',
		'href'	=> 'newsletter_write.php?case=group&g=sent'
	),
	array(
		'name'	=> _('Onverzonden'),
		'icon'	=> 'iconsets/default/section.gif',
		'href'	=> 'newsletter_write.php?case=group&g=draft'
	),
	array(
		'name'	=> '<i>'._('nieuwe nieuwsbrief...').'</i>',
		'icon'	=> 'img/btn_new_album.gif',
		'href'	=> 'newsletter_write.php?nid=0&task=template'
	),
);

// handle newsletter input
if (!empty($_POST) && !empty($_POST['target']))
{
	switch ($_POST['target'])
	{
		case ('newsletter'):
			Newsletter::handle($_POST['data']);
			break;
		case ('newsletter_item'):
			NewsletterItem::handle($_POST['data']);
			break;
		case ('newsletter_sender'):
			if(!NewsletterSender::handle($_POST['data']))
			{
				if( DEBUG )
				{
					trigger_error("Er zijn geen recipients");
				}
				
				$data['sending_error'] = true;
			}
			break;
	}
}

// 
if ( ($newsletter_id = (isset($_GET['nid'])) ? (int) $_GET['nid'] : false) !== false )
{
	$tasks = array('template' => _('Template'), 'content' => _('Inhoud'), 'preview' => _('Voorbeeld'), 'groups' => _('Groepen'), 'mailing' => _('Mailing'));
	$task = (!empty($_GET['task']) && in_array($_GET['task'], array_keys($tasks)) ? $_GET['task'] : $task[0]);
	
	$newsletter = new Newsletter();
	$newsletter->id = $newsletter_id;
	$newsletter->load();
	
	// tasks: template, content, groups, preview, send
	switch ($task)
	{
		case ('template'):
			$components['topBar'][] = 'save_link.php';
			$components['bottomBar'][] = 'save_link.php';
			$templates[] = 'write_newsletter_template.php';
			$data['templates'] = NewsletterTemplate::getTemplates();
			
			break;
		case ('content'):
			$components['topBar'][] = 'save_link.php';
			$components['bottomBar'][] = 'save_link.php';
			$templates[] = 'write_newsletter_items.php';
			
			$newsletter->loadItems();
			$template = $newsletter->getTemplate();
			$template->load();
			
			$data['newsletter_item_width'] = $template->itemimage_width;
			$data['newsletter_item_height'] = $template->itemimage_height;
			
			if ( ($item_id = (isset($_GET['iid'])) ? (int) $_GET['iid'] : false) !== false )
			{
				$item = new NewsletterItem();
				$item->id = $item_id;
				$item->load();
			} else {
				$item = new NewsletterItem();
				$item->init();
			}
			$data['newsletter_item'] = $item;
			
			$canvas->addScript('text_manipulation');
			$canvas->addScript('common');
			
			break;
		case ('groups'):
			$components['topBar'][] = 'save_link.php';
			$components['bottomBar'][] = 'save_link.php';		
			$templates[] = 'write_newsletter_groups.php';
			
			$newsletter->loadGroups();
			
			$data['groups'] = Group::getGroups();
			
			break;
		case ('preview'):
			$templates[] = 'write_newsletter_preview.php';
			
			break;
		case ('mailing'):
			$templates[] = 'write_newsletter_send.php';
			
			break;
	}
	
	$data['newsletter'] = $newsletter;
	$data['tasks'] = $tasks;
	$data['task'] = $task;
	
} else {
	
	$case = isset($_GET['case']) ? $_GET['case'] : '';
	
	switch ($case)
	{
		case ('group'):
			$group = $db->filter($_GET['g']);
			$ordergroup = 'order_'.$group;
			if( isset($_SESSION[$ordergroup] ) )
			{
				$ordering['field'] = $db->filter( $_SESSION[$ordergroup]['field'] );
				$ordering['dir'] = $db->filter( $_SESSION[$ordergroup]['direction'] );
			} else {
				$ordering['field'] = 'subject';
				$ordering['dir'] = 'ASC';
			}			

			$query = sprintf("SELECT `letter_id`, IF(`subject` IS NULL, '&lt;"._('geen titel')."&gt;', `subject`) AS subject, `created`, `modified`, `datesent` FROM `%s` WHERE `status`='".$group."' ORDER BY `".$ordering['field']."` ".$ordering['dir']."", $yourportfolio->_table['nl_letters']);
			$db->doQuery( $query, $data['letters'],  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
			$data['group'] = $group;
			$templates[] = 'write_group_'.$group.'.php';
			
			if ($group == 'sent')
			{
				// fetch letter stats
				$data['stats'] = array();
				$query = "SELECT letter_id, addressees FROM `".$yourportfolio->_table['nl_letter_stats']."`";
				$db->doQuery( $query, $data['stats'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array', false, array('index_key' => 'letter_id'));
			}
			
			if ($group == 'sent')
			{
				$page_name .= _('Verzonden').'/';
			} else {
				$page_name .= _('Onverzonden').'/';
			}
			
			break;
		default:
			$templates[] = 'write_list.php';
	}
}

do_output();

require(NL_CODE.'shutdown.php');
?>
