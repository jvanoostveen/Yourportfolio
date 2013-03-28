<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * class: Canvas
 * handles the dynamic content
 * customize this class for different output
 * 
 * @package yourportfolio
 * @subpackage Display
 */
class Canvas
{
	/**
	 * outer template (framework)
	 * @var string
	 */
	var $template;
	
	/**
	 * inner template (when template contains another template)
	 * @var string
	 */
	var $inner_template;
	
	var $menu_item;
	var $open_album;
	var $open_section;
	
	/**
	 * stylesheets
	 * @var array $stylesheets
	 */
	var $stylesheets = array();
	var $template_stylesheets = array();

	/**
	 * VBscripts or javascripts
	 * @var array
	 */
	var $scripts = array();
	var $raw_scripts = array();
	
	/**
	 * tells html to show or not show the body tag (used when having a frameset)
	 * @var boolean $showBody
	 */
	var $showBody = true;
	var $bodyTags = array();
	
	var $skipHeaders = false;
	
	/**
	 * set to true to use site templates
	 */
	var $site_templates = false;
	
	/**
	 * headers to be sent before doing ouput
	 * @var array $headers
	 */
	var $headers = array();
	
	/**
	 * meta (http-equiv) tags to output in head of html file
	 * @var array $meta_http
	 */
	var $meta_http = array();
	var $meta = array();
	
	/**
	 * magic_quotes_gpc status
	 * @var boolean magic_quotes
	 */
	var $magic_quotes;
	
	var $showCalendar = false;
	
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		$this->magic_quotes = (get_magic_quotes_gpc()) ? true : false;
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function Canvas()
	{
		$this->__construct();
	}
	
	/**
	 * wrapper function for adding javascript files to load
	 *
	 * @param string $file		without .js or .vbs
	 * @param string $language	language of script (js || vbs)
	 * @param boolean $nocache	can add no cache string
	 * @param string $query		query string to attach to the file call
	 * @return void
	 */
	function addScript($file, $language = 'js', $nocache = true, $query = '')
	{
		$script = array();
		$script['file'] = $file;
		$script['l'] = '';
		switch($language)
		{
			case('js'):
				$script['lang'] = 'javascript';
				$script['type'] = 'text/javascript';
				$script['ext']	= '.js';
				
				// the javascript is wrapped in php, because of l10n.
				if (!FRONTEND && file_exists(SCRIPTS.$script['file'].'.php'))
				{
					global $yourportfolio;
					
					$script['ext'] = '.php';
					$script['l'] = $yourportfolio->display_language;
				}
				
				break;
			case('vbs'):
				$script['lang'] = 'VBScript';
				$script['type'] = 'text/vbscript';
				$script['ext']	= '.vbs';
				break;
		}
		$script['nocache'] = $nocache;
		$script['query'] = $query;
		if (empty($script['query']))
		{
			$script['nocache'] = false;
		}
		
		$this->scripts[] = $script;
	}
	
	/**
	 * wrapper function for adding raw/direct script in the header
	 *
	 * @param string $script_txt	the script to perform
	 * @param string $language		language of script (js || vbs)
	 */
	function addRawScript($script_txt, $language = 'js')
	{
		$script = array();
		$script['script'] = $script_txt;
		switch($language)
		{
			case('js'):
				$script['lang'] = 'javascript';
				$script['type'] = 'text/javascript';
				break;
			case('vbs'):
				$script['lang'] = 'VBScript';
				$script['type'] = 'text/vbscript';
				break;
		}
		$this->raw_scripts[] = $script;
	}
	
	/**
	 * adds a body tag for later use
	 *
	 */
	function addBodyTag($name, $value)
	{
		$this->bodyTags[$name] = $value;
	}
	
	/**
	 * Checks if template is available as override in site templates, otherwise use core template.
	 * 
	 * @param $template:String
	 * @return String
	 */
	function templatePath($template)
	{
		if (file_exists(CUSTOM_HTML.$template.'.php'))
		{
			return CUSTOM_HTML.$template.'.php';
		}
		
		if ($this->site_templates)
		{
			return SITE_HTML.$template.'.php';
		}
		
		return CORE_HTML.$template.'.php';
	}
	
	/**
	 * Checks if rss template is available as override in site templates, otherwise use default template.
	 * 
	 * @param $template:String
	 * @return String
	 */
	function RSSTemplate($template)
	{
		return DEFAULT_RSS.$template.'.php';
	}
	
	function generateBodyTags()
	{
		$tags = '';
		foreach($this->bodyTags as $name => $value)
		{
			$tags .= ' '.$name.'="'.$value.'"';
		}
		return $tags;
	}
	
	/**
	 * wrapper function for adding a stylesheet file to load
	 *
	 * @param string $file without .css
	 */
	function addStyle($file)
	{
		$this->stylesheets[] = $file;
	}
	
	/**
	 * wrapper function for adding a stylesheet file to load
	 *
	 * @param string $file without .css
	 */
	function addTemplateStyle($file)
	{
		$this->template_stylesheets[] = $file;
	}
	
	/**
	 * shows icon from a given set, if the icon doesn't exist, show the default one
	 */
	function showIcon($icon, $type = null)
	{
		global $yourportfolio;
		
		if (empty($type))
		{
			if (file_exists(CUSTOM_ICONS.$icon.'.gif'))
			{
				return CUSTOM_ICONS.$icon.'.gif';
			} else if (file_exists(ICONS.$yourportfolio->iconset.'/'.$icon.'.gif'))
			{
				return ICONS.$yourportfolio->iconset.'/'.$icon.'.gif';
			} else {
				return ICONS.'default/'.$icon.'.gif';
			}
		} else {
			if (file_exists(CUSTOM_ICONS.$icon.'_'.$type.'.gif'))
			{
				return CUSTOM_ICONS.$icon.'_'.$type.'.gif';
			} else if (file_exists(ICONS.$yourportfolio->iconset.'/'.$icon.'_'.$type.'.gif'))
			{
				return ICONS.$yourportfolio->iconset.'/'.$icon.'_'.$type.'.gif';
			} else {
				if (file_exists(CUSTOM_ICONS.$icon.'.gif'))
				{
					return CUSTOM_ICONS.$icon.'.gif';
				} else if (file_exists(ICONS.$yourportfolio->iconset.'/'.$icon.'.gif'))
				{
					return ICONS.$yourportfolio->iconset.'/'.$icon.'.gif';
				} else if (file_exists(ICONS.'default/'.$icon.'_'.$type.'.gif')) {
					return ICONS.'default/'.$icon.'_'.$type.'.gif';
				} else {
					return ICONS.'default/'.$icon.'.gif';
				}
			}
		}
	}
	
	/**
	 * filters a -read_more- tag to a link or nothing
	 *
	 */
	function read_more($string, $show_link, $link = '')
	{
		$strings = array();
		
		$strings = explode('-read_more-', $string);
		
		if ($strings === false || count($strings) == 1)
		{
			return $string;
		}
		
		$new_string = '';
		if ($show_link)
		{
			$new_string = $strings[0];
			$new_string .= '<br /><a href="'.$link.'">read more</a>';
		} else {
			$new_string = str_replace('-read_more-', '', $string);
		}
		return $new_string;
	}
	
	/**
	 * filter for correct display in html view
	 *
	 * @param string $string the string to parse
	 * @param integer $truncate how many characters are to be displayed, not counting html tags
	 */
	function filter($string, $truncate = null)
	{
#		$string = html_entity_decode(nl2br(stripslashes($string)));
		$string = html_entity_decode(nl2br($string));
#		$string = htmlentities($string, ENT_QUOTES, 'utf-8');
#		$string = nl2br(stripslashes($string));
		
		if (!is_null($truncate) && strlen(strip_tags(str_replace('...', '', $string))) > $truncate)
		{
			$string = substr($string, 0, $truncate).'...';
		}
		
		return $string;
	}
	
	function f($string)
	{
		global $system;
		
		// characters to be replaced.
		$chars = array(
					"\x91"	=> '\'', // open ' to '.
					"\x92"	=> '\'', // closed ' to '.
					"\x93"	=> '"', // open " to ".
					"\x94"	=> '"', // closed " to ".
					'event:/' => $system->base_url,
					'event:' => $system->base_url,
					'asfunction:Application.asfunction_show,' => $system->base_url
					);
		
		// replace characters and encode string.
		$string = strtr($string, $chars);

		$string = nl2br($string);
		$string = utf8_encode($string);
#		$string = htmlentities($string, ENT_QUOTES, 'utf-8');
#		$string = html_entity_decode($string);


		
		return $string;
	}
	
	/**
	 * filter for correct display in ascii view
	 *
	 * @param string $string	the string to parse
	 * @param integer $truncate	how many characters are to be displayed, not counting html tags
	 */
	function text_filter($string, $truncate = null)
	{
#		$string = stripslashes($string);
		$string = stripslashes(strip_tags($string));
		
		if (!is_null($truncate) && strlen($string) > $truncate)
			$string = substr($string, 0, $truncate - 3).'...';

		return $string;
	}
	
	/**
	 * strip contents from string (with possible unknown string in between)
	 *
	 * @param string $preg
	 */
	function strip($string, $preg)
	{
		$string = preg_replace($preg, '', $string);
		return $string;
	}
	
	/**
	 * filter for correct display in textbox/textarea view
	 *
	 * @param string $string the string to parse
	 */
	function edit_filter($string)
	{
		return htmlentities($string);
#		return stripslashes(htmlentities($string));
#		return stripslashes($string);
	}
	
	function extensionListing($string)
	{
		return '.'.str_replace(',', ', .', $string);
	}
	
	function processParagraphs($string, $classes = array())
	{
		$new_string = '';
		
		$string = str_replace("\r", '', $string);
		
		$paragraphs = explode("\n", $string);
		foreach($paragraphs as $paragraph)
		{
			$class = array_shift($classes);
			$new_string .= '<p'.(!is_null($class) ? ' class="'.$class.'"' : '').'>'.$paragraph.'</p>';
		}
		
		return $new_string;
	}
	
	/**
	 * filter for incoming data from flash sites
	 * 
	 * @param $string:String
	 * @return String
	 */
	function flash_input_filter($string)
	{
		$string = urldecode($string);
		$string = utf8_decode($string);
		$string = str_replace(array("\r"), array("\n"), $string);
		
		return trim($string);
	}
	
	/**
	 * filter for use in xml files
	 *
	 * @param string $string the string to parse
	 */
	function xml_filter($string)
	{
		// filter possible double line endings.
		$string = str_replace("\r", '', $string);
		
		// characters to be replaced.
		$chars = array(
					"\x91"	=> '\'', // open ' to '.
					"\x92"	=> '\'', // closed ' to '.
					"\x93"	=> '"', // open " to ".
					"\x94"	=> '"', // closed " to ".
					);
		
		// replace characters and encode string.
		return utf8_encode(strtr($string, $chars));
	}
	
	/**
	 * filters a single quote (')
	 * filter specially for the sitemap (' filter)
	 *
	 * @param string $string
	 */
	function squote_filter($string)
	{
#		return str_replace("'", "", html_entity_decode(stripslashes($string)));
		return str_replace("'", "", html_entity_decode($string));
	}
	
	/**
	 * filter to translate database datetime format to a displayable form
	 * language: dutch
	 *
	 * @param string $datetime
	 * @param boolean $short [optional] months in short view
	 * @param boolean $time [optional] display time
	 */
	function readableDate($datetime, $short = false, $time = true)
	{
		if ($datetime == '0000-00-00 00:00:00' || $datetime == '0000-00-00' || empty($datetime))
		{
			return '-';
		}
		
		list($year, $month, $day, $hour, $minutes, $seconds) = preg_split('/[: \/.-]/', $datetime);
		
		if (!$short)
		{
			$months = array('', dgettext('backend', 'januari'), dgettext('backend', 'februari'), dgettext('backend', 'maart'), dgettext('backend', 'april'), dgettext('backend', 'mei'), dgettext('backend', 'juni'), dgettext('backend', 'juli'), dgettext('backend', 'augustus'), dgettext('backend', 'september'), dgettext('backend', 'oktober'), dgettext('backend', 'november'), dgettext('backend', 'december'));
		} else {
			$months = array('', dgettext('backend', 'jan'), dgettext('backend', 'feb'), dgettext('backend', 'mrt'), dgettext('backend', 'apr'), dgettext('backend', 'mei'), dgettext('backend', 'juni'), dgettext('backend', 'juli'), dgettext('backend', 'aug'), dgettext('backend', 'sept'), dgettext('backend', 'okt'), dgettext('backend', 'nov'), dgettext('backend', 'dec'));
		}
		
		$day = (int) $day;
		$month = (int) $month;
		
		// place in correct order, do not translate the words themselves.
		$date = str_replace(array('day', 'month', 'year'), array($day, $months[$month], $year), dgettext('backend', 'day month year'));
		
		if ($time)
		{
			$date = $hour.':'.$minutes.', '.$date;
		}
		
		return $date;
	}
	
	/**
	 * filter to translate database datetime format to a displayable form
	 * language: dutch
	 *
	 * @param string $datetime
	 * @param boolean $short [optional] months in short view
	 * @param boolean $time [optional] display time
	 */
	function unix2dutch($unixtime, $out = 'd-m-Y H:i:s')
	{
		//$unixtime = strtotime($datetime);
		return date($out, $unixtime);
	}
	
	/**
	 * filter to make cvs version info human readable
	 *
	 * @param string $string
	 * @return string
	 */
	function version($string)
	{
		if (strpos($string, '$Name:') !== false)
			return 'Development';
		return str_replace(array('_', '-', 'rel'), array(' ', '.', 'version:'), $string);
	}
	
	/**
	 * filter to make filesize more human readable
	 * 
	 * @param integer $bytes
	 * @return string
	 */
	function formatFilesize($bytes)
	{
		if ($bytes > 0)
		{
			$names = array( 'B', 'KB', 'MB', 'GB', 'TB');
			$i = 0;
			$count = count($names) ;
			while ($i <  $count && $bytes > 1024) //We expect this to terminate after two to three iterations.
			{
				$bytes = $bytes / 1024;
				$i++;
			}
			if ($i > 1)
			{
				return number_format($bytes, 2, ',', '').' '.$names[$i];
			} else {
				return number_format($bytes, 0, ',', '').' '.$names[$i];
			}
		} else {
			return '0 B';
		}
	}
	
	/**
	 * Generate a clean url.
	 * 
	 * @param $album:Album
	 * @param $section:Section
	 * @param $item:Item
	 * @return String
	 */
	function url($album = null, $section = null, $item = null, $language = '')
	{
		global $system;
		
		$url = '';
		$url .= $system->base_url;
		
		// multilanguage?
		if (YP_MULTILINGUAL)
		{
			if (!empty($language))
			{
				$url .= $language.'/';
			} else {
				if ($GLOBALS['YP_CURRENT_LANGUAGE'] != $GLOBALS['YP_DEFAULT_LANGUAGE'])
				{
					$url .= $GLOBALS['YP_CURRENT_LANGUAGE'].'/';
				}
			}
		}
		
		if ($album != null)
		{
			$url .= $album->getLink().'/';
			
			if ($section != null)
			{
				$url .= $section->getLink().'/';
				
				if ($item != null)
				{
					$url .= $item->getLink().'/';
				}
			}
		}
		
		return $url;
	}
	
	/**
	 * Generate a clean url to be used as RSS links.
	 * 
	 * @param $album:Album
	 * @param $section:Section
	 * @param $item:Item
	 * @param $language:String
	 */
	function rssUrl($album = null, $section = null, $item = null, $language = '')
	{
		global $system;
		
		$url  = 'http://'.DOMAIN;
		$url .= $system->base_url;
		$url .= 'rss/';
		
		// multilanguage?
		if (YP_MULTILINGUAL)
		{
			if (!empty($language))
			{
				$url .= $language.'/';
			} else {
				if ($GLOBALS['YP_CURRENT_LANGUAGE'] != $GLOBALS['YP_DEFAULT_LANGUAGE'])
				{
					$url .= $GLOBALS['YP_CURRENT_LANGUAGE'].'/';
				}
			}
		}
		
		if ($album != null)
		{
			$url .= $album->getLink().'/';
			
			if ($section != null)
			{
				$url .= $section->getLink().'/';
				
				if ($item != null)
				{
					$url .= $item->getLink().'/';
				}
			}
		}
		
		return $url;
	}
}
?>