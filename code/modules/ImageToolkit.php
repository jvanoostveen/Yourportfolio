<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

define('IMAGE_GDLIB', 1);
define('IMAGE_IMAGEMAGICK', 2);

/**
 * class: ImageToolkit
 * does all sorts of image stuff
 * this class requires PHP 4.3.0 or greater
 * 
 * @package yourportfolio
 * @subpackage Toolkits
 */
class ImageToolkit
{
	/**
	 * image library settings
	 * @var integer $image_lib
	 * @var string $imagemagick
	 */
	static $image_lib = IMAGE_GDLIB;
	static $imagemagick = '/usr/local/bin/';
	static $backgroundTask = false;
	
	/**
	 * various imagetype settings
	 * @var array $imagesettings
	 */
	static $imagesettings = array(
							IMAGETYPE_JPEG	=> array(
													// set the quality/compression level
													'quality' => 75,
													// apply a sharpen filter
													'sharpen' => 5,
													// strips (target) image from profiles and comments
													'strip' => true,
													// preserve settings overrides the quality settings and keeps them the save as in the source image
													'preserve-settings' => false
													),
							IMAGETYPE_PNG	=> array('quality' => 75),
							);
							
	
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		if (phpversion() < '4.3.0')
			trigger_error(__CLASS__.' requires PHP 4.3.0 or greater', E_USER_ERROR);
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function ImageToolkit()
	{
		$this->__construct();
	}
	
	/**
	 * Sets the image library to be used during runtime
	 * for sence of performance, there is no check if libraries are installed
	 *
	 * @param integer $type		IMAGE_GDLIB or IMAGE_IMAGEMAGICK
	 */
	function setImageLibrary($type)
	{
		if ($type == IMAGE_GDLIB || $type == IMAGE_IMAGEMAGICK)
			self::$image_lib = $type;
	}
	
	/**
	 * return image type
	 *
	 * @param string $i		path where image can be found
	 * 
	 * @return integer
	 * @access public
	 */
	function getImageType($i)
	{
		if (file_exists($i))
		{
			list( , , $type) = getImageSize($i);
			return $type;
		} else
			return false;
	}
	
	/**
	 * return image sizes
	 * 
	 * @param string $i		path where image can be found
	 *
	 * @return array
	 * @access public
	 */
	function getImageSizes($i)
	{
		if (file_exists($i))
		{
			list($width, $height) = getImageSize($i);
			return array($width, $height);
		} else {
			return false;
		}
	}
	
	/**
	 * Get number of channels used in the image.
	 * When number of channels could not be detected, default to 3.
	 * 
	 * @param $i:String		path of image
	 * @return Number
	 */
	function getImageChannels($i)
	{
		if (file_exists($i))
		{
			$info = getImageSize($i);
			if (isset($info['channels']))
			{
				return $info['channels'];
			} else {
				return 3;
			}
		} else {
			return 3;
		}
	}
	
	function calculateMemoryNeeded($i)
	{
		if (!file_exists($i))
			return 0;
		
		$info = getImageSize($i);
		$type = self::getImageType($i);
		$k64 = 65536; // 64KB
		$factor = 1.7; // memory factor
		$needed = 0;
		
		switch ($type)
		{
			case IMAGETYPE_JPEG:
				$needed = ceil(($info[0] * $info[1] * $info['bits'] * $info['channels'] / 8 + $k64) * $factor);
				break;
			case IMAGETYPE_PNG:
				$needed = ceil($info[0] * $info[1] * $info['bits'] + $k64);
				break;
		}
		
		return $needed;
	}
	
	/**
	 * Will scale and crop a landscape photo, but resize a portrait.
	 *
	 */
	function scaleAndCropLandscape($source, $sizes, $target, $target_type = null)
	{
		if (!file_exists($source))
		{
			trigger_error('no source file at '.$source);
			return array();
		}
		
		list($width, $height) = self::getImageSizes($source);
		if ($width >= $height)
		{
			return self::scaleAndCrop($source, $sizes, $target, $target_type);
		} else {
			return self::imageResize($source, $sizes, $target, $target_type);
		}
	}
	
	/**
	 * wrapper function for images that need resizing, otherwise just copy them to target
	 *
	 * @param array $source		source file including path
	 * @param array $sizes		resize to size[w,h]
	 * @param string $target	target location
	 * @param $target_type:Integer
	 *
	 * @return array			returns array containing width & height, or false when unsuccessful
	 * @access public
	 */
	function imageResize($source, $sizes, $target, $target_type = null)
	{
		if (!file_exists($source))
		{
			trigger_error('no source file at '.$source);
			return false;
		}
		
		list($width, $height) = self::getImageSizes($source);
		$type = self::getImageType($source);
		$channels = self::getImageChannels($source);
		
		if( $type == IMAGETYPE_GIF )
		{
			trigger_error("Sorry, don't know how to resize a gif. Just copying to $target.");
			copy( $source, $target );
			$sizes = self::getImageSizes( $source );
			return array('w' => $sizes[0], 'h' => $sizes[1] );
		}

		// only 3 channels are supported for now
		if ($type == IMAGETYPE_JPEG && $channels != 3)
		{
			trigger_error('Image contains '.$channels.' channels, where 3 channels are expected.', E_USER_WARNING);
			if (class_exists('MessageQueue'))
			{
				switch ($channels)
				{
					case (1):
						MessageQueue::add(_('Bestand bevat 1 kanaal, terwijl er 3 vereist zijn.'), MESSAGE_WARNING);
						break;
					case (4):
						MessageQueue::add(_('Bestand is opgeslagen als CMYK en kan niet verwerkt worden, bewaar het bestand als RGB.'), MESSAGE_WARNING);
						break;
					default:
						MessageQueue::add(sprintf(_('Bestand bevat %d kanalen, terwijl er 3 vereist zijn.'), $channels), MESSAGE_WARNING);
				}
			}
			return false;
		}
		
		if ($target_type == null)
		{
			$target_type = $type;
		}
		
		$quality = self::$imagesettings[IMAGETYPE_JPEG]['quality'];
		
		// determine if photo needs resizing
		if ($width <= $sizes['w'] && $height <= $sizes['h'])
		{
			if ($type == $target_type)
			{
				// can copy image
				if (!copy($source, $target))
				{
					trigger_error("file copy failed ".$source." -> ".$target, E_USER_NOTICE);
					return false;
				}
				@chmod($target, 0666);
			} else {
				// convert image
				
				if (!self::checkMemory($source))
					return false;
				
				// input
				$src_image = null;
				
				switch ($type)
				{
					case (IMAGETYPE_JPEG):
						$src_image = imageCreateFromJPEG($source);
						break;
					case (IMAGETYPE_PNG):
						$src_image = imageCreateFromPNG($source);
						break;
					case (IMAGETYPE_GIF):
						trigger_error("Doing a GIF now ");
						break;
					default:
						trigger_error('unsupported input image type: '.$type, E_USER_WARNING);
						return false;
				}
				
				// output
				$dest_image = null;
				
				switch ($target_type)
				{
					case (IMAGETYPE_JPEG):
						// convert image to jpeg with a white background
						$dest_image = imageCreateTrueColor($width, $height);
						
						// make sure background is white
						$bgc = imageColorAllocate($dest_image, 255, 255, 255);
						imageFilledRectangle($dest_image, 0, 0, $width, $height, $bgc);
						
						imageCopyResampled($dest_image, $src_image, 0, 0, 0, 0, $width, $height, $width, $height);
						
						touch($target);
						imageJPEG($dest_image, $target, $quality);
						
						break;
					default:
						trigger_error('unsupported output image type: '.$target_type, E_USER_WARNING);
						imageDestroy($src_image);
						return false;
				}
				@chmod($target, 0666);
				
				imageDestroy($src_image);
				imageDestroy($dest_image);
			}
			
			return array('w' => $width, 'h' => $height); // no resizing needed
		}
		
		// resize part
		$src_ratio 	= $width / $height;
		$dest_ratio	= $sizes['w'] / $sizes['h'];
		$dest_width = $dest_height = 0;
		
		if ($dest_ratio > $src_ratio)
		{
			$dest_width = round($sizes['h'] * $src_ratio);
			$dest_height = $sizes['h'];
		} else {
			$dest_width = $sizes['w'];
			$dest_height = round($sizes['w'] / $src_ratio);
		}
		
		if (!self::checkMemory($source, $dest_width, $dest_height))
			return false;
		
		// input
		$src_image = null;
		
		switch ($type)
		{
			case (IMAGETYPE_JPEG):
				$src_image = imageCreateFromJPEG($source);
				break;
			case (IMAGETYPE_PNG):
				$src_image = imageCreateFromPNG($source);
				break;
			default:
				trigger_error('unsupported input image type: '.$type, E_USER_WARNING);
				return false;
		}
		
		// output
		$dest_image = null;
		
		$dest_image = imageCreateTrueColor($dest_width, $dest_height);
		
		// actions before imageCopyResampled
		switch ($target_type)
		{
			case (IMAGETYPE_JPEG):
				// make sure background is white
				$bgc = imageColorAllocate($dest_image, 255, 255, 255);
				imageFilledRectangle($dest_image, 0, 0, $dest_width, $dest_height, $bgc);
				break;
			case (IMAGETYPE_PNG):
				imageAlphaBlending($dest_image, false);
				break;
		}
		
		imageCopyResampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $width, $height);
		
		// actions after imageCopyResampled
		switch ($target_type)
		{
			case (IMAGETYPE_PNG):
				imageSaveAlpha($dest_image, true);
				break;
		}
		
		touch($target);

		switch ($target_type)
		{
			case (IMAGETYPE_JPEG):
				// save image as jpeg
				imageJPEG($dest_image, $target, $quality);
				break;
			case (IMAGETYPE_PNG):
				imagePNG($dest_image, $target);
				break;
			default:
				trigger_error('unsupported output image type: '.$target_type, E_USER_WARNING);
				imageDestroy($src_image);
				imageDestroy($dest_image);
				return false;
		}
		@chmod($target, 0666);
		
		imageDestroy($src_image);
		imageDestroy($dest_image);
		
		return array('w' => $dest_width, 'h' => $dest_height);
	}
	
	function checkMemory($path, $dest_width = -1, $dest_height = -1)
	{
		$memory_limit = ((int) ini_get('memory_limit')) * pow(1024, 2);
		$needed = self::calculateMemoryNeeded($path);
		
		// calculate for destination image
		$needed2 = 0;
		if ($dest_width > -1)
		{
			$info = getImageSize($path);
			$needed2 = ceil($dest_width * $dest_height * $info['bits']);
		}
		
		$usage = 0;
		if (function_exists('memory_get_usage'))
			$usage = memory_get_usage();
		
		// test for enough memory to create image(s)
		if ($usage + $needed + $needed2 > $memory_limit)
		{
			if (class_exists('MessageQueue'))
				MessageQueue::add(_('De gebruikte afbeelding is te groot, pas deze aan en upload opnieuw.'));
			return false;
		}
		
		return true;
	}
	
	/**
	 * Resize the image to minimum sizes, and crop any overflow image.
	 * 
	 * @param array $source		source file including path
	 * @param array $sizes		resize to size[w,h]
	 * @param string $target		target location
	 * 
	 * @return array				returns array containing width & height, or false when unsuccessful
	 */
	function scaleAndCrop($source, $sizes, $target, $target_type = null)
	{
		if (!file_exists($source))
		{
			trigger_error('no source file at '.$source);
			return array();
		}
		
		list($width, $height) = self::getImageSizes($source);
		$type = self::getImageType($source);
		$channels = self::getImageChannels($source);
		
		// only 3 channels are supported for now
		if ($type == IMAGETYPE_JPEG && $channels != 3)
		{
			trigger_error('Image contains '.$channels.' channels, where 3 channels are expected.', E_USER_WARNING);
			if (class_exists('MessageQueue'))
			{
				switch ($channels)
				{
					case (1):
						MessageQueue::add(_('Bestand bevat 1 kanaal, terwijl er 3 vereist zijn.'), MESSAGE_WARNING);
						break;
					case (4):
						MessageQueue::add(_('Bestand is opgeslagen als CMYK en kan niet verwerkt worden, bewaar het bestand als RGB.'), MESSAGE_WARNING);
						break;
					default:
						MessageQueue::add(sprintf(_('Bestand bevat %d kanalen, terwijl er 3 vereist zijn.'), $channels), MESSAGE_WARNING);
				}
			}
			return false;
		}
		
		if ($target_type == null)
		{
			$target_type = $type;
		}
		
		$quality = self::$imagesettings[IMAGETYPE_JPEG]['quality'];
		
		// determine if photo needs resizing
		if ($width <= $sizes['w'] && $height <= $sizes['h'])
		{
			if ($type == $target_type)
			{
				// can copy image
				if (!copy($source, $target))
				{
					trigger_error("file copy failed ".$source." -> ".$target, E_USER_NOTICE);
					return false;
				}
			} else {
				// convert image
				
				if (!self::checkMemory($source))
					return false;
				
				// input
				$src_image = null;
				
				switch ($type)
				{
					case (IMAGETYPE_JPEG):
						$src_image = imageCreateFromJPEG($source);
						break;
					case (IMAGETYPE_PNG):
						$src_image = imageCreateFromPNG($source);
						break;
					default:
						trigger_error('unsupported input image type: '.$type, E_USER_WARNING);
						return false;
				}
				
				// output
				$dest_image = null;
				
				switch ($target_type)
				{
					case (IMAGETYPE_JPEG):
						// convert image to jpeg with a white background
						$dest_image = imageCreateTrueColor($width, $height);
						
						// make sure background is white
						$bgc = imageColorAllocate($dest_image, 255, 255, 255);
						imageFilledRectangle($dest_image, 0, 0, $width, $height, $bgc);
						
						imageCopyResampled($dest_image, $src_image, 0, 0, 0, 0, $width, $height, $width, $height);
						
						touch($target);
						imageJPEG($dest_image, $target, $quality);
						
						break;
					default:
						trigger_error('unsupported output image type: '.$target_type, E_USER_WARNING);
						imageDestroy($src_image);
						return false;
				}
				@chmod($target, 0666);
				
				imageDestroy($src_image);
				imageDestroy($dest_image);
			}
			
			return array('w' => $width, 'h' => $height); // no resizing needed
		}
		
		// variables that will be used as parameters for imagecopyresampled()
		$src_h = $height;
		$src_w = $width;
		$src_x = 0;
		$src_y = 0;
		$dst_x = 0;
		$dst_y = 0;
		$dst_w = $sizes['w'];
		$dst_h = $sizes['h'];

		// see which edge is closest to target size or which is more negative
		$diff_width = $width/$sizes['w'];
		$diff_height = $height/$sizes['h'];
		
		if($diff_width < $diff_height)	// we' re taking a snapshot over the full width
		{
			$src_y = round(($height-($sizes['h']*$diff_width))/2);	// take the snapshot out of the center
			$src_h = round(($width / $dst_w) * $dst_h);
		} else { // we're taking a snapshop over the full height
			$src_w = round(($height / $dst_h) * $dst_w);//maintain scale
			$src_x =  round(($width-($sizes['w']*$diff_height))/2);		// take the snapshot out of the center
		}
		
		if (!self::checkMemory($source, $dst_w, $dst_h))
			return false;
		
		// input
		$src_image = null;
		
		switch ($type)
		{
			case (IMAGETYPE_JPEG):
				$src_image = imageCreateFromJPEG($source);
				break;
			case (IMAGETYPE_PNG):
				$src_image = imageCreateFromPNG($source);
				break;
			default:
				trigger_error('unsupported input image type: '.$type, E_USER_WARNING);
				return false;
		}
		
		// output
		$dest_image = null;
		
		$dest_image = imageCreateTrueColor($dst_w, $dst_h);
		
		// actions before imageCopyResampled
		switch ($target_type)
		{
			case (IMAGETYPE_JPEG):
				// make sure background is white
				$bgc = imageColorAllocate($dest_image, 255, 255, 255);
				imageFilledRectangle($dest_image, 0, 0, $dst_w, $dst_h, $bgc);
				break;
			case (IMAGETYPE_PNG):
				imageAlphaBlending($dest_image, false);
				break;
		}
		
		imageCopyResampled($dest_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		
		// actions after imageCopyResampled
		switch ($target_type)
		{
			case (IMAGETYPE_PNG):
				imageSaveAlpha($dest_image, true);
				break;
		}
		
		touch($target);

		switch ($target_type)
		{
			case (IMAGETYPE_JPEG):
				// save image as jpeg
				imageJPEG($dest_image, $target, $quality);
				break;
			case (IMAGETYPE_PNG):
				imagePNG($dest_image, $target);
				break;
			default:
				trigger_error('unsupported output image type: '.$target_type, E_USER_WARNING);
				imageDestroy($src_image);
				imageDestroy($dest_image);
				return false;
		}
		@chmod($target, 0666);
		
		imageDestroy($src_image);
		imageDestroy($dest_image);
		
		return array('w' => $dst_w, 'h' => $dst_h);
	}
	
	function customCrop($src, $target, $x, $y, $w, $h, $crop_w, $crop_h)
	{
		$quality = self::$imagesettings[IMAGETYPE_JPEG]['quality'];
		
		if (!self::checkMemory($src, $crop_w, $crop_h))
			return false;
		
		$source_image = imageCreateFromJPEG($src);
		$dest_image = ImageCreateTrueColor($crop_w, $crop_h);
	
		imageCopyResampled($dest_image, $source_image, 0, 0, $x, $y, $crop_w, $crop_h, $w, $h);
		
		touch($target);
		imageJPEG($dest_image, $target, $quality);
		@chmod($target, 0666);
		
		imageDestroy($source_image);
		imageDestroy($dest_image);
	}
}
?>