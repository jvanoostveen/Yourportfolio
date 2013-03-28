<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * page doing the actual parse
 *
 * @package yourportfolio
 * @subpackage Page
 */

// start the program
require(CODE.'program/startup.php');

// program code
if ( ($album_id		= (isset($_POST['aid']))  ? intval($_POST['aid'])  : false) !== false &&
	 ($section_id	= (isset($_POST['sid']))  ? intval($_POST['sid'])  : false) !== false)
{
	$item = new Item();
	$item->init();
	$item->parseFilesSettings();
	
	$item_data = array(
					'album_id'		=> $album_id,
					'section_id' 	=> $section_id,
					'name'			=> '',
					'subname'		=> '',
					'text_original'	=> ''
					);
	$file_data = array(
					'name'	=> $_FILES['Filedata']['name'],
					'size'	=> $_FILES['Filedata']['size'],
					'tmp_name' => $_FILES['Filedata']['tmp_name']
				);
	$file_data['extension']	= $system->fileExtension($file_data['name']);
	if ($file_data['extension'] == 'jpg')
		$file_data['type'] = 'image/jpeg';
	
	$first_file_id = array_keys($item->files_settings);
	$first_file_id = $first_file_id[0];
	$item_files = array(
			$first_file_id => $file_data
		);
	
	$item->save($item_data, $item_files, false);
	
	$item->loadFiles();
	
	// we need the yourportfolio preview path
	echo $system->base_url.'../assets/yourportfolio/item-'.$item->id.'.jpg';
} else {
	echo 'false';
}

// end program code

/**
 * disconnect from database
 */
$db->disconnect();
