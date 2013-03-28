<?PHP

$this->sql['nl_addresses'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_addresses` (
  `address_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `status` int(11) default NULL,
  `status_param` int(11) default '0',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `recv_count` int(11) default '0',
  `verified` int(11) default '0',  
  PRIMARY KEY  (`address_id`),
  KEY `address` (`address`)
) ENGINE=MyISAM";

$this->sql['nl_letters'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_letters` (
  `letter_id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) default NULL,
  `pagetitle` varchar(255) default NULL,
  `template_id` int(11) default NULL,
  `edition` varchar(255) default NULL,
  `introduction` text default NULL,
  `sender` varchar(255) default NULL,
  `datesent` datetime default NULL,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  `status` varchar(20) default NULL,
  PRIMARY KEY  (`letter_id`)
) ENGINE=MyISAM";

$this->sql['nl_letter_items'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_letter_items` (
  `item_id` int(11) NOT NULL auto_increment,
  `newsletter_id` int(11) default NULL,
  `title` text,
  `content` text,
  `link` varchar(255) default NULL,
  `order` int(11) default 0,
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`item_id`)
) ENGINE=MyISAM";

$this->sql['nl_item_files'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_item_files` (
  id int(10) unsigned NOT NULL auto_increment,
  owner_id int(10) unsigned NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(250) NOT NULL default '',
  size mediumint(8) unsigned NOT NULL default '0',
  extension varchar(5) NOT NULL default '',
  `type` varchar(100) NOT NULL default '',
  width mediumint(8) unsigned NOT NULL default '0',
  height mediumint(8) unsigned NOT NULL default '0',
  path varchar(250) NOT NULL default '',
  sysname varchar(250) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;";

$this->sql['nl_templates'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_templates` (
  `template_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `default_title` varchar(255) default NULL,
  `header` text,
  `itemimage_width` int default 120,
  `itemimage_height` int default 120,
  `item` text,
  `footer` text,
  `header_text` text,
  `item_text` text,
  `footer_text` text,
  `online` enum('Y','N') default 'Y',
  `created` datetime default NULL,
  `modified` datetime default NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=MyISAM";

$this->sql['nl_maillog'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_maillog` (
  `letter_id` int(11) NOT NULL default '0',
  `address_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`letter_id`,`address_id`)
) ENGINE=MyISAM";

$this->sql['nl_groups'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_groups` (
  `group_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `visible` enum('Y','N') default 'Y',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM";

$this->sql['nl_address_group'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_address_group` (
  `group_id` int(11) default NULL,
  `address_id` int(11) default NULL,
  KEY `group_id` (`group_id`),
  KEY `address_id` (`address_id`)  
) ENGINE=MyISAM";

$this->sql['nl_recipients'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_recipients` (
  `letter_id` int(11) NOT NULL default '0',
  `group_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`letter_id`,`group_id`),
  KEY `letter_id` (`letter_id`)
) ENGINE=MyISAM";

$this->sql['nl_queue'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_queue` (
  `letter_id` int(11) NOT NULL default '0',
  `addr_name` varchar(255) default NULL,
  `addr_email` varchar(255) NOT NULL default '',
  `status` varchar(10) default NULL,
  PRIMARY KEY  (`letter_id`,`addr_email`)
) ENGINE=MyISAM";

$this->sql['nl_settings'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_settings` (
  `name` varchar(255) NOT NULL default '',
  `value` text,
  `type` varchar(20) default NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM";

$this->sql['nl_log'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_log` (
 `date` datetime default NULL,
 `type` varchar(20) default NULL,
 `message` text,
 `file` varchar(255) default NULL,
 `line` varchar(255) default NULL,
 `function` varchar(255) default NULL,
 `class` varchar(255) default NULL
) ENGINE=MyISAM";

$this->sql['nl_incoming'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_incoming` (
  `id` int(11) NOT NULL auto_increment,
  `date_inserted` datetime default NULL,
  `subject` varchar(255) default NULL,
  `address` varchar(255) default NULL,
  `type` enum('bounce','unsubscribe','unknown') default NULL,
  `headers` text,
  `body` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$this->sql['newsletter_settings'] = "INSERT INTO `".$this->table_prefix.$this->settings_dir."_nl_settings` VALUES (
  'from_name','','string'), ('mbox_address','','email'), ('mbox_host','','host'), ('mbox_user','','email'), ('mbox_pass','','pass'), ('mbox_port','110','integer'), ('mbox_method', 'APOP', 'string'), ('error_threshold','4','integer'), ('batch_size','25','integer'), ('mail_method','smtp','string'), ('smtp_host', '','string'), ('smtp_username','','string'), ('smtp_password','','string'), ('unsubscribe_mode','groep','enum:groep,systeem'), ('debug','true','enum:true,false')";

$this->sql['nl_links'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_links` (
  `id` int(11) NOT NULL auto_increment,
  `link` varchar(255) default NULL,
  `newsletter_id` int(11) default NULL,
  `item_id` int(11) default NULL,
  `clicks` int(11) default NULL,
  `date_added` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";

$this->sql['nl_letter_stats'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_letter_stats` (
`letter_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
`addressees` INT UNSIGNED NOT NULL DEFAULT '0',
`unsubscribes` INT UNSIGNED NOT NULL DEFAULT '0',
`bounces` INT UNSIGNED NOT NULL DEFAULT '0',
`errors` INT UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=MyISAM";

$this->sql['nl_optinlog'] = "CREATE TABLE IF NOT EXISTS `".$this->table_prefix.$this->settings_dir."_nl_optinlog` (
  `logstamp` datetime default NULL,
  `address` varchar(255) default NULL,
  `address_id` int(11) default NULL,
  `remoteip` varchar(15) default NULL,
  `useragent` varchar(255) default NULL,
  `method` varchar(255) default NULL
) ENGINE=MyISAM";

?>