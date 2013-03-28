<?PHP

class BaseTemplate extends Template
{
	protected $contentOnly = false;
	protected $registry;
	
	protected $template = 'no_template';
	
	public function __construct()
	{
		parent::__construct();
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
		{
			$this->contentOnly = true;
		}
	}
	
	protected function prebuild()
	{
		if ($this->contentOnly)
			return;
		
		parent::prebuild();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		$title = $yourportfolio->getTitle();
		
		require('html_start.php');
	}
	
	protected function build()
	{
		parent::build();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		$node;
		$rootNode;
		$title = $yourportfolio->getTitle();
		
		if ($this->node)
		{
			$node = $this->node;
			$rootNode = $node->root;
		} else {
			$this->template = 'index';
		}
		
		require($this->template.'.php');
	}
	
	protected function postbuild()
	{
		if ($this->contentOnly)
			return;
		
		parent::postbuild();
		
		global $yourportfolio;
		if ($yourportfolio->site['google_analytics']['enabled'])
		{
			include_once('google_analytics_helper.php');
			$GA_ACCOUNT = $yourportfolio->site['google_analytics']['account'];
		}
		
		require('html_stop.php');
	}
}