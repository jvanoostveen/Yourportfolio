<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * error file (translates errors to user readable error feedback)
 *
 * @package yourportfolio
 * @subpackage Error
 */

if (get_class($this) != 'ErrorHandler') // only run code when called from within the yourportfolio class, otherwise continue calling script
	return;

$this->error = array(
	// core errors (0 - 99)
		0	=> '',
		
	// yourportfolio errors (100 - 199)
		
	// album errors (200 - 299)
	
	// section errors (300 - 399)
	
	// item errors (400 - 499)
		
	);
?>