<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page handling album call
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

/*
if no album is selected, show index page (undefined / info/help page)

if album is selected, show contents of album (sections) in menu and highlight current album in menu
show sections in content part

if album edit mode is selected, show info of album in form

*/

if ( ($album_id = (isset($_GET['aid'])) ? (int) $_GET['aid'] : false) !== false )
{
	if ( ($switch_id = (isset($_GET['switch'])) ? (int) $_GET['switch'] : false) )
	{
		$section = new Section();
		$section->id = $switch_id;
		$section->switchOnline();
		unset($section);
		
		$system->relocate('album.php?aid='.$album_id.'#section-'.$switch_id);
		exit();
	}

	$canvas->open_album = $album_id;
	
	$mode = (isset($_GET['mode'])) ? $_GET['mode'] : 'show';
	
	$album = new Album();
	$album->id = $album_id;
	
	if ($switch_id !== false)
	{
		$album->setModified();
	}
	
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
	
	$album->loadAll();
#	$album->loadSections();
	
	// handle too many sections for in menu (general)
	$album->menuSections();
	
	if ($album_id == 0 && $yourportfolio->session['limited'])
	{
		// limited users shouldn't make new albums
		$system->relocate('album.php');
	}
	
	if (!empty($album->id))
	{
		$yourportfolio->title_url = 'http://'.DOMAIN. ((SUB_DOMAIN) ? '/'.SUB_DOMAIN : '') .'/'.$album->getLink().'/';
	}
	
	// if $album_id == 0, a new album is wanting to join
	if ($album_id == 0 || $mode == 'edit')
	{
		// check for album existence, when does not exist, init as new
		// load all album data for editing
#		$album->loadAll();
		$yourportfolio->labelsLoad();
		
		if ($album->locked == 'Y' && !$yourportfolio->session['master'])
		{
			$system->relocate('album.php?aid='.$album->id);
		}
		
		if ($album->id > 0)
		{
			$name = '';
			if (!empty($album->name))
			{
				$name = $album->name;
			} else if (YP_MULTILINGUAL && !empty($album->strings['name']))
			{
				$first_language = array_shift(array_keys($album->strings['name']));
				$name = $album->strings['name'][$first_language]['string_parsed'];
			}
			
			$yourportfolio->title = sprintf(_("wijzig '%s'"), $name);
			$yourportfolio->upload_link = true;
			$yourportfolio->back_url = 'album.php?aid='.$album->id;
		} else {
			$yourportfolio->title = _('nieuw album');
			$yourportfolio->upload_link = false;
			
			if ( ($restricted = (isset($_GET['restricted'])) ? intval($_GET['restricted']) : false) !== false )
			{
				$album->restricted = 'Y';
			}
		}
		
		if ($yourportfolio->settings['restricted_albums'])
		{
			$yourportfolio->loadClientUsers();
		}
		
		$yourportfolio->upload_link = false;

		$canvas->inner_template = 'album_edit';
		$canvas->icon = 'album_white';
		
		$canvas->addScript('text_manipulation');
		$canvas->addScript('common');
	} else {
		// normal album view
		$yourportfolio->upload_link = false;
		if (!empty($album->sections) && $album->template == 'album')
			$yourportfolio->upload_link = true;
		$yourportfolio->news_link = ($album->template == 'news') ? true : false;

		$name = '';
		if (!empty($album->name))
		{
			$name = $album->name;
		} else if (YP_MULTILINGUAL && !empty($album->strings['name']))
		{
			$first_language = array_shift(array_keys($album->strings['name']));
			$name = $album->strings['name'][$first_language]['string_parsed'];
		}
		$yourportfolio->title = ''.$name;
		
		switch($album->template)
		{
			case('album'):
				$canvas->inner_template = 'sections';
//				$yourportfolio->save_url = false;
				$canvas->addScript('common');
				break;
			case('news'):
				$canvas->inner_template = 'newsitems';
				$yourportfolio->save_url = false;
				$canvas->addScript('text_manipulation');
				$canvas->addScript('common');
				break;
			case('contact'):
				$canvas->inner_template = 'album_contact';
				$canvas->addScript('text_manipulation');
				$canvas->addScript('common');
				break;
			case('text'):
				$canvas->inner_template = 'album_text';
				$canvas->addScript('text_manipulation');
				$canvas->addScript('common');
				break;
			case('guestbook'):
//				$yourportfolio->save_url = false;
				$canvas->addScript('common');
				
				if ( ($message_id = (isset($_GET['mid'])) ? (int) $_GET['mid'] : false) !== false )
				{
					$canvas->inner_template = 'guestbook_message_edit';
					$yourportfolio->back_url = 'album.php?aid='.$album->id;
				} else {
					$canvas->inner_template = 'guestbook_listing';
				}
				break;
			default:
				trigger_error('no template for this type found: '.$album->template, E_USER_NOTICE);
				$yourportfolio->save_url = false;
				$canvas->inner_template = 'empty';
		}

		$canvas->icon = $album->template.'_white';
	}
	
} else {
	// initial view
	
	// goto first available album
	$yourportfolio->loadMenu();
	if (count($yourportfolio->menu_albums) > 0)
	{
		$system->relocate('album.php?aid='.$yourportfolio->menu_albums[0]['id']);
	}
	
	// not executed for the moment, need to create a usefull album index first
	$yourportfolio->title = 'Albums';
	$yourportfolio->save_url = false;
	$yourportfolio->upload_link = false;
	
	$canvas->inner_template = 'no_albums';
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