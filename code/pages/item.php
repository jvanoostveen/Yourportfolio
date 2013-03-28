<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * page called when editing a photo or adding a new photo
 *
 *
 * @package yourportfolio
 * @subpackage Pages
 */

require(CODE.'program/startup.php');

// handle any possible form
require(CODE.'handlers/formhandler.php');
//

// program code
if ( ($album_id =   (isset($_GET['aid'])) ? (int) $_GET['aid'] : false) !== false &&
	 ($section_id = (isset($_GET['sid'])) ? (int) $_GET['sid'] : false) !== false &&
	 ($item_id =    (isset($_GET['iid'])) ? (int) $_GET['iid'] : false) !== false )
{

	$canvas->open_album = $album_id;
	$canvas->open_section = $section_id;

	$album = new Album();
	$album->id = $album_id;
	
	// if user has limited access, can he even access this album?
	if ($yourportfolio->session['limited'])
	{
		$user = new SubUser();
		$user->id = (int) $yourportfolio->session['limited_id'];
		$user->load();
		
		if (!in_array($album->id, $user->album_ids))
		{
			$system->relocate('album.php');
		}
	}
	
	$album->load();
	$album->loadSections();
	
	$section = new Section();
	$section->id = $section_id;
	$section->load();
	
	// handle too many sections in menu (based on section)
	$album->menuSections($section->id);
	
	$item = new Item();
	$item->id = $item_id;
	$item->section_id = $section_id;
	$item->album_id = $album_id;
	$item->album = $album;
	$item->section = $section;
	
	$item->load();
	
	$yourportfolio->loadAlbums();
	
	$yourportfolio->labelsLoad();
	
	if ($yourportfolio->settings['tags'])
		$yourportfolio->loadTags();
	
	$name = '';
	if (!empty($item->name))
	{
		$name = $item->name;
	} else if (!empty($item->strings['name']))
	{
		$first_language = array_shift(array_keys($item->strings['name']));
		$name = $item->strings['name'][$first_language]['string_parsed'];
	}
	
	if (empty($name))
	{
		$yourportfolio->title = _('wijzig item');
	} else {
		$yourportfolio->title = sprintf(_("wijzig '%s'"), $name);
	}
	
	if (!empty($item->id))
	{
		$yourportfolio->title_url = 'http://'.DOMAIN. ((SUB_DOMAIN) ? '/'.SUB_DOMAIN : '') .'/'.$album->getLink().'/'.$section->getLink().'/'.$item->getLink().'/';
	}
	
	$yourportfolio->back_url = 'section.php?aid='.$item->album_id.'&sid='.$item->section_id;
	
} else {
	if ($album_id && $section_id)
	{
		$system->relocate('section.php?aid='.$album_id.'&sid='.$section_id);
	} else if ($album_id) {
		$system->relocate('album.php?aid='.$album_id);
	} else {
		$system->relocate('album.php');
	}
}

/**
 * load menu data after all the things done
 */
$yourportfolio->loadMenu();

$canvas->icon = 'item_white';
$canvas->inner_template = 'item_edit';
$canvas->addStyle('input');
$canvas->addStyle('common');
$canvas->addStyle('complex');
$canvas->addStyle('jquery-ui.custom');

$canvas->addScript('text_manipulation');
$canvas->addScript('common');
$canvas->addScript('jquery.min');
$canvas->addScript('jquery-ui.custom.min');
// end program code

if ($system->browser == 5)
{
	$canvas->template = 'page_css';
	$canvas->addStyle('page_css2');
} else {
	$canvas->template = 'page_4';
	$canvas->addStyle('page_normal_css');
}

require(CODE.'program/shutdown.php');
?>