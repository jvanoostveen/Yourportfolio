<?PHP

$this->sql['user_data'] = "INSERT INTO `".$this->table_prefix."photographer_data` SET photographer_id='".$this->user_settings['id']."', downloadable_photos='N', bg_colour='FFFFFF', core_version='".$this->core_version."', user_version='".$this->user_version."'";

$this->sql['albums'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_albums` (
  id int(10) unsigned NOT NULL auto_increment,
  created datetime NOT NULL default '0000-00-00 00:00:00',
  modified datetime NOT NULL default '0000-00-00 00:00:00',
  online enum('Y','N') NOT NULL default 'Y',
  `online_mobile` ENUM('Y','N') NOT NULL DEFAULT 'Y',
  position smallint(5) unsigned NOT NULL default '0',
  locked enum('N','Y') NOT NULL default 'N',
  restricted enum('N','Y') NOT NULL default 'N',
  user_id int(11) default NULL,
  `description` text,
  name varchar(250) default NULL,
  text_original text,
  `text` text,
  template enum('album','text','news','contact','guestbook') NOT NULL default 'album',
  `type` smallint(5) unsigned default NULL,
  link varchar(250) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM";

$this->sql['album_files'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_album_files` (
  id int(10) unsigned NOT NULL auto_increment,
  owner_id int(10) unsigned NOT NULL default '0',
  file_id varchar(32) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  online enum('Y','N') NOT NULL default 'Y',
  name varchar(250) NOT NULL default '',
  size int(10) unsigned NOT NULL default '0',
  extension varchar(5) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  width mediumint(8) unsigned NOT NULL default '0',
  height mediumint(8) unsigned NOT NULL default '0',
  basepath varchar(250) NOT NULL default '',
  path varchar(250) NOT NULL default '',
  sysname varchar(250) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['sections'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_sections` (
  id int(10) unsigned NOT NULL auto_increment,
  created datetime NOT NULL default '0000-00-00 00:00:00',
  modified datetime NOT NULL default '0000-00-00 00:00:00',
  section_date datetime default NULL,
  album_id int(11) NOT NULL default '0',
  online enum('Y','N') NOT NULL default 'Y',
  `online_mobile` ENUM('Y','N') NOT NULL DEFAULT 'Y',
  `text_node` enum('N', 'Y') NOT NULL default 'N',
  is_selection enum('N','Y') NOT NULL default 'N',
  position smallint(5) unsigned default NULL,
  `description` text,
  name varchar(250) default NULL,
  `subname` varchar(250) default NULL,
  text_original text,
  `text` text,
  `custom_data` longtext,
  template enum('section','newsitem','section_text_node') NOT NULL default 'section',
  link varchar(250) default NULL,
  `type` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['section_files'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_section_files` (
  id int(10) unsigned NOT NULL auto_increment,
  owner_id int(10) unsigned NOT NULL default '0',
  file_id varchar(32) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  online enum('Y','N') NOT NULL default 'Y',
  name varchar(250) NOT NULL default '',
  size int(10) unsigned NOT NULL default '0',
  extension varchar(5) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  width mediumint(8) unsigned NOT NULL default '0',
  height mediumint(8) unsigned NOT NULL default '0',
  basepath varchar(250) NOT NULL default '',
  path varchar(250) NOT NULL default '',
  sysname varchar(250) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['items'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_items` (
  id int(10) unsigned NOT NULL auto_increment,
  album_id int(11) NOT NULL default '0',
  section_id int(11) NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  modified datetime NOT NULL default '0000-00-00 00:00:00',
  online enum('Y','N') NOT NULL default 'Y',
  `text_node` enum('N', 'Y') NOT NULL default 'N',
  position smallint(5) unsigned NOT NULL default '0',
  `type` enum('error','image','audio','video','ext_video') NOT NULL default 'image',
  `description` text,
  name varchar(250) default NULL,
  subname varchar(250) default NULL,
  text_original text,
  `text` text,
  custom_data longtext,
  random_id smallint(6) default NULL,
  link varchar(250) default NULL,
  `label_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['item_files'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_item_files` (
  id int(10) unsigned NOT NULL auto_increment,
  owner_id int(10) unsigned NOT NULL default '0',
  file_id varchar(32) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  online enum('Y','N') NOT NULL default 'Y',
  name varchar(250) NOT NULL default '',
  size int(10) unsigned NOT NULL default '0',
  extension varchar(5) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  width mediumint(8) unsigned NOT NULL default '0',
  height mediumint(8) unsigned NOT NULL default '0',
  basepath varchar(250) NOT NULL default '',
  path varchar(250) NOT NULL default '',
  sysname varchar(250) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['parameters'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_parameters` (
  id int(10) unsigned NOT NULL auto_increment,
  album_id int(11) default NULL,
  parameter varchar(250) default NULL,
  `value` varchar(250) default NULL,
  PRIMARY KEY  (`id`),
  KEY `album_id` (`album_id`)  
) ENGINE=MyISAM;";

$this->sql['users'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_users` (
  id int(10) unsigned NOT NULL auto_increment,
  online enum('Y','N') NOT NULL default 'Y',
  name varchar(200) default NULL,
  login varchar(100) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  previous_login datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['subusers'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_subusers` (
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

$this->sql['subuser_album'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_subuser_album` (
  subuser_id int(10) unsigned NOT NULL,
  album_id int(10) unsigned NOT NULL,
  PRIMARY KEY  (subuser_id, album_id)
) ENGINE=MyISAM;";

$this->sql['language_strings'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_language_strings` ( 
 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
 `owner_id` INT UNSIGNED NOT NULL,
 `owner_type` ENUM('album', 'section', 'item' ) DEFAULT 'album' NOT NULL,
 `field` VARCHAR(50),
 `language` VARCHAR(50),
 `string` TEXT,
 `string_parsed` TEXT,
 PRIMARY KEY (`id`)
 ) ENGINE=MyISAM";

$this->sql['links'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_links` (
 `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `link` VARCHAR( 250 ) NOT NULL ,
 `object_id` INT UNSIGNED NOT NULL ,
 `type` ENUM(  'album',  'section',  'item' ) NOT NULL DEFAULT  'album'
) ENGINE=MyISAM";

$this->sql['statistics'] = "";

$this->sql['views'] = "";

$this->sql['contact'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_contact` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `message` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$this->sql['guestbook'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_guestbook_messages` (
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
) ENGINE=MyISAM";

$this->sql['tags'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_tags` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) default NULL,
  `tag` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$this->sql['tag_groups'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_tag_groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$this->sql['album_tags'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_album_tags` (
  `tag_id` int(11) default NULL,
  `album_id` int(11) default NULL
) ENGINE=MyISAM";

$this->sql['section_tags'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_section_tags` (
  `tag_id` int(11) default NULL,
  `section_id` int(11) default NULL
) ENGINE=MyISAM";

$this->sql['item_tags'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_item_tags` (
  `tag_id` int(11) default NULL,
  `item_id` int(11) default NULL
) ENGINE=MyISAM";

$this->sql['metadata'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_metadata` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `owner_type` enum('album','section','item') NOT NULL DEFAULT 'album',
  `field` varchar(50) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM";

?>