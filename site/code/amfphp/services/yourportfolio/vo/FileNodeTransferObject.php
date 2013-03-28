<?PHP

class FileNodeTransferObject
{
	public $id;
	public $name;
	public $key;
	public $path;
	public $width = 0;
	public $height = 0;
	
	public $downloadURL;
	public $showURL;
	
	function __construct($data = null)
	{
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
		}
	}
}