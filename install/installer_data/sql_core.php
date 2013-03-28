<?PHP
$this->sql['photographer_data'] = "CREATE TABLE `".$this->table_prefix."photographer_data` (
  id int(10) unsigned NOT NULL auto_increment,
  photographer_id int(11) NOT NULL default '0',
  email varchar(250) default NULL,
  phone varchar(25) default NULL,
  mobile varchar(20) default NULL,
  fax varchar(25) default NULL,
  downloadable_photos enum('N','Y') NOT NULL default 'N',
  settings int(32) unsigned NOT NULL default '0',
  title varchar(250) default NULL,
  copyright text,
  description text,
  keywords text,
  bg_colour varchar(6) NOT NULL default 'FFFFFF',
  google_site_verification varchar(75) default NULL,
  google_analytics_account varchar(75) default NULL,
  `facebook_user_ids` varchar(250) DEFAULT NULL,
  `facebook_app_id` varchar(250) DEFAULT NULL,
  upload_dir varchar(250) default NULL,
  custom_fields mediumtext,
  core_version varchar(30) default NULL,
  user_version varchar(30) default NULL,
  PRIMARY KEY  (id),
  KEY photographer_id (photographer_id)
) ENGINE=MyISAM;";

$this->sql['photographers'] = "CREATE TABLE `".$this->table_prefix."photographers` (
  id int(10) unsigned NOT NULL auto_increment,
  `master` enum('N','Y') NOT NULL default 'N',
  firstname varchar(250) default NULL,
  lastname varchar(250) default NULL,
  email varchar(250) default NULL,
  online enum('Y','N') NOT NULL default 'Y',
  login varchar(250) NOT NULL default '',
  `password` varchar(100) NOT NULL default '',
  domain int(11) default NULL,
  previous_login datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['challenges'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix."challenges` (
 `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `challenge` VARCHAR( 32 ) NOT NULL,
 `user_id` INT UNSIGNED NOT NULL,
 `created` DATETIME NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM;";

$this->sql['admin'] = "INSERT INTO  `".$this->table_prefix."photographers` (  `id` ,  `master` ,  `firstname` ,  `lastname` ,  `email` ,  `online` ,  `login` ,  `password` ,  `domain` ,  `previous_login` ,  `last_login` ) 
VALUES (
'',  'Y',  '".$this->db->filter($this->admin_settings['firstname'])."', NULL , NULL ,  'Y',  '".$this->db->filter($this->admin_settings['login'])."', MD5('".$this->db->filter($this->admin_settings['password'])."'), NULL, '0000-00-00 00:00:00',  '0000-00-00 00:00:00'
);";

$this->sql['user'] = "INSERT INTO  `".$this->table_prefix."photographers` (  `id` ,  `master` ,  `firstname` ,  `lastname` ,  `email` ,  `online` ,  `login` ,  `password` ,  `domain` ,  `previous_login` ,  `last_login` ) 
VALUES (
'',  'N',  '".$this->db->filter($this->user_settings['firstname'])."', '".$this->db->filter($this->user_settings['lastname'])."' , NULL ,  'Y',  '".$this->db->filter($this->user_settings['login'])."', MD5('".$this->db->filter($this->user_settings['password'])."') , NULL ,  '0000-00-00 00:00:00',  '0000-00-00 00:00:00'
);";

?>