<?PHP
class Template
{
	protected $node;
	
	public function __construct()
	{
		
	}
	
	public function setNode($node)
	{
		$this->node = $node;
	}
	
	protected function prebuild()
	{
	}
	
	protected function build()
	{
	}
	
	protected function postbuild()
	{
	}
	
	public function html()
	{
		ob_start();
		
		$this->prebuild();
		$this->build();
		$this->postbuild();
		
		return ob_get_clean();
	}
}
