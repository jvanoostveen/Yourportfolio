<?PHP

/**
 * Node.
 */
class NodeTransferObject
{
	const ALBUM = 'album';
	const SECTION = 'section';
	const ITEM = 'item';
	//public $_explicitType = 'yourportfolio.data.provider.amf.NodeTransferObject';
	
	public $id;
	public $link;
	public $type;
	public $template;
	
	public $title; // array based on languages
	public $text; // array based on languages
	
	public $date;
	
	public $nodeType;
	
	public $root;
	public $parent;
	
	public $children;
	
	public $tags;
	public $files;
	
	public $fields;
	
	public function __construct($data = null)
	{
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
			
			if (YP_MULTILINGUAL)
			{
				$this->loadLanguageStrings();
			}
			
			$this->loadFiles();
			
			if (!empty($this->fields))
				$this->parseFields();
			
			global $yourportfolio;
			
			if ($yourportfolio->settings['tags'])
				$this->loadTags();
		}
	}
	
	public function load()
	{
		global $db;
		
		switch ($this->nodeType)
		{
			case self::ALBUM:
				$query = "SELECT `name` AS `title`, `text`, `template`, `type`, `link`, UNIX_TIMESTAMP(`modified`) AS `date` 
						  FROM `".$db->_table['albums']."` WHERE `id`='".$this->id."'";
				break;
			case self::SECTION:
				$query = "SELECT `name` AS `title`, `text`, `custom_data` AS `fields`, `template`, `type`, `link`, UNIX_TIMESTAMP(`section_date`) AS `date` 
						  FROM `".$db->_table['sections']."` WHERE `id`='".$this->id."'";
				break;
			case self::ITEM:
				$query = "SELECT `name` AS `title`, `text`, `custom_data` AS `fields`, `link`, `label_type` AS `type` 
						  FROM `".$db->_table['items']."` WHERE `id`='".$this->id."'";
				break;
		}
		$db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
		
		if (empty($this->link))
			$this->link = $this->id;
		
		if (YP_MULTILINGUAL)
		{
			$this->loadLanguageStrings();
		}
		
		if ($this->nodeType == self::ITEM)
		{
			$this->parseFields();
		}
	}
	
	public function loadFiles()
	{
		$this->files = array();
		
		if (empty($this->nodeType))
			return;
		
		global $db;
		
		$query = "SELECT `id`, `file_id` AS `key`, `name`, `width`, `height`, CONCAT(`basepath`, `sysname`) AS `path` FROM `".$db->_table[$this->nodeType.'_files']."` WHERE `owner_id`='".$this->id."' AND `online`='Y'";
		$db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'FileNodeTransferObject'));
		
		if ($this->files === false)
			$this->files = array();
	}
	
	public function loadTags()
	{
		global $db;
		
		$query = "SELECT `tag_id` FROM `".$db->_table[$this->nodeType.'_tags']."` WHERE `".$this->nodeType."_id`='".$db->filter($this->id)."'";
		$db->doQuery($query, $this->tags, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		
		if ($child->tags === false)
			$child->tags = array();
	}
	
	public function loadChildren()
	{
		global $db;
		
		switch ($this->nodeType)
		{
			case self::ALBUM:
				$query = "SELECT `id`, `name` AS `title`, `text`, `template`, `type`, `link`, UNIX_TIMESTAMP(`section_date`) AS `date`, `custom_data` AS `fields`, '".self::SECTION."' AS `nodeType` 
						  FROM `".$db->_table['sections']."`
						  WHERE `album_id`='".$this->id."' AND `online`='Y' ORDER BY IF(`position` > 0, `position`, 999) ASC, id ASC";
				break;
			case self::SECTION:
				$query = "SELECT `id`, `position`, `type`, `name` AS `title`, `text`, `link`, `label_type` AS `type`, `custom_data` AS `fields`, '".self::ITEM."' AS `nodeType`
						  FROM `".$db->_table['items']."` 
						  WHERE section_id='".$this->id."' AND online='Y' AND type!='error' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
				break;
			default:
				throw new Exception("nodeType is empty");
				break;
		}
		
		$db->doQuery($query, $this->children, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'NodeTransferObject'));
		
		if ($this->children === false)
			$this->children = array();
	}
	
	private function loadLanguageStrings()
	{
		global $db;
		
		$strings = array();
		$query = "SELECT id, field, language, string, string_parsed FROM `".$db->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".$this->nodeType."' ";
		$db->doQuery($query, $strings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array', false, array('index_key_1' => 'field', 'index_key_2' => 'language'));
		
		$this->title = array($GLOBALS['YP_DEFAULT_LANGUAGE'] => $this->title);
		$this->text = array($GLOBALS['YP_DEFAULT_LANGUAGE'] => $this->text);
		
		if (empty($strings))
		{
			return;
		}
		
		foreach ($strings as $field => $languages)
		{
			foreach ($languages as $language => $data)
			{
				switch ($field)
				{
					case 'name':
						$this->title[$language] = $data['string_parsed'];
						break;
					case 'text_original':
						$this->text[$language] = $data['string_parsed'];
						break;
				}
			}
		}
	}
	
	private function parseFields()
	{
		if (empty($this->fields))
		{
			$this->fields = array();
			return;
		}
		
		if (is_array($this->fields))
		{
			return;
		}
		
		$data = $this->fields;
		$this->fields = array();
			
		$fields = explode("\n", $data);
		foreach($fields as $field)
		{
			$tmp_field = array();
			list($tmp_field['key'], $tmp_field['value']) = explode(' :: ', $field);
			if (empty($tmp_field['value']))
				continue;
			
			$this->fields[$tmp_field['key']] = $tmp_field['value'];
		}
	}
}
