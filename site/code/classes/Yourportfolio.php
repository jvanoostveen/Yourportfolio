<?PHP
/**
 * Project:			yourportfolio
 * 
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * class: Yourportfolio
 * 
 * Yourportfolio front end object
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Yourportfolio
{
	/**
	 * title for the site
	 * @var string $title
	 */
	var $title;
	
	/**
	 * general feedback variable
	 * @var string
	 */
	var $feedback;
	
	/**
	 * the albums for generating the menu
	 */
	var $menu_albums;
	var $restricted_albums = array();
	
	/**
	 * array for the album side images
	 */
	var $randomImages = array();
	
	var $preferences	= array();
	var $site			= array();
	
	
	/**
	 * database object
	 * @var object
	 */
	var $_db;
	
	/**
	 * database tables
	 * @var array
	 */
	var $_table;
	
	/**
	 * runtime vars:
	 * 		user_id for owner of current site
	 */
	var $user_id = null;
	var $settings_cache;
	
	var $multilingual	= false;
	var $skip_no_language	= false;
	var $languages		= array();
	var $default_language;
	
	/**
	 * objects needed to run this component
	 */
	var $_system;
	var $_canvas;
	
	/**
	 * constructor (PHP5)
	 * 
	 */
	function __construct()
	{
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		$this->_system = &$system;
		
		global $canvas;
		$this->_canvas = &$canvas;
		
		$this->loadSettings();
	}
	
	/**
	 * constructor (PHP4)
	 * redirect construction call to __construct
	 *  
	 * @uses __construct()
	 */
	function Yourportfolio()
	{
		$this->__construct();
	}
	
	/**
	 * and retrieves the tablename to be used
	 *
	 * @uses $_db, $_table
	 *
	 * @return void
	 * @access public
	 */
	function loadSettings()
	{		
		// load runtime vars needed
		if (!file_exists(SETTINGS.'runtime.ini'))
		{
			trigger_error('runtime.ini file is missing!', E_USER_ERROR);
		}
		
		foreach (parse_ini_file(SETTINGS.'runtime.ini') as $key => $value)
		{
			$this->{$key} = $value;
		}
		
		$this->settings = array(
								'text_nodes' => false,
								'quicktime_check' => false,
								'autopublish' => false,
								'xml_amf_hybrid' => false,
								'xml_filter_items' => false,
								'xml_filter_news' => false,
								'rss_news_only' => false,
								'subusers' => false,
								'internal_links' => false,
								'tags' => false,
								'guestbook' => false,
								'guestbook_approval' => false,
								'newsletter' => false,
								'html_only' => false,
								'mobile' => false,
								'tablet' => false,
								'can_add_albums' => false,
								'download_photos' => false,
								'restricted_albums' => false,
								'has_upload_folder'	=> false,
								'can_edit_types' => false,
								'has_custom_fields' => false,
								'unassigned_restricted_albums_for_all' => false,
								'news_templates' => false,
								'fixed_sections' => false,
								'items_have_subname' => false,
								'sections_have_subname' => false
							);
		
		$this->preferencesLoad();
		$this->advancedSettingsLoad();
	}
	
	/**
	 * load advanced settings
	 */
	function advancedSettingsLoad()
	{
		// default settings
		if ( file_exists(CORE_SETTINGS.'site.ini') )
		{
			// parse ini file
			$this->site = parse_ini_file(CORE_SETTINGS.'site.ini', true);
		} else {
			trigger_error('default site.ini not found...', E_USER_NOTICE);
		}

		if ( file_exists(SETTINGS.'site.ini') )
		{
			// parse ini file
			$site_override = parse_ini_file(SETTINGS.'site.ini', true);
			foreach($site_override as $section => $settings)
			{
				if (!isset($this->site[$section]))
					$this->site[$section] = array();
				
				$this->site[$section] = array_merge($this->site[$section], $settings);
			}
		}
		
		if (!empty($this->prefs['google_analytics_account']))
		{
			$this->site['google_analytics']['enabled'] = true;
			$this->site['google_analytics']['account'] = $this->prefs['google_analytics_account'];
			unset($this->prefs['google_analytics_account']);
		}
		
		// make sure Google Analytics is not enabled when DEBUG is true.
		if (DEBUG)
		{
			$this->site['google_analytics']['enabled'] = false;
		}
		
		// apply fix for older site.ini files
		if (isset($this->site['frontend']['resize_div_min_w']))
		{
			$this->site['frontend']['resize_div']['min_width']	= $this->site['frontend']['resize_div_min_w'];
			$this->site['frontend']['resize_div']['min_height']	= $this->site['frontend']['resize_div_min_h'];
		}
		
		$this->default_language = $this->site['frontend']['default_language'];
		if ( file_exists(SETTINGS.'languages.ini') )
		{
			$language_settings = parse_ini_file(SETTINGS.'languages.ini', true);
			$this->languages = $language_settings['languages'];
			$this->default_language = $language_settings['settings']['default'];
			if (isset($language_settings['settings']['skip']))
				$this->skip_no_language = $language_settings['settings']['skip'];
			
			$this->multilingual = true;
			
			unset($language_settings);
		}
		
		if (!defined('YP_MULTILINGUAL'))
		{
			define('YP_MULTILINGUAL', $this->multilingual);
			define('YP_SKIP_NO_LANGUAGE', $this->skip_no_language);
			define('YP_DEFAULT_LANGUAGE', $this->default_language);
			$GLOBALS['YP_LANGUAGES'] = $this->languages;
			$GLOBALS['YP_DEFAULT_LANGUAGE'] = $this->default_language;
			$GLOBALS['YP_CURRENT_LANGUAGE'] = $this->default_language;
		}

		$file = SETTINGS.$this->settings_cache.'settings.dat';
		if(defined('FRONTEND') && is_readable($file))
		{
			$content = file($file);
			$this->settings = array_merge($this->settings, unserialize(trim($content[0])));
		} else {
			$query = "SELECT d.settings, d.custom_fields, d.facebook_user_ids, d.facebook_app_id FROM `".$this->_table['data']."` d, `".$this->_table['users']."` u WHERE d.photographer_id = '".$this->user_id."'";
	
			// may be false after trying the database, if we're working in the frontend and there's no db connection present
			$preferences = null;
	
			$frontend = defined('FRONTEND');
			$this->_db->doQuery($query, $preferences, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', !$frontend);
			
			$this->preferences['custom_fields']			= $preferences['custom_fields'];
			$this->preferences['facebook_user_ids']		= $preferences['facebook_user_ids'];
			$this->preferences['facebook_app_id']		= $preferences['facebook_app_id'];
			$settings = $preferences['settings'];
			
			$settings = str_pad(decbin($settings), 32, '0', STR_PAD_LEFT);
			/*
				 0 - 
	`			 1 - text nodes
				 2 - perform quicktime check
				 3 - auto publish
				 4 - XML and AMFPHP hybrid
				 5 - XML - filter items
				 6 - XML - filter news
				 7 - 
				 8 - RSS - news only
				 9 - 
				10 - has subusers
				11 - can create internal links (flash has public static function Application.asfunction_show())
				12 - tags
				13 - keywords (reserved: now only in MVRDV branch)
				14 - has guestbook
				15 - automatic approval of guestbook entries
				16 - newsletter
				17 - html only
				18 - mobile version enabled
				19 - tablet version enabled
				20 - user can add normal albums
				21 - can add download photos link
				22 - restricted albums/users
				23 - has an upload folder
				24 - user can edit specific template types
				25 - has custom fields
				26 - unassigned_restricted_albums_for_all
				27 - can use news template
				28 - fixed sections (can't add sections, or delete them, when creating an album, sections are automaticly generated)
				29 - items have subtitle/subname
				30 - 
				31 - 
				----
			*/
			
			if (!$this->settings)
				$this->settings = array();
			
			$this->settings['quicktime_check']		= ($settings{2}) ? true : false;
			$this->settings['xml_amf_hybrid']		= ($settings{4}) ? true : false;
			$this->settings['xml_filter_items']		= ($settings{5}) ? true : false;
			$this->settings['xml_filter_news']		= ($settings{6}) ? true : false;
			$this->settings['rss_news_only']		= ($settings{8}) ? true : false;
			$this->settings['tags']					= ($settings{12}) ? true : false;
			$this->settings['guestbook']			= ($settings{14}) ? true : false;
			$this->settings['guestbook_approval']	= ($settings{15}) ? true : false;
			$this->settings['html_only']			= ($settings{17}) ? true : false;
			$this->settings['mobile']				= ($settings{18}) ? true : false;
			$this->settings['tablet']				= ($settings{19}) ? true : false;
			$this->settings['restricted_albums']	= ($settings{22}) ? true : false;
			$this->settings['unassigned_restricted_albums_for_all']	= ($settings{26}) ? true : false;
			$this->settings['news_templates']		= ($settings{27}) ? true : false;
		}
		
		$GLOBALS['amf_hybrid'] = $this->settings['xml_amf_hybrid'];
		$GLOBALS['xml_filter_items'] = $this->settings['xml_filter_items'];
		$GLOBALS['xml_filter_news'] = $this->settings['xml_filter_news'];
	}
	
	public function setLocale()
	{
		$mapping = array(
					'en' => 'en_GB',
					'nl' => 'nl_NL',
					'de' => 'de_DE',
					'fr' => 'fr_FR'
				);
		
		$lang = $mapping['en'];
		if (array_key_exists($GLOBALS['YP_CURRENT_LANGUAGE'], $mapping))
		{
			$lang = $mapping[$GLOBALS['YP_CURRENT_LANGUAGE']];
		} else if (array_key_exists($GLOBALS['YP_DEFAULT_LANGUAGE'], $mapping))
		{
			$lang = $mapping[$GLOBALS['YP_DEFAULT_LANGUAGE']];
		}
		
		@putenv('LANG='.$lang);
		setlocale(LC_ALL, $lang);
		
		bindtextdomain('backend', LOCALE);
		bindtextdomain('newsletter', LOCALE);
		bindtextdomain('frontend', LOCALE);
		
		textdomain('frontend');
	}
	
	function parseCustomFields()
	{
		$this->custom_fields = array();
		
		if (empty($this->preferences['custom_fields']))
		{
			return;
		}
		
		$fields = explode("\r\n", $this->preferences['custom_fields']);
		
		foreach($fields as $field)
		{
			// empty line
			if (empty($field))
			{
				continue;
			}
			
			$tmp_field = array();
			list($tmp_field['key'], $tmp_field['label'], $tmp_field['length']) = explode(' :: ', $field);
			$this->custom_fields[] = $tmp_field;
		}
	}
	
	/**
	 * retrieves the title
	 * @return string
	 * @access public
	 */
	function getTitle()
	{
		if (!is_null($this->title))
			return $this->title;
		else
		{
			$this->preferencesLoad();
			return $this->title;
		}
/*		$query = "SELECT d.title FROM ".$this->_table['data']." AS d WHERE d.photographer_id = '".$this->user_id."' LIMIT 1";
		
		$this->_db->doQuery($query, $this->title, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		return $this->title;*/
	}
	
	/**
	 * retrieves preferences
	 *
	 */
	function preferencesLoad()
	{
		$file = SETTINGS.$this->settings_cache.'preferences.dat';
		
		if (defined('FRONTEND') && file_exists($file) && is_readable($file)) 
		{
			$contents = file_get_contents($file);
			$this->prefs = unserialize(trim($contents));
			foreach($this->prefs as $key => $value)
			{
				if (is_string($value))
				{
					$this->prefs[$key] = stripslashes($value);
				}
			}
			$this->title = $this->prefs['title'];
			unset($this->prefs['title']);
		} else {
			$query = "SELECT d.description, d.keywords, d.copyright, d.bg_colour, d.google_site_verification, d.google_analytics_account, d.title FROM `".$this->_table['data']."` AS d WHERE d.photographer_id = '".$this->user_id."' LIMIT 1";
			$this->_db->doQuery($query, $this->prefs, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
			$this->title = $this->prefs['title'];
			unset($this->prefs['title']);
		}
		
		$this->preferences = $this->prefs;
	}
	
	/**
	 * retrieve contact info (email, phone, fax) from user account
	 *
	 */
	function getContactInfo()
	{
		$file = SETTINGS.$this->settings_cache.'contact.dat';
		
		if (file_exists($file) && is_readable($file))
		{
			$contents = file($file);
			$contact = unserialize(trim($contents[0]));
			foreach($contact as $key => $value)
			{
				$this->{$key} = $value;
			}
		} else {
			$query = "SELECT d.email, d.phone, d.mobile, d.fax FROM `".$this->_table['data']."` AS d WHERE d.photographer_id = '".$this->user_id."' LIMIT 1";
			$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false);
		}
	}
	
	/**
	 * retrieves the id of the top most album (when online)
	 */
	function getFirstAlbum()
	{
		$query = "SELECT id FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC LIMIT 1";
		$album_id = null;
		$this->_db->doQuery($query, $album_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		return $album_id;
	}
	
	
	/**
	 * retrieves the id of the top most album (when online) of type album (template = album)
	 */
	function getFirstRealAlbum()
	{
		$query = "SELECT id FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='N' AND template='album' ORDER BY IF(position > 0, position, 999) ASC, id ASC LIMIT 1";
		$album_id = null;
		$this->_db->doQuery($query, $album_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		return $album_id;
	}
	
	/**
	 * gets random $amount of online items from $album
	 * assigns them to $randomImages for later use
	 *
	 * @access public
	 */
	function getRandomImages($album = 3, $amount = 5)
	{
		$this->randomImages = array();
		
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE album_id=".$album." AND online='Y' AND source_preview IS NOT NULL ORDER BY RAND() LIMIT ".$amount;
		$this->_db->doQuery($query, $this->randomImages, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
		
		if (!$this->randomImages)
			$this->randomImages = array();
	}
	
	/**
	 * get the images from $section_name in $album
	 *
	 * @access public
	 */
	function getImages($album_id, $section_name)
	{
		$this->randomImages = array();
		
		if (empty($album_id))
			return;
		
		$query = "SELECT id FROM `".$this->_table['sections']."` WHERE album_id='".$album_id."' AND name='".$section_name."' AND online='Y' LIMIT 1";
		$section_id = null;
		$this->_db->doQuery($query, $section_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		if ($section_id === false)
			return;
		
		$query = "SELECT id FROM `".$this->_table['items']."` WHERE album_id=".$album_id." AND section_id='".$section_id."' AND online='Y' AND source_preview IS NOT NULL ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->randomImages, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
		
		if (!$this->randomImages)
			$this->randomImages = array();
	}
	
	/**
	 * loads names for in menu
	 *
	 * @uses $_db, $_table
	 *
	 * @assigns $menu_albums
	 *
	 * @access public
	 */
	function loadAlbums()
	{
		$query = "SELECT id, name, template, type, link FROM `".$this->_table['albums']."` a WHERE online='Y' AND restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
		
		if (!$this->albums)
		{
			$this->albums = array();
		}
		
		if ($this->site['frontend']['filter_bracket_album'])
		{
			foreach($this->albums as $key => $album)
			{
				if ($album->name{0} == '[' && $album->name{strlen($album->name) - 1} == ']')
				{
					unset($this->albums[$key]);
				}
			}
		}
	}
	
	/**
	 * Get all news albums.
	 * 
	 * @return Array
	 */
	function fetchNewsAlbums()
	{
		$albums = array();
		$query = "SELECT id, name, template, type, link FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='N' AND template='news' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
		
		if (!$albums)
		{
			$albums = array();
		}
		
		return $albums;
	}
	
	function loadRestrictedAlbums()
	{
		$user_id = (int) $_SESSION['session_client_shield']['id'];
		$query = "SELECT id, name, template FROM `".$this->_table['albums']."` WHERE user_id='".$user_id."' AND online='Y' AND restricted='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->restricted_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
		
		if (!$this->restricted_albums)
		{
			$this->restricted_albums = array();
		}
		
		foreach($this->restricted_albums as $key => $album)
		{
			if ($album->template == 'album' && $album->countOnlineSections() == 0)
			{
				unset($this->restricted_albums[$key]);
			}
		}
	}
	
	function searchAlbum($album_q)
	{
		if (empty($album_q))
		{
			return null;
		}
		
		// search by name
		$album = null;
		$query = "SELECT id FROM `".$this->_table['albums']."` WHERE link='".$this->_db->filter($album_q)."' AND online='Y' LIMIT 1";
		if (!$this->_db->doQuery($query, $album, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Album')))
		{
			// is a number, search by id
			if (is_numeric($album_q))
			{
				$query = "SELECT id FROM `".$this->_table['albums']."` WHERE id='".$this->_db->filter($album_q)."' AND online='Y' LIMIT 1";
				if (!$this->_db->doQuery($query, $album, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Album')))
				{
					return null;
				}
			} else {
				// couldn't find this link, search for it in the links archive
				$query = "SELECT object_id FROM `".$this->_table['links']."` WHERE link='".$this->_db->filter($album_q)."' AND type='album' ORDER BY id DESC LIMIT 1";
				$album_id = null;
				$this->_db->doQuery($query, $album_id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
				
				if ($album_id !== false)
				{
					$query = "SELECT id FROM `".$this->_table['albums']."` WHERE id='".$this->_db->filter($album_id)."' LIMIT 1";
					$album = null;
					if ($this->_db->doQuery($query, $album, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'new_object', false, array('object' => 'Album')))
					{
						return $album;
					}
				}
				
				return null;
			}
		}
		
		return $album;
	}
}
?>