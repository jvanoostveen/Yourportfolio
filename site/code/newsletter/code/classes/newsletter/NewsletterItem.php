<?PHP

require_once(dirname(__FILE__).'/ItemImage.php');

class NewsletterItem
{
	var $id;
	var $newsletter_id;
	var $title;
	var $content;
	var $link;
	
	var $order;
	var $created;
	var $modified;
	
	var $image;
	
	// runtime parameters
	var $db;
	var $sys;
	var $yp;
	
	var $_autosave = array('newsletter_id', 'title', 'content', 'link', 'order');
	
	function NewsletterItem($data = array())
	{
		$this->__construct($data);
	}

	function __construct($data = array())
	{
		global $db, $system, $yourportfolio;
		
		$this->db = $db;
		$this->sys = $system;
		$this->yp = $yourportfolio;
		
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
		}
		
		$this->loadImage();
	}
	
	function init()
	{
		$this->id = 0;
		
		$this->title = '';
		$this->content = '';
		$this->link = '';
		
		$this->order = 0;
	}
	
	function load()
	{
		if (empty($this->id))
		{
			$this->init();
			return;
		}
		
		$query = sprintf( "SELECT newsletter_id, title, content, link, `order`, created, modified FROM `%s` WHERE `item_id`='".$this->id."'", $this->yp->_table['nl_letter_items'] );
		if (!$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false))
		{
			$this->init();
		}
		
		$this->loadImage();
	}
	
	function loadImage()
	{
		if (empty($this->id))
		{
			$this->image = new ItemImage();
			return;
		}
		
		$query = "SELECT id, name, size, extension, type, width, height, path, sysname FROM `".$this->db->_table['nl_item_files']."` WHERE owner_id='".$this->id."' LIMIT 1";
		if (!$this->db->doQuery($query, $this->image,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'ItemImage')))
		{
			$this->image = new ItemImage();
		}
	}
	
	function getImage()
	{
		if (is_null($this->image))
		{
			$this->image = new ItemImage();
		}
		
		return $this->image;
	}
}
?>
