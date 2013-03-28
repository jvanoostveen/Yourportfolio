<?PHP

require_once(dirname(__FILE__).'/newsletter/NewsletterItem.php');

class Newsletter
{
	var $id;
	
	var $template;
	var $template_id;
	
	var $subject;
	var $pagetitle;
	var $sender;
	var $edition;
	var $introduction;
	
	var $status;
	
	var $datesend;
	var $created;
	var $modified;
	
	var $items;
	var $groups;
	
	// runtime parameters
	var $db;
	var $sys;
	var $yp;
	
	function Newsletter()
	{
		$this->__construct();
	}
	
	function __construct()
	{
		global $db, $system, $yourportfolio;
		
		$this->db = $db;
		$this->sys = $system;
		$this->yp = $yourportfolio;
	}
	
	function load()
	{
		if (empty($this->id))
		{
			return false;
		}
		
		$query = sprintf( "SELECT subject, pagetitle, template_id, sender, edition, `introduction` FROM `%s` WHERE `letter_id`='".$this->id."'", $this->yp->_table['nl_letters'] );
		if (!$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false))
		{
			return false;
		}
		
		return true;
	}
	
	function loadItems()
	{
		$query = sprintf("SELECT item_id AS id, newsletter_id, title, content, link, `order`, created, modified FROM `%s` WHERE newsletter_id='".$this->id."' ORDER BY `order` ASC", $this->yp->_table['nl_letter_items']);
		$this->db->doQuery($query, $this->items,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'NewsletterItem'));
		
		if ($this->items == false)
		{
			$this->items = array();
		}
	}
	
	function getTemplate()
	{
		if (is_null($this->template))
		{
			// create template
			$this->template = new NewsletterTemplate();
			$this->template->id = $this->template_id;
		}
		
		return $this->template;
	}
}
?>
