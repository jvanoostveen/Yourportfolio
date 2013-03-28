<?
/**
 * Project:			yourportfolio
 * File:			$RCSfile: shutdown_download.php,v $
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name: rel_2-5-23 $
 */

/**
 * last file called, disconnects from the database and exists the page
 * as this is a shutdown for a download script, output has already be done by the download routine
 *
 * @package yourportfolio
 * @subpackage System
 * @version $Revision: 1.1 $
 * @date $Date: 2005/01/27 10:47:54 $
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