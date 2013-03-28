<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * switch to redirect the form data to the correct object
 *
 * @package yourportfolio
 * @subpackage Core
 */

// handle forms
switch( isset($_POST['targetObj']) ? $_POST['targetObj'] : 'none' )
{
	case('yourportfolio'):
		$yourportfolio->handleInput($_POST[$_POST['formName']."Form"]);
		unset($_POST); // remove data form _POST
		break;
	case('none'):
		// nothing
		break;
	default:
		// there is posted something, but target is undefined
		trigger_error("Undefined targetObj for \$_POST method", E_USER_NOTICE);
		// continue like nothing is happened
}
?>