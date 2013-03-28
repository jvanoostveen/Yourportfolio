<?PHP
/**
 * Alles netjes afsluiten
 *
 * Project: Yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 */

if( $system->browser == 5 )
{
	require(NL_TEMPLATES . 'page_bottom_5.php');
} else {
	require(NL_TEMPLATES . 'page_bottom_4.php');
}

if( isset( $post_templates ) )
{
	foreach( $post_templates as $t )
	{
		require(NL_TEMPLATES . $t);
	}
}
require(BASE . 'design/html/html_stop.php');

?>