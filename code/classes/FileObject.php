<?PHP
/**
 * Project:		yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * FileObject
 *
 * @package yourportfolio
 * @subpackage Core
 */
class FileObject
{
	var $id = 0;
	var $owner_id;
	var $owner_type;
	var $file_id;
	var $created;
	var $online = 'Y';
	var $name;
	var $size = 0;
	var $extension;
	var $type;
	var $width = 0;
	var $height = 0;
	var $basepath;
	var $path;
	var $sysname;
	var $tmp_name;
	
	var $_db = null;
	
	function __constructor($data = array())
	{
		foreach($data as $key => $value)
		{
			$this->{$key} = $value;
		}
	}
	
	function FileObject($data = array())
	{
		$this->__constructor($data);
	}
	
	function init()
	{
		
	}
	
	function load()
	{
		if (empty($this->owner_type))
		{
			return false;
		}
		
		$this->checkDB();
		
		$query = "SELECT owner_id, online, name, file_id, size, extension, type, width, height, basepath, path, sysname FROM `".$this->_db->_table[$this->owner_type.'_files']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			$this->init();
		}
	}

	function save()
	{
		if (empty($this->owner_type) || empty($this->id))
		{
			return false;
		}
		
		$this->checkDB();
		
		$query = "UPDATE `".$this->_db->_table[$this->owner_type.'_files']."` SET 
			`owner_id`='" . $this->owner_id . "',
			`online`='" . $this->online . "',
			`name`='" . $this->name . "',
			`file_id`='" . $this->file_id . "',
			`size`='" . $this->size . "',
			`extension`='" . $this->extension . "',
			`type`='" . $this->type . "',
			`width`='" . $this->width . "',
			`height`='" . $this->height . "',
			`basepath`='" . $this->basepath . "',
			`path`='" . $this->path . "',
			`sysname`='" . $this->sysname . "'
			WHERE id='".$this->id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	function checkDB()
	{
		if (is_null($this->_db))
		{
			global $db;
			$this->_db = &$db;
		}
	}
	
	/**
	 * generates a cache file based upon current file info and source
	 */
	function buildCacheFile($naming)
	{
		global $system;
		
		// generate storage name
		$search = array('{id}', '{ext}');
		$replace = array($this->id, $this->extension);
		$this->sysname = $this->owner_type.'-'.str_replace($search, $replace, $naming);
		
		$replace = array($this->owner_id, $this->extension);
		$sourcename = str_replace($search, $replace, $naming);
		
		// generate a cache/thumbnail version for the backend (for upload box detail view)
		$sizes = array('w' => 120, 'h' => 90);
		$imageToolkit = $system->getModule('ImageToolkit');
		if (file_exists($this->path.$this->owner_type.'-'.$sourcename))
			$imageToolkit->imageResize($this->path.$this->owner_type.'-'.$sourcename, $sizes, CACHE_UPLOAD_DIR.$this->sysname);
		else if (file_exists($this->path.$sourcename))
			$imageToolkit->imageResize($this->path.$sourcename, $sizes, CACHE_UPLOAD_DIR.$this->sysname);
	}
	
	function isEmpty()
	{
		if (empty($this->sysname))
		{
			return true;
		}
		
		return false;
	}
	
	function canCrop($settings)
	{
		foreach ($settings['actions'] as $action)
		{
			switch ($action['action'])
			{
				case 'customCrop':
				case 'scaleAndCrop':
				case 'scaleAndCropLandscape':
					return true;
			}
		}
		return false;
	}
	
	/**
	 * deletes all files related to this fileObject, will _NOT_ delete the entry from the database
	 * @param $actions:Array		contains the actions which are applied to the file, some of which lead to files to be deleted
	 */
	function destroy($actions = array())
	{
		if (empty($this->file_id))
		{
			error_log('FileObject id is empty, debug backtrace:');
			ob_start();
			debug_print_backtrace();
			error_log(ob_get_clean());
			return;
		}
		
		if (file_exists($this->path.$this->sysname))
		{
			unlink($this->path.$this->sysname);
		}
		
		if (file_exists(CACHE_UPLOAD_DIR.$this->sysname))
			unlink(CACHE_UPLOAD_DIR.$this->sysname);
		
		if (!empty($actions))
		{
			foreach($actions as $action)
			{
				switch($action['action'])
				{
					case('saveOriginal'):
						if (file_exists(ORIGINALS_DIR.$this->sysname))
						{
							unlink(ORIGINALS_DIR.$this->sysname);
						}
						break;
					case('yourportfolio'):
						if (file_exists(YOURPORTFOLIO_DIR.$this->sysname))
						{
							unlink(YOURPORTFOLIO_DIR.$this->sysname);
						}
						break;
				}
			}
		}
		
		// remove cache file
		if (file_exists(CACHE_DIR))
		{
			$cache_files = glob(CACHE_DIR.$this->owner_type.'-'.$this->id.'-'.$this->file_id.'-*x*.'.$this->extension);
			if (is_array($cache_files))
			{
				foreach ($cache_files as $cache)
				{
					unlink($cache);
				}
			}
		}
	}
}
?>
