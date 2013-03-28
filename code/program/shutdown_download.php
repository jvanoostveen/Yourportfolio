<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * last file called, disconnects from the database and exists the page
 * as this is a shutdown for a download script, output has already be done by the download routine
 *
 * @package yourportfolio
 * @subpackage System
 */

/**
 * disconnect from database
 */
$db->disconnect();

/**
 * page is done, flush the toilet and exit, please lower the toilet seat
 */
exit();
?>