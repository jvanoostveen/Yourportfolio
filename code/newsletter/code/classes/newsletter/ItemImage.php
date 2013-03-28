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
	var $cache_name;
	
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
	
	function copy($newsletter_id)
	{
		global $yourportfolio, $db;
		
		$duplicate_id = $this->id;
		$old_sysname = $this->sysname;
		$this->id = 0;
		
		$this->sysname = $newsletter_id.'_'.$this->owner_id.'.'.$this->extension;
		
		if (copy($this->path.$old_sysname, $this->path.$this->sysname))
		{
			chmod($this->path.$this->sysname, 0666);
			
			$query = "INSERT INTO `".$yourportfolio->_table['nl_item_files']."` SET ".
						"owner_id='".$this->owner_id."', ".
						"created=NOW(), ".
						"name='".$db->filter($this->name)."', ".
						"size='".$db->filter($this->size)."', ".
						"extension='".$db->filter($this->extension)."', ".
						"type='".$db->filter($this->type)."', ".
						"width='".$this->width."', ".
						"height='".$this->height."', ".
						"path='".$this->path."', ".
						"sysname='".$this->sysname."'";
			$result = null;
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
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
	
	/**
	 * Delete the cache files for this image
	 */
	function clear_cache()
	{
		$this->load();
		if( $this->isEmpty())
		{
			return;
		}
		
		$cache_dir = SETTINGS.'newsletter/cache/';
		$dh = opendir($cache_dir);
		// extension filteren
		$extension = NewsletterView::extension($this->sysname);
	
		while (false !== ($file = readdir($dh)))
		{
			$id_part =substr($file,0,strlen($this->sysname)-(strlen($extension)+1));
			$cache_name = $id_part . '.' . $extension; 
			if($cache_name == $this->sysname)
			{
				if(DEBUG)
				{
					trigger_error("Unlinking ".SETTINGS.$file);
				}
				unlink(SETTINGS.'newsletter/cache/'.$file);
			}
		}
		
	}
	
	/**
	 * 
	 * 
	 */
	function delete()
	{
		$this->load();
		
		if ($this->isEmpty())
		{
			return;
		}
		
		if (file_exists($this->path.$this->sysname))
		{
			unlink($this->path.$this->sysname);
		}
		
		$nil = '';
		$query = sprintf("DELETE FROM `%s` WHERE id = '".$this->id."' LIMIT 1", $this->yp->_table['nl_item_files']);
		$this->db->doQuery($query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
}

?>
