<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Section
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Section
{
	/**
	 * vars available in database
	 */
	var $id;
	var $section_date;
	var $online;
	var $position;
	var $name;
	var $subname;
	var $text_original;
	var $text;
	var $source_preview;
	var $template;
	var $type;
	
	var $modified;
	
	// multilanguage
	var $strings = array();
	
	// file related
	var $files = array();
	
	/**
	 * item count
	 * @var integer
	 */
	var $itemCount = 0;
	
	/**
	 * run time vars
	 */
	var $items = array();
	
	/**
	 * objects needed to run
	 */
	var $_db;
	var $_table;
	var $_system;

	/**
	 * constructor (PHP5)
	 *
	 * locates and assigns run time objects
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
			
			if (YP_MULTILINGUAL)
			{
				$this->loadLanguageStrings();
			}
#			$this->countItems();
			
			if (isset($GLOBALS['GENERATING_XML']) && $GLOBALS['GENERATING_XML'] == true)
			{
				$this->loadFiles();
				$this->loadItems();
			}
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Section($data = null)
	{
		$this->__construct($data);
	}
	
	/**
	 * retrieves and returns the id of the item on the first position
	 * @return integer
	 */
	function getFirstItem()
	{
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC LIMIT 1";
		$item_id = null;
		$this->_db->doQuery($query, $item_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		if (!$item_id)
		{
			return null;
		}
		return $item_id;
	}

	/**
	 * retrieves and returns the id of the item on last position
	 * @return integer
	 */
	function getLastItem()
	{
		$item = 0;
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) DESC, id DESC LIMIT 1";
		$this->_db->doQuery($query, $item, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		return $item;
	}
	
	/**
	 * loads data of this section
	 * also calls function to count for online items
	 * 
	 * @return Boolean
	 */
	function load()
	{
		$query = "SELECT UNIX_TIMESTAMP(section_date) AS section_date, online, position, name, subname, text, section_date, template, link, type, UNIX_TIMESTAMP(modified) AS modified FROM `".$this->_table['sections']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			return false;
		}
		
		if (YP_MULTILINGUAL)
		{
			$this->loadLanguageStrings();
		}
		
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
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, sysname FROM `".$this->_table['section_files']."` WHERE owner_id='".$this->id."'";
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
	
	function searchItem($item_q)
	{
		if (empty($item_q))
		{
			return null;
		}
		
		// search by name
		$item = null;
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE link='".$this->_db->filter($item_q)."' AND section_id='".$this->id."' AND online='Y' LIMIT 1";
		if (!$this->_db->doQuery($query, $item, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Item')))
		{
			// is a number, search by id
			if (is_numeric($item_q))
			{
				$query = "SELECT id FROM `".$this->_table['items']."` WHERE id='".$this->_db->filter($item_q)."' AND section_id='".$this->id."' AND online='Y' LIMIT 1";
				if (!$this->_db->doQuery($query, $item, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Item')))
				{
					return null;
				}
			} else {
				// couldn't find this link, search for it in the links archive
				$query = "SELECT object_id FROM `".$this->_table['links']."` WHERE link='".$this->_db->filter($item_q)."' AND type='item' ORDER BY id DESC LIMIT 1";
				$item_id = null;
				$this->_db->doQuery($query, $item_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
				
				if ($item_id !== false)
				{
					$query = "SELECT id FROM `".$this->_table['items']."` WHERE id='".$this->_db->filter($item_id)."' LIMIT 1";
					$item = null;
					if ($this->_db->doQuery($query, $item, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Item')))
					{
						return $item;
					}
				}
				
				return null;
			}
		}
		
		return $item;
	}
	
	/**
	 * returns the section name, in the current language.
	 * 
	 * @return String
	 */
	function getName()
	{
		if (YP_MULTILINGUAL)
		{
			$language = $GLOBALS['YP_CURRENT_LANGUAGE'];
			if (isset($this->strings['name'][$language]))
			{
				return $this->strings['name'][$language]['string_parsed'];
			} else {
				// return name in default language
				return $this->name;
			}
		} else {
			return $this->name;
		}
	}
	
	/**
	 * returns the section text, in the current language.
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
		if (empty($name))
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
	 * count the online items
	 * @assigns $itemCount
	 */
	function countItems()
	{
		$query = "SELECT COUNT(*) FROM `".$this->_table['items']."` WHERE section_id = ".$this->id." AND online='Y'";
		$this->_db->doQuery($query, $this->itemCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	}
	
	/**
	 * retrieves the currently online items
	 *
	 */
	function loadItems()
	{
		if (isset($GLOBALS['xml_filter_items']) && $GLOBALS['xml_filter_items'])
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' AND type!='error'";
			$this->_db->doQuery($query, $this->itemCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			$this->items = array();
			
			return;
		}
		
		$query = "SELECT id, type, text, name, custom_data, link FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
		
		if (empty($this->items))
			$this->items = array();
		
		$this->itemCount = count($this->items);
	}
	
	/**
	 * Retrieves items ordered by modification date instead of position.
	 * 
	 */
	function loadItemsRSS()
	{
		$query = "SELECT id, type, name, text, link, custom_data, UNIX_TIMESTAMP(modified) AS modified FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' ORDER BY modified DESC, id DESC";
		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
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