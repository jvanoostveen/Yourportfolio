<?PHP
class DataProvider
{
	private $specialNodes;
	
	private $node;
	private $root;
	
	public $current_url;
	
	public $nodes;
	public $rootNodes;
	
	public function __construct()
	{
		$this->loadSiteStructure();
	}
	
	public function getNodes()
	{
		return $this->nodes;
	}
	
	public function getCurrentURL()
	{
		return $this->current_url;
	}
	
	public function findByTemplate($template)
	{
		foreach ($this->nodes as $node)
		{
			if ($node->template == $template)
				return $node;
		}
		
		return null;
	}
	
	public function findByType($type)
	{
		foreach ($this->nodes as $node)
		{
			if ($node->type == $type)
				return $node;
		}
		
		foreach ($this->specialNodes as $node)
		{
			if ($node->type == $type)
				return $node;
		}
		
		return null;
	}
	
	private function loadSiteStructure()
	{
		global $db, $yourportfolio;
		
		$this->nodes = array();
		$this->rootNodes = array();
		$this->specialNodes = array();
		
		if (Browser::isMobile() || Browser::isTablet())
		{
			$query = "SELECT `id`, `link`, `type`, `name` AS `title`, `text`, `template`, '".NodeTemplate::ALBUM."' AS `nodeType` 
					  FROM `".$db->_table['albums']."` a 
					  WHERE `online`='Y' AND `online_mobile`='Y' AND `restricted`='N' 
					  ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
		} else {
			$query = "SELECT `id`, `link`, `type`, `name` AS `title`, `text`, `template`, '".NodeTemplate::ALBUM."' AS `nodeType` 
					  FROM `".$db->_table['albums']."` a 
					  WHERE `online`='Y' AND `restricted`='N' 
					  ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
		}
		$db->doQuery($query, $this->rootNodes, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Node'));
		
		if (!$this->rootNodes)
			$this->rootNodes = array();
		
		$this->nodes = $this->rootNodes;
		
		if ($yourportfolio->site['frontend']['filter_bracket_album'])
		{
			foreach($this->nodes as $key => $node)
			{
				$title = $node->getTitle();
				if ($title{0} == '[' && $title{strlen($title) - 1} == ']')
				{
					$this->specialNodes[] = $node;
					unset($this->nodes[$key]);
				}
			}
		}
	}
	
	public function currentNode()
	{
		if ($this->node)
			return $this->node;
		
		// extract url
		$path = isset($_GET['q']) ? explode('/', $_GET['q']) : array();
		if (isset($_SESSION['deeplink']))
		{
			$path = explode('/', $_SESSION['deeplink']);
			unset($_SESSION['deeplink']);
		}
		
		if (empty($path[count($path) - 1]))
			array_pop($path);
		
		$this->current_url = join('/', $path);
		if (!empty($this->current_url))
			$this->current_url .= '/';
		
		if (count($path) == 0)
			return null;
		
		if (YP_MULTILINGUAL)
		{
			if (in_array($path[0], array_keys($GLOBALS['YP_LANGUAGES'])))
			{
				$GLOBALS['YP_CURRENT_LANGUAGE'] = array_shift($path);
			}
		}
		
		global $yourportfolio;
		$yourportfolio->setLocale();
		
		if (count($path) == 0)
			return null;
		
		$rootPath = array_shift($path);
		foreach ($this->nodes as $rootNode)
		{
			if ($rootNode->link == $rootPath)
			{
				$this->root = $rootNode;
				break;
			}
		}
		
		// No node found on path, check all root nodes to be sure.
		// Node might be moved with NodeMap. 
		if (!$this->root)
		{
			foreach ($this->rootNodes as $rootNode)
			{
				// Node found, add parents to path for redirection.
				if ($rootNode->link == $rootPath)
				{
					$node = $rootNode;
					$this->root = $node->root;
					
					while ($node->parent)
					{
						array_unshift($path, $node->link);
						$node = $node->parent;
					}
					
					$node = null;
					break;
				}
			}
		}
		
		// handle rest of url
		$node = $this->root;
		if (!$node)
			return null;
		
		while ($part = array_shift($path))
		{
			if ($node->hasChildren())
			{
				foreach ($node->getChildren() as $childNode)
				{
					if ($childNode->link == $part)
					{
						$node = $childNode;
						continue 2;
					}
				}
			}
			
			$routeNode = Routes::check($node, $part);
			if (!is_null($routeNode))
			{
				$node = $routeNode;
				continue;
			}
			
			// this break will break out of the while loop when no child node is matched
			// it's useless to check the children for matching any other parts of the url.
			break;
		}
		
		$this->node = $node;
		
		return $this->node;
	}
}