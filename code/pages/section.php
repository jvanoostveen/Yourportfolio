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
if ( ($album_id   = (isset($_GET['aid'])) ? (int) $_GET['aid'] : false) !== false &&
	 ($section_id = (isset($_GET['sid'])) ? (int) $_GET['sid'] : false) !== false)
{
	if ($switch_id = (isset($_GET['switch'])) ? (int) $_GET['switch'] : false)
	{
		$item = new Item();
		$item->id = $switch_id;
		$item->switchOnline();
		unset($item);
		
		$system->relocate('section.php?aid='.$album_id.'&sid='.$section_id.'#item-'.$switch_id);
		exit();
	}

	$canvas->open_album = $album_id;
	$canvas->open_section = $section_id;
	
	$mode = (isset($_GET['mode'])) ? $_GET['mode'] : 'show';
	
	// load album data for menu
	$album = new Album();
	$album->id = $album_id;
	
	// if user has limited access, can he even access this album?
	if ($yourportfolio->session['limited'])
	{
		$user = new SubUser();
		$user->id = $yourportfolio->session['limited_id'];
		$user->load();
		
		if (!in_array($album->id, $user->album_ids))
		{
			$system->relocate('album.php');
		}
	}
	
	$yourportfolio->labelsLoad();
	
	$album->load();
	$album->loadSections();
	
	$section = new Section();
	
	$section->id = $section_id;
	$section->album = &$album;
	$section->load();
	
	if ($switch_id !== false)
	{
		$section->setModified();
		$album->setModified();
	}
	
	if ($section->text_node == 'Y')
	{
		$mode = 'edit';
	}
	
	// handle too many sections in menu (based on section)
	$album->menuSections($section->id);
	
	if (!empty($section->id))
	{
		$yourportfolio->title_url = 'http://'.DOMAIN. ((SUB_DOMAIN) ? '/'.SUB_DOMAIN : '') .'/'.$album->getLink().'/'.$section->getLink().'/';
	}
	
	// if $section_id == 0, a new section is wanting to join
	if ($section_id == 0 || $mode == 'edit')
	{
		if (in_array($album->type, $yourportfolio->preferences['album_types_with_text_sections']) || in_array($album->id, $yourportfolio->preferences['album_ids_with_text_sections']))
		{
			$section->text_node = 'Y';
		}
		
		$name = '';
		if (!empty($section->name))
		{
			$name = $section->name;
		} else if (YP_MULTILINGUAL && !empty($section->strings['name']))
		{
			$first_language = array_shift(array_keys($section->strings['name']));
			$name = $section->strings['name'][$first_language]['string_parsed'];
		}
		
		$yourportfolio->title = ($section->id > 0) ? sprintf(_("wijzig '%s'"), $name) : _('nieuwe sectie');
		$yourportfolio->back_url = ($album->template != 'news') ? 'section.php?aid='.$album->id.'&sid='.$section->id : 'album.php?aid='.$album->id;
		$yourportfolio->upload_link = false;
		
		if ($album->template == 'news')
		{
			$section->template = 'newsitem';
		}
		
		$canvas->inner_template = 'section_edit';
		$canvas->icon = 'items_white';
		
		$canvas->addStyle('section_edit');
		$canvas->addScript('text_manipulation');
		$canvas->addScript('common');
		$canvas->showCalendar = true;
	} else {
		// normal album view
		$name = '';
		if (!empty($section->name))
		{
			$name = $section->name;
		} else if (YP_MULTILINGUAL && !empty($section->strings['name']))
		{
			$first_language = array_shift(array_keys($section->strings['name']));
			$name = $section->strings['name'][$first_language]['string_parsed'];
		}
		
		$yourportfolio->title = ''.$name;
		
		switch($section->template)
		{
			case('section'):
				$section->loadItems();
				$canvas->inner_template = 'items';
//				$yourportfolio->save_url = false;
				
				$canvas->addScript('common');
				$yourportfolio->upload_dir = true;
				
				break;
			default:
				trigger_error('unknown template', E_USER_NOTICE);
				$canvas->inner_template = 'empty';
				$canvas->addScript('common');
		}
		
		
		$canvas->icon = 'items_white';
	}
	
} else {
	// initial view
	$yourportfolio->title = _('Secties');
	$yourportfolio->save_url = false;
	
	$canvas->inner_template = 'album_index';
	$canvas->icon = 'album_white';
}

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
$canvas->addStyle('jquery-ui.custom');

$canvas->addScript('jquery.min');
$canvas->addScript('jquery-ui.custom.min');
// end program code


require(CODE.'program/shutdown.php');
?>