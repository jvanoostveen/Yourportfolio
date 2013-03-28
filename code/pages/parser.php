<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @copyright 2011 Axis fm
 * @author Joeri van Oostveen <joeri@axis.fm>
 */
 
// start the program
require(CODE.'program/startup.php');

// program code
if ( ($album_id   = (isset($_GET['aid'])) ? intval($_GET['aid']) : false) !== false &&
	 ($section_id = (isset($_GET['sid'])) ? intval($_GET['sid']) : false) !== false)
{
	$item = new Item();
	$item->init();
	$item->parseFilesSettings();
	$first_file_id = array_keys($item->files_settings);
	$first_file_id = $first_file_id[0];
	$file_settings = $item->files_settings[$first_file_id];
	
	$file_types = '*.*';
	$extension = $file_settings['extension'];
	if ($extension != '*')
	{
		$extensions = explode(',', $extension);
		$file_types = '*.'.join(', *.', $extensions);
	}
	$file_description = $file_settings['type'];
	
	$canvas->template = 'parser';
} else {
	$canvas->template = 'parser_error';
}

$yourportfolio->title = _('Importeren');

$canvas->addStyle('common');
$canvas->addStyle('page_normal_css');
$canvas->addScript('jquery.min');
$canvas->addScript('swfupload');
$canvas->addScript('swfupload.queue');
// end program code

require(CODE.'program/shutdown.php');
?>