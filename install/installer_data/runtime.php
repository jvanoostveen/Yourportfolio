<?PHP
$runtime = ';
; Project:		Yourportfolio
;
; @link http://www.yourportfolio.nl
; @copyright 2009 Furthermore
; @author Joeri van Oostveen <joeri@furthermore.nl>

; runtime ini file (read only settings)
; 
; @package yourportfolio
; @subpackage Settings
;

[runtime]
user_id			= '.$this->user_settings['id'].'
settings_cache	= cache/

[communication]
amfphp = '.($this->install_amfphp ? 'true' : 'false').'

[debug]
amfphp = false
';
?>