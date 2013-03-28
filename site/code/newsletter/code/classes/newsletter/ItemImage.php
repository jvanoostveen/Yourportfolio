<?PHP

class ItemImage
{
	var $id = 0;
	var $owner_id;
	var $created;
	var $name;
	var $size = 0;
	var $extension;
	var $type;
	var $width = 0;
	var $height = 0;
	var $path;
	var $sysname;
	var $tmp_name;
	
	var $alt;
	var $url;
	
	var $db = null;
	var $yp;
	
	function ItemImage($data = array())
	{
		$this->__construct($data);
	}
	
	function __construct($data = array())
	{
		foreach($data as $key => $value)
		{
			$this->{$key} = $value;
		}
		
		$this->alt = $this->name;
		$this->url = $this->path.$this->sysname;
	}
	
	function init()
	{
		
	}
	
	function load()
	{
		$this->checkDB();
		
		$query = "SELECT owner_id, name, size, extension, type, width, height, path, sysname FROM `".$this->yp->_table['nl_item_files']."` WHERE id='".$this->id."'";
		if ( !$this->db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			$this->init();
		}

		$this->alt = $this->name;
		$this->url = $this->path.$this->sysname;
	}
	
	function checkDB()
	{
		if (is_null($this->db))
		{
			global $db, $yourportfolio;
			$this->db = $db;
			$this->yp = $yourportfolio;
		}
	}
	
	function isEmpty()
	{
		if (empty($this->sysname))
		{
			return true;
		}
		
		return false;
	}
}

?>
