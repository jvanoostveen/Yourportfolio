<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Album
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Album
{
	/**
	 * vars available from database
	 * @var integer $id
	 * @var enum $online
	 * @var integer $position
	 * @var string $name
	 * @var string $text_original
	 * @var string $text
	 * @var string $template
	 * @var integer $type
	 * @var array $parameters
	 */
	var $id;
	var $online;
	var $position;
	var $locked;
	var $restricted;
	var $user_id;
	var $name;
	var $text_original;
	var $text;
	var $template;
	var $type;
	var $link;
	var $parameters;
	var $modified;
	
	// multilanguage
	var $strings = array();
	
	// file related
	var $files = array();
	
	/**
	 * run time vars
	 * @var array $sections
	 */
	var $sections;
	var $sectionCount = null;
	
	/**
	 * objects needed to run this component
	 * @var object $_db
	 * @var array $_table
	 * @var object $_system
	 */
	var $_db;
	var $_table = array();
	var $_system;
	var $_yourportfolio;
	
	/**
	 * constructor (PHP5)
	 *
	 * @param array $data	can contain data needed to create an album without fetching from database itself
	 */
	function __construct($data = null)
	{
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		$this->_system = &$system;
		
		global $yourportfolio;
		$this->_yourportfolio = &$yourportfolio;
		
		if (!empty($data))
		{
			foreach($data as $key => $value)
			{
				$this->{$key} = $value;
			}
			
			if ($this->restricted == 'Y')
			{
				$this->loadSections();
			}
			
			if (YP_MULTILINGUAL)
			{
				$this->loadLanguageStrings();
			}
			#$this->countOnlineSections();
			
			if (isset($GLOBALS['GENERATING_XML']) && $GLOBALS['GENERATING_XML'] == true)
			{
				$this->loadOnlineFiles();
			}
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Album($data = null)
	{
		$this->__construct($data);
	}
	
	function getFirstSection()
	{
		$query = "SELECT id FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$section_id = null;
		$this->_db->doQuery($query, $section_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		if (!$section_id)
		{
			return null;
		}
		
		return $section_id;
	}
	
	/**
	 * loads album
	 * when no album available, initialize album
	 * load album parameters
	 *
	 * @return Boolean
	 */
	function load()
	{
		$query = "SELECT online, position, restricted, user_id, name, text, template, type, link, UNIX_TIMESTAMP(modified) AS modified FROM `".$this->_table['albums']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			return false;
		}
		
		if (YP_MULTILINGUAL)
		{
			$this->loadLanguageStrings();
		}
		
		$this->loadFiles();
		
		$this->parameters = array();
		$query = "SELECT id, parameter, value FROM `".$this->_table['parameters']."` WHERE album_id='".$this->id."'";
		$parameters = array();
		$this->_db->doQuery($query, $parameters, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (!empty($parameters))
		{
			foreach($parameters as $parameter)
			{
				$this->parameters[$parameter['parameter']] = $parameter;
			}
		}
		
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
		$query = "SELECT id, field, language, string, string_parsed FROM `".$this->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".strtolower(get_class($this))."' ";
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
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, sysname FROM `".$this->_table['album_files']."` WHERE owner_id='".$this->id."'";
		$this->_db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'file_id', 'object' => 'FileObject'));
	}
	
	/**
	 * 
	 */
	function loadOnlineFiles()
	{
		if (empty($this->id))
		{
			return;
		}
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, path, sysname FROM `".$this->_table['album_files']."` WHERE owner_id='".$this->id."' AND online='Y'";
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
	
	function loadAll()
	{
		$this->load();
		$this->loadSections();
	}

	function loadSections()
	{
		$query = "SELECT id, UNIX_TIMESTAMP(section_date) AS section_date, online, is_selection, name, text, template, link, type FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Section'));
		
		if ($this->sections === false)
			$this->sections = array();
		
		$this->sectionCount = count($this->sections);
	}
	
	function countOnlineSections()
	{
		if (is_null($this->sectionCount))
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y'";
			$this->_db->doQuery($query, $this->sectionCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		}
		return $this->sectionCount;
	}
	
	function searchSection($section_q)
	{
		if (empty($section_q))
		{
			return null;
		}
		
		// search by name
		$section = null;
		$query = "SELECT id FROM `".$this->_table['sections']."` WHERE link='".$this->_db->filter($section_q)."' AND album_id='".$this->id."' AND online='Y' LIMIT 1";
		if (!$this->_db->doQuery($query, $section, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Section')))
		{
			// is a number, search by id
			if (is_numeric($section_q))
			{
				$query = "SELECT id FROM `".$this->_table['sections']."` WHERE id='".$this->_db->filter($section_q)."' AND album_id='".$this->id."' AND online='Y' LIMIT 1";
				if (!$this->_db->doQuery($query, $section, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Section')))
				{
					return null;
				}
			} else {
				// couldn't find this link, search for it in the links archive
				$query = "SELECT object_id FROM `".$this->_table['links']."` WHERE link='".$this->_db->filter($section_q)."' AND type='section' ORDER BY id DESC LIMIT 1";
				$section_id = null;
				$this->_db->doQuery($query, $section_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
				
				if ($section_id !== false)
				{
					$query = "SELECT id FROM `".$this->_table['sections']."` WHERE id='".$this->_db->filter($section_id)."' LIMIT 1";
					$section = null;
					if ($this->_db->doQuery($query, $section, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Section')))
					{
						return $section;
					}
				}
				
				return null;
			}
		}
		
		return $section;
	}
	
	/**
	 * Load items in all sections of album for use in RSS.
	 * So order it by modification date.
	 * 
	 * @return Array
	 */
	function loadItemsRSS()
	{
		$items = array();
		$query = "SELECT id, section_id, type, name, text, link, custom_data, UNIX_TIMESTAMP(modified) AS modified FROM `".$this->_table['items']."` WHERE album_id='".$this->id."' AND online='Y' AND section_id IN (SELECT id FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y') ORDER BY modified DESC, id DESC";
		$this->_db->doQuery($query, $items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
		
		if (empty($items))
		{
			$items = array();
		}
		
		return $items;
	}
	
	/**
	 * returns the album name, in the current language.
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
				if (empty($this->name) && isset($this->strings['name']))
				{
					$first_language = array_shift(array_keys($this->strings['name']));
					return $this->strings['name'][$first_language]['string_parsed'];
				} else {
					return $this->name;
				}
			}
		} else {
			return $this->name;
		}
	}
	
	/**
	 * returns the album text, in the current language.
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
				return $this->text;
			}
		} else {
			return $this->text;
		}
	}
	
	/**
	 * retrieve parameter
	 *
	 * @param string $param		name of parameter to fetch
	 * @param string $key		name of field to fetch value from
	 *
	 * @return string
	 * @access public
	 */
	function getParameter($param, $key = 'value')
	{
		if (is_array($this->parameters))
		{
			if (isset($this->parameters[$param]))
			{
				return $this->parameters[$param][$key];
			} else {
				return '';
			}
		}
	}
	
	/**
	 * Get link value.
	 * 
	 * @return String
	 */
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