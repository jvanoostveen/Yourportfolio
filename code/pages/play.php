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
 * @package yourportfolio
 * @subpackage Pages
 */

require(CODE.'program/startup.php');

// handle any possible form
require(CODE.'handlers/formhandler.php');
//

// program code
if ( ($file_id =    (isset($_GET['fid'])) ? intval($_GET['fid']) : false) !== false )
{
	// owner object type
	$object_types = array('album', 'section', 'item');
	$object_type = (ctype_alpha($_GET['obj']) && in_array($_GET['obj'], $object_types)) ? $_GET['obj'] : 'item';
	
	$type = (isset($_GET['type'])) ? $_GET['type'] : 'video';
	
	$file = new FileObject();
	$file->id = $file_id;
	$file->owner_type = $object_type;
	$file->load();
	
	if ($file->isEmpty())
	{
		$file->path = 'design/swf/';
		$file->sysname = 'error.flv';
	}
	
	if (empty($file->name))
	{
		$yourportfolio->title = _('Player');
	} else {
		$yourportfolio->title = $file->name;
	}
} else {
	$system->relocate('album.php');
}

//$canvas->addScript('detect');
//$canvas->addScript('detect_win', 'vbs');
$canvas->addScript('flash');

$canvas->addRawScript('if(!flash>0) flash = detectFlash();');
$canvas->addRawScript('flash8_pass = testVersion(flash, 8);');

switch($type)
{
	case('audio'):
		$player = 'mp3player';
		$background = '#FFFFFF';
		break;
	case('video'):
	default:
		$player = 'movieplayer';
		$background = '#CCCCCC';
}

$canvas->template = 'flash';

require(CODE.'program/shutdown.php');
?>