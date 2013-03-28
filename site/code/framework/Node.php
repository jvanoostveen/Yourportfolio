<?PHP
class Node
{
	public $id; // uint
	public $link; // String
	
	public $type = 0; // uint
	public $template; // String
	protected $title; // Dictionary
	protected $subtitle; // Dictionary
	protected $text; // Dictionary
	public $date; // Date
	public $section_date; // Date
	
	public $nodeType; // String
	
	public $root; // Node
	public $parent; // Node
	public $childCount;
	protected $children; // Array
	public $files; // Dictionary
	public $fields; // Dictionary
	
	// metadata
	public $metadata = array();
	
	protected $_loaded = false;
	protected $_childrenLoaded = false;
	
	public function __construct(array $data = null) //, Node &$parent = null)
	{
		$this->root = $this;
		$this->parent = null;
		
		$this->childCount = 0;
		$this->children = array();
		$this->files = array();
		
		$this->title = array();
		$this->text = array();
		
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->$key = $value;
			}
			
			$this->setLink($this->link);
			
			if ($this->nodeType == NodeTemplate::ALBUM)
				$this->loadChildren();
			else
				$this->countChildren();
			
			$this->load();
		}
	}
	
	public function load()
	{
		if ($this->_loaded)
			return;
		
		$this->_loaded = true;
		
		$this->loadFiles();
		if ($this->nodeType == NodeType::ITEM)
			$this->parseFields();
		
		if (YP_MULTILINGUAL)
			$this->loadLanguageStrings();
		
		$this->loadMetadata();
	}
	
	protected function loadLanguageStrings()
	{
		global $db;
		
		$query = "SELECT id, field, language, string, string_parsed FROM `".$db->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".$this->nodeType."'";
		$db->doQuery($query, $strings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array', false, array('index_key_1' => 'field', 'index_key_2' => 'language'));
		
		if (empty($strings))
			$strings = array();
		
		foreach ($strings as $field => $data)
		{
			foreach ($data as $language => $value)
			{
				switch ($field)
				{
					case 'name':
						$this->addTitle($value['string_parsed'], $language);
						break;
					case 'text_original':
						$this->addText($value['string_parsed'], $language);
						break;
				}
			}
		}
	}
	
	protected function loadMetadata()
	{
		global $db;
		$query = "SELECT id, field, language, value FROM `".$db->_table['metadata']."` WHERE owner_id='".$this->id."' AND owner_type='".$this->nodeType."'";
		$db->doQuery($query, $this->metadata, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array_value', false, array('index_key_1' => 'field', 'index_key_2' => 'language', 'value' => 'value'));
		
		if (empty($this->metadata))
			$this->metadata = array();
	}
	
	protected function loadFiles()
	{
		if (empty($this->id))
			return;
		
		global $db, $system;
		
		switch ($this->nodeType)
		{
			case NodeType::ALBUM:
				$table = $db->_table['album_files'];
				break;
			case NodeType::SECTION:
				$table = $db->_table['section_files'];
				break;
			case NodeType::ITEM:
				$table = $db->_table['item_files'];
				break;
		}
		
		$query = "SELECT `id`, `file_id` AS `key`, `name`, `extension`, `width`, `height`, 
				  CONCAT(`basepath`, `sysname`) AS `path`,
				  '".$this->nodeType."' AS `owner_type` 
				  FROM `".$table."` 
				  WHERE `owner_id`='".$this->id."' AND `online`='Y'";
		$db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'key', 'object' => 'FileNode'));
		
		if (!$this->files)
			$this->files = array();
	}
	
	protected function parseFields()
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
	
	public function countChildren()
	{
		if ($this->_childrenLoaded)
			return;
		
		if ($this->nodeType == NodeType::ITEM)
			return;
		
		global $db;
		
		switch ($this->nodeType)
		{
			case NodeType::ALBUM:
				if (Browser::isMobile() || Browser::isTablet())
				{
					$query = "SELECT COUNT(*) 
							  FROM `".$db->_table['sections']."` 
							  WHERE `album_id`='".$this->id."' AND `online`='Y' AND `online_mobile`='Y'";
				} else {
					$query = "SELECT COUNT(*) 
							  FROM `".$db->_table['sections']."` 
							  WHERE `album_id`='".$this->id."' AND `online`='Y'";
				}
				break;
			case NodeType::SECTION:
				$query = "SELECT COUNT(*) 
						  FROM `".$db->_table['items']."` 
						  WHERE `section_id`='".$this->id."' AND `online`='Y'";
				break;
		}
		
		$db->doQuery($query, $this->childCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	}
	
	public function getChildren()
	{
		if (!$this->_childrenLoaded)
			$this->loadChildren();
		
		return $this->children;
	}
	
	public function getFirstChild()
	{
		if (!$this->_childrenLoaded)
			$this->loadChildren();
		
		if (count($this->children) > 0)
			return $this->children[0];
		else
			return null;
	}
	
	protected function loadChildren()
	{
		if ($this->_childrenLoaded)
			return;
		
		$this->_childrenLoaded = true;
		
		global $db;
		
		switch ($this->nodeType)
		{
			case NodeType::ALBUM:
				if (Browser::isMobile() || Browser::isTablet())
				{
					$query = "SELECT `id`, `link`, `type`, `name` AS `title`, `subname` AS `subtitle`, `text`, `template`, UNIX_TIMESTAMP(`section_date`) AS `date`, '".NodeType::SECTION."' AS `nodeType` 
							  FROM `".$db->_table['sections']."` 
							  WHERE `album_id`='".$this->id."' AND `online`='Y' AND `online_mobile`='Y' 
							  ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
				} else {
					$query = "SELECT `id`, `link`, `type`, `name` AS `title`, `subname` AS `subtitle`, `text`, `template`, UNIX_TIMESTAMP(`section_date`) AS `date`, '".NodeType::SECTION."' AS `nodeType` 
							  FROM `".$db->_table['sections']."` 
							  WHERE `album_id`='".$this->id."' AND `online`='Y' 
							  ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
				}
				break;
			case NodeType::SECTION:
				// no mobile/tablet setting for items
				$query = "SELECT `id`, `link`, `label_type` AS `type`, `name` AS `title`, `subname` AS `subtitle`, `text`, `custom_data` AS `fields`, '".NodeType::ITEM."' AS `nodeType` 
						  FROM `".$db->_table['items']."` 
						  WHERE `section_id`='".$this->id."' AND `online`='Y' 
						  ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
				break;
		}
		
		$db->doQuery($query, $this->children, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Node'));
		if (!$this->children)
			$this->children = array();
		
		$this->childCount = count($this->children);
		
		$this->setParent($this);
	}
	
	public function removeChildren()
	{
		$this->children = array();
		$this->childCount = 0;
		$this->_childrenLoaded = true;
	}
	
	public function setParent(&$parent)
	{
		if ($parent != $this)
		{
			$this->parent = $parent;
			$this->root = $parent->root;
		}
		
		foreach($this->children as $child)
		{
			$child->setParent($this);
		}
	}
	
	public function addChild(Node $node)
	{
		$this->_childrenLoaded = true;
		
		$this->children[] = $node;
		$node->setParent($this);
		
		$this->childCount = count($this->children);
	}
	
	public function addFile(FileObject $file)
	{
		$this->files[$file->file_id] = $file;
	}
	
	public function getFile($key)
	{
		if (array_key_exists($key, $this->files))
			return $this->files[$key];
		
		return null;
	}
	
	public function setCustomField($key, $value)
	{
		$this->fields[$key] = $value;
	}
	
	public function getCustomField($key)
	{
		if (!isset($this->fields[$key]))
			return "";
		
		return $this->fields[$key];
	}
	
	public function hasChildren()
	{
		return ($this->childCount > 0 ? true : false);
	}
	
	public function setLink($link)
	{
		$this->link = $link;
		
		if (empty($this->link))
			$this->link = $this->id;
	}
	
	public function isAvailableInCurrentLanguage()
	{
		return $this->isAvailableInLanguage($GLOBALS['YP_CURRENT_LANGUAGE']);
	}
	
	public function isAvailableInLanguage($language)
	{
		if (is_array($this->title))
		{
			return (!empty($this->title[$language]));
		}
		
		return true;
	}
	
	public function getTitle()
	{
		if (is_array($this->title))
		{
			if (!empty($this->title[$GLOBALS['YP_CURRENT_LANGUAGE']]))
				return $this->title[$GLOBALS['YP_CURRENT_LANGUAGE']];
			
			return $this->title[$GLOBALS['YP_DEFAULT_LANGUAGE']];
		}
		
		return $this->title;
	}
	
	public function setTitle($value)
	{
		$this->title = $value;
	}
	
	public function addTitle($value, $language)
	{
		if (!is_array($this->title))
		{
			$old = $this->title;
			$this->title = array();
			$this->addTitle($old, $GLOBALS['YP_DEFAULT_LANGUAGE']);
		}
		
		$this->title[$language] = $value;
	}
	
	public function getSubtitle()
	{
		if (is_array($this->subtitle))
		{
			if (!empty($this->subtitle[$GLOBALS['YP_CURRENT_LANGUAGE']]))
				return $this->subtitle[$GLOBALS['YP_CURRENT_LANGUAGE']];
			
			return $this->subtitle[$GLOBALS['YP_DEFAULT_LANGUAGE']];
		}
		
		return $this->subtitle;
	}
	
	public function setSubtitle($value)
	{
		if (!is_array($this->subtitle))
		{
			$old = $this->subtitle;
			$this->subtitle = array();
			$this->addSubtitle($old, $GLOBALS['YP_DEFAULT_LANGUAGE']);
		}
		
		$this->subtitle = $value;
	}
	
	public function addSubtitle($value, $language)
	{
		$this->subtitle[$language] = $value;
	}
	
	public function getText()
	{
		if (is_array($this->text))
		{
			if (!empty($this->text[$GLOBALS['YP_CURRENT_LANGUAGE']]))
				return $this->text[$GLOBALS['YP_CURRENT_LANGUAGE']];
			
			return $this->text[$GLOBALS['YP_DEFAULT_LANGUAGE']];
		}
		
		return $this->text;
	}
	
	public function addText($value, $language)
	{
		if (!is_array($this->text))
		{
			$old = $this->text;
			$this->text = array();
			$this->addText($old, $GLOBALS['YP_DEFAULT_LANGUAGE']);
		}
		
		$this->text[$language] = $value;
	}
	
	function getMetadata($key, $language = null)
	{
		if (!YP_MULTILINGUAL)
			$language = 'default';
		else if (!$language)
			$language = $GLOBALS['YP_CURRENT_LANGUAGE'];
		
		if (!isset($this->metadata[$key]))
		{
			return '';
		}
		
		if (!isset($this->metadata[$key][$language]))
		{
			if ($language == $GLOBALS['YP_DEFAULT_LANGUAGE'])
				return '';
			else
				return $this->getMetadata($key, $GLOBALS['YP_DEFAULT_LANGUAGE']);
		}
		
		return $this->metadata[$key][$language];
	}
	
	public function nodeUrl()
	{
		$url = $this->link.'/';
		$n = $this;
		while ($n = $n->parent)
		{
			$url = $n->link.'/'.$url;
		}
		
		if (YP_MULTILINGUAL)
			$url = $GLOBALS['YP_CURRENT_LANGUAGE'].'/'.$url;
		
		return $url;
	}
	
	public function url()
	{
		global $system;
		
		return $system->base_url.$this->nodeUrl();
	}
	
	public function parentUrl()
	{
		if (!$this->parent)
		{
			global $system;
			
			$url = '';
			if (YP_MULTILINGUAL)
				$url = $GLOBALS['YP_CURRENT_LANGUAGE'].'/';
			
			return $system->base_url.$url;
		}
		
		return $this->parent->url();
	}
	
	public function getPrevious()
	{
		if (!$this->parent)
			return false;
		
		$node = false;
		$index = array_search($this, $this->parent->children);
		if (isset($this->parent->children[$index - 1]))
			$node = $this->parent->children[$index - 1];
		
		return $node;
	}
	
	public function getNext()
	{
		if (!$this->parent)
			return false;
		
		$node = false;
		$index = array_search($this, $this->parent->children);
		if (isset($this->parent->children[$index + 1]))
			$node = $this->parent->children[$index + 1];
		
		return $node;
	}
}