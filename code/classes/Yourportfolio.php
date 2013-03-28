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
 * extended Yourportfolio class for editing functionality
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Yourportfolio
{
	/**
	 * database object
	 * @var object
	 */
	var $_db;
	var $config_db;
	
	/**
	 * database tables
	 * @var array
	 */
	var $_table;
	
	/**
	 * general feedback variable
	 * @var string
	 */
	var $feedback;
	
	/**
	 * name of photographer for use in title
	 */
	var $photographer_name;
	
	/**
	 * preferences listing
	 * @var array $preferences
	 */
	var $preferences = array();
	
	/**
	 * advanced settings listing
	 * @var array $settings
	 */
	var $settings = array();
		
	/**
	 * containing back url if one is needed
	 * @var string
	 */
	var $back_url;
	
	var $title;
	var $title_url;
	
	/**
	 * menu albums containers
	 */
	var $menu_albums = array();
	var $menu_restricted_albums = array();
	
	/**
	 * albums used for switching sections and items
	 */
	var $albums = array();
	
	/**
	 * contains (default) submit url
	 * @var string
	 */
	var $save_url = 'javascript:save();';
	
	/**
	 * is upload link shown?
	 * @var boolean
	 */
	var $upload_link = true;
	
	/**
	 * new newsitem link
	 * @var boolean
	 */
	var $news_link = false;
	
	/**
	 * show an upload dir link
	 * @var boolean
	 */
	var $upload_dir = false;
	
	/**
	 * available templates
	 */
	var $templates;
	
	/**
	 * labels which can be customized with a labels.ini file
	 */
	var $labels = array();
	
	/**
	 * runtime vars:
	 * 		user_id for owner of current site
	 * 		iconset
	 */
	var $user_id			= null;
	var $iconset			= 'default';
	var $settings_cache		= null;
	var $display_language	= 'nl_NL';
	
	var $runtime			= array();
	
	var $multilingual	= false;
	var $languages		= array();
	var $default_language;
	
	/**
	 * objects needed to run this component
	 */
	var $_system;
	var $_canvas;

	/**
	 * constructor (PHP5)
	 * make session info available in the object
	 * 
	 * @uses $_SESSION['session_shield']
	 *
	 * @assigns $session (containing values from session_shield)
	 */
	function __construct()
	{
		if (isset($_SESSION['session_shield']))
		{
			$this->session = $_SESSION['session_shield']; // copy the info, so we can close the session
		}
		
		// set default runtime values.
		$this->runtime = array(
						'communication' => array('amfphp' => false),
						'debug' => array('yourportfolio' => false, 'amfphp' => false),
						);
		
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		$config_db_path = SETTINGS.'db/config.db';
		if (file_exists($config_db_path) && is_writable($config_db_path))
		{
			$this->config_db = new SQLite3($config_db_path, SQLITE3_OPEN_READWRITE);
		}
		
		global $system;
		$this->_system = &$system;
		
		global $canvas;
		$this->_canvas = &$canvas;
		
		$this->loadSettings();
		
		$this->setLocaleSettings();

		// process updates if necessary
		if (isset($_GET['update']))
		{
			require(CODE.'program/update.php');
		}
		
		$this->loadPhotographerName();
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
	 * this function is called from startup.php for handling forms
	 * it can also trigger errors, but doesn't mean it contains a bug
	 * read the message in the log file to find the reason for the error
	 *
	 */
	function handleInput($input)
	{
		switch( isset($input['action']) ? $input['action'] : 'none' ) // checks to see if action is given, else it defaults to 'none'
		{
			case('guestbook_quick_save'):
				
				require_once(CODE.'classes/Guestbook.php');
				$guestbook = new Guestbook($input['guestbook']['album_id']);
				$guestbook->setMessagesOnline($input['guestbook']['online']);
				
				break;
			case('guestbook_message_save'):
				
				require_once(CODE.'classes/Guestbook.php');
				$guestbook = new Guestbook($input['guestbook']['album_id']);
				
				$message = new GuestbookMessage();
				$message->save($input['guestbook']);
				
				$guestbook->updateGuestbookXML();
				
				break;
			case('guestbook_message_delete'):
				
				require_once(CODE.'classes/Guestbook.php');
				$guestbook = new Guestbook($input['delete']['album_id']);
				$guestbook->deleteMessage($input['delete']['id']);
				
				break;
			case('album_save'):
				
				$album = new Album();
				$album->save($input['album'], $this->_system->postedFiles('album'));

				break;
			case('album_delete'):
				
				$album = new Album();
				$album->id = $input['delete']['id'];
				$album->destroy();
				
				Album::rePosition();
				
				break;
			case('album_file_delete'):
				
				$album = new Album();
				$album->id = $input['delete']['id'];
				$album->destroyFile($input['delete']['file_id']);
				
				break;
			case('sections_position_save'):
				
				$album = new Album();
				$album->id = $input['id'];
				$album->saveChildPositions($input['ids']);
				
				break;
			case('section_save'):
				
				$section = new Section();
				$section->save($input['section'], $this->_system->postedFiles('section'));
				
				break;
			case('section_delete'):
				
				$section = new Section();
				$section->id = $input['delete']['id'];
				$section->destroy();
				
				break;
			case('section_file_delete'):
				
				$section = new Section();
				$section->id = $input['delete']['id'];
				$section->destroyFile($input['delete']['file_id']);
				
				break;
			case('items_position_save'):
				
				$section = new Section();
				$section->id = $input['id'];
				$section->saveChildPositions($input['ids']);
				
				break;
			case('item_save'):
				
				$item = new Item();
				$item->save($input['item'], $this->_system->postedFiles('item'));
				
				break;
			case('item_delete'):
				
				$item = new Item();
				$item->id = $input['delete']['id'];
				$item->destroy();
				
				break;
			case('item_file_delete'):
				
				$item = new Item();
				$item->id = $input['delete']['id'];
				$item->destroyFile($input['delete']['file_id']);
				
				break;
			case('item_copy'):
				
				$item = new Item();
				$item->id = $input['copy']['id'];
				$item->copyTo($input['copy']['album_section']);
				
				break;
			case('preferences_save'):
				
				unset($input['action']);
				$this->preferencesSave($input);
				
				break;
			case('advancedsettings_save'):
				
				unset($input['action']);
				$this->advancedSettingsSave($input);
				unset($input);
				
				break;
			case('client_user_save'):
				
				$user = new ClientUser();
				$user->save($input['user']);
				
				break;
			case('client_user_delete'):
				
				$user = new ClientUser();
				$user->id = $input['delete']['id'];
				$user->destroy();
				
				break;
			case('user_save'):
				
				$user = new SubUser();
				$user->save($input['user']);
				
				break;
			case('user_delete'):
				
				$user = new SubUser();
				$user->id = $input['delete']['id'];
				$user->destroy();
				
				break;
			case('tag_rename'):
				$this->tagRename( $input );
				break;
			case('tag_move'):
				$this->tagMove( $input );
				break;
			case('tag_add'):
				$this->tagAdd( $input );
				break;
			case('tag_delete'):
				$this->tagDelete( $input );
				break;
			case('group_delete'):
				$this->tagGroupDelete( $input );
				break;
			case('group_rename'):
				$this->tagGroupRename( $input );
				break;
			case('group_add'):
				$this->tagGroupAdd( $input );
				break;
			default:
				// no action or an unknown action is given, trigger error to the log
				trigger_error("Unknown action given (".$input['action'].").\n".__CLASS__."::".__FUNCTION__.".", E_USER_NOTICE);
		}
	}
	
	/**
	 * Renames a tag
	 */
	function tagRename( $input )
	{
		$tag_id = (int) $input['tag_id'];
		$tag_value = $this->_db->filter( $input['tag_value'] );
		if( !empty($tag_value) )
		{
			$q = sprintf( "UPDATE `%s` SET `tag`='%s' WHERE `id`=%d", $this->_table['tags'], $tag_value, $tag_id );
			$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
		} else {
			trigger_error( "blocked attempt to rename tag to null" );
		}
	}
	
	/**
	 * Moves a tag to a different group
	 */
	function tagMove( $input )
	{
		$tag_id = (int)$input['tag_id'];
		$group_id = (int)$input['group_id'];
		$q = sprintf( "UPDATE `%s` SET `group_id`=%d WHERE `id`=%d", $this->_table['tags'], $group_id, $tag_id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
	}
	
	/**
	 * Deletes a tag
	 */
	function tagDelete( $input )
	{
		$id = (int)$input['tag_id'];
		$q = sprintf( "DELETE FROM `%s` WHERE `id`=%d", $this->_table['tags'], $id);
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		$q = sprintf( "DELETE FROM `%s` WHERE `tag_id`=%d", $this->_table['section_tags'], $id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		$q = sprintf( "DELETE FROM `%s` WHERE `tag_id`=%d", $this->_table['item_tags'], $id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
	}
	
	/**
	 * Adds a new tag
	 */
	function tagAdd( $input )
	{
		$group_id = (int)$input['group_id'];
		$tag_value = str_replace('"', "''", $input['tag_value']);
		$tag_value = $this->_db->filter( $tag_value );
		$q = sprintf( "INSERT INTO `%s` SET `group_id`=%d, `tag`='%s'", $this->_table['tags'], $group_id, $tag_value );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
	}
	
	/**
	 * Delete a tag group and its contents
	 */
	function tagGroupDelete( $input )
	{
		$id = (int)$input['group_id'];
		$q = sprintf( "SELECT `id` FROM `%s` WHERE `group_id`=%d", $this->_table['tags'], $id );
		$tags = array();
		$this->_db->doQuery( $q, $tags, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false );
		foreach( $tags as $t )
		{
			$q = sprintf( "DELETE FROM `%s` WHERE `tag_id`=%d", $this->_table['section_tags'], $t );
			$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
			$q = sprintf( "DELETE FROM `%s` WHERE `tag_id`=%d", $this->_table['item_tags'], $t );
			$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		}
		$q = sprintf( "DELETE FROM `%s` WHERE `group_id`=%d", $this->_table['tags'], $id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
		$q = sprintf( "DELETE FROM `%s` WHERE `id`=%d", $this->_table['tag_groups'], $id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false );
	}	
	
	/**
	 * Adds a tag group
	 */
	function tagGroupAdd( $input )
	{
		$name = str_replace('"', "''", $input['name']);
		$name = $this->_db->filter( $name );
		$q = sprintf( "INSERT INTO `%s` SET `name`='%s'", $this->_table['tag_groups'], $name );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
	}
	
	/**
	 * Renames a tag group
	 */
	function tagGroupRename( $input )
	{
		$id = (int)$input['group_id'];
		$name = $this->_db->filter( $input['new_name'] );
		
		$q = sprintf( "UPDATE `%s` SET `name`='%s' WHERE `id`=%d", $this->_table['tag_groups'], $name, $id );
		$this->_db->doQuery($q, $sink, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );	
	}
	
	/**
	 * retrieves main information of current user
	 * and retrieves the tablename to be used
	 *
	 * @uses _check_id()
	 * @uses $_db,  $_table
	 * @uses photobooks_load_menu()
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
		
		$this->runtime = parse_ini_file(SETTINGS.'runtime.ini', true);
		
		// required settings
		$this->user_id			= $this->runtime['runtime']['user_id'];
		$this->settings_cache	= $this->runtime['runtime']['settings_cache'];
		
		// optional settings
		if (isset($this->runtime['runtime']['iconset']))
		{
			$this->iconset = $this->runtime['runtime']['iconset'];
		}
		
		if (isset($this->runtime['runtime']['display_language']))
		{
			$this->display_language = $this->runtime['runtime']['display_language'];
		}
		
		// check if sqlite settings contains display language setting
		if ($this->config_db)
		{
				// check if settings table exists or not
				if ($this->config_db->querySingle('SELECT COUNT(name) FROM sqlite_master WHERE ((type = \'table\') AND (name = \'settings\'))') == 1)
				{
					$language = $this->config_db->querySingle('SELECT "value" FROM "settings" WHERE "name"=\'language\' LIMIT 1');
					if ($language)
					{
						$this->display_language = $language;
					} else {
						$stmt = $this->config_db->prepare('INSERT INTO "settings" ("name", "value") VALUES (:name, :value)');
						$stmt->bindValue(':name', 'language', SQLITE3_TEXT);
						$stmt->bindValue(':value', $this->display_language, SQLITE3_TEXT);
						$result = $stmt->execute();
					}
				}
		}
		
		// set defaults if not set.
		if (!isset($this->runtime['communication']))
		{
			$this->runtime['communication'] = array();
		}
		
		if (!isset($this->runtime['communication']['amfphp']))
		{
			$this->runtime['communication']['amfphp'] = false;
		}
		//
		
		$this->templates = array(
				'album'		=> array('name' => _('Album'), 'value' => 'album', 'section' => 'section'),
				'text' 		=> array('name' => _('Tekst'), 'value' => 'text', 'section' => 'section'),
				'news'		=> array('name' => _('Nieuws'), 'value' => 'news', 'section' => 'newsitem'),
				'contact'	=> array('name' => _('Contact'), 'value' => 'contact', 'section' => 'section'),
				'guestbook'	=> array('name' => _('Gastenboek'), 'value' => 'guestbook', 'section' => null),
				);
		
		// load the settings
#		$this->advancedSettingsLoad();
		
		// count number of photos (online)
		// bad query, returns zero (0)
#		$query = "SELECT COUNT(*) FROM ".$this->_table['items'].", ".$this->_table['sections']." WHERE ".$this->_table['items'].".online='Y' AND ".$this->_table['sections'].".online=".$this->_table['items'].".photobook_id AND ".$this->_table['sections'].".online='Y'";
		// count number of photos (all)
#		$query = "SELECT COUNT(*) FROM ".$this->_table['items']."";
#		$this->_db->doQuery($query, $this->online_photos, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "value", true);
	}
	
	/**
	 * Load available tags, in groups
	 */
	function loadTags()
	{
		$query = sprintf( "SELECT `id`,`name` FROM `%s` ORDER BY `name`", $this->_table['tag_groups'] );
		$groups = array();
		$this->_db->doQuery($query, $groups, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
		$this->tags = array();
		
		if ($groups === false)
			$groups = array();
		
		foreach( $groups as $g )
		{
			$group = array('id' => $g['id'], 'name' => $g['name'], 'tags' => array() );
			$query = sprintf( "SELECT `id`,`group_id`, `tag` FROM `%s` WHERE `group_id`=%d ORDER BY `tag`", $this->_table['tags'], $g['id'] );
			$tags = array();
			$this->_db->doQuery( $query, $group['tags'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
			$this->tags[] = $group;
		}
					
	}
	
	/**
	 * Set locale related settings.
	 * 
	 * @return Void
	 */
	function setLocaleSettings()
	{
		$lang = $this->display_language;
		
		@putenv("LANG=$lang");
		if (!setlocale(LC_ALL, $lang))
		{
			//trigger_error('Locale '.$lang.' not found');
		}
		
		// language domains
		bindtextdomain('backend', LOCALE);
		bindtextdomain('newsletter', LOCALE);
		
		// current domain
		textdomain('backend');
	}
	
	/**
	 * sets the name of the photographer
	 *
	 */
	function loadPhotographerName()
	{
		$query = "SELECT CONCAT_WS(' ', firstname, lastname) FROM `".$this->_table['users']."` WHERE id='".$this->user_id."' LIMIT 1";
		$this->_db->doQuery($query, $this->photographer_name, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	}
	
	function parseCustomFields()
	{
		$this->custom_fields = array();
		
		if (empty($this->preferences['custom_fields']))
			return;
		
		$fields = explode("\r\n", $this->preferences['custom_fields']);
		foreach($fields as $field)
		{
			// empty line
			if (empty($field))
			{
				continue;
			}
			
			$tmp_field = explode(' :: ', $field);
			$tmp_field['key'] = $tmp_field[0];
			$tmp_field['label'] = $tmp_field[1];
			$tmp_field['length'] = $tmp_field[2];
			if (isset($tmp_field[3]))
				$tmp_field['owner'] = $tmp_field[3];
			if (isset($tmp_field[4]))
				$tmp_field['type'] = $tmp_field[4];
			$this->custom_fields[] = $tmp_field;
		}
	}
	
	/**
	 * load advanced settings
	 */
	function advancedSettingsLoad($basicOnly = false)
	{
		// default settings
		if ( file_exists(CODE.'settings/site.ini') )
		{
			// parse ini file
			$this->preferences = array_merge($this->preferences, parse_ini_file(CODE.'settings/site.ini'));
		} else {
			trigger_error('default site.ini not found...', E_USER_NOTICE);
		}

		if ( file_exists(SETTINGS.'site.ini') )
		{
			// parse ini file
			$this->preferences = array_merge($this->preferences, parse_ini_file(SETTINGS.'site.ini'));
		}
		
		if (isset($this->preferences['album_types_with_text_sections']))
		{
			$this->preferences['album_types_with_text_sections'] = explode(',', $this->preferences['album_types_with_text_sections']);
		} else {
			$this->preferences['album_types_with_text_sections'] = array();
		}
		
		if (isset($this->preferences['album_ids_with_text_sections']))
		{
			$this->preferences['album_ids_with_text_sections'] = explode(',', $this->preferences['album_ids_with_text_sections']);
		} else {
			$this->preferences['album_ids_with_text_sections'] = array();
		}
		
		if (isset($this->preferences['album_types_with_text_items']))
		{
			$this->preferences['album_types_with_text_items'] = explode(',', $this->preferences['album_types_with_text_items']);
		} else {
			$this->preferences['album_types_with_text_items'] = array();
		}
		
		// optional settings (overrides the one set in runtime.ini as this is the new location for these settings)
		if (isset($this->preferences['iconset']))
		{
			$this->iconset = $this->preferences['iconset'];
		}
		
		if (isset($this->preferences['display_language']))
		{
			$this->display_language = $this->preferences['display_language'];
			$this->setLocaleSettings();
		}
		
		if ($basicOnly)
			return;
		
		if ( file_exists(SETTINGS.'languages.ini') )
		{
			$language_settings = parse_ini_file(SETTINGS.'languages.ini', true);
			$this->languages = $language_settings['languages'];
			$this->default_language = $language_settings['settings']['default'];
			
			$this->multilingual = true;
			
			unset($language_settings);
		}
		
		if (!defined('YP_MULTILINGUAL'))
		{
			define('YP_MULTILINGUAL', $this->multilingual);
			$GLOBALS['YP_LANGUAGES'] = $this->languages;
			$GLOBALS['YP_DEFAULT_LANGUAGE'] = $this->default_language;
		}
		
		$preferences = array();
		$query = "SELECT settings, bg_colour, google_site_verification, google_analytics_account, `facebook_user_ids`, `facebook_app_id`, custom_fields FROM `".$this->_table['data']."` WHERE photographer_id='".$this->session['id']."'";
		$this->_db->doQuery($query, $preferences, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', true);
		
		// thumb and preview sizes
		$this->image_sizes['yourportfolio']['w'] 		= 120;
		$this->image_sizes['yourportfolio']['h'] 		= 90;
		
		$this->preferences['bg_colour']                = $preferences['bg_colour'];
		$this->preferences['google_site_verification'] = $preferences['google_site_verification'];
		$this->preferences['google_analytics_account'] = $preferences['google_analytics_account'];
		$this->preferences['facebook_user_ids']		   = $preferences['facebook_user_ids'];
		$this->preferences['facebook_app_id']		   = $preferences['facebook_app_id'];
		$this->preferences['custom_fields']            = $preferences['custom_fields'];
		
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
			 8 - RSS - alleen news
			 9 - 
			10 - has subusers
			11 - can create internal links
			12 - tags
			13 - keywords (reserved: now only in MVRDV branch)
			14 - has guestbook
			15 - automatic approval of guestbook entries
			16 - has newsletter
			17 - html only
			18 - mobile version enabled
			19 - tablet version enabled
			20 - user can add normal albums
			21 - 
			22 - restricted albums/users
			23 - 
			24 - user can edit specific template types
			25 - has custom fields
			26 - unassigned_restricted_albums_for_all
			27 - can use news template
			28 - moving items to other album/section places on position 1
			29 - items have subtitle/subname
			30 - sections have subtitle/subname
			31 - 
			----
		*/
		
		$this->settings = array();
		
		// content settings
		
		$this->settings['text_nodes']			= ($settings{1}) ? true : false;
		$this->settings['quicktime_check']		= ($settings{2}) ? true : false;
		
		$this->settings['autopublish']			= ($settings{3}) ? true : false;
		$this->settings['xml_amf_hybrid']		= ($settings{4}) ? true : false;
		$this->settings['xml_filter_items']		= ($settings{5}) ? true : false;
		$this->settings['xml_filter_news']		= ($settings{6}) ? true : false;
		
		$this->settings['rss_news_only']		= ($settings{8}) ? true : false;
		
		$this->settings['subusers']				= ($settings{10}) ? true : false;
		$this->settings['internal_links']		= ($settings{11}) ? true : false;
		
		$this->settings['tags']					= ($settings{12}) ? true : false;
		
		$this->settings['guestbook']			= ($settings{14}) ? true : false;
		$this->settings['guestbook_approval']	= ($settings{15}) ? true : false;
		
		$this->settings['newsletter']			= ($settings{16}) ? true : false;
		
		$this->settings['html_only']			= ($settings{17}) ? true : false;
		$this->settings['mobile']				= ($settings{18}) ? true : false;
		$this->settings['tablet']				= ($settings{19}) ? true : false;
		
		$this->settings['can_add_albums']		= ($settings{20}) ? true : false;
		
		$this->settings['restricted_albums']	= ($settings{22}) ? true : false;
		$this->settings['can_edit_types']		= ($settings{24}) ? true : false;
		$this->settings['has_custom_fields']	= ($settings{25}) ? true : false;
		$this->settings['unassigned_restricted_albums_for_all']	= ($settings{26}) ? true : false;
		$this->settings['news_templates']		= ($settings{27}) ? true : false;
		$this->settings['moving_sets_position_to_one']		= ($settings{28}) ? true : false;
		$this->settings['items_have_subname']	= ($settings{29}) ? true : false;
		$this->settings['sections_have_subname']	= ($settings{30}) ? true : false;
		
		// dirty settings/news template hack
		if (!$this->settings['news_templates'])
		{
			unset($this->templates['news']);
		}
		if (!$this->settings['guestbook'])
		{
			unset($this->templates['guestbook']);
		}
	}
	
	/**
	 * saves administration settings
	 *
	 * @param array $settings
	 * @return void
	 * @access private
	 */
	function advancedSettingsSave($settings)
	{
		$new_settings = '';
		$new_settings = str_pad($new_settings, 32, '0', STR_PAD_LEFT);
		
		$new_settings{1}  = ($settings['text_nodes']) ? 1 : 0;
		$new_settings{2}  = ($settings['quicktime_check']) ? 1 : 0;
		$new_settings{3}  = ($settings['autopublish']) ? 1 : 0;
		$new_settings{4}  = ($settings['xml_amf_hybrid']) ? 1 : 0;
		$new_settings{5}  = ($settings['xml_filter_items']) ? 1 : 0;
		$new_settings{6}  = ($settings['xml_filter_news']) ? 1 : 0;
		$new_settings{8}  = ($settings['rss_news_only']) ? 1 : 0;
		$new_settings{10} = ($settings['subusers']) ? 1 : 0;
		$new_settings{11} = ($settings['internal_links']) ? 1 : 0;
		$new_settings{12} = ($settings['tags']) ? 1 : 0;
		$new_settings{14} = ($settings['guestbook']) ? 1 : 0;
		$new_settings{15} = ($settings['guestbook_approval']) ? 1 : 0;
		$new_settings{16} = ($settings['newsletter']) ? 1 : 0;
		$new_settings{17} = ($settings['html_only']) ? 1 : 0;
		$new_settings{18} = ($settings['mobile']) ? 1 : 0;
		$new_settings{19} = ($settings['tablet']) ? 1 : 0;
		$new_settings{20} = ($settings['can_add_albums']) ? 1 : 0;
		$new_settings{22} = ($settings['restricted_albums']) ? 1 : 0;
		$new_settings{24} = ($settings['can_edit_types']) ? 1 : 0;
		$new_settings{25} = ($settings['has_custom_fields']) ? 1 : 0;
		$new_settings{26} = ($settings['unassigned_restricted_albums_for_all']) ? 1 : 0;
		$new_settings{27} = ($settings['news_templates']) ? 1 : 0;
		$new_settings{28} = ($settings['moving_sets_position_to_one']) ? 1 : 0;
		$new_settings{29} = ($settings['items_have_subname']) ? 1 : 0;
		$new_settings{30} = ($settings['sections_have_subname']) ? 1 : 0;
		
		$new_settings = bindec($new_settings);
		
		// load the settings
		$query = "UPDATE `".$this->_table['data']."` SET settings='".$new_settings."', 
													   bg_colour='".$settings['bg_colour']."', 
													   google_site_verification='".$this->_db->filter($settings['google_site_verification'])."', 
													   google_analytics_account='".$this->_db->filter($settings['google_analytics_account'])."',
													   `facebook_user_ids`='".$this->_db->filter($settings['facebook_user_ids'])."',
													   `facebook_app_id`='".$this->_db->filter($settings['facebook_app_id'])."',
													   custom_fields='".$settings['custom_fields']."'
				  WHERE photographer_id='".$this->user_id."'";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update');
		
		$this->advancedSettingsLoad();
		
		// has newsletter, check for newsletter tables and add them if not exists.
		if ($this->settings['newsletter'])
		{
			$this->table_prefix = 'yp_';
			$this->settings_dir = str_replace(array($this->table_prefix, '_albums'), '', $this->_table['albums']);
			
			require_once(CODE.'../install/installer_data/sql_newsletter.php');
			$sql = array();
			
			// check for existence of newsletter tables.
			$tables = $this->_db->getTables();
			foreach ($this->_table as $table)
			{
				if (strpos($table, '_nl_') !== false && !in_array($table, $tables))
				{
					$name = substr($table, strlen($this->table_prefix.'_'.$this->settings_dir.'_') - 1);
					if (array_key_exists($name, $this->sql))
					{
						$sql[] = $this->sql[$name];
						
						if ($name == 'nl_settings')
						{
							$sql[] = $this->sql['newsletter_settings'];
						}
					}
				}
			}
			
			foreach($sql as $query)
			{
				$result = null;
				$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			}
			unset($this->sql, $this->table_prefix, $this->settings_dir);
		}
		
		$this->updateCacheFiles();
	}
	
	/**
	 * Write a variable to a file in serialized form
	 * 
	 * @param mixed $var the variable to write
	 * @param mixed $file the file to write to
	 * @return void
	 */
	 function write_cache($var, $file)
	 {
		if ((file_exists($file) && is_writable($file)) || (!file_exists($file)))
		{
			if (!$fp = fopen($file, 'w'))
			{
				trigger_error('Failed to open cache file for writing.', E_USER_ERROR);
			}
			
			@chmod($file, 0666);
			if (!fwrite($fp, serialize($var)))
			{
				trigger_error('Failed to write to cache.', E_USER_ERROR);
			}
		} else {
			trigger_error('Failed to write to '.$file. ' for cache.', E_USER_ERROR);
		}
	 }
	 
	/**
	 * loads labels.ini file, default one first, if there is a custom labels.ini, duplicate sections will be overwritten
	 */
	function labelsLoad()
	{
		// labels are already loaded
		if (!empty($this->labels))
		{
			return;
		}
		
		// default settings
		if ( file_exists(CODE.'settings/labels.ini') )
		{
			$this->labels = parse_ini_file(CODE.'settings/labels.ini', true);
		} else {
			trigger_error('default labels.ini not found...', E_USER_NOTICE);
		}
		
		if ( file_exists(SETTINGS.'labels.ini') )
		{
			$custom_labels = array();
			$custom_labels = parse_ini_file(SETTINGS.'labels.ini', true);
			
			foreach($custom_labels as $section => $labels)
			{
				$this->labels[$section] = $labels;
			}
		}
	}
	
	/**
	 * loads names for in menu
	 *
	 * @uses _check_id()
	 * @uses $_db, $_table
	 *
	 * @assigns $menu_albums
	 *
	 * @access public
	 */
	function loadMenu()
	{
		$this->_check_id();
		
		if (!$this->session['limited'])
		{
			$query = "SELECT id, online, locked, IF(LENGTH(name) > 20, CONCAT(SUBSTRING(name FROM 1 FOR 20),'...'), name) AS name, template, type FROM `".$this->_table['albums']."` WHERE restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->menu_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
			$query = "SELECT id, online, locked, IF(LENGTH(name) > 20, CONCAT(SUBSTRING(name FROM 1 FOR 20),'...'), name) AS name, template, type FROM `".$this->_table['albums']."` WHERE restricted='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->menu_restricted_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		} else {
			$user = new SubUser();
			$user->id = $this->session['limited_id'];
			$user->load();
			
			$query = "SELECT id, online, locked, IF(LENGTH(name) > 20, CONCAT(SUBSTRING(name FROM 1 FOR 20),'...'), name) AS name, template, type FROM `".$this->_table['albums']."` WHERE id IN (SELECT `album_id` FROM `".$this->_table['subuser_album']."` WHERE `subuser_id` = ".$user->id.") AND restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->menu_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
			
			if (empty($this->menu_albums))
				$this->menu_albums = array();
			
			$this->menu_restricted_albums = array();
		}
	}
	
	function loadAlbums()
	{
		if (!$this->session['limited'])
		{
			// load all albums for normal user
			$query = "SELECT id, online, name, template FROM `".$this->_table['albums']."` WHERE restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
			
			if (empty($this->albums))
			{
				$this->albums = array();
			}
			
			foreach($this->albums as $key => $album)
			{
				$this->albums[$key]->loadSections();
			}
	
			$query = "SELECT id, online, name, template FROM `".$this->_table['albums']."` WHERE restricted='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->restricted_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
			
			if (!empty($this->restricted_albums))
			{
				foreach($this->restricted_albums as $key => $album)
				{
					$this->restricted_albums[$key]->loadSections();
				}
			}
		} else {
			// load albums which limited user can see/edit
			$user = new SubUser();
			$user->id = $this->session['limited_id'];
			$user->load();
			
			$query = "SELECT id, online, name, template FROM `".$this->_table['albums']."` WHERE id IN ('".implode("','", $user->album_ids)."') AND restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
			$this->_db->doQuery($query, $this->albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
			
			foreach($this->albums as $key => $album)
			{
				$this->albums[$key]->loadSections();
			}
		}
	}
	
	/**
	 * loads the client users
	 */
	function loadClientUsers()
	{
		$query = "SELECT id, online, name, last_login FROM `".$this->_table['client_users']."` ORDER BY name ASC";
		$this->_db->doQuery($query, $this->client_users, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'ClientUser'));
		
		if (!$this->client_users)
		{
			$this->client_users = array();
		}
	}

	/**
	 * loads the subusers
	 */
	function loadSubUsers()
	{
		$query = "SELECT id, online, name, last_login FROM `".$this->_table['subusers']."`";
		$this->_db->doQuery($query, $this->subusers, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'SubUser'));
		
		if (!$this->subusers)
		{
			$this->subusers = array();
		}
	}
	
	/**
	 * loads preferences
	 *
	 * @assign array $preferences
	 * @return void
	 */
	function preferencesLoad()
	{
		$this->_check_id();

		$query = "SELECT id, firstname, lastname, login, password FROM `".$this->_table['users']."` WHERE id='".$this->session['id']."' LIMIT 1";
		$this->_db->doQuery($query, $this->preferences, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row_merge', true);
		
		$query = "SELECT email, phone, mobile, fax, title, copyright, description, keywords FROM `".$this->_table['data']."` WHERE photographer_id='".$this->session['id']."' LIMIT 1";
		$this->_db->doQuery($query, $this->preferences, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row_merge', true);
	}
	
	/**
	 * saves preferences
	 *
	 * @param array $prefs
	 *
	 * @return void
	 *
	 * @access private
	 */
	function preferencesSave($prefs)
	{
		global $system;
		
		$this->_check_id();
		
		// to do:
		//	- check for email correctness
		//	- compare old_login to login, if different, check for duplicates
		//	- check login for forbidden characters (allow only a-z, A-Z, 0-9, _)
		//	- password_1 == password_2 ? update password : wrong password (javascript?)
		
		// users table
		$query  = "UPDATE `".$this->_table['users']."` SET firstname='".$prefs['firstname']."', lastname='".$prefs['lastname']."'";
		$this->feedback .= _("voorkeuren bewaard.")."<br>";
		
		if (!empty($prefs['password_1']))
		{
			if ( $prefs['password_1'] == $prefs['password_2'] )
			{
				$query .= ", password='".md5($prefs['password_1'])."'";
				$this->feedback .= _("wachtwoord is bewaard.")."<br>";
			} else {
				// save the rest, provide feedback
				$this->feedback .= _("wachtwoord was incorrect en dus niet bewaard, oude wachtwoord is nog actief.")."<br>";
			}
		}
		
		$result = null;
		$query .= " WHERE id='".$this->session['id']."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update');
		
		// save display language
		if ($this->config_db)
		{
			$this->config_db->exec('UPDATE "settings" SET "value"=\''.$prefs['language'].'\' WHERE "name"=\'language\'');
		}
		
		// data table
		$prefs['title'] 	  		= strip_tags($prefs['title']);
		$prefs['copyright']		= strip_tags($prefs['copyright']);
		$prefs['description']		= strip_tags($prefs['description']);
		$prefs['keywords']		= strip_tags($prefs['keywords']);
		
		$query = new Query();
		$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
		$query->setTable($this->_table['data']);
		
		$query->addValue('email', $prefs['email']);
		$query->addValue('phone', $prefs['phone']);
		$query->addValue('mobile', $prefs['mobile']);
		$query->addValue('fax', $prefs['fax']);
		$query->addValue('title', $prefs['title']);
		$query->addValue('copyright', $prefs['copyright']);
		$query->addValue('description', $prefs['description']);
		$query->addValue('keywords', $prefs['keywords']);
		
		$query->addWhere('photographer_id', $this->session['id']);
		
		$query->setQuery("UPDATE `%s` SET email='%s', phone='%s', mobile='%s', fax='%s', title='%s', copyright='%s', description='%s', keywords='%s' WHERE photographer_id='%d' LIMIT 1");
		$this->_db->doQuery($query, $result);
		
		$this->updateCacheFiles();
		
		$system->relocate($system->thisFile());
	}
	
	function updateCacheFiles()
	{
		if (empty($this->settings_cache))
			return;
		
		$this->advancedSettingsLoad();
		
		// settings.dat
		$file = SETTINGS.$this->settings_cache.'settings.dat';
		$this->write_cache($this->settings, $file);
		
		// preferences.dat
		$file = SETTINGS.$this->settings_cache.'preferences.dat';
		$prefs = array();
		$query = "SELECT d.description, d.keywords, d.copyright, d.title FROM `".$this->_table['data']."` d WHERE d.photographer_id = '".$this->user_id."' LIMIT 1";
		$this->_db->doQuery($query, $prefs, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
		$prefs = array(
				'bg_colour'                => $this->preferences['bg_colour'],
				'google_site_verification' => $this->preferences['google_site_verification'],
				'google_analytics_account' => $this->preferences['google_analytics_account'],
				'facebook_user_ids'		   => $this->preferences['facebook_user_ids'],
				'facebook_app_id'		   => $this->preferences['facebook_app_id'],
				'custom_fields'            => $this->preferences['custom_fields'],
				'description'              => $prefs['description'],
				'keywords'                 => $prefs['keywords'],
				'copyright'                => $prefs['copyright'],
				'title'                    => $prefs['title']
		);
		$this->write_cache($prefs, $file);
	}
	
	/**
	 * function for id in session check so that not other rows are overwritten
	 *
	 * @access private
	 */
	function _check_id()
	{
		if (!isset($this->session['id']) || empty($this->session['id']))
		{
			// bail out
			trigger_error('session id not ok', E_USER_ERROR);
			$this->_system->relocate($this->_system->file."?logoff=yes");
		}
	}
	
	function clearServicesCache()
	{
		$dir = SETTINGS.$this->settings_cache.'service/';
		if (!file_exists($dir))
			return;
		
		if ($handle = opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file{0} == '.')
					continue;
				
				unlink($dir.$file);
			}
		}
		
		closedir($handle);
	}
	
	/**
	 * update the xml file
	 *
	 */
	function update_xml()
	{
		// do not create or update the xml file when:
		// - communication is set by amfphp and not a hybrid
		// or
		// - site is html only
		if ( ($this->runtime['communication']['amfphp']) || $this->settings['html_only'])
		{
			if ($this->settings['xml_amf_hybrid'])
			{
				$this->clearServicesCache();
			} else {
				return;
			}
		}
		
		$GLOBALS['amf_hybrid'] = $this->settings['xml_amf_hybrid'];
		$GLOBALS['xml_filter_items'] = $this->settings['xml_filter_items'];
		$GLOBALS['xml_filter_news'] = $this->settings['xml_filter_news'];
		
		$this->preferencesLoad();
		
		$query = "SELECT id, name, text, template, type, link FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='N' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$this->_db->doQuery($query, $this->xml_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (YP_MULTILINGUAL)
		{
			$xml_file = (file_exists(SETTINGS.'yourportfolio_multilingual.xml')) ? SETTINGS.'yourportfolio_multilingual.xml' : XML.'yourportfolio_multilingual.xml';
		} else {
			$xml_file = (file_exists(SETTINGS.'yourportfolio.xml')) ? SETTINGS.'yourportfolio.xml' : XML.'yourportfolio.xml';
		}
		
		/*
		ob_start();
		require($xml_file);
		$gzip_contents = ob_get_contents();
		ob_end_clean();
		
		$gzip_size = strlen($gzip_contents);
		$gzip_crc = crc32($gzip_contents);
		
		$gzip_contents = gzcompress($gzip_contents, 6);
		$gzip_contents = substr($gzip_contents, 0, strlen($gzip_contents) - 4);
		
		$contents  = '\x1f\x8b\x08\x00\x00\x00\x00\x00';
		$contents .= $gzip_contents;
		$contents .= pack('V', $gzip_crc);
		$contents .= pack('V', $gzip_size);
		*/
		
		require_once(XML.'XMLUtil.php');

		ob_start();
		require($xml_file);
		$contents = ob_get_contents();
		ob_end_clean();
		
		$file = DATA_DIR.'yourportfolio.xml';
		
		if (file_exists($file) && !is_writeable($file))
		{
			trigger_error('File is not writable ('.$file.'). '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		if (!$fp = fopen($file, 'w'))
		{
			trigger_error('Failed to create/open file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		@chmod($file, 0666);
		
		if (!fwrite($fp, $contents))
		{
			trigger_error('Failed to write to file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		fclose($fp);
	}
	
	/**
	 * creates a google sitemap in the site root
	 */
	function createGoogleSitemap()
	{
#		$file = DATA_DIR.'sitemap.xml.gz';
		$file = SITEROOT_DIR.'sitemap.xml';
		
		if (!file_exists($file) || !is_writeable($file))
		{
#			trigger_error('Please create the sitemap.xml file in the site root', E_USER_WARNING);
			return;
		}
		
		$query = "SELECT id, name, text, template, type, DATE_FORMAT(modified, '%Y-%m-%d') AS modified, link FROM `".$this->_table['albums']."` WHERE online='Y' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		$sitemap_albums = array();
		$this->_db->doQuery($query, $sitemap_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Album'));
		
		if (empty($sitemap_albums))
		{
			return;
		}
		
		$xml_file = (file_exists(SETTINGS.'google_sitemap.xml')) ? SETTINGS.'google_sitemap.xml' : XML.'google_sitemap.xml';
		
		ob_start();
		require($xml_file);
		$content = ob_get_contents();
		ob_end_clean();
		
#		$content = gzcompress($content);
		
		if (!$fp = fopen($file, 'w'))
		{
			trigger_error('Failed to create/open file.', E_USER_ERROR);
		}
		
#		@chmod($file, 0666);
		
		if (!fwrite($fp, $content))
		{
			trigger_error('Failed to write to file.', E_USER_ERROR);
		}
		
		fclose($fp);
	}
	
	/**
	 * permissions seem to go wrong now and then...
	 * so better make a check for it
	 *
	 * @return void
	 * @access public
	 */
	function fix_chmod()
	{
		$folders = array(THUMBS_DIR, PREVIEW_DIR, MUSIC_DIR, MOVIES_DIR, DOWNLOADS_DIR);
		
		foreach($folders as $folder)
		{
			if ($handle = opendir($folder.'/'))
			{
				while (false !== ($file = readdir($handle)))
				{
					if ($file{0} == '.')
					{
						// skip
					} else {
						$permissions = decoct(fileperms($folder.'/'.$file));
						if ($permissions != "100666")
						{
							chmod($file, 0666);
						}
					}
				}
			}
		}
	}
	
	/**
	 * parse first item from upload folder
	 * only jpg files are supported now
	 *
	 */
	function parseFirstItem($album_id, $section_id, $random_id)
	{
		if (UPLOAD_DIR === false)
		{
			trigger_error('site has no upload dir!', E_USER_NOTICE);
			return false;
		}
		
		if (empty($album_id) || empty($section_id))
		{
			trigger_error('$album_id and $section_id shouldn\'t be empty!', E_USER_NOTICE);
			return false;
		}
		
		$items = glob(UPLOAD_DIR.'{*.jpg,*.JPG}', GLOB_BRACE);
		natsort($items);
		
		if (empty($items))
		{
			return false;
		}
		
		$workitem = array_shift($items);
		
		if (!is_writable($workitem))
		{
			trigger_error('File in ftp parse folder is not writable.', E_USER_WARNING);
//			$this->_system->relocate('parser.php?error=1');
			return false;
		}
		
		$item = new Item();
		$item->init();
		$item->parseFilesSettings();
		
		$item_data = array(
						'album_id'		=> $album_id,
						'section_id'		=> $section_id,
						'name'			=> '',
						'subname'		=> '',
						'text_original'	=> '',
						'random_id'		=> $random_id,
					 );
		$first_file_id = array_keys($item->files_settings);
		$first_file_id = $first_file_id[0];
		$item_files = array(
						$first_file_id	=> $this->_system->getFileInfo($workitem),
					  );
		
		$item->save($item_data, $item_files, false);
		
		return $item->id;
	}
	
	/**
	 * fetches the contents of the parse folder
	 * 
	 * @return array
	 */
	function getParseFolder()
	{
		if (UPLOAD_DIR === false)
		{
			trigger_error('site has no upload dir!', E_USER_NOTICE);
			return false;
		}
		$contents_tmp = glob(UPLOAD_DIR.'{*.jpg,*.JPG}', GLOB_BRACE);
		natsort($contents_tmp);
		
		$contents = array();
		if (!empty($contents_tmp))
		{
			foreach($contents_tmp as $file)
			{
				$contents[] = str_replace(array('\''), '', substr($file, strrpos($file,'/') + 1));
			}
		}
		return $contents;
	}
	
	function validateInstallation()
	{
		global $messages;
		
		// make sure tmp directory is readable.
		clearstatcache();
		if (!is_writable(CODE.'tmp'))
		{
			$messages->add(sprintf(_('De %s directory is niet schrijfbaar.'), 'code/tmp'), MESSAGE_ERROR);
		}
	}
	
	function checkForUpdate()
	{
		$version = '';
		$query = "SELECT user_version AS user FROM `".$this->_table['data']."` WHERE photographer_id='".$this->user_id."'";
		$this->_db->doQuery($query, $version, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		if (!defined('USER_VERSION'))
			require(CODE.'program/version.php');
		
		return (version_compare(USER_VERSION, $version) > 0);
	}
	
	/**
	 * get version information from database
	 *
	 */
	function version()
	{
		$version = '';
		$query = "SELECT core_version AS core, user_version AS user FROM `".$this->_table['data']."` WHERE photographer_id='".$this->user_id."'";
		$this->_db->doQuery($query, $version, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
		
		if (strcmp($version['core'], $version['user']) == 0)
			$return = $version['core'];
		else
			$return = $version['core'].' / '.$version['user'];
		
		return $return;
	}
}
?>