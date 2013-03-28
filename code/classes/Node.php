<?php

/**
 * Node base class
 */
class Node
{
	const ALBUM = 'album';
	const SECTION = 'section';
	const ITEM = 'item';
	
	// basic properties
	public $id;
	public $text;
	public $link;
	
	// multilanguage
	public $strings = array();
	
	// metadata
	public $metadata = array();
	
	// tags
	public $tags = array();
	
	// file related
	public $files = array();
	public $files_settings = array();
	public $files_properties = array();
	
	protected $node_type;
	
	protected $clean_posted_files = true;
	
	public function __construct($data = null)
	{
		$this->node_type = strtolower(get_class($this));
		
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$this->{$key} = $value;
			}
			
			if (YP_MULTILINGUAL)
			{
				$this->loadLanguageStrings();
			}
		}
	}
	
	public function load()
	{
		global $yourportfolio;
		global $db;
		
		switch ($this->node_type)
		{
			case self::ALBUM:
				$query = "SELECT online, online_mobile, position, locked, restricted, user_id, name, text_original, template, type, link FROM `".$this->_table['albums']."` WHERE id='".$this->id."'";
				break;
			case self::SECTION:
				$query = "SELECT UNIX_TIMESTAMP(section_date) AS section_date, online, online_mobile, text_node, is_selection, position, name, subname, text_original, custom_data, template, link, type FROM `".$this->_table['sections']."` WHERE id='".$this->id."'";
				break;
			case self::ITEM:
				$query = "SELECT album_id, section_id, online, text_node, position, type, name, subname, text_original, custom_data, link, label_type FROM `".$this->_table['items']."` WHERE id='".$this->id."'";
				break;
		}
		
		if ( !$db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
			$this->init();
		
		if (YP_MULTILINGUAL)
			$this->loadLanguageStrings();
		
		$this->loadMetadata();
		$this->parseFilesSettings();
		$this->loadFiles();
		
		if ($yourportfolio->settings['tags'])
			$this->loadTags();
	}
	
	protected function parseFilesSettings()
	{
		
	}
	
	/**
	 * wrapper function to get the text in the requested language
	 * 
	 */
	public function getText($field, $language)
	{
		if ($language == $GLOBALS['YP_DEFAULT_LANGUAGE'])
		{
			return $this->{$field};
		}
		
		if (!isset($this->strings[$field]))
		{
			return null;
		}
		
		if (!isset($this->strings[$field][$language]))
		{
			return null;
		}
		
		return $this->strings[$field][$language]['string'];
	}

	/**
	 * wrapper function to get the text in the requested language
	 * 
	 */
	public function getParsedText($field, $language)
	{
		if ($language == $GLOBALS['YP_DEFAULT_LANGUAGE'] || is_null($language))
		{
			if ($field == 'text_original')
			{
				return $this->text;
			} else {
				return $this->{$field};
			}
		}
		
		if (!isset($this->strings[$field]))
		{
			return null;
		}
		
		if (!isset($this->strings[$field][$language]))
		{
			return null;
		}
		
		return $this->strings[$field][$language]['string_parsed'];
	}
	
	/**
	 * stores posted files
	 * uses the settings supplied by the ini file, and applies all available actions on the new file
	 */
	function storeFiles($files)
	{
		global $messages;
		global $db;
		global $system;
		
		if (empty($this->id))
		{
			trigger_error('No id set for storing files', E_USER_WARNING);
			return false;
		}
		
		// if settings are not loaded, do so now
		if (empty($this->files_settings))
		{
			$this->parseFilesSettings();
		}
		
		$action_error = false;
		
		foreach($files as $file_id => $file_data)
		{
			// file no longer in ini definitions, ignore
			if (!isset($this->files_settings[$file_id]))
				continue;
			
			$settings = $this->files_settings[$file_id];

			$file = new FileObject($file_data);
			
			// extension correct?
			$extensions = explode(',', $settings['extension']);
			if ($settings['extension'] != '*' && !in_array($file->extension, $extensions))
			{
				$ext_list = '';
				for ($i = 0; $i < count($extensions); $i++)
				{
					if (count($extensions) > 1 && $i > 0)
					{
						if ($i + 1 == count($extensions))
						{
							$ext_list .= '` '.gettext('of').' `';
						} else {
							$ext_list .= '`, `';
						}
					}
					
					$ext_list .= '.'.$extensions[$i];
				}
				
				trigger_error('extensions don\'t match', E_USER_NOTICE);
				$messages->add(sprintf(_('Bestandextensie `.%1$s` komt niet overeen met verwachte extensie `%2$s`.'), $file->extension, $ext_list), MESSAGE_ERROR);
				continue;
			}
			
			// check for existing entry on this file_id for this item
			// if so, delete files and record
			$query = "SELECT id, extension, path, sysname FROM `".$db->_table[$this->node_type.'_files']."` WHERE owner_id='".$this->id."' AND file_id='".$file_id."'";
			$old_file_data = null;
			$db->doQuery($query, $old_file_data, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
			if (!empty($old_file_data))
			{
				$old_file = new FileObject($old_file_data);
				$old_file->destroy($settings['actions']);
				
				$query = "DELETE FROM `".$db->_table[$this->node_type.'_files']."` WHERE id='".$old_file->id."'";
				$result = null;
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
			}
			//
			
			// generate storage name
			$search = array('{id}', '{ext}', '{filename.ext}');
			$replace = array($this->id, $file->extension, $file->name);
			$file->sysname = $this->node_type.'-'.str_replace($search, $replace, $settings['naming']);
			
			// assign path
			$file->basepath = str_replace('../', '', $settings['target_dir']); // filters the ../ away, path for front site
			$file->path = $settings['target_dir']; // path for backend
			
			// let loose the actions
			foreach($settings['actions'] as $action)
			{
				switch($action['action'])
				{
					case('copy'):
						if (copy($file->tmp_name, $file->path.$file->sysname))
						{
							chmod($file->path.$file->sysname, 0666);
							
							switch ($file->extension)
							{
								case ('jpg'):
								case ('png'):
									list($file->width, $file->height) = getimagesize($file->path.$file->sysname);
									break;
								case ('flv'):
									require_once(VENDOR.'flv4php/FLV.php');
									
									$flv = new FLV();
									
									if ($flv->open($file->path.$file->sysname))
									{
										$file->width = $flv->metadata['width'];
										$file->height = $flv->metadata['height'];
									} else {
										trigger_error('An error occurred while trying to open a FLV file "'.$file->sysname.'": ', E_USER_ERROR);
									}
									$flv->close();									
									break;
								case ('mp4'):
								case ('mov'):
									require_once(MODULES.'QuickTime.php');
									
									$file_info = QuickTime::getFileInfo($file->path.$file->sysname);
									
									if (isset($file_info['width']))
									{
										$file->width = $file_info['width'];
										$file->height = $file_info['height'];
									}
									break;
								case ('swf'):
									require_once(VENDOR.'SWFHeader/SWFHeader.php');
									
									$swf = new SWFHeader();
									if ($swf->loadswf($file->path.$file->sysname))
									{
										$file->width = $swf->width;
										$file->height = $swf->height;
									}
									break;
							}
						} else {
							// file copy failed
							trigger_error('file copy failed', E_USER_NOTICE);
							
							// don't save the file, continue with next
							continue 3;
						}
						break;
					case('saveOriginal'):
						if (copy($file->tmp_name, ORIGINALS_DIR.$file->sysname))
						{
							chmod(ORIGINALS_DIR.$file->sysname, 0666);
						}
						break;
					case('yourportfolio'):
						// save a jpeg image with specified size.
						$sizes = array('w' => 120, 'h' => 90);
						$imageToolkit = $system->getModule('ImageToolkit');
						$yourportfolio_name = $this->node_type.'-'.$this->id.'.jpg';
						if ($imageToolkit->imageResize($file->tmp_name, $sizes, YOURPORTFOLIO_DIR.$yourportfolio_name, IMAGETYPE_JPEG) === false)
						{
							// yourportfolio preview could not be created.
							trigger_error('Yourportfolio preview could not be created for '.$this->node_type.': '.$this->id, E_USER_NOTICE);
							continue;
						}
						break;
					case('autoResize'):
						$sizes = array('w' => $action['args'][0], 'h' => $action['args'][1]);
						
						$imageToolkit = $system->getModule('ImageToolkit');
						if (($sizesResized = $imageToolkit->imageResize($file->tmp_name, $sizes, $file->path.$file->sysname)) !== false)
						{
							$file->width = $sizesResized['w'];
							$file->height = $sizesResized['h'];
							$file->size = filesize($file->path.$file->sysname);
						} else {
							// something went wrong, could be wrong file format after all
							trigger_error('resizing failed', E_USER_NOTICE);
							
							$action_error = true;
							
							// don't save file, and continue
							continue 3;
						}
						break;
					case('scaleAndCrop'):
						$sizes = array('w' => $action['args'][0], 'h' => $action['args'][1]);
						
						$imageToolkit = $system->getModule('ImageToolkit');
						if (($sizesResized = $imageToolkit->scaleAndCrop($file->tmp_name, $sizes, $file->path.$file->sysname)) !== false)
						{
							$file->width = $sizesResized['w'];
							$file->height = $sizesResized['h'];
							$file->size = filesize($file->path.$file->sysname);
						} else {
							// something went wrong, could be wrong file format after all
							trigger_error('scale and crop failed', E_USER_NOTICE);
							
							$action_error = true;
							
							// don't save file, and continue
							continue 3;
						}
						break;
					case('scaleAndCropLandscape'):
						$sizes = array('w' => $action['args'][0], 'h' => $action['args'][1]);
						
						$imageToolkit = $system->getModule('ImageToolkit');
						if (($sizesResized = $imageToolkit->scaleAndCropLandscape($file->tmp_name, $sizes, $file->path.$file->sysname)) !== false)
						{
							$file->width = $sizesResized['w'];
							$file->height = $sizesResized['h'];
							$file->size = filesize($file->path.$file->sysname);
						} else {
							// something went wrong, could be wrong file format after all
							trigger_error('scale and crop landscape failed', E_USER_NOTICE);
							
							$action_error = true;
							
							// don't save file, and continue
							continue 3;
						}
						break;
					case('autoGenerate'):
						//autoGenerate|target|default|string
						$target_id = $action['args'][0];
						
						// is checkbox checked? and thumbnail box is empty?
						if (isset($files[$target_id]))
						{
							// file in upload, skip this action
							#trigger_error($target_id.' has a file, skipping this action', E_USER_NOTICE);
							break;
						}
						
						$newfile_data = $file_data;
						$newfile_data['tmp_name'] = CODE.'tmp/'.md5($file_data['tmp_name']);
						if (!copy($file_data['tmp_name'], $newfile_data['tmp_name']))
						{
							// autogenerate file copy failed
							trigger_error('autogenerate file copy failed', E_USER_NOTICE);
							break;
						}
						$newfiles = array($target_id => $newfile_data);
						$this->storeFiles($newfiles);
						break;
				}
			}
			
			// still here? save file entry to the database
			$query = "INSERT INTO `".$db->_table[$this->node_type.'_files']."` SET ".
						"owner_id='".$this->id."', ".
						"file_id='".$file_id."', ".
						"created=NOW(), ".
						"online='Y', ".
						"name='".$db->filter($file->name)."', ".
						"size='".$db->filter($file->size)."', ".
						"extension='".$db->filter($file->extension)."', ".
						"type='".$db->filter($file->type)."', ".
						"width='".$file->width."', ".
						"height='".$file->height."', ".
						"basepath='".$file->basepath."', ".
						"path='".$file->path."', ".
						"sysname='".$file->sysname."'";
			$file->id = null;
			$db->doQuery($query, $file->id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
		
		if ($this->clean_posted_files)
			$system->cleanPostedFiles($files);
		
		return $action_error;
	}
	
	/**
	 * save properties of each file when they are changed
	 * current supported properties are:
	 * - online
	 */
	function saveFileProperties()
	{
		if (!empty($this->files_properties))
		{
			global $db;
			
			while (list($file_id, $properties) = each($this->files_properties))
			{
				$q = '';
				if ($properties['online'] != $properties['online_old'])
				{
					$q = "online='".$db->filter($properties['online'])."',";
					
					if (!isset($this->files_settings[$file_id]))
						continue;
					
					foreach($this->files_settings[$file_id]['actions'] as $action)
					{
						if ($action['action'] == 'autoGenerate')
						{
							$this->files_properties[$action['args'][0]]['online'] = $properties['online'];
							break;
						}
					}
				}
				
				if (isset($properties['created']))
				{
					$q .= "created='".$db->filter($properties['created'])."',";
				}
				
				// all properties checked, apply changes
				if (!empty($q))
				{
					$result = null;
					$q = substr($q, 0, -1);
					$query = "UPDATE `".$db->_table[$this->node_type.'_files']."` SET ".$q." WHERE owner_id='".$this->id."' AND file_id='".$db->filter($file_id)."'";
					$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
				}
			}
		}
	}

	/**
	 * load all the files associated with this item, including the offline ones
	 */
	function loadFiles()
	{
		if (empty($this->id))
		{
			return;
		}
		
		global $db;
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, path, sysname FROM `".$this->_table[$this->node_type.'_files']."` WHERE owner_id='".$this->id."' ORDER BY id ASC";
		$db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'file_id', 'object' => 'FileObject'));
		
		if ($this->files === false)
			$this->files = array();
		
		foreach($this->files as $file_id => $file)
		{
			$file = $this->files[$file_id];
			$file->owner_type = $this->node_type;
		}
	}

	/**
	 * only load files marked with online='y' in the database
	 */
	function loadOnlineFiles()
	{
		if (empty($this->id))
		{
			return;
		}
		
		global $db;
		
		$query = "SELECT id, owner_id, file_id, created, online, name, size, extension, type, width, height, basepath, path, sysname FROM `".$this->_table[$this->node_type.'_files']."` WHERE owner_id='".$this->id."' AND online='Y' ORDER BY id ASC";
		$db->doQuery($query, $this->files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'file_id', 'object' => 'FileObject'));
	}

	/**
	 * wrapper function to get a file, and otherwise returns an empty FileObject
	 * @param $file_id:String
	 */
	function getFile($file_id)
	{
		if (isset($this->files[$file_id]))
		{
			return $this->files[$file_id];
		} else {
			return new FileObject();
		}
	}
	
	/**
	 * removes all language strings from the table for this object
	 * needs to be called before saving new language strings, which is now done from within the saveLanguageStrings function
	 * 
	 */
	protected function removeLanguageStrings()
	{
		global $db;
		
		$result = null;
		$query = "DELETE FROM `".$db->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".$this->node_type."'";
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	/**
	 * load this object language strings
	 * and puts them into $this->strings
	 * 
	 * the language array is built as follows:
	 * $this->strings[field][language] = array(value, etc);
	 * 
	 */
	public function loadLanguageStrings()
	{
		global $db;
		
		$query = "SELECT id, field, language, string, string_parsed FROM `".$db->_table['strings']."` WHERE owner_id='".$this->id."' AND owner_type='".$this->node_type."' ";
		$db->doQuery($query, $this->strings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array', false, array('index_key_1' => 'field', 'index_key_2' => 'language'));
		
		if (empty($this->strings))
		{
			$this->strings = array();
		}
	}
	
	/**
	 * saves language strings of this object
	 * first, removes the language string currently stored
	 * skips empty values to keep database stuff lower
	 * 
	 */
	protected function saveLanguageStrings()
	{
		$this->removeLanguageStrings();
		
		global $system;
		global $db;
		
		$textToolkit = $system->getModule('TextToolkit');
		
		foreach ($this->strings as $field => $language)
		{
			foreach ($language as $lang_key => $value)
			{
				if (empty($value))
				{
					continue;
				}
				
				$value_parsed = $textToolkit->parseText($value);
				
				$result = null;
				$query = "INSERT INTO `".$db->_table['strings']."` SET owner_id='".$this->id."', owner_type='".$this->node_type."', field='".$db->filter($field)."', language='".$db->filter($lang_key)."', string='".$db->filter($value)."', string_parsed='".$db->filter($value_parsed)."'";
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			}
		}
	}
	
	public function getMetadata($key, $language)
	{
		if (!isset($this->metadata[$key]))
		{
			return null;
		}
		
		if (!isset($this->metadata[$key][$language]))
		{
			return null;
		}
		
		return $this->metadata[$key][$language];
	}
	
	protected function loadMetadata()
	{
		if (empty($this->id))
		{
			$this->metadata = array();
			return;
		}
		
		global $db;
		
		$query = "SELECT `id`, `field`, `language`, `value` FROM `".$db->_table['metadata']."` WHERE `owner_id`='".$this->id."' AND `owner_type`='".$this->node_type."'";
		$db->doQuery($query, $this->metadata, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'multi_index_array_value', false, array('index_key_1' => 'field', 'index_key_2' => 'language', 'value' => 'value'));
		
		if (empty($this->metadata))
			$this->metadata = array();
	}
	
	protected function saveMetadata()
	{
		if (empty($this->id))
			return;
		
		$this->removeMetadata();
		
		global $db;
		
		foreach ($this->metadata as $field => $language)
		{
			foreach ($language as $language_key => $value)
			{
				if (empty($value))
				{
					continue;
				}
				
				$query = "INSERT INTO `".$db->_table['metadata']."` SET `owner_id`='".$this->id."', `owner_type`='".$this->node_type."', `field`='".$db->filter($field)."', `language`='".$db->filter($language_key)."', `value`='".$db->filter($value)."'";
				$result = null;
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			}
		}
	}
	
	protected function removeMetadata()
	{
		if (empty($this->id))
			return;
		
		global $db;
		
		$query = "DELETE FROM `".$db->_table['metadata']."` WHERE `owner_id`='".$this->id."' AND `owner_type`='".$this->node_type."'";
		$result = null;
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	/**
	 * tag functionality
	 */
	
	protected function saveTags()
	{
		global $yourportfolio;
		if (!$yourportfolio->settings['tags'])
			return;
		
		$this->removeTags();
		
		// save new tag bindings
		foreach (array_keys($this->tags) as $tag_id)
		{
			if (isset($this->tags[$tag_id] ) && $this->tags[$tag_id] == 'on') 
			{
				// save tag
				$q = sprintf("INSERT INTO `%s` SET `".$this->node_type."_id`=%d, `tag_id`=%d", $this->_table[$this->node_type.'_tags'], $this->id, $tag_id);
				$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			}
		}
	}
	
	protected function loadTags()
	{
		$query = sprintf( "SELECT `tag_id` FROM `%s` WHERE `".$this->node_type."_id`=%d", $this->_table[$this->node_type.'_tags'], $this->id );
		$this->_db->doQuery($query, $this->tags, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		
		if ($this->tags == false)
		{
			$this->tags = array();
		}
	}
	
	protected function removeTags()
	{
		// delete tags
		$query = "DELETE FROM `".$this->_table[$this->node_type.'_tags']."` WHERE `".$this->node_type."_id`='".$this->id."'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	/**
	 * translate the actions string to actions
	 */
	protected function parseActions($actions_string)
	{
		$actions = array();
		
		$tmp_actions = explode(',', $actions_string);
		foreach($tmp_actions as $key => $tmp_action)
		{
			$tmp_action = explode('|', trim($tmp_action));
			$actions[$key]['action'] = array_shift($tmp_action);
			$actions[$key]['args'] = $tmp_action;
		}
		return $actions;
	}
	
	/**
	 * removes files assigned to item
	 */
	protected function destroyFiles()
	{
		global $db;
		
		// fetch all ids of files belonging to this item
		$query = "SELECT id FROM `".$db->_table[$this->node_type.'_files']."` WHERE owner_id='".$this->id."'";
		$files = array();
		$db->doQuery($query, $files, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		
		if (!empty($files))
		{
			foreach($files as $file_id)
			{
				$this->destroyFile($file_id);
			}
		}
	}
	
	/**
	 * removes a file entry from the database and all related files
	 * @param $file_id:Number
	 */
	public function destroyFile($file_id)
	{
		// if settings are not loaded, do so now
		if (empty($this->files_settings))
		{
			$this->parseFilesSettings();
		}
		
		global $db;

		$query = "SELECT id, file_id, extension, path, sysname FROM `".$db->_table[$this->node_type.'_files']."` WHERE owner_id='".$this->id."' AND id='".$file_id."'";
		$old_file_data = null;
		$db->doQuery($query, $old_file_data, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
		if (!empty($old_file_data))
		{
			$old_file = new FileObject($old_file_data);
			$old_file->owner_type = $this->node_type;
			$old_file->destroy($this->files_settings[$old_file->file_id]['actions']);
			
			$query = "DELETE FROM `".$db->_table[$this->node_type.'_files']."` WHERE id='".$old_file->id."'";
			$result = null;
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		}
		
		// update xml
		global $system;
		$system->setChanged();
		$system->forceUpdate();
	}
	
	public function getLink()
	{
		if (!empty($this->link))
		{
			return $this->link;
		} else {
			return $this->id;
		}
	}
}
