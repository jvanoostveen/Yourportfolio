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
ob_end_clean();

ob_start();

$start = isset($_GET['start_id']) ? (int) $_GET['start_id'] : 0;

// select all items
$query = "SELECT id FROM `".$db->_table['items']."` WHERE id > '".$start."' ORDER BY id LIMIT 20";
$db->doQuery($query, $items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));

if (empty($items))
{
	$yourportfolio->update_xml();
	$yourportfolio->createGoogleSitemap();
	exit('done');
}

$last_item_id = 0;
foreach ($items as $item)
{
	$preview = $item->getFile('preview');
	
	copy($preview->path.$preview->sysname, CODE.'tmp'.'/'.$preview->sysname);
	chmod(CODE.'tmp'.'/'.$preview->sysname, 0666);
	
	$file_id = array_keys($item->files_settings);
	$file_id = $file_id[1];
	$item_files = array(
					$file_id	=> $system->getFileInfo(CODE.'tmp/'.$preview->sysname, $preview->name),
				);
	
	$item->save(array('old_section_id' => $item->section_id, 'old_position' => $item->position, 'old_album_id' => $item->album_id), $item_files, false);
	
	$last_item_id = $item->id;
}

header("Location: recreate_thumbs.php?start_id=".$last_item_id);

// end program code

ob_end_clean();

#require(CODE.'program/shutdown.php');
?>