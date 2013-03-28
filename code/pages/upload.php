<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page handling uploads
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
if ( ($album_id = (isset($_GET['aid'])) ? intval($_GET['aid']) : false) !== false )
{
	$section_id = (isset($_GET['sid'])) ? intval($_GET['sid']) : false;

	$canvas->open_album = $album_id;
	$canvas->open_section = $section_id;
	
	$album = new Album();
	$album->id = $album_id;
	$album->load();
	$album->loadSections();
	
	$section = new Section();
	if ($section_id === false)
	{
		// get album's first section
		if (!empty($album->sections))
		{
			$section_id = $album->sections[0]['id'];
		}
	}
	$section->id = ($section_id !== false) ? $section_id : 0;
	
	// handle too many sections in menu (based on section)
	$album->menuSections($section->id);
	
	$item = new Item();
	$item->init();
	$item->album = $album;
	$item->section = $section;
	
	if (in_array($album->type, $yourportfolio->preferences['album_types_with_text_items']))
	{
		$item->text_node = 'Y';
	}
	
	$item->parseFilesSettings();
	
	$yourportfolio->loadAlbums();

	$yourportfolio->labelsLoad();

	$yourportfolio->title = _('nieuw item');
	$yourportfolio->upload_link = false;
	$yourportfolio->back_url = ($section_id == false) ? 'album.php?aid='.$album->id : 'section.php?aid='.$album->id.'&sid='.$section->id;

	$canvas->inner_template = 'item_edit';
	$canvas->icon = 'item_upload';
} else {
	$system->relocate('album.php');
}

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

$canvas->addScript('common');
$canvas->addScript('text_manipulation');
$canvas->addScript('jquery.min');
$canvas->addScript('jquery-ui.custom.min');

/**
 * load menu data after all the things done
 */
$yourportfolio->loadMenu();

// end program code


require(CODE.'program/shutdown.php');
?>