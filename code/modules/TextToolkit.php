<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * class: TextToolkit
 * handles and parses texts
 * this class requires PHP 4.3.0 or greater
 * 
 * @package yourportfolio
 * @subpackage Toolkits
 */
class TextToolkit
{
	/**
	 * filter variable
	 * @var boolean
	 */
	var $filter = false;

	/**
	 * boolean if patterns are already loaded
	 * @var boolean
	 */
	var $_patterns_loaded = false;
	
	/**
	 * patterns and macros
	 * @var array ...
	 */
	var $_pattern_begin;
	var $_pattern_end;
	var $_macros_begin;
	var $_macros_end;
	
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		if (phpversion() < '4.3.0')
			trigger_error(__CLASS__.' requires PHP 4.3.0 or greater', E_USER_ERROR);
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function TextToolkit()
	{
		$this->__construct();
	}
	
	/**
	 * normalizes string, so it contains only [a-z] and [0-9] and -
	 * (for flat urls)
	 * 
	 * @param string $string
	 * @return string
	 */
	function normalize($string)
	{
		$string = strtolower(htmlentities(strtr($string, array("�"=>"Ae", "�"=>"Ue", "�"=>"Oe", "�"=>"ae", "�"=>"ue", "�"=>"oe"))));
		$string = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $string);
		$string = preg_replace("/([^a-z0-9]+)/", '', html_entity_decode($string));
		$string = trim($string);
		return $string;
	}
	
	/**
	 * removes all attributes from tags in the string
	 * @param string $string
	 * @return string
	 */
	function stripattributes($string)
	{
		// removing all tag attributes
		$string = preg_replace('!<([A-Z]\w*) (?:\s* (?:\w+) \s* = \s* (?(?=["\']) (["\'])(?:.*?\2)+ | (?:[^\s>]*) ) )* \s* (\s/)? >!ix', '<\1\3>', $string);
		
		return $string;
	}
	
	/**
	 * removes all tags
	 * can be allowed some tags
	 * 
	 * @param string $string
	 * @param string $allowed
	 * @return string
	 */
	function striptags($string, $allowed = '')
	{
		$string = strip_tags($string, $allowed);
		
		return $string;
	}
	
	/**
	 * wrapper function for stripattributes and striptags
	 * 
	 * @param string $string
	 * @param string $allowed
	 * @return string
	 */
	function stripall($string, $allowed = '')
	{
		$string = $this->stripattributes($string);
		$string = $this->striptags($string, $allowed);
		
		return $string;
	}
	
	/**
	 * wrapper function for parsing the text
	 * searches for a pattern and calls the function
	 *
	 * @uses $_patterns_loaded
	 * @uses _patterns_load()
	 * @uses _parseBeginTags()
	 * @uses _parseEndTags()
	 *
	 * @param string $text
	 * @return string
	 */
	function parseText($text)
	{
		if ( $this->_patterns_loaded === false && $this->_patterns_load() === false ) // check if patterns are available, otherwise load them (once)
			trigger_error('Could not load patterns and macros', E_USER_ERROR);
		
		$this->me = &$this;
		
		$text = preg_replace_callback($this->_pattern_begin, array($this->me, '_parseBeginTags'), $text); // $this->me otherwise called as Class::Function, then no $this-> available in called functions
# PHP5?	$text = preg_replace_callback($pattern, array('self', '_parseBeginTags'), $text);

		$text = preg_replace_callback($this->_pattern_end, array($this->me, '_parseEndTags'), $text);
# PHP5?	$text = preg_replace_callback($pattern, array('self', '_parseEndTags'), $text);

		// delete line breaks in lists
		$text = str_replace('</li>\r\n', '</li>', $text);
		$text = str_replace('<ul>\r\n', '<ul>', $text);
		$text = str_replace('</ul>\r\n', '</ul>', $text);
		$text = str_replace('<ol>\r\n', '<ol>', $text);
		$text = str_replace('</ol>\r\n', '</ol>', $text);

		unset($this->me);
		return $text;
	}
	
	/**
	 * replaces the begin tags [tag] and [tag=value]
	 *
	 * @param array $matches the matches by the preg_replace_callback()
	 * @return string
	 * @access private
	 */
	function _parseBeginTags($matches)
	{
		if (array_key_exists(strtolower($matches[1]), $this->_macros_begin)) // it is a tag to parse
		{
			$string = $this->_macros_begin[$matches[1]];
			if (isset($matches[2])) // has a value after =
				$string = str_replace('%%VALUE%%', $matches[2], $string);
			return $string;
		}
		if (!$this->filter) return $matches[0];
	}
	
	/**
	 * replaces the end tags [/tag]
	 *
	 * @param array $matches the matches by the preg_replace_callback()
	 * @return string
	 * @access private
	 */
	function _parseEndTags($matches)
	{
		if (array_key_exists(strtolower($matches[1]), $this->_macros_end)) // it is a tag to parse
			return $this->_macros_end[$matches[1]];
		if (!$this->filter) return $matches[0];
	}
	
	/**
	 * loads preg patterns and macros for text parsing
	 * macro part can be rewritten so it is retrieved from a database
	 *
	 * @return boolean
	 * @access private
	 */
	function _patterns_load()
	{
		global $yourportfolio;
		
		/**
		 * pattern for begin tags
		 * @var string
		 */
		$this->_pattern_begin = "|\[([a-zA-Z]*)(?:=(.*))?\]|U";
		
		/**
		 * marcos for the begin tags [tag] and [tag=value]
		 * todo: maybe load from database for dynamic editing
		 * @var array
		 */
		$this->_macros_begin = array(
			// external link
			'elink'		=> '<a href="%%VALUE%%" target="_blank" class="elink">',
			// link to email
			'email'		=> '<a href="mailto:%%VALUE%%" class="email">',
			// list item
			'li'		=> '<li>',
			// bold
			'b'			=> '<b>',
			// italic
			'i'			=> '<i>',
			);
		
		// internal link
		if ($yourportfolio->preferences['version'] < 9)
		{
			$this->_macros_begin['link'] = '<a href="asfunction:Application.asfunction_show,%%VALUE%%" class="link" rel="nofollow">';
		} else {
			$this->_macros_begin['link'] = '<a href="event:%%VALUE%%" class="link" rel="nofollow">';
		}
		
		/**
		 * pattern for end tags
		 * @var string
		 */
		$this->_pattern_end = "|\[/([a-zA-Z]*)\]|U";
	
		/**
		 * marcos for the end tags [/tag]
		 * todo: maybe load from database for dynamic editing
		 * @var array
		 */
		$this->_macros_end = array(
			// internal link
			'link'		=> '</a>',
			// external link
			'elink'		=> '</a>',
			// link to email
			'email'		=> '</a>',
			// list item
			'li'		=> '</li>',
			// bold
			'b'			=> '</b>',
			// italic
			'i'			=> '</i>',
			);
		
		$this->_patterns_loaded = true;
		return $this->_patterns_loaded;
	}
}
?>