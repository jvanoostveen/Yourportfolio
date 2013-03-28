<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Guestbook Message
 *
 * @package yourportfolio
 * @subpackage Core
 */
class GuestbookMessage
{
	var $id;
	var $created;
	var $modified;
	var $online;
	var $name;
	var $email;
	var $message;
	var $language;
	
	var $_autostore = array('online', 'name', 'email', 'message');
	
	/**
	 * objects needed to run this component
	 * @var object $_db
	 * @var array $_table
	 * @var object $_system
	 */
	var $_db;
	var $_table;
	var $_system;
	var $_yourportfolio;
	
	/**
	 * constructor (PHP5)
	 *
	 * @param array $data can contain data needed to create an album without fetching from database itself
	 */
	function __construct($data = array())
	{
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		$this->_system = &$system;
		
		global $yourportfolio;
		$this->_yourportfolio = &$yourportfolio;
		
		if (!empty($data))
		{
			foreach($data as $key => $value)
			{
				$this->$key = $value;
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
	
	function load()
	{
		$query = "SELECT created, modified, online, name, email, message, language FROM `".$this->_table['guestbook']."` WHERE id='".$this->_db->filter($this->id)."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			//
		}
	}
	
	function save($data = array())
	{
		// first, set id
		if (!isset($data['id']))
		{
			trigger_error('$id is missing', E_USER_ERROR);
		}
		$this->id = (int) $data['id'];
		unset($data['id']);
		
		// load old data if album is existing one
		$this->load();
		
		// overwrite data with new data
		foreach($data as $key => $value)
		{
			if (!is_null($value))
			{
				$this->$key = $value;
			}
		}
		
		$new_entry = false;
		
		$q_start = '';
		$q_where = '';
		$q = '';
		$return = '';
		
		if ($this->id === 0)
		{
			// new
			$new_entry = true;
			
			$q_start .= "INSERT INTO ";
			$return = 'insert';
		} else {
			// update
			$q_start .= "UPDATE ";
			$q_where .= " WHERE id='".$this->id."' LIMIT 1";
			$return = 'update';
		}
		$q_start .= "`".$this->_table['guestbook']."` SET ";
		
		foreach($this->_autostore as $key)
		{
			if ($this->$key == '' || is_null($this->$key))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->_db->filter($this->$key)."',";
			}
		}
		
		if (empty($this->id))
		{
			$q .= "created=NOW(),";
		}
		$q .= "modified=NOW()";
		
		$query = $q_start.$q.$q_where;
		$result = 0;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, TRUE);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		unset($q_start, $q, $q_where, $return);
	}
}
?>