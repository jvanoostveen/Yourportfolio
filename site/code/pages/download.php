<?PHP
/**
 * Project:		yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * Script for downloading content, sets correct file name, and forces browser to download it to the hd, instead of opening it in a browser window
 *
 * @package yourportfolio
 * @subpackage Site
 */

// start the program
require(CODE.'program/startup_download.php');

// item_id is a numeric id or the item record
// file_id is a string id, or better, the key under which the file is known
$item_id		= (isset($_GET['iid']) && ($_GET['iid'] = (int) $_GET['iid']) > 0) ? $_GET['iid'] : false;
$section_id		= (isset($_GET['sid']) && ($_GET['sid'] = (int) $_GET['sid']) > 0) ? $_GET['sid'] : false;
$album_id		= (isset($_GET['aid']) && ($_GET['aid'] = (int) $_GET['aid']) > 0) ? $_GET['aid'] : false;
$file_id		= (isset($_GET['fid'])) ? $_GET['fid'] : false;
$show			= (isset($_GET['show'])) ? true : false;

$owner_id	= false;
$owner_type	= "";

if ($item_id !== false && $section_id === false && $album_id === false)
{
	$owner_id = $item_id;
	$owner_type = "Item";
} else if ($item_id === false && $section_id !== false && $album_id === false)
{
	$owner_id = $section_id;
	$owner_type = "Section";
} else if ($item_id === false && $section_id === false && $album_id !== false)
{
	$owner_id = $album_id;
	$owner_type = "Album";
} else {
	$owner_id = false;
}

if ($owner_id === false)
{
	exit('Object doesn\'t exist');
}

if ($file_id === false)
{
	exit('Wrong File parameter');
}

$owner = new $owner_type();
$owner->id = $owner_id;
$owner->load();

if ($owner->id == 0)
{
	// Object doesn't exist
	exit('Object doesn\'t exist');
}

if ($owner->online == 'N')
{
	// item is offline
	exit('Object is offline');
}

$file = $owner->getFile($file_id); // where file_id = key

if ($file->isEmpty())
{
	// no file
	exit('No such file');
}

if ($file->online == 'N')
{
	// file is offline
	exit('File is offline');
}

// overwrite file mimetype to make sure it downloads
if (!$show)
{
	$file->type = 'application/octet-stream';
}

if (!file_exists($file->basepath.$file->sysname))
{
	// can't find file!
	trigger_error('Could not find file: '.$file->basepath.$file->sysname, E_USER_NOTICE);
	exit('Sorry, couldn\'t find the file');
}

if ( $fp = fopen($file->basepath.$file->sysname, 'rb') )
{
	$disposition = ($show) ? 'inline' : 'attachment';
	
	header("Cache-Control: ");
	header("Pragma: ");
	header('Content-Disposition: '.$disposition.'; filename="'.$file->name.'"');
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
	trigger_error('Could not open file: '.$file->basepath.$file->sysname, E_USER_NOTICE);
}

// end program
require(CODE.'program/shutdown_download.php');
?>