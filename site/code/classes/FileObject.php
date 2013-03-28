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
	var $size;
	var $extension;
	var $type;
	var $width = 0;
	var $height = 0;
	var $basepath;
	var $path;
	var $sysname;
	var $tmp_name;
	
	function __constructor($data = array())
	{
		foreach($data as $key => $value)
		{
			$this->{$key} = $value;
		}
		
		if (empty($this->created))
		{
			$this->created = date("Y-m-d H:i:s");
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
		global $db;
		$query = "SELECT owner_id, name, size, extension, type, width, height, basepath, path, sysname FROM `".$db->_table[$this->owner_type.'_files']."` WHERE id='".$this->id."'";
		if ( !$db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			$this->init();
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
