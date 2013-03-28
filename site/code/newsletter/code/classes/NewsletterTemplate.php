<?PHP
class NewsletterTemplate
{
	var $id;
	var $name;
	var $default_title;
	var $itemimage_width;
	var $itemimage_height;	
	var $header;
	var $item;
	var $footer;
	var $online;
	var $created;
	var $modified;
	
	// runtime parameters
	var $db;
	var $sys;
	var $yp;
	
	function NewsletterTemplate($data = array())
	{
		$this->__construct($data);
	}
	
	function __construct($data = array())
	{
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
		} else {
			global $db, $system, $yourportfolio;
			
			$this->db = $db;
			$this->sys = $system;
			$this->yp = $yourportfolio;
		}
	}
	
	function init()
	{
		$this->id = 0;
	}
	
	function load()
	{
		$query = sprintf("SELECT name, default_title, itemimage_width, itemimage_height, online, created, modified FROM `%s` WHERE template_id='".$this->id."' LIMIT 1", $this->yp->_table['nl_templates']);
		$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
	}
	
	function loadDesign()
	{
		$query = sprintf("SELECT header, item, footer FROM `%s` WHERE template_id='".$this->id."' LIMIT 1", $this->yp->_table['nl_templates']);
		$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
	}
}
?>
