<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2009 Furthermore
 * @author Daan Smit <daansmit@gmail.com>
 */
 
/**
 * Image crop
 *
 * @package yourportfolio
 * @subpackage Page
 */

// start the program
require(CODE.'program/startup.php');

function getCustomCropSettings($file, $owner_type, $owner_id)
{
	switch($owner_type)
	{
		case 'album':
			$owner = new Album();
			break;
		case 'section':
			$owner = new Section();
			break;
		case 'item':
			$owner = new Item();
			break;
	}
	$owner->id = $owner_id;
	$owner->load();
	
	$file_setting_actions = $owner->files_settings[$file->file_id]['actions'];
	foreach($file_setting_actions as $key => $value)
	{
		switch ($value['action'])
		{
			case 'customCrop':
			case 'scaleAndCrop':
			case 'scaleAndCropLandscape':
				$customCropSettings = $value['args'];
				break 2;
		}
	}
	
	return $customCropSettings;
}

/*  if Crop submit */
if(isset($_POST['action']) && $_POST['action'] == 'crop_image' && isset($_POST['file_id']) && isset($_POST['owner_type']) && isset($_POST['owner_id']))
{
	$file_id = intval($_POST['file_id']);
	$owner_type = $_POST['owner_type'];
	$owner_id = intval($_POST['owner_id']);
	
	$x = intval($_POST['x']);
	$y = intval($_POST['y']);
	$w = intval($_POST['w']);
	$h = intval($_POST['h']);

	$file = new FileObject();
	$file->id = $file_id;
	$file->owner_id = $owner_id;
	$file->owner_type = $owner_type;
	$file->load();
	
	
	$customCropSettings = getCustomCropSettings($file, $owner_type, $owner_id);

	$crop_width = intval($customCropSettings[0]);
	$crop_height = intval($customCropSettings[1]);
	
	$file->width = $crop_width;
	$file->height = $crop_height;
	$file->save();
	
	$imageToolkit = $system->getModule('ImageToolkit');
	$imageToolkit->customCrop(ORIGINALS_DIR.$file->sysname, $file->path.$file->sysname, $x, $y, $w, $h, $crop_width, $crop_height);


	/* Remove cache upload image */
	$cacheImage = "";
	switch($file->owner_type)
	{
		case 'album':
		case 'section':
			$cacheImage .= $file->owner_type . '-';
			break;
	}
	$cacheImage .= $file->id . "." . $file->extension;
	if(file_exists(CACHE_UPLOAD_DIR.$cacheImage))
		unlink(CACHE_UPLOAD_DIR.$cacheImage);
	
	$return = '<html>
		<head>
		<script type="text/javascript" language="javascript">
		<!--
		top.opener.location.reload();
		window.close();
		//-->
		</script>
		</head>
		<body></body>
		</html>';
	
	exit($return);
}

if (isset($_GET['fid']) && isset($_GET['owner']) && isset($_GET['oid']))
{
	$file_id = intval($_GET['fid']);
	$owner_type = $_GET['owner'];
	$owner_id = intval($_GET['oid']);
	
	$file = new FileObject();
	$file->id = $file_id;
	$file->owner_id = $owner_id;
	$file->owner_type = $owner_type;
	$file->load();

	if ($file->isEmpty())
	{
		exit('no image to crop');
	}

	$customCropSettings = getCustomCropSettings($file, $owner_type, $owner_id);

	$crop_width = intval($customCropSettings[0]);
	$crop_height = intval($customCropSettings[1]);

	$original_image_size = getimagesize(ORIGINALS_DIR.$file->sysname);
	
	$yourportfolio->title = _("Afbeelding bijsnijden");
	$canvas->template = 'crop';
	$canvas->addStyle('crop');
	$canvas->addStyle('jquery.Jcrop');
	
	$canvas->addScript('jquery.min');
	$canvas->addScript('jquery.Jcrop.min');
	$canvas->addScript('crop');
} else {
	exit('error, data missing');
}

require(CODE.'program/shutdown.php');

?>