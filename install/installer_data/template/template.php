<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created May 8, 2007
 */
 
function get_template($db)
{
	
	$base = dirname(__FILE__).'/';
	
	$header = file_get_contents($base.'header.php');
	$item 	= file_get_contents($base.'item.php');
	$footer = file_get_contents($base.'footer.php');
	
	$header_text = file_get_contents($base.'header_text.php');
	$item_text = file_get_contents($base.'item_text.php');
	$footer_text = file_get_contents($base.'footer_text.php');

	$t = array(
		'name'			=> 'default',
		'default_title'	=> 'Nieuwsbrief',
		'header'		=> $db->filter($header),
		'item'			=> $db->filter($item),
		'footer'		=> $db->filter($footer),
		'header_text'	=> $db->filter($header_text),
		'item_text'		=> $db->filter($item_text),
		'footer_text'	=> $db->filter($footer_text)
	);
	
	return $t;
}

?>
