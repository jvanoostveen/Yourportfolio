<?PHP

class NewsletterView
{
	var $newsletter;
	var $template;
	
	var $html;
	var $text;
	
	var $in_mail = true;
	
	function NewsletterView()
	{
		$this->__construct();
	}
	
	function __construct()
	{
		$this->html = '';
	}
	
	/**
	 * Return the filename part of a file (w/o extension)
	 */
	function filename($name)
	{
		$parts = explode('.', $name);
		array_pop($parts);
		return join('.',$parts);
	}
	
	/**
	 * Return the extension of a file
	 */
	function extension($name)
	{
		$parts = explode('.', $name);
		return $parts[count($parts)-1];
	}
	
	function build( $clickthrough_links = false )
	{
		global $system, $settings, $yourportfolio, $db;
		
		$IN_MAIL = $this->in_mail;
		
		$TEMPLATE_PATH = 'newsletter/template/';
		$CONTENT_PATH = 'newsletter/cache/';
		$PREVIEW_URL = 'http://'.DOMAIN.''.substr($system->base_url, 0, -5).'newsletter/'.$this->newsletter->id.'/';
		$VERIFY_URL = 'http://'.DOMAIN.''.substr( $system->base_url, 0, -5).'newsletter_verify.php?aid=';
		
		$NEWSLETTER_EMAIL = $settings['mbox_address'];
		$NEWSLETTER_ID = $this->newsletter->id;
		$CLICKTHROUGH_URL = 'http://'.DOMAIN.''.substr($system->base_url, 0, -5).'newsletter_link.php';
		$UNSUBSCRIBE_LINK = 'mailto:'.$NEWSLETTER_EMAIL.'?subject=unsubscribe '.$NEWSLETTER_ID;
		
		$newsletter = $this->newsletter;
		
		// fetch template settings
		$this->template->load($this->template->id);
		$sizes = array('w' => $this->template->itemimage_width, 'h' => $this->template->itemimage_height);
		
		ob_start();
		eval('?>'.$this->template->header.'<?');
		foreach ($this->newsletter->items as $ITEM)
		{

			// do link clickthrough-replacement?
			if( $clickthrough_links && !empty( $ITEM->link ) )
			{
				// does link already exist?
				$exists = null;
				$link_id = 0;
				$query = sprintf( "SELECT EXISTS (SELECT * FROM `%s` WHERE `newsletter_id`=%d AND `item_id`=%d AND `link`='%s' )", $yourportfolio->_table['nl_links'], $newsletter->id, $ITEM->id, $ITEM->link );
				$db->doQuery( $query, $exists,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
				
				if( $exists == 1 )
				{
					// get ID of the existing entry
					$query = sprintf( "SELECT `id` FROM `%s` WHERE `newsletter_id`=%d AND `item_id`=%d AND `link`='%s' LIMIT 1", $yourportfolio->_table['nl_links'], $newsletter->id, $ITEM->id, $ITEM->link );
					$db->doQuery( $query, $link_id,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );	
				} else {
					// insert into db and get ID
					$query = sprintf( "INSERT INTO `%s` SET `newsletter_id`=%d, `item_id`=%d, `link`='%s', `clicks`=0, `date_added`=NOW()", $yourportfolio->_table['nl_links'], $newsletter->id, $ITEM->id, $ITEM->link );
					$db->doQuery( $query, $link_id,  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );	
				}
				
				// replace link with clickthrough link
				$ITEM->link = $CLICKTHROUGH_URL . "?id=$link_id";
				
			}
			
			$IMAGE = $ITEM->image;
			
			if (!$IMAGE->isEmpty())
			{
				$filepart = $this->filename($IMAGE->sysname).'-'.$sizes['w'].'x'.$sizes['h'].'.'.$this->extension($IMAGE->sysname);
				$tmp_name = SETTINGS.'newsletter/cache/'.$filepart;
				
				if (file_exists($tmp_name) && filemtime($tmp_name) < filemtime($IMAGE->path.$IMAGE->sysname))
				{
					unlink($tmp_name);
				}
	
				if( !file_exists($tmp_name) )
				{
					// no usable file was found, we need to resize it ourselves.
					if( $settings['debug'] == true )
					{
						trigger_error("No correctly sized image found in cache, resizing");
					}
					
					$imageToolkit = $system->getModule('ImageToolkit');
					if (($sizesResized = $imageToolkit->imageResize($IMAGE->path.$IMAGE->sysname, $sizes, $tmp_name)) === false)
					{
						trigger_error('resizing failed', E_USER_WARNING);
						
						// show item without image
						$filepart = '';
						$IMAGE->width = 0;
						$IMAGE->height = 0;
					} else {
						$IMAGE->width = $sizesResized['w'];
						$IMAGE->height = $sizesResized['h'];
					}
				} else {
					$real_sizes = getimagesize($tmp_name);
					$IMAGE->width = $real_sizes[0];
					$IMAGE->height = $real_sizes[1];
				}
				
				$IMAGE->cache_name = $filepart;
				$IMAGE->sysname = $filepart;
			}
									
			eval('?>'.$this->template->item.'<?');
		}
		eval('?>'.$this->template->footer.'<?');
		$this->html = ob_get_clean();
		
	}
	
	/**
	 * Build a text only version of the newsletter.
	 * 
	 * @return Void
	 */
	function buildText()
	{
		global $system, $settings;
		
		$IN_MAIL = $this->in_mail;
		
		$TEMPLATE_PATH = 'newsletter/template/';
		$CONTENT_PATH = 'newsletter/content/';
		$PREVIEW_URL = 'http://'.DOMAIN.''.substr($system->base_url, 0, -5).'newsletter/'.$this->newsletter->id.'/';
		$NEWSLETTER_EMAIL = $settings['mbox_address'];
		$NEWSLETTER_ID = $this->newsletter->id;
				
		$UNSUBSCRIBE_LINK = 'unsubscribe '.$NEWSLETTER_ID;

		$newsletter = $this->newsletter;
		
		ob_start();
		eval('?>'.$this->template->header_text.'<?');
		foreach ($this->newsletter->items as $ITEM)
		{
			eval('?>'.$this->template->item_text.'<?');
		}
		eval('?>'.$this->template->footer_text.'<?');
		$this->text = ob_get_clean();
	}
}

/**
 * Filter de BBCode-input naar HTML
 */
function f($string)
{
	global $system;
	
	// fetch code part
	$delimeterLeft = '[code]';
	$delimeterRight = '[/code]';
	$posLeft  = strpos($string, $delimeterLeft);
	if ($posLeft !== false)
	{
		$begin = $posLeft + strlen($delimeterLeft);
		
		$posRight = strpos($string, $delimeterRight, $begin + 1);
		$code = substr($string, $begin, $posRight - $begin);
		
		$string = str_replace($delimeterLeft.$code.$delimeterRight, $delimeterLeft.$delimeterRight, $string);
	}
	$string = htmlentities($string);
	$string = nl2br($string);
	
	if ($posLeft !== false)
	{
		$string = str_replace($delimeterLeft.$delimeterRight, $code, $string);
	}
	
	// parse some utf8 characters to html entities
	// (can't we do them all?)
	$chars = array(
				"\x91"	=> htmlentities('\''), // open ' to '
				"\x92"	=> htmlentities('\''), // closed ' to '
				"\x93"	=> htmlentities('"'), // open " to "
				"\x94"	=> htmlentities('"'), // closed " to "
				"\x80"	=> '&euro;', // euro sign
				);
	$string = strtr($string, $chars);
	
	$textToolkit = $system->getModule('TextToolkit');
	$string = $textToolkit->parseText($string);
	
	return $string;
}

/**
 * Parse een gegeven text voor textuele weergave (strip BBCode tags)
 */
function f_t($string)
{
	/*
	 *  in plaats van (.*) tussen de begin- en end-tags gebruik ik ([^\[]+)
	 * omdat anders een string "[i]een[/i] stukje [i]italic[/i] tekst" omgezet zou
	 * worden in "een[/i] stukje [i]italic tekst". preg_replace zoekt niet naar de
	 * eerste match van [/i], maar vind eerst de laatste.
	 */
	$string = preg_replace("/\[elink=(.*)\](.*?)\[\/elink\]/", "$2 ($1)", $string);
	$string = preg_replace("/\[email=(.*)\](.*?)\[\/email\]/", "$2 ($1)", $string);
	$string = preg_replace("/\[i\](.*?)\[\/i\]/", "$1", $string);
	$string = preg_replace("/\[b\](.*?)\[\/b\]/", "$1", $string);
	$string = preg_replace("/\[li\](.*?)\[\/li\]/", "\n  - $1", $string);
	$string = strip_tags(html_entity_decode($string));
	return $string;
}
?>
