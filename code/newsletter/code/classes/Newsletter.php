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
	
	var $datesent;
	var $created;
	var $modified;
	
	var $items;
	var $groups;
	
	// runtime parameters
	var $db;
	var $sys;
	var $yp;
	
	var $_autosave = array('template_id', 'subject', 'pagetitle', 'sender', 'edition', 'introduction', 'status', 'datesent');
	
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
	
	function init()
	{
		global $settings;
		
		$this->id = 0;
		$this->items		= array();
		$this->groups		= array();
		
		$this->template		= NewsletterTemplate::getFirstTemplate();
		$this->template_id	= $this->template->id;
		
		$this->subject		= $this->template->default_title;
		$this->pagetitle	= $this->template->default_title;
		$this->sender		= $settings['from_name'];
		$this->edition		= '';
		
		$this->status		= 'draft';
	}
	
	function loadAll()
	{
		$this->load();
		$this->loadItems();
		$this->loadGroups();
	}
	
	function load()
	{
		if (empty($this->id))
		{
			$this->init();
			return;
		}
		
		$query = sprintf( "SELECT subject, pagetitle, template_id, sender, edition, introduction, datesent, created, modified, status FROM `%s` WHERE `letter_id`='".$this->id."'", $this->yp->_table['nl_letters'] );
		if (!$this->db->doQuery($query, $this,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false))
		{
//			$this->init();
		} else {
//			$this->template = new NewsletterTemplate();
//			$this->template->id = $this->template_id;
//			$this->template->load();
		}
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
	
	function loadGroups()
	{
		$query = sprintf("SELECT `group_id` AS id FROM `%s` WHERE `letter_id`='".$this->id."'", $this->yp->_table['nl_recipients']);
		$this->db->doQuery($query, $this->groups,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false );
		
		if ($this->groups == false)
		{
			$this->groups = array();
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
	
	function save($data = array())
	{
		// first, set id
		if (!isset($data['id']))
		{
			trigger_error('$id is missing', E_USER_ERROR);
		}
		$this->id = (int) $data['id'];
		unset($data['id']);
		
		// load old data if is existing one
		$this->load();
		
		// overwrite data with new data
		foreach($data as $key => $value)
		{
			if (!is_null($value))
			{
				$this->{$key} = $value;
			}
		}
		
		$new_entry = false;
		
		$q_start = '';
		$q_where = '';
		$q = '';
		$return = '';
		
		if ($this->id === 0)
		{
			// new
			$new_entry = true;
			
			$q_start .= "INSERT INTO ";
			$return = 'insert';
		} else {
			// update
			$q_start .= "UPDATE ";
			$q_where .= " WHERE letter_id='".$this->id."' LIMIT 1";
			$return = 'update';
		}
		$q_start .= "`".$this->yp->_table['nl_letters']."` SET ";
		
		foreach($this->_autosave as $key)
		{
			if ($this->{$key} == '' || is_null($this->{$key}))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->db->filter($this->{$key})."',";
			}
		}
		
		if (empty($this->id))
		{
			$q .= "created=NOW(),";
		}
		$q .= "modified=NOW()";
		
		$query = $q_start.$q.$q_where;
		$result = 0;
		$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		unset($q_start, $q, $q_where, $return);
	}
	
	function copy()
	{
		// first, set id
		if (empty($this->id))
		{
			trigger_error('$id is missing', E_USER_ERROR);
		}
		
		// load data
		$this->loadAll();
		
		$duplicate_id = $this->id;
		
		$this->id = 0;
		$this->status = 'draft';
		$this->datesent = '0000-00-00 00:00:00';
		
		$q_start = '';
		$q_where = '';
		$q = '';
		$return = '';
		
		$q_start .= "INSERT INTO ";
		$return = 'insert';
		$q_start .= "`".$this->yp->_table['nl_letters']."` SET ";
		
		foreach($this->_autosave as $key)
		{
			if ($this->{$key} == '' || is_null($this->{$key}))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->db->filter($this->{$key})."',";
			}
		}
		
		$q .= "created=NOW(),";
		$q .= "modified=NOW()";
		
		$query = $q_start.$q.$q_where;
		$result = 0;
		$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
		$this->id = $result;
		
		unset($q_start, $q, $q_where, $return);
		
		// copy content items
		foreach ($this->items as $item)
		{
			$item->newsletter_id = $this->id;
			$item->copy();
		}
		
		$this->saveGroups($this->groups);
	}
	
	function changeStatus($status)
	{
		$result = null;
		$query = "UPDATE `".$this->yp->_table['nl_letters']."` SET status='".$status."', datesent=NOW() WHERE letter_id='".$this->id."'";
		$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	function delete()
	{
		// remove items
		// remove images of items
		
		$this->loadItems();
		foreach($this->items as $item)
		{
			$item->delete();
		}
		
		$nil = '';
		
//		$query = sprintf("DELETE FROM %s WHERE newsletter_id = '".$this->id."'", $this->yp->_table['nl_letter_items']);
//		$this->db->doQuery( $query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$query = sprintf("DELETE FROM `%s` WHERE letter_id = '".$this->id."'", $this->yp->_table['nl_recipients']);
		$this->db->doQuery( $query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$query = sprintf("DELETE FROM `%s` WHERE letter_id = '".$this->id."' LIMIT 1", $this->yp->_table['nl_letters']);
		$this->db->doQuery( $query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		// remove possible statistics for this newsletter
		$query = sprintf("DELETE FROM `%s` WHERE letter_id = '".$this->id."' LIMIT 1", $this->yp->_table['nl_letter_stats']);
		$this->db->doQuery( $query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	function saveGroups($groups)
	{
		// remove current groups
		$query = sprintf("DELETE FROM `%s` WHERE `letter_id` = '".$this->id."'", $this->yp->_table['nl_recipients']);
		$result = null;
		$this->db->doQuery($query, $result,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		
		// insert new group settings
		if( count($groups) > 0 )
		{
			$group_query = sprintf("INSERT INTO `%s` VALUES", $this->yp->_table['nl_recipients']);
			foreach( $groups as $group )
			{
				$group_query .= " ('".$this->id."', '".$group."'),";
			}
			
			$group_query = substr($group_query, 0, -1);
			$this->db->doQuery($group_query, $result,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
		}
	}
	
	function getNextOrder()
	{
		$order = 0;
		$query = sprintf("SELECT MAX(`order`) + 1 AS `order` FROM `%s` WHERE newsletter_id='".$this->id."'", $this->yp->_table['nl_letter_items']);
		$this->db->doQuery($query, $order,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
		
		if (is_null($order))
		{
			$order = 1;
		}
		
		return $order;
	}
	
	/**
	 * STATIC
	 */
	function handle($data = array())
	{
		if (empty($data) || empty($data['action']))
		{
			return;
		}
		
		global $system;
		
		switch ($data['action'])
		{
			case ('delete'):
				$newsletter = new Newsletter();
				$newsletter->id = $data['id'];
				$newsletter->delete();
				
				$redirect = $data['redirect'];
				break;
			case ('save'):
				$newsletter = new Newsletter();
				$newsletter->save($data['newsletter']);
				
				$redirect = 'nid='.$newsletter->id.'&task='.$data['task'];
				break;
			case ('duplicate'):
				$newsletter = new Newsletter();
				$newsletter->id = $data['id'];
				$newsletter->copy();
				
				$redirect = 'nid='.$newsletter->id.'&task=template';
				break;
			case ('save_groups'):
				$newsletter = new Newsletter();
				$newsletter->id = $data['newsletter']['id'];
				
				if (!isset($data['newsletter']['groups']))
				{
					$data['newsletter']['groups'] = array();
				}
				$newsletter->saveGroups($data['newsletter']['groups']);
				
				$redirect = 'nid='.$newsletter->id.'&task='.$data['task'];
				break;
			case ('ignore'):
				$redirect = 'nid='.$data['newsletter']['id'].'&task='.$data['task'];
				break;
		}
		
		$system->relocate('newsletter_write.php?'.$redirect);
	}
}
?>
