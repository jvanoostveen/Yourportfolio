<?PHP

class TemplateController
{
	public $base = TEMPLATES;
	public $path = '';
	
	private $templates;
	private $defaultTemplate;
	
	public function __construct()
	{
		$this->templates = array();
	}
	
	public function register($template, $nodeType = NodeTemplate::ALBUM, $type = 0)
	{
		if ($type == -1)
		{
			$this->templates[$nodeType] = $template;
		} else {
			$this->templates[$nodeType.'_'.$type] = $template;
		}
	}
	
	public function registerDefault($template)
	{
		$this->defaultTemplate = $template;
	}
	
	public function findTemplate($node)
	{
		if (is_null($node) or
			(empty($this->templates[$node->root->template.'_'.$node->root->type]) && empty($this->templates[$node->root->template])))
		{
			if (!$this->defaultTemplate)
				throw new Exception('No template found for '.$node->root->template.'_'.$node->root->type.' (no template or overal default set).');
			
			return $this->defaultTemplate;
		}
		
		if (empty($this->templates[$node->root->template.'_'.$node->root->type]) && !empty($this->templates[$node->root->template]))
			return $this->templates[$node->root->template];
		
		return $this->templates[$node->root->template.'_'.$node->root->type];
	}
}