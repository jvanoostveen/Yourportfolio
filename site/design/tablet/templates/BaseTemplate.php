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
		$node = $this->node;
		$home_url = $system->base_url;
		
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
		
		if ($this->node)
		{
			$node = $this->node;
			$rootNode = $node->root;
		} else {
			$this->template = 'index';
		}
		
		if (!empty($this->template))
			require($this->template.'.php');
	}
	
	protected function postbuild()
	{
		if ($this->contentOnly)
			return;
		
		parent::postbuild();
		
		global $yourportfolio;
		global $dataprovider;
		global $canvas, $system;
		
		require('html_stop.php');
	}
}