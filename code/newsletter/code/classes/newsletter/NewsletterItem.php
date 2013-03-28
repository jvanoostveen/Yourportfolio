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
	
	function save($data = array())
	{
		global $settings;
		
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
		
		if ($this->id === 0 && empty($this->title) && empty($this->content))
		{
			// don't save this item, it's bogus
			return -1;
		}
		
		// set order
		if (empty($this->order))
		{
			// get highest order of newsletter
			$newsletter = new Newsletter();
			$newsletter->id = $this->newsletter_id;
			
			$this->order = $newsletter->getNextOrder();
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
			$q_where .= " WHERE item_id='".$this->id."' LIMIT 1";
			$return = 'update';
		}
		$q_start .= "`".$this->yp->_table['nl_letter_items']."` SET ";
		
		// check link field for http:// or https:// prefix
		if(!empty( $this->link ) && substr( $this->link, 0, strlen('http://') ) != 'http://' && substr( $this->link, 0, strlen('https://') ) != 'https://')
		{
			$this->link = 'http://' . $this->link;
		}
		
		foreach($this->_autosave as $key)
		{
			if ($this->{$key} == '' || is_null($this->{$key}))
			{
				$q .= "`".$key."`=NULL,";
			} else {
				$q .= "`".$key."`='".$this->db->filter($this->{$key})."',";
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
		
		// save files
		$files = $this->sys->postedFiles();
		
		if (count($files) > 0)
		{
			foreach($files as $file_id => $file_data)
			{
				$image = new ItemImage($file_data);
				
				$image->sysname = $this->newsletter_id.'_'.$this->id.'.'.$image->extension;
				$image->path = 'newsletter/content/';
				
				$imageToolkit = $this->sys->getModule('ImageToolkit');
				if( !rename($image->tmp_name, $image->path.$image->sysname) )
				{
					trigger_error("Move failed");
					
					// do not save file entry to the database.
					continue;
				}
				
				chmod($image->path.$image->sysname, 0666);
				
				$sizes = $imageToolkit->getImageSizes($image->path.$image->sysname);
				$image->width	= $sizes[0];
				$image->height	= $sizes[1];
				$image->size	= filesize($image->path.$image->sysname);
				
				$query = "INSERT INTO `".$this->yp->_table['nl_item_files']."` SET ".
							"owner_id='".$this->id."', ".
							"created=NOW(), ".
							"name='".$this->db->filter($image->name)."', ".
							"size='".$this->db->filter($image->size)."', ".
							"extension='".$this->db->filter($image->extension)."', ".
							"type='".$this->db->filter($image->type)."', ".
							"width='".$image->width."', ".
							"height='".$image->height."', ".
							"path='".$image->path."', ".
							"sysname='".$image->sysname."'";
				$file->id = null;
				$this->db->doQuery($query, $file->id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			}
		}
		
		return $this->id;
	}
	
	function copy()
	{
		$duplicate_id = $this->id;
		$this->id = 0;
		$this->order = 0;
		
		// set order
		if (empty($this->order))
		{
			// get highest order of newsletter
			$newsletter = new Newsletter();
			$newsletter->id = $this->newsletter_id;
			
			$this->order = $newsletter->getNextOrder();
		}
		
		$q_start = '';
		$q_where = '';
		$q = '';
		$return = '';
		
		$q_start .= "INSERT INTO ";
		$return = 'insert';
		$q_start .= "`".$this->yp->_table['nl_letter_items']."` SET ";
		
		foreach($this->_autosave as $key)
		{
			if ($this->{$key} == '' || is_null($this->{$key}))
			{
				$q .= "`".$key."`=NULL,";
			} else {
				$q .= "`".$key."`='".$this->db->filter($this->{$key})."',";
			}
		}
		
		$q .= "created=NOW(),";
		$q .= "modified=NOW()";
		
		$query = $q_start.$q.$q_where;
		$result = 0;
		$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
		$this->id = $result;
		unset($q_start, $q, $q_where, $return);
		
		// save files
		if (!$this->image->isEmpty())
		{
			$this->image->owner_id = $this->id;
			$this->image->copy($this->newsletter_id);
		}
	}
	
	function delete()
	{
		// remove images
		
		$this->loadImage();
		$this->image->clear_cache();
		$this->image->delete();
		
		$nil = '';
		
		$query = sprintf("DELETE FROM `%s` WHERE item_id = '".$this->id."' LIMIT 1", $this->yp->_table['nl_letter_items']);
		$this->db->doQuery( $query, $nil, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	function move($direction)
	{
		$this->load();
		
		$old_order = $this->order;
		$this->order = $this->order + $direction;
		
		// is new postion < or > then old postion?
		if ($this->order > $old_order)
		{
			$move_query = "UPDATE `".$this->yp->_table['nl_letter_items']."` SET `order` = `order` - 1 WHERE `order` <= '".$this->order."' AND `order` > '".$old_order."' AND newsletter_id='".$this->newsletter_id."'";
		} else { // moved backward
			$move_query = "UPDATE `".$this->yp->_table['nl_letter_items']."` SET `order` = `order` + 1 WHERE `order` >= '".$this->order."' AND `order` < '".$old_order."' AND newsletter_id='".$this->newsletter_id."'";
		}
		$result = null;
		$this->db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		$query = "UPDATE `".$this->yp->_table['nl_letter_items']."` SET `order`='".$this->order."' WHERE item_id='".$this->id."'";
		$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	function getImage()
	{
		if (is_null($this->image))
		{
			$this->image = new ItemImage();
		}
		
		return $this->image;
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
		
		$redirect = '';
		
		switch ($data['action'])
		{
			case ('delete'):
				$item = new NewsletterItem();
				$item->id = $data['id'];
				$item->delete();
				
				$redirect = $data['redirect'];
				break;
			case ('delete_file'):
				
//				$item = new NewsletterItem();
//				$item->id = $data['id'];
				
				$image = new ItemImage();
				$image->id = $data['file_id'];
				$image->owner_id = $data['id'];
				
				$image->delete();
				
				$redirect = $data['redirect'];
				break;
			case ('save'):
				$item = new NewsletterItem();
				$item->save($data['item']);
				
				$redirect = 'nid='.$item->newsletter_id.'&task='.$data['task'];
				break;
			case ('move'):
				
				$item = new NewsletterItem();
				$item->id = $data['id'];
				$item->move( (int) $data['direction']);
				
				$redirect = $data['redirect'];
				break;
		}
		
		$system->relocate('newsletter_write.php?'.$redirect);
	}
}
?>
