<?PHP

require_once(dirname(__FILE__).'/vo/NodeTransferObject.php');
require_once(dirname(__FILE__).'/vo/FileNodeTransferObject.php');

/**
 * Yourportfolio AMFPHP service.
 */
class YourportfolioService
{
	protected $cache = false;
	
	function __construct()
	{
		// TODO: implement method table
		
		global $yourportfolio;
		
		$cache = SETTINGS.$yourportfolio->settings_cache.'service/';
		if (file_exists($cache) && is_dir($cache) && is_writable($cache))
			$this->cache = $cache;
	}
	
	protected function loadCache($key)
	{
		if ($this->cache === false)
			return false;
		
		if (DEBUG_AMFPHP)
			return false;
		
		if (!file_exists($this->cache.$key.'.data'))
			return false;
		
		$data = file_get_contents($this->cache.$key.'.data');
		$data = unserialize($data);
		
		return $data;
	}
	
	protected function saveCache($key, $data)
	{
		if ($this->cache === false)
			return false;
		
		if (!file_exists($this->cache))
			return;
		
		$data = serialize($data);
		file_put_contents($this->cache.$key.'.data', $data, LOCK_EX);
	}
	
	/**
	 * @desc Retrieve certain site specific settings as title and id.
	 * @access remote
	 * @returns object
	 */
	public function getSiteSettings()
	{
		$cache = $this->loadCache(__CLASS__.'_'.__FUNCTION__);
		if ($cache !== false)
			return $cache;
		
		global $yourportfolio;
		$yourportfolio->preferencesLoad();
		
		$settings = array();
		$settings['siteid'] = $yourportfolio->user_id;
		$settings['title'] = $yourportfolio->getTitle();
		$settings['clientlogin'] = $yourportfolio->settings['restricted_albums'];
		
		$this->saveCache(__CLASS__.'_'.__FUNCTION__, $settings);
		
		return $settings;
	}
	
	public function fetchRootNodes()
	{
		global $db, $yourportfolio;
		
		$nodes = array();
		$query = "SELECT `id`, `name` AS `title`, `template`, `type`, `link`, 'album' AS `nodeType` 
				  FROM `".$db->_table['albums']."` 
				  WHERE `online`='Y' AND `restricted`='N' ORDER BY IF(`position` > 0, `position`, 999) ASC, `id` ASC";
		$db->doQuery($query, $nodes, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'NodeTransferObject'));
		
		if (!$nodes)
		{
			return array();
		}
		
		$yourportfolio->loadSettings();
		if ($yourportfolio->site['frontend']['filter_bracket_album'])
		{
			foreach($nodes as $key => $node)
			{
				$title = $node->title;
				if (is_array($title))
				{
					$first_language = array_shift(array_keys($title));
					$title = $title[$first_language];
				}
				
				if ($title{0} == '[' && $title{strlen($title) - 1} == ']')
				{
					unset($nodes[$key]);
				}
			}
		}
		
		return $nodes;
	}
	
	public function loadChildNodes($parent)
	{
		$cache = $this->loadCache(__CLASS__.'_'.__FUNCTION__.'_'.$parent['nodeType'].'_'.$parent['id']);
		if ($cache !== false)
			return $cache;
		
		$parentNode = new NodeTransferObject();
		$parentNode->id = $parent['id'];
		$parentNode->nodeType = $parent['nodeType'];
		
		$parentNode->loadChildren();
		
		$this->saveCache(__CLASS__.'_'.__FUNCTION__.'_'.$parent['nodeType'].'_'.$parent['id'], $parentNode->children);
		
		return $parentNode->children;
	}
	
	public function loadNode($id, $type, $dataset)
	{
		$node = new NodeTransferObject();
		$node->id = $id;
		$node->nodeType = $type;
		$node->load();
		
		return $node;
	}
}
