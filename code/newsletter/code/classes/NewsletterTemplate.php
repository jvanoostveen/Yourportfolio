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
	
	var $header_text;
	var $item_text;
	var $footer_text;
	
	var $online;
	var $created;
	var $modified;
	
	// runtime parameters
	var $db;
	var $sys;
	var $yp;
	
	var $_autosave = array();
	
	var $save_fields = array('name','online','default_title','itemimage_width','itemimage_height','header','header_text','item','item_text','footer','footer_text');
	
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
		$query = sprintf("SELECT header, item, footer, header_text, item_text, footer_text FROM `%s` WHERE template_id='".$this->id."' LIMIT 1", $this->yp->_table['nl_templates']);
		$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
	}
	
	/**
	 * Get all templates.
	 * To be used as static function.
	 * 
	 * @return Array
	 */
	function getTemplates( $show_offline = false )
	{
		global $yourportfolio;
		global $db;
		global $settings;
		
		$templates = array();
		
		$query = sprintf("SELECT template_id AS id, name, default_title, itemimage_width, itemimage_height, online, created, modified FROM `%s`", $yourportfolio->_table['nl_templates']);
		if( !$show_offline )
		{
				$query .= " WHERE online='Y'";
		}
		
		$query .= " ORDER BY name";
		
		$db->doQuery($query, $templates,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'NewsletterTemplate'));
		
		if (empty($templates))
		{
			if( $settings['debug'] == true)
			{
				trigger_error('no templates installed');
			}
			$templates = array();
		}
		
		return $templates;
	}
	
	/**
	 * Get first in line template.
	 * To be used as static function.
	 * 
	 * @return NewsletterTemplate
	 */
	function getFirstTemplate()
	{
		global $yourportfolio;
		global $db;
		global $settings;
		
		$template = null;
		
		$query = sprintf("SELECT template_id AS id, name, default_title, itemimage_width, itemimage_height, online, created, modified FROM `%s` WHERE online='Y' ORDER BY template_id ASC LIMIT 1", $yourportfolio->_table['nl_templates']);
		$db->doQuery($query, $template,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'NewsletterTemplate'));
		
		if (empty($template))
		{
			if( $settings['debug'] == true)
			{
				trigger_error('no templates installed');
			}
			
			$template = new NewsletterTemplate();
			$template->init();
		}
		
		return $template;
	}
	
	/**
	 * Save a template to database
	 */
	function save()
	{
		$type = 'insert';
		if( isset($this->id) && $this->id > 0)
		{
			$query = "UPDATE";
			$where = "WHERE `template_id` = ".$this->id;
			$type = 'update';
		} else {
			$query = "INSERT";
			$where = '';
		}
		
		$query .= sprintf(" `%s` SET ", $this->yp->_table['nl_templates']);
		
		foreach($this->save_fields as $field )
		{
			$query .= " `$field` = '".$this->$field."', ";
		}
		
		if( $type == 'insert')
		{
			$query .= "`created` = NOW(), ";
		}
		
		$query .= "`modified` = NOW(), ";
		
		// laatste komma weg
		$query = substr($query, 0, strlen($query)-2);
		
		$query .= " ".$where;
		
		// finally, execute query
		$res = '';
		$this->db->doQuery($query, $res,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $type, false);
		
		// return ID		
		if( $type == 'insert' )
		{
			return $res;
		} else {
			return $this->id;
		}
	}
}
?>
