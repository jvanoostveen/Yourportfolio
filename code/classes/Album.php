<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Album
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Album extends Node
{
	/**
	 * vars available from database
	 * @var integer $id
	 * @var enum $online
	 * @var integer $position
	 * @var string $name
	 * @var string $text_original
	 * @var string $text
	 * @var string $template
	 * @var integer $type
	 * @var array $parameters
	 */
	var $online;
	var $online_mobile;
	var $position;
	var $locked;
	var $restricted;
	var $user_id;
	var $name;
	var $text_original;
	var $template;
	var $type;
	var $parameters;

	var $_autostore = array(	'online', 'online_mobile', 'position', 'locked', 'restricted', 'user_id', 
								'name', 'text_original', 'text', 'template', 'type', 'link');

	/**
	 * run time vars
	 * @var array $sections
	 */
	var $sections = array();
	var $sectionCount = 0;
	var $menu_sections;
	var $_previous_more = false;
	var $_next_more = false;
	
	/**
	 * objects needed to run this component
	 * @var object $_db
	 * @var array $_table
	 * @var object $_system
	 */
	var $_db;
	var $_table;
	var $_system;
	var $_yourportfolio;
	
	/**
	 * constructor (PHP5)
	 *
	 * @param array $data can contain data needed to create an album without fetching from database itself
	 */
	function __construct($data = null, $selection = false)
	{
		parent::__construct($data);
		
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		$this->_system = &$system;
		
		global $yourportfolio;
		$this->_yourportfolio = &$yourportfolio;
		
		if (!empty($data))
		{
			if ($selection)
			{
				$this->loadSelectedSections();
			} else {
				$this->loadOnlineSections();
			}
			
			$this->loadOnlineFiles();
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Album($data = null, $selection = false)
	{
		$this->__construct($data, $selection);
	}
	
	/**
	 * initialize new Album
	 *
	 */
	function init()
	{
		$this->id = 0;
		$this->online = 'N';
		$this->online_mobile = 'N';
		$this->position = 0;
		$this->name = '';
		$this->text_original = '';
		$this->text = '';
		$this->template = 'album';
		$this->parameters = array();
		$this->locked = 'N';
		$this->restricted = 'N';
	}
	
	/**
	 * loads album
	 * when no album available, initialize album
	 * load album parameters
	 *
	 * @return void
	 */
	function load()
	{
		parent::load();
		
		$this->loadParameters();
	}
	
	private function loadParameters()
	{
		global $db;
		
		$this->parameters = array();
		$parameters = array();
		$query = "SELECT id, parameter, value FROM `".$db->_table['parameters']."` WHERE album_id='".$this->id."'";
		$db->doQuery($query, $parameters, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (!empty($parameters))
		{
			foreach($parameters as $parameter)
			{
				$this->parameters[$parameter['parameter']] = $parameter;
			}
		}
	}

	function loadAll()
	{
		$this->load();
		$this->loadSections();
	}

	function loadOnlineSections()
	{
		if ($this->template == 'news' && isset($GLOBALS['xml_filter_news']) && $GLOBALS['xml_filter_news'])
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y'";
			$this->_db->doQuery($query, $this->sectionCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			$this->sections = array();
			
			return;
		}
		
		$query = "SELECT id, section_date, name, subname, text, custom_data, template, link, type, is_selection FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (empty($this->sections))
			$this->sections = array();
		
		$this->sectionCount = count($this->sections);
	}
	
	function loadSelectedSections()
	{
		$query = "SELECT id, section_date, name, text, custom_data, template FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' AND online='Y' AND is_selection='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	}
	
	function loadSections()
	{
		$query = "SELECT id, section_date, online, name, template, text_node FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	}
	
	function menuSections($section_id = null)
	{
		$this->menu_sections = $this->sections;
	}
	
	/**
	 * Updates album to set modified date to now when content inside album has changed.
	 * 
	 * @return Void
	 */
	function setModified()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['albums']."` SET modified=NOW() WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	/**
	 * save changes to the Album
	 *
	 * after finishing, relocate to album overview
	 *
	 * @param array $album	array from form containing all user album data
	 * @return void
	 */
	function save($data = array(), $files = array())
	{
		$return_to_edit = false;
		
		// first, set id
		if (!isset($data['id']))
		{
			trigger_error('$id is missing', E_USER_ERROR);
		}
		$this->id = (int) $data['id'];
		unset($data['id']);
		
		// load old data if album is existing one
		$this->load();
		
		$old_link = $this->link;
		
		// overwrite data with new data
		foreach($data as $key => $value)
		{
			if (!is_null($value))
			{
				$this->$key = $value;
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
			$q_where .= " WHERE id='".$this->id."' LIMIT 1";
			$return = 'update';
		}
		$q_start .= "`".$this->_table['albums']."` SET ";
		
		$textToolkit = $this->_system->getModule('TextToolkit');
		$this->name	= $textToolkit->stripall($this->name);
		$this->text	= $textToolkit->parseText($this->text_original);
		$this->link	= $textToolkit->normalize($this->name);
		
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
		
		$this->checkDuplicateLink();
				
		// positioning code here
		if (empty($this->position))
		{
			// fetch highest position from album
			$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['albums']."` LIMIT 1";
			$this->_db->doQuery($query, $this->position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			if (empty($this->position))
			{
				$this->position = 1;
			}
			
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

						$next_position = null;
						$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['albums']."` LIMIT 1";
						$this->_db->doQuery($query, $next_position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
						
						if ( $this->position >= $next_position || $move == 0) // beyond highest or a +0 move
						{
							$this->position = $next_position - 1;
						}	
					} else {
						// unknown symbol
						trigger_error('Unknown symbol given in position', E_USER_NOTICE);
						$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['albums']."` LIMIT 1";
						$this->_db->doQuery($query, $this->position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
					}
					
				} else { // first character is a number
					// act normal
					$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['albums']."` LIMIT 1";
					$next_position = null;
					$this->_db->doQuery($query, $next_position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
					if ( $this->position > $next_position ) // beyond highest
					{
						$this->position = $next_position;
					} else {
						$this->position = round($this->position);
					}
				}
				
				// is new postion < or > then old postion?
				if (empty($this->old_position)) // insert into this position from new upload
				{
					$move_query = "UPDATE `".$this->_table['albums']."` SET position = position + 1 WHERE position >= '".$this->position."'";
				} else if ($this->position > $this->old_position) // moved forward
				{
					$move_query = "UPDATE `".$this->_table['albums']."` SET position = position - 1 WHERE position <= '".$this->position."' AND position > '".$this->old_position."'";
				} else { // moved backward
					$move_query = "UPDATE `".$this->_table['albums']."` SET position = position + 1 WHERE position >= '".$this->position."' AND position < '".$this->old_position."'";
				}
				$result = null;
				$this->_db->doQuery($move_query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', true);
				
			} // end postion swapping
		}
		
		unset($this->old_position);
		//
		
		if (!$this->_yourportfolio->settings['restricted_albums'])
		{
			$this->restricted = 'N';
		}
		
		if ($this->restricted == 'N')
		{
			$this->user_id = '';
		}
		
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
		$result = 0;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, TRUE);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		unset($q_start, $q, $q_where, $return);
		
		if (YP_MULTILINGUAL)
		{
			$this->saveLanguageStrings();
		}
		
		$this->saveMetadata();
		
		// store parameters (if any)
		if (!empty($this->parameters))
		{
			foreach($this->parameters as $parameter => $values)
			{
				$q_start = '';
				$q_where = '';
				$q = '';
				$return = '';

				if (empty($values['id']))
				{
					$q_start .= 'INSERT INTO ';
					$return .= 'insert';
					$q .= "album_id='".$this->id."', ";
				} else {
					$q_start .= 'UPDATE ';
					$q_where .= " WHERE  id='".$values['id']."' AND album_id='".$this->id."' LIMIT 1";
					$return .= 'update';
				}
				$q_start .= "`".$this->_table['parameters']."` SET ";
				
				$q .= "parameter='".$parameter."', value='".$values['value']."'";
				$query = $q_start.$q.$q_where;
				
				$result = null;
				$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, true);
			}
		}
		
		if (!empty($old_link) && $old_link != $this->link)
		{
			// add old link to link archives
			$query = "INSERT INTO `".$this->_table['links']."` SET link='".$this->_db->filter($old_link)."', object_id='".$this->id."', type='album'";
			$result = null;
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
		
		# 2) store file uploads
		$error = $this->storeFiles($files);
		if ($error)
			$return_to_edit = true;
		
		// save file properties
		$this->saveFileProperties();
		
		// save tags
		$this->saveTags();
		
		// update xml
		$this->_system->setChanged();
		$this->_system->setSitemapChanged();
		
		// everything done, goto photobook overview
		if ($return_to_edit)
		{
			$this->_system->relocate('album.php?aid='.$this->id.'&mode=edit');
		} else {
			$this->_system->relocate('album.php?aid='.$this->id);
		}
	}
	
	/**
	 * Checks current link value against other link values in the database.
	 * When a duplicate is found, it adds a number to the link value and checks again untill a free number / link value is found.
	 * 
	 * @return Void
	 */
	function checkDuplicateLink()
	{
		// check for duplicate link
		if (!empty($this->link))
		{
			$valid_link = false;
			$link = $this->link;
			$count = 1;
			
			do
			{
				$resource = null;
				$query = "SELECT id FROM `".$this->_table['albums']."` WHERE link='".$this->_db->filter($link)."' AND id != '".$this->_db->filter($this->id)."'";
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
	}
	
	/**
	 * retrieves next (highest) position in a given album
	 *
	 * @return integer
	 *
	 * @access public
	 */
	function next_position()
	{
		$position = 0;
		$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."' LIMIT 1";
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
		$settings = CODE.'settings/album_files.ini';
		if (file_exists(SETTINGS.'album_files.ini'))
		{
			$settings = SETTINGS.'album_files.ini';
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
				if (isset($settings['description']))
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
				case 'album':
				case 'text':
				case 'news':
				case 'contact':
					if (empty($settings['not-for-type']))
						return ($this->template == $settings['not-for'] ? false : true);
					else
						return ($this->template == $settings['not-for'] && in_array($this->type, $settings['not-for-type']) ? false : true);
			}
		}
		
		switch ($settings['for'])
		{
			case ('album'):
			case ('text'):
			case ('news'):
			case ('contact'):
				if (empty($settings['for-type']))
				{
					return ($this->template == $settings['for']) ? true : false;
				} else {
					return ($this->template == $settings['for'] && in_array($this->type, $settings['for-type'])) ? true : false;
				}
		}
		
		return true;
	}
		
	/**
	 * remove this album and all of it's minions from the database
	 */
	function destroy()
	{
		// select sections of album
		$query = "SELECT id FROM `".$this->_table['sections']."` WHERE album_id='".$this->id."'";
		$this->_db->doQuery($query, $this->sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		//	let each section do it's own doom
		if ($this->sections)
		{
			foreach($this->sections as $loop_section)
			{
				$section = new Section();
				$section->id = $loop_section['id'];
				$section->destroy();
				unset($section);
			}
		}
		
		// delete own files (if any)
		$this->destroyFiles();
		
		// delete album
		$result = null;
		$query = "DELETE FROM `".$this->_table['albums']."` WHERE id='".$this->id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		// delete parameters assigned to the album
		$query = "DELETE FROM `".$this->_table['parameters']."` WHERE album_id='".$this->id."'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		// delete old links assigned to this object
		$query = "DELETE FROM `".$this->_table['links']."` WHERE object_id='".$this->id."' AND type='album'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$this->removeMetadata();
		
		// update xml
		$this->_system->setChanged();
		$this->_system->forceUpdate();
	}
	
	/**
	 * retrieve parameter
	 *
	 * @param string $param		name of parameter to fetch
	 * @param string $key		name of field to fetch value from
	 *
	 * @return string
	 * @access public
	 */
	function getParameter($param, $key = 'value')
	{
		if (is_array($this->parameters))
		{
			if (isset($this->parameters[$param]))
				return $this->parameters[$param][$key];
			else
				return '';
		}
	}

	/**
	 * Reposition albums so positions are correct.
	 * Use as static function.
	 * 
	 * @return Void
	 */
	function rePosition()
	{
		global $db;
		
		$albums = array();
		$query = "SELECT id FROM `".$db->_table['albums']."` ORDER BY position ASC, id ASC";
		$db->doQuery($query, $albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array');
		
		if (!empty($albums))
		{
			$position = 1;
			foreach ($albums as $album)
			{
				$result = null;
				$query = "UPDATE `".$db->_table['albums']."` SET position = '".$position."' WHERE id='".$album."'";
				$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update');
				
				$position++;
			}
		}
	}
	
	function saveChildPositions($ids_list)
	{
		global $db;
		
		$ids = explode(',', $ids_list);
		$position = 1;
		foreach ($ids as $node_id)
		{
			$result = null;
			$query = "UPDATE `".$db->_table['sections']."` SET position='".$position."' WHERE id='".$node_id."' LIMIT 1";
			$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			
			$position++;
		}
		
		$this->_system->setChanged();
	}
}
?>
