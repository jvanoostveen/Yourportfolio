<?PHP

require_once(MODULES.'ImageToolkit.php');

class FileNode
{
	public $id;
	public $name;
	
	public $key;
	public $owner_type;
	
	public $syspath;
	public $path;
	public $extension;
	
	public $width;
	public $height;
	
	public $downloadURL;
	public $showURL;
	
	public function __construct($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
			
			$this->syspath = $this->path;
			
			global $system;
			$this->path = $system->base_url.$this->path.'?v='.$this->id;
		}
	}
	
	public function getPath($options = array())
	{
		global $system;
		
		if (empty($options))
			return $this->path;
		
		if (!isset($options['width']))
			$options['width'] = 0;
		if (!isset($options['height']))
			$options['height'] = 0;
		if (!isset($options['crop']))
			$options['crop'] = true;
		
		$cache = CACHE.$this->owner_type.'-'.$this->id.'-'.$this->key.'-'.$options['width'].'x'.$options['height'].($options['crop'] ? '-cropped' : '').'.'.$this->extension;
		
		if (file_exists($cache))
		{
			list($this->width, $this->height) = ImageToolkit::getImageSizes($cache);
			return $system->base_url.$cache;
		}
		
		if ($options['width'] == 0 || $options['height'] == 0 || $options['crop'] == false)
		{
			if ($options['width'] == 0)
				$options['width'] = 99999;
			if ($options['height'] == 0)
				$options['height'] = 99999;
			
			$sizes = ImageToolkit::imageResize($this->syspath, array('w' => $options['width'], 'h' => $options['height']), $cache);
		} else {
			$sizes = ImageToolkit::scaleAndCrop($this->syspath, array('w' => $options['width'], 'h' => $options['height']), $cache);
		}
		$this->width = $sizes['w'];
		$this->height = $sizes['h'];
		
		return $system->base_url.$cache;
	}
}