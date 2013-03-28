<?PHP
/**
 * Project:		yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Item class
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Item extends Node
{
	/**
	 * vars available from database
	 */
	var $album_id;
	var $section_id;
	var $online;
	var $text_node;
	var $position;
	var $type;
	var $name;
	var $subname;
	var $text_original;
	var $custom_data; // mixed
	var $label_type;
	
	var $album;
	var $section;
	
	/**
	 * runtime vars
	 */
	var $_autostore = array('album_id', 'section_id', 'online', 'text_node', 'position', 
							'name', 'subname', 'text_original', 'text',
							'custom_data', 'link', 'label_type');
	
	/**
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;
	var $_system;

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
			$this->load();
			
			$files = array();
			$this->loadOnlineFiles();
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Item($data = null)
	{
		$this->__construct($data);
	}
	
	public function load()
	{
		parent::load();
		
		$this->loadCustomData();
	}
	
	/**
	 * give object some default values
	 */
	function init()
	{
		$this->id = 0;
#		$this->section_id = 0;
#		$this->album_id = 0;
		$this->online = 'Y';
		$this->text_node = 'N';
		$this->position = '';
		$this->label_type = 0;
		$this->files = array();
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
	 * Copies entire item to the given album/section combination.
	 *
	 * @param $album_section:String
	 * @return void
	 */
	function copyTo($album_section)
	{
		$this->load();
		
		if (!empty($album_section))
		{
			list($this->album_id, $this->section_id) = explode('__', $album_section);
			$this->album_id		= intval($this->album_id);
			$this->section_id	= intval($this->section_id);
		} else {
			// if not set, copy to current album/section (default)
		}
		
		$this->id = 0;
		
		$album = new Album();
		$album->id = $this->album_id;
		
		$section = new Section();
		$section->id = $this->section_id;
		
		$this->position = $section->next_position();
		
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
				$query = "SELECT id FROM `".$this->_table['items']."` WHERE link='".$this->_db->filter($link)."' AND id != '".$this->_db->filter($this->id)."' AND section_id = '".$this->_db->filter($this->section_id)."'";
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
		
		if (isset($this->custom_data))
		{
			$this->saveCustomData();
		}
		
		$q_start = "INSERT INTO `".$this->_table['items']."` SET ";
		$q_where = "";
		$q = "";
		$return = 'insert';
		
		foreach($this->_autostore as $key)
		{
			if ($this->$key == '' || is_null($this->$key))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->_db->filter($this->$key)."',";
			}
		}
		$q = substr($q, 0, -1);
		
		$is_new = true;
		
		
		$query = $q_start.$q.$q_where;
		
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
		$this->id = $result;
		
		if (YP_MULTILINGUAL)
		{
			// fill language strings array as it would have been posted
			// that way the regular saveLanguagesStrings can be called.
			$l_strings = array();
			
			foreach ($this->strings as $field => $language)
			{
				foreach ($language as $lang_key => $values)
				{
					if (empty($values['string']))
					{
						continue;
					}
					
					if (!isset($l_strings[$field]))
					{
						$l_strings[$field] = array();
					}
					
					$l_strings[$field][$lang_key] = $values['string'];
				}
			}			
			
			$this->strings = $l_strings;
			
			$this->saveLanguageStrings();
		}
		
		// metadata
		$this->saveMetadata();
		
		# 2) store file uploads
		$files = array();
		$this->files_properties = array();
		
		if (!empty($this->files))
		{
			foreach ($this->files as $key => $file)
			{
				$files[$key] = array('name'		=> $file->name,
									'type'		=> $file->type,
									'tmp_name'	=> $file->path.$file->sysname,
									'size'		=> $file->size,
									'extension'	=> $file->extension
									);
				$this->files_properties[$key]['online']		= $file->online;
				$this->files_properties[$key]['online_old']	= '';
				$this->files_properties[$key]['created']		= $file->created;
			}
		}
		
		$this->clean_posted_files = false;
		$this->storeFiles($files);
		
		// save file properties
		$this->saveFileProperties();
		
		// all new data is saved and available, update last information based upon current data
		$this->checkIntegrity();
		
		// update xml
		$this->_system->setChanged();
		
		#$relocate = false;
		// everything done, goto photobook overview
		$this->_system->relocate('section.php?aid='.$album->id.'&sid='.$section->id);
	}
	
	/**
	 * 
	 */
	function save($data, $files, $relocate = true)
	{
		global $yourportfolio;
		
		// parse data to object
		
		$return_to_edit = false;
		
		if (isset($data['id']))
		{
			$this->id = (int) $data['id'];
			unset($data['id']);
		}
		
		$this->load(); // load old data
		
		$old_link = $this->link;
		
		// overwrite old data with new values
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
		
		# 1) store the page
		$q_start = "";
		$q_where = "";
		$q = "";
		$return = "";
		
		if ($this->id == 0)
		{
			// new
			$is_new = true;
			
			$q_start .= "INSERT INTO ";
			$return = 'insert';
		} else if (is_numeric($this->id) && $this->id > 0)
		{
			$this->id = (int) $this->id;
			
			// update
			$is_new = false;
			
			$q_start .= "UPDATE ";
			$q_where .= " WHERE id='".$this->id."'";
			$return = 'update';
		}
		$q_start .= "`".$this->_table['items']."` SET ";
		
		// conditions
		if (isset($this->album_id__section_id))
		{
			list($new_album_id, $new_section_id) = explode('__', $this->album_id__section_id);
			
			$this->album_id = intval($new_album_id);
			$this->section_id = intval($new_section_id);
		} else {
			if ( !isset($this->album_id) || !isset($this->section_id) )
			{
				trigger_error('album_id / section_id data missing', E_USER_ERROR);
			}
		}
		
		$album = new Album();
		$album->id = $this->album_id;
		
		$section = new Section();
		$section->id = $this->section_id;
		
		// when album / section id changed:
		//	update positions on old section
		//	update positions on new section (place section at end)
		if (	$this->id != 0
			&&	$this->section_id != $this->old_section_id)
		{
			$result = null;
			// redo positions in old section
			$move_query = "UPDATE `".$this->_table['items']."` SET position = position - 1 WHERE position > '".$this->old_position."' AND album_id='".$this->old_album_id."' AND section_id='".$this->old_section_id."'";
			$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
			
			$this->position = null;
			$this->old_position = null;
			if ($yourportfolio->settings['moving_sets_position_to_one'])
			{
				$this->position = "1";
				$this->old_position = "1";
				$move_query = "UPDATE `".$this->_table['items']."` SET `position`=`position` + 1 WHERE `album_id`='".$this->album_id."' AND `section_id`='".$this->section_id."'";
				$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
			}
		}
		unset($this->album_id__section_id, $this->old_album_id, $this->old_section_id);

		
		// new photobook
		if ($section->id == 0)
		{
			if (empty($this->new_section))
			{
				$this->new_section = 'naamloze sectie';
			}
			// insert new photobook
			$section->album_id = $album->id;
			$section->online = 'Y';
			$section->name = $this->new_section;
			$section->template = 'section';
			
			$this->section_id = $section->save(array(), array(), true);
		}
		unset($this->new_section);
		
		// position
		
		/*
			positioning needs some improvements:
			- if last item is set to empty, it's new value will be last + 1, so there will be a hole before the last one
			- positioning storage is prone to errors, maybe a recalc for every login or different method of sorting
		 */
		
		$this->position = trim($this->position);
		
		if ( empty($this->position) ) // photo without position (nothing filled in, or zero)
		{
			// fetch highest position from album
			$this->position = $section->next_position();
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
							$this->position = 1;

					} elseif ($this->position{0} == '+') {
						// move forward
						$move = round(substr($this->position, 1));
						$this->position = $this->old_position + $move;
						if ( $this->position >= $section->next_position() || $move == 0) // beyond highest or a +0 move
							$this->position = $section->next_position() - 1;
							
					} else {
						// unknown symbol
						trigger_error('Unknown symbol given in position', E_USER_NOTICE);
						$this->position = $section->next_position(); // send photo to back
					}
					
				} else { // first character is a number
					// act normal
					
					if ( $this->position >= $section->next_position() ) // beyond highest
						$this->position = $section->next_position() - 1;
					else
						$this->position = round($this->position);
				}
				
				// is new position < or > then old postion?
				if (empty($this->old_position)) // insert into this position from new upload
				{
					$move_query = "UPDATE `".$this->_table['items']."` SET position = position + 1 WHERE position >= '".$this->position."' AND album_id='".$album->id."' AND section_id='".$section->id."'";
				} else if ($this->position > $this->old_position) // moved forward
				{
					$move_query = "UPDATE `".$this->_table['items']."` SET position = position - 1 WHERE position <= '".$this->position."' AND position > '".$this->old_position."' AND album_id='".$album->id."' AND section_id='".$section->id."'";
				} else { // moved backward
					$move_query = "UPDATE `".$this->_table['items']."` SET position = position + 1 WHERE position >= '".$this->position."' AND position < '".$this->old_position."' AND album_id='".$album->id."' AND section_id='".$section->id."'";
				}
				$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
				
			} // end postion swapping
		}
		
		// double check position
		if (empty($this->position))
		{
			$this->position = 1;
		}
		
		unset($this->old_position);
		// end conditions
		
		$textToolkit = $this->_system->getModule('TextToolkit');
		$this->text = $textToolkit->parseText($this->text_original);
		$this->link = $textToolkit->normalize($this->name);
		
		if (empty($this->link) && YP_MULTILINGUAL && !empty($this->strings))
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
				$query = "SELECT id FROM `".$this->_table['items']."` WHERE link='".$this->_db->filter($link)."' AND id != '".$this->_db->filter($this->id)."' AND section_id = '".$this->_db->filter($this->section_id)."'";
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
		
		if (isset($this->custom_data))
		{
			$this->saveCustomData();
		}
		
		//$exclude = array('id', 'use_full', 'type', 'source_thumb', 'twidth', 'theight', 'source_preview', 'pwidth', 'pheight', 'source_file', 'fwidth', 'fheight', 'source_gsm');
		$this->checkValues();
		
		if (isset($this->random_id))
		{
			$this->_autostore[] = 'random_id';
		}
		
		foreach($this->_autostore as $key)
		{
			if (!is_numeric($this->$key) && ($this->$key == '' || is_null($this->$key)))
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
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
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
			$query = "INSERT INTO `".$this->_table['links']."` SET link='".$this->_db->filter($old_link)."', object_id='".$this->id."', type='item'";
			$result = null;
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
		
		$album->setModified();
		$section->setModified();
		
		# 2) store file uploads
		$error = $this->storeFiles($files, true);
		if ($error)
			$return_to_edit = true;
		
		// save file properties
		$this->saveFileProperties();
		
		// save tags
		$this->saveTags();
		
		// all new data is saved and available, update last information based upon current data
		$this->checkIntegrity();
		
		// update xml
		$this->_system->setChanged();
		
		if( $return_to_edit )
		{
			$this->_system->relocate('item.php?aid='.$album->id.'&sid='.$section->id.'&iid='.$this->id);
		} else if ($relocate) {
			// everything done, goto photobook overview
			$this->_system->relocate('section.php?aid='.$album->id.'&sid='.$section->id.'#item-'.$this->id);
		}
	}
	
	/**
	 * 
	 */
	function checkIntegrity()
	{
		if ($this->text_node == 'Y' && $this->type != 'error')
		{
			return;
		}
		
		$newtype = 'image';
		
		if (empty($this->files_settings))
		{
			$this->parseFilesSettings();
		}
		
		// load all data and files
		$this->load();
		
		// check if all required files are present and set the type of the item
		foreach($this->files_settings as $file_id => $settings)
		{
			if ($settings['required'] == true)
			{
				if (!isset($this->files[$file_id]))
				{
#					trigger_error("required file is missing");
					$newtype = 'error';
					break;
				}
			}
			
			if (isset($this->files[$file_id]))
			{
				if ($settings['type'] != 'download')
				{
					$newtype = $settings['type'];
				}
				// more checks?
			}
		}
		
		if ($this->text_node == 'Y')
		{
			$newtype = 'image';
		}
		
		// update type status
		if ($newtype != $this->type)
		{
			$query = "UPDATE `".$this->_table['items']."` SET type='".$newtype."' WHERE id='".$this->id."'";
			$result = null;
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		}
	}
		
	/**
	 * reads the item_files settings
	 */
	function parseFilesSettings()
	{
		// keep them from parsing again if this function is called again
		if (!empty($this->files_settings))
		{
			return;
		}
		
		// check for existence of overwrite ini, otherwise use default
		$settings = CODE.'settings/item_files.ini';
		if (file_exists(SETTINGS.'item_files.ini'))
		{
			$settings = SETTINGS.'item_files.ini';
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
	 * Checks if file settings apply to this item.
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
					if (is_null($this->section))
					{
						break;
					}
					
					if (empty($settings['not-for-type']))
					{
						return false;
					} else {
						$this->section->load();
						return (in_array($this->section->type, $settings['not-for-type']) ? false : true);
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
				case ('section'):
					if (is_null($this->section))
					{
						return true;
					}
					
					if (empty($settings['for-type']))
					{
						return true;
					} else {
						$this->section->load();
						return (in_array($this->section->type, $settings['for-type']) ? true : false);
					}
					break;
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
	 * checks for values that are not allowed to be NULL
	 * and assigns the default value to them
	 *
	 */
	function checkValues()
	{
		if (is_null($this->online))
			$this->online = 'Y';
	}
	
	/**
	 * switch online status of item
	 *
	 * @uses $_db
	 * @uses _xml_photo_update()
	 *
	 * @access public
	 */
	function switchOnline()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['items']."` SET online=IF(online='Y','N','Y') WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "update", false);

		// update xml
		$this->_system->setChanged();
	}
	
	/**
	 * 
	 */
	function destroy()
	{
		global $yourportfolio;
		
		// update positions for all greater then position of the to be deleted item in the same section
		
		// retrieve settings for the to be deleted item
		$query = "SELECT section_id, position FROM `".$this->_table['items']."` WHERE id='".$this->id."'";
		$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
		
		$result = null;
		$query = "UPDATE `".$this->_table['items']."` SET position = position - 1 WHERE position > '".$this->position."' AND section_id = '".$this->section_id."'";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		// delete files [id].jpg in directories PHOTOS_DIR, THUMBS_DIR, PREVIEW_DIR, ORIGINALS_DIR
		$this->destroyFiles();
		
		$query = "DELETE FROM `".$this->_table['items']."` WHERE id='".$this->id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		// delete old links assigned to this object
		$query = "DELETE FROM `".$this->_table['links']."` WHERE object_id='".$this->id."' AND type='item'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		if ($yourportfolio->settings['tags'])
			$this->removeTags();
		
		$this->removeMetadata();
		
		$this->_system->setChanged();
		$this->_system->forceUpdate();
	}
}
?>