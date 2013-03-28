<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2008 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * update file (is included from within yourportfolio object)
 *
 * @package yourportfolio
 * @subpackage Update
 */

if (strtolower(get_class($this)) != 'yourportfolio') // only run code when called from within the yourportfolio class, otherwise continue calling script
{
	return;
}

global $messages;

$query = "SELECT core_version, user_version FROM `".$this->_table['data']."` WHERE photographer_id = '".$this->user_id."'";
$version = false;
$this->_db->doQuery($query, $version, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);

if (!$version) // nothing found?
{
	trigger_error('No version information found!', E_USER_NOTICE);
	return;
}

// overwrite version info to force update a specific version
if (isset($_GET['version']))
{
	$version['core_version'] = $_GET['version'];
	$version['user_version'] = $_GET['version'];
}

require(CODE.'program/version.php');

$update_core_to = CORE_VERSION;
$update_user_to = USER_VERSION;

$settings = array();

$config_db = $this->config_db;
if ($config_db)
{
	// check if settings table exists or not
	if ($config_db->querySingle('SELECT COUNT(name) FROM sqlite_master WHERE ((type = \'table\') AND (name = \'settings\'))') == 0)
	{
		// create settings table
		// uses default values (as they are the same for every installation before this settings where saved.
		
		$settings = $config_db->querySingle('SELECT "realname" FROM "tables" LIMIT 1');
		$settings = explode('_', $settings);
		$settings = $settings[1];
		
		$config_db->exec('CREATE TABLE IF NOT EXISTS "settings" ("name" STRING, "value" STRING)');
		$stmt = $config_db->prepare('INSERT INTO "settings" ("name", "value") VALUES (:name, :value)');
		
		$data = array();
		$data[] = array('prefix', 'yp');
		$data[] = array('divider', '_');
		$data[] = array('settings', $settings);
		
		$config_db->exec('BEGIN TRANSACTION');
		foreach ($data as $row)
		{
			$stmt->bindValue(':name', $row[0], SQLITE3_TEXT);
			$stmt->bindValue(':value', $row[1], SQLITE3_TEXT);
			
			$result = $stmt->execute();
		}
		$config_db->exec('COMMIT TRANSACTION');
		
		unset($data);
	}
	
	$settings = array();
	$result = $config_db->query('SELECT "name", "value" FROM "settings"');
	while ($row = $result->fetchArray(SQLITE3_ASSOC))
	{
		$settings[$row['name']] = $row['value'];
	}
	
	$settings['table_prefix'] = $settings['prefix'].$settings['divider'].$settings['settings'].$settings['divider'];
}

/*
	database queries related to a release are put one version before the current 
*/

// update core first

$core_columns = $this->_db->getColumns($this->_table['data']);

$sql = array();
switch($version['core_version'])
{
	case('2.0.0'): // all versions before 2.3.4, as database changes where not documented by version
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `copyright` TEXT AFTER `title`";
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `description` TEXT, ADD `keywords` TEXT";
		$sql[] = "ALTER TABLE `".$this->_table['data']."` CHANGE `contact_email` `email` VARCHAR( 250 ) DEFAULT NULL";
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `phone` VARCHAR( 25 ) AFTER `email` ADD `fax` VARCHAR( 25 ) AFTER `phone`";
		$sql[] = "ALTER TABLE `".$this->_table['domains']."` ADD `first_domain` INT AFTER `domain`";
	case('2.3.4'):
	case('2.3.5'):
	case('2.3.6'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `spwidth` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `downloadable_photos` , ADD `spheight` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `spwidth`";
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `bg_colour` VARCHAR( 6 ) DEFAULT 'FFFFFF' NOT NULL";
	case('2.3.7'):
	case('2.3.8'):
	case('2.3.9'):
	case('2.3.10'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `mobile` VARCHAR( 25 ) AFTER `phone`";
	case('2.3.11'):
	case('2.3.12'):
	case('2.3.13'):
	case('2.3.14'):
	case('2.4.0'):
	case('2.4.0a'):
	case('2.4.0b'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `upload_dir` VARCHAR( 250 )";
	case('2.5.0'):
	case('2.5.1'):
	case('2.5.2'):
		#$this->advancedSettingsLoad();
		#$this->settings['can_add_albums'] = true;
		#$this->advancedSettingsSave(array_merge($this->preferences, $this->settings));
		trigger_error('set \'can add albums\' setting to correct value!', E_USER_NOTICE);
	case('2.5.3'):
	case('2.5.3a'):
	case('2.5.4'):
	case('2.5.5'):
		trigger_error('update manually: ini files for yourportfolio in '.CODE.'settings/', E_USER_NOTICE);
	case('2.5.6'):
	case('2.5.7'):
	case('2.5.7a'):
	case('2.5.8'):
	case('2.5.9'):
	case('2.5.9a'):
	case('2.5.10'):
	case('2.5.11'):
	case('2.5.12'):
	case('2.5.13'):
	case('2.5.14'):
	case('2.5.15'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` DROP `xml_file`";
		//$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `core_version` VARCHAR( 30 ) , ADD `user_version` VARCHAR( 30 )";
	case('2.5.16'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `apwidth` SMALLINT( 5 ) UNSIGNED NOT  NULL  AFTER `downloadable_photos`, ADD `apheight`  SMALLINT( 5  ) UNSIGNED NOT  NULL  AFTER `apwidth`";
	case('2.5.17'):
	case('2.5.18'):
		$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `custom_fields` MEDIUMTEXT AFTER `upload_dir`";
	case('2.5.19'):
	case('2.5.20'):
	case('2.5.20a'):
	case('2.5.20b'):
	case('2.5.20c'):
	case('2.5.20d'):
	case('2.5.20e'):
	case('2.5.22'):
	case('2.6.0'):
	case('2.6.1'):
	case('2.6.2'):
	case('2.6.3'):
	case('2.6.4'):
	case('2.6.5'):
	case('2.7.0'):
	case('2.7.1'):
	case('2.7.2'):
	case('2.7.3'):
	case('2.7.4'):
	case('2.7.5'):
	case('2.7.6'):
	case('2.7.7'):
	case('2.7.8'):
	case('2.7.9'):
	case('2.7.10'):
	case('2.7.11'):
	case('2.7.12'):
	case('2.7.13'):
	case('2.8.0'):
	case('2.8.1'):
	case('2.8.2'):
	case('2.8.3'):
	case('2.8.4'):
		if (!in_array('google_site_verification', $core_columns))
		{
			$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `google_site_verification` VARCHAR(75) NULL AFTER `bg_colour`";
			$core_columns[] = 'google_site_verification';
		}
	case('2.8.5'):
	case('2.8.6'):
	case('2.8.7'):
		
		// before 2.9.0
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['challenges']."` (
		 `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		 `challenge` VARCHAR( 32 ) NOT NULL,
		 `user_id` INT UNSIGNED NOT NULL,
		 `created` DATETIME NOT NULL default '0000-00-00 00:00:00'
		) ENGINE=MyISAM;";
	case('2.9.0'):
	case('2.9.1'):
	case('2.9.2'):
	case('2.9.3'):
	case('2.9.4'):
	case('2.9.5'):
	case('2.9.6'):
	case('2.9.7'):
	case('2.9.8'):
	case('2.9.9'):
	case('2.9.10'):
	case('2.9.11'):
		if (!in_array('google_analytics_account', $core_columns))
		{
			$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `google_analytics_account` VARCHAR(75) NULL AFTER `google_site_verification`";
			$core_columns[] = 'google_analytics_account';
		}
	case('2.9.12'):
	case('2.9.13'):
	case('2.9.14'):
	case('2.9.15'):
	case('2.9.16'):
	case('2.9.17'):
	case('2.9.17.1'):
	case('2.9.18'):
	case('2.9.19'):
	case('2.9.20'):
	case('2.9.21'):
		// pre 2.10.x
	case('2.10.0'):
		if (!in_array('facebook_user_ids', $core_columns))
		{
			$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `facebook_user_ids` VARCHAR(250) NULL AFTER `google_analytics_account`";
			$core_columns[] = 'facebook_user_ids';
		}
		if (!in_array('facebook_app_id', $core_columns))
		{
			$sql[] = "ALTER TABLE `".$this->_table['data']."` ADD `facebook_app_id` VARCHAR(250) NULL AFTER `facebook_user_ids`";
			$core_columns[] = 'facebook_app_id';
		}
	case('2.10.1'):
	case('2.10.2'):
	case('2.10.2.1'):
	case('2.10.2.2'):
	case('2.10.2.3'):
	case('2.10.3'):
	default:
		$sql[] = "UPDATE `".$this->_table['data']."` SET `core_version`='".$update_core_to."'";
		break;
}

foreach($sql as $query)
{
	$result = null;
	$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
}

// update user tables
$sql = array();
switch($version['user_version'])
{
	case('2.0.0'):
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `type` ENUM( 'image' , 'music' , 'video' , 'ext_video' ) DEFAULT 'image' NOT NULL AFTER `position`";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `source_gsm` VARCHAR( 250 )";
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `locked` ENUM( 'N' , 'Y' ) DEFAULT 'N' NOT NULL AFTER `position`";
	case('2.3.4'):
	case('2.3.5'):
	case('2.3.6'):
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `source_full` `source_preview` VARCHAR( 250 ) DEFAULT NULL";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `pwidth` INT DEFAULT '0' NOT NULL AFTER `source_preview` , ADD `pheight` INT DEFAULT '0' NOT NULL AFTER `pwidth`";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `pwidth` `pwidth` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL , CHANGE `pheight` `pheight` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL";
	case('2.3.7'):
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `subname` VARCHAR( 250 ) AFTER `name`";
	case('2.3.8'):
	case('2.3.9'):
	case('2.3.10'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `type` TINYINT( 2 ) UNSIGNED";
	case('2.3.11'):
	case('2.3.12'):
	case('2.3.13'):
	case('2.3.14'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `restricted` ENUM( 'N' , 'Y' ) DEFAULT 'N' NOT NULL AFTER `locked`";
		$sql[] = "CREATE TABLE `".$this->_table['client_users']."` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `online` ENUM( 'Y' , 'N' ) DEFAULT 'Y' NOT NULL , `name` VARCHAR( 200 ) , `login` VARCHAR( 100 ) , `password` VARCHAR( 100 ) , PRIMARY KEY ( `id` ) )";
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `user_id` INT AFTER `restricted`";
		#$sql[] = "CREATE TABLE `yp_0011_album_user` ( `album_id` INT UNSIGNED NOT NULL , `user_id` INT UNSIGNED NOT NULL )";
		#$sql[] = "ALTER TABLE `yp_0011_album_user` ADD PRIMARY KEY ( `album_id` , `user_id` ) ";
	case('2.4.0'):
	case('2.4.0a'):
	case('2.4.0b'):
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `random_id` SMALLINT";
		$sql[] = "ALTER TABLE `".$this->_table['client_users']."` ADD `last_login` DATETIME NOT NULL";
		$sql[] = "ALTER TABLE `".$this->_table['client_users']."` ADD `previous_login` DATETIME NOT NULL AFTER `password`";
	case('2.5.0'):
	case('2.5.1'):
	case('2.5.2'):
	case('2.5.3'):
	case('2.5.3a'):
	case('2.5.4'):
	case('2.5.5'):
	case('2.5.6'):
	case('2.5.7'):
	case('2.5.7a'):
	case('2.5.8'):
	case('2.5.9'):
	case('2.5.9a'):
	case('2.5.10'):
	case('2.5.11'):
	case('2.5.12'):
	case('2.5.13'):
	case('2.5.14'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` CHANGE `position` `position` SMALLINT UNSIGNED DEFAULT '0' NOT NULL ";
		trigger_error('update manually: ini files for the site in '.SETTINGS);
	case('2.5.15'):
	case('2.5.16'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `source_preview` VARCHAR( 250 ) AFTER `text`, ADD `pwidth` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `source_preview` , ADD `pheight` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `pwidth`";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `is_selection` ENUM( 'N' , 'Y' ) DEFAULT 'N' NOT NULL AFTER `online`";
	case('2.5.17'):
	case('2.5.18'):
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `custom_data` LONGTEXT AFTER `text`";
	case('2.5.19'):
	case('2.5.20'):
	case('2.5.20a'):
	case('2.5.20b'):
	case('2.5.20c'):
	case('2.5.20d'):
	case('2.5.20e'):	
	case('2.5.22'):
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['item_files']."` ( `id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT , `item_id` int( 10 ) unsigned NOT NULL default  '0', `file_id` varchar( 10 ) NOT NULL default  '', `created` datetime NOT NULL default  '0000-00-00 00:00:00', `online` enum(  'Y',  'N' ) NOT NULL default  'Y', `name` varchar( 250 ) NOT NULL default  '', `size` mediumint( 8 ) unsigned NOT NULL default  '0', `extension` varchar( 5 ) NOT NULL default  '', `type` varchar( 100 ) NOT NULL default  '', `width` mediumint( 8 ) unsigned NOT NULL default  '0', `height` mediumint( 8 ) unsigned NOT NULL default  '0', `basepath` varchar( 250 ) NOT NULL default  '', `path` varchar( 250 ) NOT NULL default  '', `sysname` varchar( 250 ) NOT NULL default  '',PRIMARY KEY (  `id` )) ENGINE = MYISAM";
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='video' WHERE type='ext_video'";
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='audio' WHERE type='music'";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` CHANGE `type` `type` ENUM('error','image','audio','video') NOT NULL DEFAULT 'image'";
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='audio' WHERE type='' OR type IS NULL";
	case('2.6.0'):
		// still from 2.5.22, but now we are sure that the site is already converted (are we?), and it is not safe to drop these columns
		//$sql[] = "ALTER TABLE  `".$this->_table['items']."` DROP  `source_thumb`, DROP  `twidth` , DROP  `theight` , DROP  `source_preview` , DROP  `pwidth` , DROP  `pheight` , DROP  `source_file` , DROP  `fwidth` , DROP  `fheight` , DROP  `source_gsm`";
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['section_files']."` ( `id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT , `section_id` int( 10 ) unsigned NOT NULL default  '0', `file_id` varchar( 10 ) NOT NULL default  '', `created` datetime NOT NULL default  '0000-00-00 00:00:00', `online` enum(  'Y',  'N' ) NOT NULL default  'Y', `name` varchar( 250 ) NOT NULL default  '', `size` mediumint( 8 ) unsigned NOT NULL default  '0', `extension` varchar( 5 ) NOT NULL default  '', `type` varchar( 100 ) NOT NULL default  '', `width` mediumint( 8 ) unsigned NOT NULL default  '0', `height` mediumint( 8 ) unsigned NOT NULL default  '0', `basepath` varchar( 250 ) NOT NULL default  '', `path` varchar( 250 ) NOT NULL default  '', `sysname` varchar( 250 ) NOT NULL default '', PRIMARY KEY (`id`)) ENGINE=MYISAM";
	case('2.6.1'):
		$sql[] = "ALTER TABLE `".$this->_table['item_files']."` CHANGE  `file_id`  `file_id` VARCHAR( 32 ) NOT NULL";
	case('2.6.2'):
	case('2.6.3'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `created` DATETIME NOT NULL AFTER `id`, ADD `modified` DATETIME NOT NULL AFTER `created`";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `modified` `modified` DATETIME NOT NULL";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `created` DATETIME NOT NULL AFTER  `id`";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `created` DATETIME NOT NULL AFTER `section_id`, ADD `modified` DATETIME NOT NULL AFTER `created`";
		$sql[] = "UPDATE `".$this->_table['albums']."` SET created=NOW(), modified=NOW()";
		$sql[] = "UPDATE `".$this->_table['sections']."` SET created=NOW()";
		$sql[] = "UPDATE `".$this->_table['sections']."` SET modified=NOW() WHERE modified = '0000-00-00 00:00:00' OR modified IS NULL";
		$sql[] = "UPDATE `".$this->_table['items']."` SET created=NOW(), modified=NOW()";
	case('2.6.4'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `link` VARCHAR(250)";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `link` VARCHAR(250)";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `link` VARCHAR(250)";
		
		$textToolkit = $this->_system->getModule('TextToolkit');

		$query = "SELECT id, name FROM `".$this->_table['albums']."`";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (!empty($result))
		{
			foreach ($result as $album)
			{
				$sql[] = "UPDATE `".$this->_table['albums']."` SET link='".$this->_db->filter($textToolkit->normalize($album['name']))."' WHERE id='".$album['id']."' LIMIT 1";
			}
		}
		
		$query = "SELECT id, name FROM `".$this->_table['sections']."`";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (!empty($result))
		{
			foreach ($result as $section)
			{
				$sql[] = "UPDATE `".$this->_table['sections']."` SET link='".$this->_db->filter($textToolkit->normalize($section['name']))."' WHERE id='".$section['id']."' LIMIT 1";
			}
		}

		$query = "SELECT id, name FROM `".$this->_table['items']."`";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (!empty($result))
		{
			foreach ($result as $item)
			{
				$sql[] = "UPDATE `".$this->_table['items']."` SET link='".$this->_db->filter($textToolkit->normalize($item['name']))."' WHERE id='".$item['id']."' LIMIT 1";
			}
		}
		
		if ( (isset($this->_table['subusers']) && $this->_table['subusers'] != 'yp_0000_subusers') && (isset($this->_table['subuser_album']) && $this->_table['subuser_album'] != 'yp_0000_subuser_album'))
		{
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['subusers']."` (
					  id int(10) unsigned NOT NULL auto_increment,
					  site_user_id int(10) unsigned NOT NULL,
					  online enum('Y','N') NOT NULL default 'Y',
					  name varchar(200) default NULL,
					  login varchar(100) NOT NULL default '',
					  `password` varchar(100) NOT NULL default '',
					  previous_login datetime NOT NULL default '0000-00-00 00:00:00',
					  last_login datetime NOT NULL default '0000-00-00 00:00:00',
					  PRIMARY KEY  (id)
					) ENGINE=MyISAM;";
								
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['subuser_album']."` (
					  subuser_id int(10) unsigned NOT NULL,
					  album_id int(10) unsigned NOT NULL,
					  PRIMARY KEY  (subuser_id, album_id)
					) ENGINE=MyISAM;";
			
		} else {
			trigger_error('add subusers and subuser_album tables in table.ini, update is cancelled!', E_USER_ERROR);
		}
		
		if ( (isset($this->_table['album_files']) && $this->_table['album_files'] != 'yp_0000_album_files'))
		{
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['album_files']."` (
					  id int(10) unsigned NOT NULL auto_increment,
					  owner_id int(10) unsigned NOT NULL default '0',
					  file_id varchar(32) NOT NULL default '',
					  created datetime NOT NULL default '0000-00-00 00:00:00',
					  online enum('Y','N') NOT NULL default 'Y',
					  name varchar(250) NOT NULL default '',
					  size mediumint(8) unsigned NOT NULL default '0',
					  extension varchar(5) NOT NULL default '',
					  `type` varchar(100) NOT NULL default '',
					  width mediumint(8) unsigned NOT NULL default '0',
					  height mediumint(8) unsigned NOT NULL default '0',
					  basepath varchar(250) NOT NULL default '',
					  path varchar(250) NOT NULL default '',
					  sysname varchar(250) NOT NULL default '',
					  PRIMARY KEY  (id)
					) ENGINE=MYISAM;";
		} else {
			trigger_error('add album_files tables in table.ini, update is cancelled!', E_USER_ERROR);
		}
		
	case('2.6.5'):
	case('2.7.0'):
	case('2.7.1'):
	case('2.7.2'):
		$sql[] = "UPDATE `".$this->_table['albums']."` SET link=REPLACE(link, '_', '')";
		$sql[] = "UPDATE `".$this->_table['sections']."` SET link=REPLACE(link, '_', '')";
		$sql[] = "UPDATE `".$this->_table['items']."` SET link=REPLACE(link, '_', '')";
	case('2.7.3'):
		$sql[] = "ALTER TABLE `".$this->_table['item_files']."` CHANGE `item_id` `owner_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'";
		$sql[] = "ALTER TABLE `".$this->_table['section_files']."` CHANGE `section_id` `owner_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT  '0'";
	case('2.7.4'):
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['strings']."` ( 
				 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				 `owner_id` INT UNSIGNED NOT NULL,
				 `owner_type` ENUM('album', 'section', 'item' ) DEFAULT 'album' NOT NULL,
				 `field` VARCHAR(50),
				 `language` VARCHAR(50),
				 `string` TEXT,
				 `string_parsed` TEXT,
				 PRIMARY KEY (`id`)
				 ) ENGINE=MYISAM";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `text_node` ENUM('N', 'Y') DEFAULT 'N' NOT NULL AFTER `online`";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `text_node` ENUM('N', 'Y') DEFAULT 'N' NOT NULL AFTER `online`";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `template` `template` ENUM('section', 'newsitem', 'section_text_node') NOT NULL DEFAULT 'section'";
	case('2.7.5'):
	case('2.7.6'):
		// update album positions
		$query = "SELECT id FROM `".$this->_table['albums']."` WHERE position = 0 ORDER BY id ASC";
		$album_ids = null;
		$this->_db->doQuery($query, $album_ids, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		if (!empty($album_ids))
		{
			$query = "SELECT MAX(position) + 1 AS position FROM `".$this->_table['albums']."`";
			$next_position = null;
			$this->_db->doQuery($query, $next_position, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
			
			foreach($album_ids as $album_id)
			{
				$sql[] = "UPDATE `".$this->_table['albums']."` SET position = '".$next_position."' WHERE id='".$album_id."'";
				$next_position++;
			}
		}
	case('2.7.7'):
	case('2.7.8'):
	case('2.7.9'):
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='video' WHERE type='ext_video'";
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='audio' WHERE type='music'";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` CHANGE `type` `type` ENUM('error','image','audio','video') NOT NULL DEFAULT 'image'";
		$sql[] = "UPDATE `".$this->_table['items']."` SET type='audio' WHERE type='' OR type IS NULL";
	case('2.7.10'):
	case('2.7.11'):
	case('2.7.12'):
	case('2.7.13'):
		
		// create a links table for old links (must be applied for 2.8.0)
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['links']."` (
				 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				 `link` VARCHAR( 250 ) NOT NULL ,
				 `object_id` INT UNSIGNED NOT NULL ,
				 `type` ENUM(  'album',  'section',  'item' ) NOT NULL DEFAULT  'album'
				) ENGINE = MYISAM";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['contact']."` (
				  `id` int(11) NOT NULL auto_increment,
				  `date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `name` varchar(255) default NULL,
				  `address` varchar(255) default NULL,
				  `message` text,
				  PRIMARY KEY  (`id`)
				) ENGINE = MYISAM";
		
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['guestbook']."` (
				 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				 `album_id` INT UNSIGNED NOT NULL,
				 `created` DATETIME NOT NULL,
				 `modified` DATETIME NOT NULL,
				 `online` ENUM('N', 'Y') NOT NULL DEFAULT 'N',
				 `name` VARCHAR(250) NULL,
				 `email` VARCHAR(250) NULL,
				 `message` TEXT NULL,
				 `homepage` VARCHAR(250) NULL,
				 `language` VARCHAR(20) NOT NULL DEFAULT 'nl',
				INDEX (`album_id`)
				) ENGINE = MYISAM";
		
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` CHANGE `template` `template` ENUM( 'album', 'text', 'news', 'contact', 'guestbook' ) NOT NULL DEFAULT 'album'";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `label_type` TINYINT NOT NULL DEFAULT '0'";
	case('2.8.0'):
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `type` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0'";
	case('2.8.1'):
	case('2.8.2'):
	case('2.8.3'):
	case('2.8.4'):
	case('2.8.5'):
	case('2.8.6'):
	case('2.8.7'):
		// before 2.9.0
		$this->advancedSettingsLoad();
		if ($this->settings['newsletter'])
		{
			$this->table_prefix = 'yp_';
			$this->settings_dir = str_replace(array($this->table_prefix, '_albums'), '', $this->_table['albums']);
			
			require_once(CODE.'../install/installer_data/sql_newsletter.php');
			
			$sql = array_merge($this->sql, $sql);
			unset($this->sql, $this->table_prefix, $this->settings_dir);
		}
	case('2.9.0'):
		$this->advancedSettingsLoad();
		if ($this->settings['newsletter'])
		{
			$template_columns = $this->_db->getColumns($this->_table['nl_templates']);
			if (!in_array('header_text', $template_columns))
			{
				$sql[] = "ALTER TABLE `".$this->_table['nl_templates']."` ADD `header_text` TEXT NULL AFTER `footer` ,
							ADD `item_text` TEXT NULL AFTER `header_text` ,
							ADD `footer_text` TEXT NULL AFTER `item_text`";
			}
			unset($template_columns);
			
			$group_columns = $this->_db->getColumns($this->_table['nl_groups']);
			if (!in_array('visible', $group_columns))
			{
				$sql[] = "ALTER TABLE `".$this->_table['nl_groups']."` ADD `visible` ENUM('Y', 'N') NOT NULL DEFAULT 'Y'";
			}
			unset($group_columns);
		}
	case('2.9.1'):
		$this->advancedSettingsLoad();
		if ($this->settings['newsletter'])
		{
			$template_columns = $this->_db->getColumns($this->_table['nl_templates']);
			if (!in_array('itemimage_width', $template_columns))
			{
				$sql[] = "ALTER TABLE `".$this->_table['nl_templates']."` ADD `itemimage_width` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `footer_text`, ADD `itemimage_height` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `itemimage_width`";
			}
			unset($template_columns);
		}
	case('2.9.2'):
	case('2.9.3'):
	case('2.9.4'):
	case('2.9.5'):
		$this->advancedSettingsLoad();
		if ($this->settings['newsletter'])
		{
			$letter_columns = $this->_db->getColumns($this->_table['nl_letters']);
			if (!in_array('introduction', $letter_columns))
			{
				$sql[] = "ALTER TABLE `".$this->_table['nl_letters']."` ADD `introduction` TEXT NULL AFTER `edition`";
			}
			unset($letter_columns);
		}
	case('2.9.6'):
		// fix old rss feeds
		// remove current rss.xml, replace it for a custom rss.xml which displays a message that there are new rss feeds and people have check the main website for it.
		$rss_path = DATA_DIR.'rss.xml';
		if (file_exists($rss_path))
		{
			$this->preferencesLoad();
			$this->advancedSettingsLoad();
			
			$rss_content = '<?xml version="1.0" encoding="ISO-8859-1" ?>
<rss version="2.0">
<channel>
	<title><![CDATA['.$this->_canvas->filter($this->preferences['title']).']]></title>
	<link>http://'.DOMAIN.'</link>
	<description><![CDATA['.$this->_canvas->filter($this->preferences['description']).']]></description>
	<copyright><![CDATA['.$this->_canvas->filter($this->preferences['copyright']).']]></copyright>
<item>
	<title><![CDATA[New RSS feeds available]]></title>
	<description><![CDATA[This RSS feed has been surpassed by new feeds. Please check the site for the new feeds.]]></description>
	<link><![CDATA[http://'.DOMAIN.'/]]></link>
</item>
</channel>';
			
			unlink($rss_path);
			
			if ( !function_exists('file_put_contents') )
			{
				if (!defined('FILE_APPEND'))
					define('FILE_APPEND', 1);
				
				function file_put_contents($n, $d, $flag = false)
				{
					$mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
					$f = fopen($n, $mode);
					if ($f === false)
					{
						return 0;
					} else {
						if (is_array($d)) $d = implode($d);
						$bytes_written = fwrite($f, $d);
						fclose($f);
						return $bytes_written;
				    }
				}
			}
			
			file_put_contents($rss_path, $rss_content);
		}
		
		// add newsletter log table
		$this->advancedSettingsLoad();
		if ($this->settings['newsletter'])
		{
			if (!isset($this->_table['nl_log']))
			{
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'nl_log', 'tables.ini'));
				return;
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['nl_log']."` (
					 `date` datetime default NULL,
					 `type` varchar(20) default NULL,
					 `message` text,
					 `file` varchar(255) default NULL,
					 `line` varchar(255) default NULL,
					 `function` varchar(255) default NULL,
					 `class` varchar(255) default NULL
					) ENGINE=MyISAM";
			
			if (!isset($this->_table['nl_incoming']))
			{
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'nl_incoming', 'tables.ini'));
				return;
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['nl_incoming']."` (
					  `id` int(11) NOT NULL auto_increment,
					  `date_inserted` datetime default NULL,
					  `subject` varchar(255) default NULL,
					  `address` varchar(255) default NULL,
					  `type` enum('bounce','unsubscribe','unknown') default NULL,
					  `headers` text,
					  `body` text,
					  PRIMARY KEY  (`id`)
					) ENGINE=MyISAM";

			// change smtp account settings
			$query = sprintf("SELECT `name`, `value` FROM `%s` WHERE name IN ('smtp_username', 'smtp_password')", $this->_table['nl_settings']);
			$smtp_settings = array();
			$db->doQuery($query, $smtp_settings, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_value', false, array('index_key' => 'name', 'value' => 'value'));
		}
	case('2.9.7'):
		// load settings
		$this->advancedSettingsLoad();
		
		if ($this->settings['newsletter'])
		{
			if (!isset($this->_table['nl_links']))
			{
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'nl_links', 'tables.ini'));
				return;
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['nl_links']."` (
					  `id` int(11) NOT NULL auto_increment,
					  `link` varchar(255) default NULL,
					  `newsletter_id` int(11) default NULL,
					  `item_id` int(11) default NULL,
					  `clicks` int(11) default NULL,
					  `date_added` datetime default NULL,
					  PRIMARY KEY  (`id`)
					) ENGINE=MyISAM";
			
			// check for new symlink
			if (!file_exists(SITEROOT_DIR.'newsletter_link.php'))
			{
				$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), _('de website root'), 'newsletter_link.php'));
			}
		}
	case('2.9.8'):
		// load settings
		$this->advancedSettingsLoad();
		
		if ($this->settings['newsletter'])
		{
			if (!isset($this->_table['nl_letter_stats']))
			{
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'nl_letter_stats', 'tables.ini'));
				return;
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['nl_letter_stats']."` (
					`letter_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
					`addressees` INT UNSIGNED NOT NULL DEFAULT '0',
					`unsubscribes` INT UNSIGNED NOT NULL DEFAULT '0',
					`bounces` INT UNSIGNED NOT NULL DEFAULT '0',
					`errors` INT UNSIGNED NOT NULL DEFAULT '0'
					) ENGINE=MYISAM";
		}
	case('2.9.9'):
	case('2.9.10'):
	case('2.9.11'):
	case('2.9.12'):
		// This will increase the size column to store filesizes of �4GB instead of 16MB.
		$sql[] = "ALTER TABLE `".$this->_table['album_files']."` CHANGE `size` `size` INT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['section_files']."` CHANGE `size` `size` INT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['item_files']."` CHANGE `size` `size` INT UNSIGNED NOT NULL DEFAULT '0'";
	case('2.9.13'):
	case('2.9.14'):
	case('2.9.15'):
		// load settings
		$this->advancedSettingsLoad();
		
		if ($this->settings['newsletter'])
		{
			if (!isset($this->_table['nl_optinlog']))
			{
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'nl_optinlog', 'tables.ini'));
				return;
			}
			
			// check for new symlink
			if (!file_exists(SITEROOT_DIR.'newsletter_verify.php'))
			{
				$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), _('de website root'), 'newsletter_verify.php'));
			}
			
			if (!file_exists('newsletter_optin.php'))
			{
				$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), 'edit', 'newsletter_optin.php'));
			}
			
			$addr_cols = $this->_db->getColumns($this->_table['nl_addresses']);
			if (!in_array('verified', $addr_cols))
			{
				$sql[] = "ALTER TABLE `".$this->_table['nl_addresses']."` ADD `verified` int default 0";
			}
			unset($addr_cols);
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['nl_optinlog']."` (
					  `logstamp` datetime default NULL,
					  `address` varchar(255) default NULL,
					  `address_id` int(11) default NULL,
					  `remoteip` varchar(15) default NULL,
					  `useragent` varchar(255) default NULL,
					  `method` varchar(255) default NULL
					) ENGINE=MyISAM";
		}
	case('2.9.16'):
	case('2.9.17'):
		if (!file_exists('crop.php'))
		{
			$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), 'edit', 'crop.php'));
		}
		
		$messages->add(sprintf(_('`%1$s` in `%2$s` dient te worden vervangen door een nieuwere versie uit `%3$s.'), '.htaccess', _('de website root'), 'install/installer_data/'));
	case('2.9.17.1'):
	case('2.9.18'):
	case('2.9.19'):
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` CHANGE `type` `type` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['albums']."` CHANGE `position` `position` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `type` `type` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['sections']."` CHANGE `position` `position` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` CHANGE `label_type` `label_type` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		$sql[] = "ALTER TABLE `".$this->_table['items']."` CHANGE `position` `position` SMALLINT UNSIGNED NOT NULL DEFAULT '0'";
		
		$section_columns = $this->_db->getColumns($this->_table['sections']);
		if (!in_array('subname', $section_columns))
			$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `subname` VARCHAR(250) DEFAULT NULL AFTER `name`";
		
		if (!in_array('custom_data', $section_columns))
			$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `custom_data` LONGTEXT AFTER `text`";
		unset($section_columns);
	case('2.9.20'):
	case('2.9.21'):
		
		// pre 2.10.x
		$this->advancedSettingsLoad();
		if ($this->settings['tags'])
		{
			if (!file_exists('tags.php'))
			{
				$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), 'edit', 'tags.php'));
			}
			
			if (!isset($this->_table['tags']))
			{
				if ($config_db)
				{
					$table_entry = $settings['table_prefix'].'tags';
					$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
					$stmt->bindValue(':name', 'tags', SQLITE3_TEXT);
					$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
					$result = $stmt->execute();
					
					$this->_table['tags'] = $table_entry;
					unset($table_entry);
				} else {
					// abort upgrade
					$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'tags', 'tables.ini'));
					return;
				}
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['tags']."` (
					  `id` int(11) NOT NULL auto_increment,
					  `group_id` int(11) default NULL,
					  `tag` varchar(255) default NULL,
					  PRIMARY KEY  (`id`)
					) ENGINE=MYISAM";
			
			if (!isset($this->_table['tag_groups']))
			{
				if ($config_db)
				{
					$table_entry = $settings['table_prefix'].'tag_groups';
					$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
					$stmt->bindValue(':name', 'tag_groups', SQLITE3_TEXT);
					$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
					$result = $stmt->execute();
					
					$this->_table['tag_groups'] = $table_entry;
					unset($table_entry);
				} else {
					// abort upgrade
					$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'tag_groups', 'tables.ini'));
					return;
				}
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['tag_groups']."` (
					  `id` int(11) NOT NULL auto_increment,
					  `name` varchar(255) default NULL,
					  PRIMARY KEY  (`id`)
					) ENGINE=MYISAM";
			
			if (!isset($this->_table['item_tags']))
			{
				if ($config_db)
				{
					$table_entry = $settings['table_prefix'].'item_tags';
					$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
					$stmt->bindValue(':name', 'item_tags', SQLITE3_TEXT);
					$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
					$result = $stmt->execute();
					
					$this->_table['item_tags'] = $table_entry;
					unset($table_entry);
				} else {
					// abort upgrade
					$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'item_tags', 'tables.ini'));
					return;
				}
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['item_tags']."` (
					  `tag_id` int(11) default NULL,
					  `item_id` int(11) default NULL
					) ENGINE=MYISAM";
		}
		
		$album_columns = $this->_db->getColumns($this->_table['albums']);
		if (!in_array('online_mobile', $album_columns))
			$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `online_mobile` ENUM('Y','N') NOT NULL DEFAULT 'Y' AFTER `online`";
		unset($album_columns);
		
		$section_columns = $this->_db->getColumns($this->_table['sections']);
		if (!in_array('online_mobile', $section_columns))
			$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `online_mobile` ENUM('Y','N') NOT NULL DEFAULT 'Y' AFTER `online`";
		unset($section_columns);
		
		if (!file_exists(CACHE_DIR))
		{
			$messages->add(sprintf(_('Deze update vereist een nieuwe directory `%1$s` in de `%2$s` directory met %3$s-rechten.'), 'cache', 'assets', 'lees-/schrijf'));
		}
		
		// Google Analytics for mobile site.
		if (!file_exists(SITEROOT_DIR.'ga.php'))
		{
			$messages->add(sprintf(_('Er dient een nieuwe koppeling gemaakt te worden in %1$s voor `%2$s`.'), _('de website root'), 'ga.php'));
		}
		
		// meta description fields for SEO
		$album_columns = $this->_db->getColumns($this->_table['albums']);
		if (!in_array('description', $album_columns))
			$sql[] = "ALTER TABLE `".$this->_table['albums']."` ADD `description` TEXT AFTER `user_id`";
		unset($album_columns);
		
		$section_columns = $this->_db->getColumns($this->_table['sections']);
		if (!in_array('description', $section_columns))
			$sql[] = "ALTER TABLE `".$this->_table['sections']."` ADD `description` TEXT AFTER `position`";
		unset($section_columns);
		
		$item_columns = $this->_db->getColumns($this->_table['items']);
		if (!in_array('description', $item_columns))
			$sql[] = "ALTER TABLE `".$this->_table['items']."` ADD `description` TEXT AFTER `type`";
		unset($item_columns);
	case('2.10.0'):
		if (!isset($this->_table['metadata']))
		{
			if ($config_db)
			{
				$table_entry = $settings['table_prefix'].'metadata';
				$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
				$stmt->bindValue(':name', 'metadata', SQLITE3_TEXT);
				$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
				$result = $stmt->execute();
				
				$this->_table['metadata'] = $table_entry;
				unset($table_entry);
			} else {
				// abort upgrade
				$messages->add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'metadata', 'tables.ini'));
				return;
			}
		}
		
		$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['metadata']."` (
				  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				  `owner_id` int(10) unsigned NOT NULL,
				  `owner_type` enum('album','section','item') NOT NULL DEFAULT 'album',
				  `field` varchar(50) DEFAULT NULL,
				  `language` varchar(50) DEFAULT NULL,
				  `value` text,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM";
		
	case('2.10.1'):
	case('2.10.2'):
	case('2.10.2.1'):
	case('2.10.2.2'):
		MessageQueue::add(sprintf(_('`%1$s` in `%2$s` dient te worden vervangen door een nieuwere versie uit `%3$s.'), '.htaccess', _('de website root'), 'install/installer_data/'));
		
		$this->advancedSettingsLoad();
		if ($this->settings['tags'])
		{
			if (!isset($this->_table['album_tags']))
			{
				if ($config_db)
				{
					$table_entry = $settings['table_prefix'].'album_tags';
					$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
					$stmt->bindValue(':name', 'album_tags', SQLITE3_TEXT);
					$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
					$result = $stmt->execute();
					
					$this->_table['album_tags'] = $table_entry;
					unset($table_entry);
				} else {
					// abort upgrade
					MessageQueue::add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'album_tags', 'tables.ini'));
					return;
				}
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['album_tags']."` (
					  `tag_id` int(11) default NULL,
					  `album_id` int(11) default NULL
					) ENGINE=MYISAM";
			
			if (!isset($this->_table['section_tags']))
			{
				if ($config_db)
				{
					$table_entry = $settings['table_prefix'].'section_tags';
					$stmt = $config_db->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
					$stmt->bindValue(':name', 'section_tags', SQLITE3_TEXT);
					$stmt->bindValue(':realname', $table_entry, SQLITE3_TEXT);
					$result = $stmt->execute();
					
					$this->_table['section_tags'] = $table_entry;
					unset($table_entry);
				} else {
					// abort upgrade
					MessageQueue::add(sprintf(_('Deze update vereist een extra tabel met referentie `%1$s` in `%2$s`. Update website na aanpassing nogmaals.'), 'section_tags', 'tables.ini'));
					return;
				}
			}
			
			$sql[] = "CREATE TABLE IF NOT EXISTS `".$this->_table['section_tags']."` (
					  `tag_id` int(11) default NULL,
					  `section_id` int(11) default NULL
					) ENGINE=MYISAM";
		}
 	case('2.10.2.3'):
 	case('2.10.3'):
	default:
		$sql[] = "UPDATE `".$this->_table['data']."` SET `user_version`='".$update_user_to."' WHERE `photographer_id`='".$this->user_id."'";
		break;
}

$table_list = $this->_db->getTables();
$optimize_list = array_intersect($this->_db->getTables(), $this->_table);

// queries always to perform
$sql[] = "OPTIMIZE TABLE `".implode("`,`", $optimize_list)."`";

foreach($sql as $query)
{
	$result = null;
	$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
}

// clean tmp directories
$dirs = array(CODE.'tmp', SETTINGS.'tmp');
foreach ($dirs as $dir)
{
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file{0} == '.' || $file == 'readme.txt')
				continue;
			
			if (time() - filemtime($dir.'/'.$file) > 10 * 60) // 10 minutes
			{
				unlink($dir.'/'.$file);
			}
		}
	}
}

$messages->title = _('Yourportfolio bijgewerkt');
$messages->add(sprintf(_('Yourportfolio is bijgewerkt naar versie %s.'), CORE_VERSION));
?>