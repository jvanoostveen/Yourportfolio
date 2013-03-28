<?PHP

class NewsletterView
{
	var $newsletter;
	var $template;
	
	var $html;
	
	var $in_mail = false;
	
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
		
	function build()
	{
		global $system, $db, $yourportfolio, $settings;
		
		$IN_MAIL = $this->in_mail;
		
		$TEMPLATE_PATH = $system->base_url.'newsletter_images/template/';
		$CONTENT_PATH = $system->base_url.'newsletter_images/cache/';
		$PREVIEW_URL = 'http://'.DOMAIN.''.$system->base_url.'newsletter/'.$this->newsletter->id.'/';
		$query = sprintf("SELECT value FROM `%s` WHERE name='mbox_address'", $yourportfolio->_table['nl_settings']);
		$address = null;
		$db->doQuery( $query, $address, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		$NEWSLETTER_EMAIL = $address;
		
		$newsletter = $this->newsletter;
		
		// fetch template settings
		$this->template->load($this->template->id);
		$sizes = array('w' => $this->template->itemimage_width, 'h' => $this->template->itemimage_height);
		
		ob_start();
		eval('?>'.$this->template->header.'<?');
		foreach ($this->newsletter->items as $ITEM)
		{
			$IMAGE = $ITEM->image;
			
			if (!$IMAGE->isEmpty())
			{
				$filepart = $this->filename($IMAGE->sysname).'-'.$sizes['w'].'x'.$sizes['h'].'.'.$this->extension($IMAGE->sysname);
				
				$tmp_name = SETTINGS.'newsletter/cache/'.$filepart;
				$IMAGE->path = str_replace('newsletter', 'newsletter_images', $IMAGE->path);
				
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
					}
				}
				
				$IMAGE->cache_name = $filepart;
				$IMAGE->sysname = $filepart;
			}
			
			eval('?>'.$this->template->item.'<?');
		}
		eval('?>'.$this->template->footer.'<?');
		$this->html = ob_get_clean();
	}
}


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
	
	// parse euro sign
	$string = str_replace("\x80", "&euro;", $string);
	
	$textToolkit = $system->getModule('TextToolkit');
	$string = $textToolkit->parseText($string);
	
	return $string;
}
?>
