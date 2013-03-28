<?PHP
/**
 * Project:			yourportfolio
 * 
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Guestbook Message class
 *
 * @package yourportfolio
 * @subpackage Site
 */
class GuestbookMessage
{
	/**
	 * variables from form
	 */
	var $album_id;
	var $date;
	var $name;
	var $email;
	var $message;
	
	var $allowed = array('album_id', 'date', 'name', 'email', 'message', 'language');
	
	/**
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;

	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct($data = array())
	{
		global $db;
		
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		if (!empty($data))
		{
			foreach($data as $key => $value)
			{
				if (in_array($key, $this->allowed))
				{
					$this->{$key} = $value;
				}
			}
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function GuestbookMessage($data = array())
	{
		$this->__construct($data);
	}
}
?>
