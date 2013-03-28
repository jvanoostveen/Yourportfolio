<?PHP
/**
 * Project:			yourportfolio
 * 
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Item class
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Item
{
	/**
	 * vars available from database
	 */
	var $id;
	var $album_id;
	var $section_id;
	var $online;
	var $position;
	var $name;
	var $text_original;
	var $text;
	var $type;
	var $label_type;
	var $modified;
	
	// file related
	var $files = array();

	// multilanguage
	var $strings = array();
	
	/**
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;
	var $_system;

	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct($data = null)
	{
		global $db;
		
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		
		$this->_system = &$system;
		
		if (!empty($data))
		{
			foreach($data as $key => $value)
			{
				$this->$key = $value;
			}
			
			$this->loadCustomData();
			
			if (YP_MULTILINGUAL)
			{
				$this->loadLanguageStrings();
			}

			#$this->load();
			if (isset($GLOBALS['GENERATING_XML']) && $GLOBALS['GENERATING_XML'] == true)
			{
				$this->loadFiles();
			}
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Item($data = null)
	{
		$this->__construct($data);
	}
	
	/**
	 * give object some default values
	 */
	function init()
	{
		$this->id = 0;
		$this->online = 'Y';
		$this->position = '';
		$this->files = array();
	}
	
	/**
	 * load data needed for editing
	 * or when item can't be found in database, init default values
	 *
	 */
	function load()
	{
		$query = "SELECT album_id, section_id, online, type, position, name, text, custom_data, link, label_type FROM `".$this->_table['items']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			return false;
		}
		
		if (YP_MULTILINGUAL)
		{
			$this->loadLanguageStrings();
		}
		
		$this->loadCustomData();
		$this->loadFiles();
		
		return true;
	}
	
	/**
	 * load this object language strings
	 * and puts them into $this->strings
	 * 
	 * the language array is built as follows:
	 * $this->strings[field][language] = array(value, etc);
	 * 
	 */
	function loadLanguageStrings()
	{
		$query = "SELECT id, field, language, string_parsed FROM `".$this->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".strtolower(get_class($this))."' ";
		$this->_db->doQuery($query, $this->strings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array', false, array('index_key_1' => 'field', 'index_key_2' => 'language'));
		
		if (empty($this->strings))
		{
			$this->strings = array();
		}
	}
	
	/**
	 * wrapper function to get the text in the requested language
	 * 
	 */
	function getParsedText($field, $language)
	{
		if ($language == $GLOBALS['YP_DEFAULT_LANGUAGE'] || is_null($language))
		{
			if ($field == 'text_original')
			{
				return $this->text;
			} else {
				return $this->{$field};
			}
		}
		
		if (!isset($this->strings[$field]) || !isset($this->strings[$field][$language]))
		{
			if (!YP_SKIP_NO_LANGUAGE)
			{
				return $this->getParsedText($field, YP_DEFAULT_LANGUAGE);
			}
			return null;
		}
		
		return $this->strings[$field][$language]['string_parsed'];
	}
	
	/**
	 * 
	 */
	function loadFiles()
	{
		if (empty($this->id))
		{
			return;
		}
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, sysname FROM `".$this->_table['item_files']."` WHERE owner_id='".$this->id."' AND online='Y'";
		$this->_db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'file_id', 'object' => 'FileObject'));
	}
	
	/**
	 * wrapper function to get a file, and otherwise returns an empty FileObject
	 * @param $file_id:String
	 */
	function getFile($file_id)
	{
		if (isset($this->files[$file_id]))
		{
			return $this->files[$file_id];
		} else {
			return new FileObject();
		}
	}
	
	function imageSize($type, $dimensions)
	{
		$sizes = array();
		
		switch($type)
		{
			case('preview'):
				$sizes = array($this->pwidth, $this->pheight);
				break;
			case('thumbnail'):
				$sizes = array($this->twidth, $this->theight);
				break;
			case('original'):
				$dir = ORIGINAL;
				$sizes = getimagesize($dir.$this->id.'.jpg');
				break;
		}
		
		switch($dimensions)
		{
			case('width'):
				return $sizes[0];
				//break;
			case('height'):
				return $sizes[1];
				//break;
		}
	}
	
	/**
	 * returns the item name, in the current language.
	 * 
	 * @return String
	 */
	function getName()
	{
		$name = '';
		
		if (YP_MULTILINGUAL)
		{
			$language = $GLOBALS['YP_CURRENT_LANGUAGE'];
			if (isset($this->strings['name'][$language]))
			{
				$name = $this->strings['name'][$language]['string_parsed'];
			} else {
				// return name in default language
				$name = $this->name;
			}
		} else {
			$name = $this->name;
		}
		
		if (empty($name))
		{
			$name = 'item '.$this->id;
		}
		
		return $name;
	}
	
	/**
	 * returns the item text, in the current language.
	 * 
	 * @return String
	 */
	function getText()
	{
		if (YP_MULTILINGUAL)
		{
			$language = $GLOBALS['YP_CURRENT_LANGUAGE'];
			if (isset($this->strings['text_original'][$language]))
			{
				return $this->strings['text_original'][$language]['string_parsed'];
			} else {
				// return text in default language
				return $this->text;
			}
		} else {
			return $this->text;
		}
	}
	
	/**
	 * Checks if both name and text are empty in current language.
	 * 
	 * @return Boolean
	 */
	function isEmpty()
	{
		$name = $this->getName();
		if (empty($name) || $name == 'item '.$this->id)
		{
			$text = $this->getText();
			if (empty($text))
			{
				return true;
			}
		}
		
		return false;
	}
		
	/**
	 * wrapper for retrieving custom fields, if one is not set, return an empty string
	 * @return string
	 */
	function getCustomData($key)
	{
		if (!isset($this->custom_data[$key]))
		{
			return '';
		}
		
		return $this->custom_data[$key];
	}
	
	/**
	 * 
	 */
	function loadCustomData()
	{
		if (empty($this->custom_data))
		{
			$this->custom_data = array();
			return;
		}
		
		if (is_array($this->custom_data))
		{
			return;
		}
		
		$data = $this->custom_data;
		$this->custom_data = array();
			
		$fields = explode("\n", $data);
		foreach($fields as $field)
		{
			$tmp_field = array();
			list($tmp_field['key'], $tmp_field['value']) = explode(' :: ', $field);
			$this->custom_data[$tmp_field['key']] = $tmp_field['value'];
		}
	}
	
	function getLink()
	{
		if (!empty($this->link))
		{
			return $this->link;
		} else {
			return $this->id;
		}
	}
}
?>