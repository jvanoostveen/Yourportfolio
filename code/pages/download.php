<?PHP
/**
 * Project:		yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * script for downloading content, sets correct file name, and forces browser to download it to the hd, instead of opening it in a browser window
 *
 * @package yourportfolio
 * @subpackage Site
 */

// start the program
require(CODE.'program/startup.php');

if ( ($file_id =    (isset($_GET['fid'])) ? (int) $_GET['fid'] : false) !== false )
{
	// owner object type
	$object_types = array('album', 'section', 'item');
	$object_type = (ctype_alpha($_GET['obj']) && in_array($_GET['obj'], $object_types)) ? $_GET['obj'] : 'item';
	
	$file = new FileObject();
	$file->id = $file_id;
	$file->owner_type = $object_type;
	$file->load();
	
	if ($file->isEmpty())
	{
		exit('nothing to download');
	}
}

/**
 * close current ob and prepare for commencing of download
 */
ob_end_clean();

// overwrite file mimetype to make sure it downloads
$file->type = 'application/octet-stream';

if (!file_exists($file->path.$file->sysname))
{
	// can't find file!
	trigger_error('Could not find file: '.$file->path.$file->sysname, E_USER_NOTICE);
	exit('File could not be found');
}

if ( $fp = fopen($file->path.$file->sysname, 'rb') )
{
	header("Cache-Control: ");
	header("Pragma: ");
	header('Content-Disposition: attachment; filename="'.$file->name.'"');
	header('Content-Type: '.$file->type.'; charset="iso-8859-1"; name="'.$file->name.'"');
	header('Content-Length: '.(string) $file->size);
	header('Content-Transfer-Encoding: binary');
	
	while( (!feof($fp)) && (connection_status() == 0) )
	{
		echo fread($fp, 1024*8);
		flush();
	}
	fclose($fp);
} else {
	trigger_error('Could not open file: '.$file->path.$file->sysname, E_USER_NOTICE);
}

// end program
require(CODE.'program/shutdown_download.php');
?>