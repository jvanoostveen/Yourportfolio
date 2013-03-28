<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Section
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Section extends Node
{
	/**
	 * vars available in database
	 */
	var $album_id;
	var $section_date;
	var $online;
	var $online_mobile;
	var $text_node;
	var $is_selection;
	var $position;
	var $name;
	var $subname;
	var $text_original;
	var $custom_data; // mixed
	var $template;
	var $type;
	
	/**
	 * run time vars
	 */
	var $album;
	var $items = array();
	var $itemCount = 0;
	var $_autostore = array(	'album_id', 'section_date', 'online', 'online_mobile', 'text_node', 'is_selection', 'position', 
								'name', 'subname', 'text_original', 'text', 'template', 'link', 'type', 'custom_data');

	/**
	 * objects needed to run
	 */
	var $_db;
	var $_table;
	var $_system;
	var $_yourportfolio;
	
	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct($data = null)
	{
		parent::__construct($data);
		
		global $db;
		
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		
		$this->_system = &$system;
		
		if (!empty($data))
		{
			$this->loadCustomData();
			$this->loadOnlineFiles();
			$this->loadOnlineItems();
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Section($data = null)
	{
		$this->__construct($data);
	}
	
	/**
	 * give object some default values
	 */
	function init()
	{
		$this->id = 0;
		$this->album_id = 0;
		//$this->created = date("Y-m-d H:i:s");
		$this->section_date = time();
		$this->online = 'N';
		$this->online_mobile = 'N';
		$this->text_node = 'N';
		$this->is_selection = 'N';
		$this->position = 0;
		$this->name = '';
		$this->text_original = '';
		$this->text = '';
		$this->type = 0;
		
		if (!is_null($this->album))
		{
			$this->template = ($this->album->template == 'news') ? 'newsitem' : 'section';
		} else {
			$this->template = 'section';
		}
	}
	
	public function load()
	{
		parent::load();
		
		$this->loadCustomData();
	}
	
	/**
	 * 
	 */
	function saveCustomData()
	{
		if (empty($this->custom_data))
		{
			$this->custom_data = null;
			return;
		}
		
		$data = '';
		foreach($this->custom_data as $field => $value)
		{
			$data .= $field.' :: '.$value."\n";
		}
		$data = substr($data, 0, -1);
		
		$this->custom_data = $data;
	}
	
	/**
	 * 
	 */
	function loadCustomData()
	{
		if (empty($this->custom_data))
		{
			$this->custom_data = array();
			return;
		}
		
		$data = $this->custom_data;
		$this->custom_data = array();
		
		$fields = explode("\n", $data);
		foreach($fields as $field)
		{
			$tmp_field = array();
			list($tmp_field['key'], $tmp_field['value']) = explode(' :: ', $field);
			$this->custom_data[$tmp_field['key']] = $tmp_field['value'];
		}
	}
	
	/**
	 * wrapper for retrieving custom fields, if one is not set, return an empty string
	 * @return string
	 */
	function getCustomData($key)
	{
		if (!isset($this->custom_data[$key]))
		{
			return '';
		}
		
		return $this->custom_data[$key];
	}
	
	/**
	 * switch online state
	 */
	function switchOnline()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['sections']."` SET online=IF(online='Y','N','Y') WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "update", false);

		// update xml
		$this->_system->setChanged();
	}
	
	/**
	 * retrieves the currently online items
	 * also filters out the type, which cannot be error (as it means the item is incomplete)
	 */
	function loadOnlineItems()
	{
		if (isset($GLOBALS['xml_filter_items']) && $GLOBALS['xml_filter_items'])
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' AND type!='error'";
			$this->_db->doQuery($query, $this->itemCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			$this->items = array();
			
			return;
		}
		
		$query = "SELECT id, position, type, text, name, link, label_type FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND online='Y' AND type!='error' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "array", false);
		
		if (empty($this->items))
			$this->items = array();
		
		$this->itemCount = count($this->items);
	}
	
	function loadItems()
	{
		$query = "SELECT id, online, position, type, IF(LENGTH(name) > 20, CONCAT(SUBSTRING(name FROM 1 FOR 20),'...'), name) AS name FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "array", false);
#		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
	}
	
	function loadLastItem($r_id)
	{
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' AND random_id='".$r_id."' LIMIT 1";
		$item_id = null;
		$this->_db->doQuery($query, $item_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		$query = "UPDATE `".$this->_table['items']."` SET random_id=NULL WHERE id='".$item_id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		return $item_id;
	}
	
	/**
	 * Updates section to set modified date to now when content inside section has changed.
	 * 
	 * @return Void
	 */
	function setModified()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['sections']."` SET modified=NOW() WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	function save($section = array(), $files = array(), $from_item = false)
	{
		// parse data to object
		
		$return_to_edit = false;
		
		if (isset($section['id']))
		{
			$this->id = (int) $section['id'];
			unset($section['id']);
		}
		
		$this->load(); // load old data
		
		$old_link = $this->link;
		
		// parse tags first, then save
		if (!empty($data['tags']))
		{
			$this->tags = $data['tags'];
			unset( $data['tags'] );
		}
		
		// overwrite old data with new values
		foreach($section as $key => $value)
		{
			$this->$key = $value;
		}
		
		$album = new Album();
		$album->id = $this->album_id;
		
		$q_start = '';
		$q_where = '';
		$q = '';
		$return = '';
		
		if ($this->id == 0)
		{
			// new
			$q_start .= "INSERT INTO ";
			$return = 'insert';
		} else if (is_numeric($this->id) && $this->id > 0)
		{
			// update
			$q_start .= "UPDATE ";
			$q_where .= " WHERE id='".$this->id."' LIMIT 1";
			$return = 'update';
		}
		$q_start .= "`".$this->_table['sections']."` SET ";
		
		
		// when album id changed:
		//	update all items to new album id
		//	update positions on old album
		//	update positions on new album (place section at end)
		if (	$this->id != 0
			&&	is_numeric($this->album_id)
			&&	$this->album_id != $this->old_album_id)
		{
			$this->album_id = intval($this->album_id);
			
			$result = null;
			$q_items = "UPDATE `".$this->_table['items']."` SET album_id = '".$this->album_id."' WHERE section_id='".$this->id."'";
			$this->_db->doQuery($q_items, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			
			// redo positions in old album
			$move_query = "UPDATE `".$this->_table['sections']."` SET position = position - 1 WHERE position > '".$this->old_position."' AND album_id='".$this->old_album_id."'";
			$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
			
			$this->position = null;
		}
		unset($this->old_album_id);
		
		// position
		if (	empty($this->position)
			&&	$this->template == 'newsitem' )
		{
			$this->position = '1';
			$this->old_position = '999999'; // make sure the older items positions are +1 instead of -1
		}
		
		if ( empty($this->position) ) // photo without position (nothing filled in, or zero)
		{
			// fetch highest position from album
			$this->position = $album->next_position();
		} else {
		
			// new position != old position
			if ($this->position != $this->old_position)
			{
				// is new position a relative postion? -3 or +5 etc
				if ( !is_numeric($this->position{0}) ) // first character is not a number
				{
					// move to new position and remap other photos
					if ($this->position{0} == '-')
					{
						// move backward
						$move = round(substr($this->position, 1));
						$this->position = $this->old_position - $move;
						if ( $this->position < 1 || $move == 0) // below one or a -0 move
						{
							$this->position = 1;
						}
					} elseif ($this->position{0} == '+') {
						// move forward
						$move = round(substr($this->position, 1));
						$this->position = $this->old_position + $move;
						if ( $this->position >= $album->next_position() || $move == 0) // beyond highest or a +0 move
						{
							$this->position = $album->next_position() - 1;
						}	
					} else {
						// unknown symbol
						trigger_error('Unknown symbol given in position', E_USER_NOTICE);
						$this->position = $album->next_position(); // send photo to back
					}
					
				} else { // first character is a number
					// act normal
					if ( $this->position > $album->next_position() ) // beyond highest
					{
						$this->position = $album->next_position();
					} else {
						$this->position = round($this->position);
					}
				}
				
				// is new postion < or > then old postion?
				if (empty($this->old_position)) // insert into this position from new upload
				{
					$move_query = "UPDATE `".$this->_table['sections']."` SET position = position + 1 WHERE position >= '".$this->position."' AND album_id='".$album->id."'";
				} else if ($this->position > $this->old_position) // moved forward
				{
					$move_query = "UPDATE `".$this->_table['sections']."` SET position = position - 1 WHERE position <= '".$this->position."' AND position > '".$this->old_position."' AND album_id='".$album->id."'";
				} else { // moved backward
					$move_query = "UPDATE `".$this->_table['sections']."` SET position = position + 1 WHERE position >= '".$this->position."' AND position < '".$this->old_position."' AND album_id='".$album->id."'";
				}
				$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
				
			} // end postion swapping
		}
		unset($this->old_position);
		
		if ($this->text_node == 'Y')
		{
			$this->template = 'section_text_node';
		} else if (empty($this->template) || $this->template == 'section_text_node') {
			$this->template = 'section';
		}
		
		$textToolkit = $this->_system->getModule('TextToolkit');
		$this->text = $textToolkit->parseText($this->text_original);
		$this->link = $textToolkit->normalize($this->name);
		
		if (empty($this->link) && YP_MULTILINGUAL)
		{
			foreach ($this->strings['name'] as $key => $value)
			{
				if (empty($value))
				{
					continue;
				}
				
				$this->link = $textToolkit->normalize($value);
				break;
			}
		}
		
		// check for duplicate link
		if (!empty($this->link))
		{
			$valid_link = false;
			$link = $this->link;
			$count = 1;
			
			do
			{
				$resource = null;
				$query = "SELECT id FROM `".$this->_table['sections']."` WHERE link='".$this->_db->filter($link)."' AND id != '".$this->_db->filter($this->id)."' AND album_id = '".$this->_db->filter($this->album_id)."'";
				if ($this->_db->doQuery($query, $resource, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false) > 0)
				{
					$count++;
					$link = $this->link.$count;
				} else {
					$this->link = $link;
					$valid_link = true;
				}
			} while (!$valid_link);
		}
		
		if (!empty($this->section_date) && !empty($this->section_time))
		{
			$this->section_date .= ' '.$this->section_time;
			list($day, $month, $year, $hour, $minutes, $seconds) = preg_split('/[ :-]+/', $this->section_date);
			$this->section_date = $year.'-'.$month.'-'.$day.' '.$hour.':'.$minutes.':'.$seconds;
			unset($year, $month, $day, $hour, $minutes, $seconds);
		} else {
			$this->section_date = date("Y-m-d H:i:s");
		}
		unset($this->section_time);
		
		if (isset($this->custom_data))
		{
			$this->saveCustomData();
		}
		
		$this->checkValues();
		foreach($this->_autostore as $key)
		{
			if ($this->$key == '' || is_null($this->$key))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->_db->filter($this->$key)."',";
			}
		}
		
		if (empty($this->id))
		{
			$q .= "created=NOW(),";
		}
		$q .= "modified=NOW()";
		
		$query = $q_start.$q.$q_where;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, TRUE);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		
		if (YP_MULTILINGUAL)
		{
			$this->saveLanguageStrings();
		}
		
		$this->saveMetadata();
		
		if (!empty($old_link) && $old_link != $this->link)
		{
			// add old link to link archives
			$query = "INSERT INTO `".$this->_table['links']."` SET link='".$this->_db->filter($old_link)."', object_id='".$this->id."', type='section'";
			$result = null;
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
		
		$album->setModified();
		
		# 2) store file uploads
		$error = $this->storeFiles($files);
		if ($error)
			$return_to_edit = true;
		
		// save file properties
		$this->saveFileProperties();
		
		// save tags
		$this->saveTags();
		
		if ($from_item)
		{
			$this->id = $result;
			return $this->id;
		}
		
		// update xml
		$this->_system->setChanged();
		
		// everything done, goto section overview or album overview when it's a newsitem
		if( $return_to_edit )
		{
			$this->_system->relocate('section.php?aid='.$this->album_id.'&sid='.$this->id."&mode=edit");
		} else {
			if ($this->template == 'newsitem')
			{
				$this->_system->relocate('album.php?aid='.$this->album_id);
			} else {
				$this->_system->relocate('section.php?aid='.$this->album_id.'&sid='.$this->id);
			}
		}
	}
	
	/**
	 * checks for values that are not allowed to be NULL
	 * and assigns the default value to them
	 *
	 */
	function checkValues()
	{
		if (is_null($this->online))
		{
			$this->online = 'N';
		}
		if (is_null($this->is_selection))
		{
			$this->is_selection = 'N';
		}
		if (is_null($this->template))
		{
			$this->template = 'section';
		}
		if (is_null($this->type) || $this->type == '')
		{
			$this->type = '0';
		}
	}
	
	/**
	 * retrieves next (highest) position in a given section
	 *
	 * @return integer
	 *
	 * @access public
	 */
	function next_position()
	{
		$position = 0;
		$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['items']."` WHERE section_id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		if (!$position)
			return 1;
		else
			return $position;
	}
	
	/**
	 * reads the section_files settings
	 */
	function parseFilesSettings()
	{
		// keep them from parsing again if this function is called again
		if (!empty($this->files_settings))
		{
			return;
		}
		
		// check for existence of overwrite ini, otherwise use default
		$settings = CODE.'settings/section_files.ini';
		if (file_exists(SETTINGS.'section_files.ini'))
		{
			$settings = SETTINGS.'section_files.ini';
		}
		
		$this->files_settings = parse_ini_file($settings, true);
		
		foreach($this->files_settings as $file_id => $settings)
		{
			if (isset($this->files_settings[$file_id]['for-type']))
			{
				$this->files_settings[$file_id]['for-type'] = explode(',', $this->files_settings[$file_id]['for-type']);
			}
			
			if (isset($this->files_settings[$file_id]['not-for-type']))
			{
				$this->files_settings[$file_id]['not-for-type'] = explode(',', $this->files_settings[$file_id]['not-for-type']);
			}
			
			if ($this->checkFileSettingsRules($this->files_settings[$file_id]))
			{
				$this->files_settings[$file_id]['id'] = $file_id;
				$this->files_settings[$file_id]['description'] = str_replace(array('{UPLOAD_MAX_SIZE}'), array(UPLOAD_MAX_SIZE), $settings['description']);
				$this->files_settings[$file_id]['actions'] = $this->parseActions($settings['actions']);
			} else {
				unset($this->files_settings[$file_id]);
			}
		}
	}
	
	/**
	 * Checks if file settings apply to this section.
	 * 
	 * @param $settings:Array
	 * @return Boolean
	 */
	function checkFileSettingsRules($settings)
	{
		if (!isset($settings['for']) && !isset($settings['not-for']))
		{
			return true;
		}
		
		if (isset($settings['not-for']))
		{
			switch ($settings['not-for'])
			{
				case ('section'):
					if (empty($settings['not-for-type']))
					{
						return false;
					} else {
						return (in_array($this->type, $settings['not-for-type']) ? false : true);
					}
					break;
				case ('album'):
				case ('text'):
				case ('news'):
				case ('contact'):
					if (is_null($this->album))
					{
						break;
					}
					
					if (empty($settings['not-for-type']))
					{
						return ($this->album->template == $settings['not-for']) ? false : true;
					} else {
						return ($this->album->template == $settings['not-for'] && in_array($this->album->type, $settings['not-for-type'])) ? false : true;
					}
					break;
			}
		}
		
		if (isset($settings['for']))
		{
			switch ($settings['for'])
			{
				case ('newsitem'):
					return ($this->template == 'newsitem') ? true : false;
				case ('album'):
				case ('text'):
				case ('news'):
				case ('contact'):
					if (is_null($this->album))
					{
						return true;
					}
				
					if (empty($settings['for-type']))
					{
						return ($this->album->template == $settings['for']) ? true : false;
					} else {
						return ($this->album->template == $settings['for'] && in_array($this->album->type, $settings['for-type'])) ? true : false;
					}
			}
		}
		
		return true;
	}
	
	/**
	 * deletes an entire section and containing items
	 * relocate to album when done
	 *
	 * @uses $_db, $_table
	 *
	 * @access public
	 */
	function destroy()
	{
		// select photos of photobook
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE section_id='".$this->id."'";
		$this->_db->doQuery($query, $this->items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		//	- delete files of photos (if there are any)
		if ($this->items)
		{
			foreach($this->items as $loop_item)
			{
				$item = new Item();
				$item->id = $loop_item['id'];
				$item->destroy();
				unset($item);
			}
		}
		
		$this->destroyFiles();
		
		// delete photobook
		$query = "DELETE FROM `".$this->_table['sections']."` WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);

		// delete old links assigned to this object
		$query = "DELETE FROM `".$this->_table['links']."` WHERE object_id='".$this->id."' AND type='section'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$this->removeMetadata();

		// update xml
		$this->_system->setChanged();
		$this->_system->forceUpdate();
		
		// everything done, goto album overview
#		$this->_system->relocate(');
	}
	
	function saveChildPositions($ids_list)
	{
		global $db;
		
		$ids = explode(',', $ids_list);
		$position = 1;
		foreach ($ids as $node_id)
		{
			$result = null;
			$query = "UPDATE `".$db->_table['items']."` SET position='".$position."' WHERE id='".$node_id."' LIMIT 1";
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			
			$position++;
		}
		
		$this->_system->setChanged();
	}
}
?>