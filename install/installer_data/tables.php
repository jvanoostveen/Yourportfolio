<?PHP
$tables = ';
; Project:		yourportfolio
;
; @link http://www.yourportfolio.nl
; @copyright 2008 Furthermore
; @author Joeri van Oostveen <joeri@furthermore.nl>

; default table ini file (read only settings)
; 
; @package yourportfolio
; @subpackage Settings
;

[tables]
albums			= '.$this->table_prefix.$this->settings_dir.'_albums
album_files		= '.$this->table_prefix.$this->settings_dir.'_album_files
sections		= '.$this->table_prefix.$this->settings_dir.'_sections
section_files	= '.$this->table_prefix.$this->settings_dir.'_section_files
items			= '.$this->table_prefix.$this->settings_dir.'_items
item_files		= '.$this->table_prefix.$this->settings_dir.'_item_files
parameters		= '.$this->table_prefix.$this->settings_dir.'_parameters
client_users	= '.$this->table_prefix.$this->settings_dir.'_users
subusers		= '.$this->table_prefix.$this->settings_dir.'_subusers
subuser_album	= '.$this->table_prefix.$this->settings_dir.'_subuser_album
strings			= '.$this->table_prefix.$this->settings_dir.'_language_strings
links			= '.$this->table_prefix.$this->settings_dir.'_links
statistics		= '.$this->table_prefix.$this->settings_dir.'_statistics
views			= '.$this->table_prefix.$this->settings_dir.'_views
contact			= '.$this->table_prefix.$this->settings_dir.'_contact
guestbook		= '.$this->table_prefix.$this->settings_dir.'_guestbook_messages
tags			= '.$this->table_prefix.$this->settings_dir.'_tags
tag_groups		= '.$this->table_prefix.$this->settings_dir.'_tag_groups
item_tags		= '.$this->table_prefix.$this->settings_dir.'_item_tags
metadata		= '.$this->table_prefix.$this->settings_dir.'_metadata
nl_addresses	= '.$this->table_prefix.$this->settings_dir.'_nl_addresses
nl_letters		= '.$this->table_prefix.$this->settings_dir.'_nl_letters
nl_letter_items	= '.$this->table_prefix.$this->settings_dir.'_nl_letter_items
nl_item_files	= '.$this->table_prefix.$this->settings_dir.'_nl_item_files
nl_templates	= '.$this->table_prefix.$this->settings_dir.'_nl_templates
nl_images		= '.$this->table_prefix.$this->settings_dir.'_nl_images
nl_maillog		= '.$this->table_prefix.$this->settings_dir.'_nl_maillog
nl_pending		= '.$this->table_prefix.$this->settings_dir.'_nl_pending
nl_groups		= '.$this->table_prefix.$this->settings_dir.'_nl_groups
nl_address_group	= '.$this->table_prefix.$this->settings_dir.'_nl_address_group
nl_bindings		= '.$this->table_prefix.$this->settings_dir.'_nl_address_group
nl_recipients	= '.$this->table_prefix.$this->settings_dir.'_nl_recipients
nl_queue		= '.$this->table_prefix.$this->settings_dir.'_nl_queue
nl_settings		= '.$this->table_prefix.$this->settings_dir.'_nl_settings
nl_log			= '.$this->table_prefix.$this->settings_dir.'_nl_log
nl_incoming		= '.$this->table_prefix.$this->settings_dir.'_nl_incoming
nl_links		= '.$this->table_prefix.$this->settings_dir.'_nl_links
nl_letter_stats	= '.$this->table_prefix.$this->settings_dir.'_nl_letter_stats
nl_optinlog		= '.$this->table_prefix.$this->settings_dir.'_nl_optinlog
';
?>