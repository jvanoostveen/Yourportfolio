<?PHP
$database = ';
; Project:		yourportfolio
; File:			$RCSfile: database.php,v $
;
; @link http://www.yourportfolio.nl
; @copyright 2007 Furthermore
; @author Joeri van Oostveen <joeri@furthermore.nl>
; @release $Name: rel_2-5-23 $

; custom database ini file (read only settings)
; 
; @package yourportfolio
; @subpackage Settings
; @version $Revision: 1.1 $
; @date $Date: 2005/06/15 08:00:15 $
;

[database]
host	= "'.$this->db_settings['host'].'"
user	= "'.$this->db_settings['user'].'"
pass	= "'.$this->db_settings['pass'].'"
name	= "'.$this->db_settings['name'].'"
';
?>