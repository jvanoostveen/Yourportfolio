<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * ClientUser class
 * for managing users and restricted Albums
 *
 * @package yourportfolio
 * @subpackage Core
 */
class ClientUser
{
	/**
	 * vars available from database
	 */
	var $id;
	var $online;
	var $name;
	var $login;
	var $password;
	var $last_login;
	
	/**
	 * vars used in class etc
	 * don't save them to database
	 */
	var $albumCount;
	
	var $_autostore = array('online', 'name', 'login', 'password', 'last_login');
	
	/**
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;
	var $_system;

	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct($data = null)
	{
		global $db;
		
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		
		$this->_system = &$system;
		
		if (!empty($data))
		{
			foreach($data as $key => $value)
			{
				$this->$key = $value;
			}
			#$this->load();
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function ClientUser($data = null)
	{
		$this->__construct($data);
	}
	
	/**
	 * give object some default values
	 */
	function init()
	{
		$this->id = 0;
		$this->online = 'Y';
		$this->last_login = '0000-00-00 00:00:00';
	}
	
	/**
	 * load data needed for editing
	 * or when item can't be found in database, init default values
	 *
	 */
	function load()
	{
		$query = "SELECT online, name, login, password, last_login FROM `".$this->_table['client_users']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
			$this->init();
	}

	function save($data)
	{
		// parse data to object
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
		
		if (!isset($this->id))
			trigger_error('$id is missing', E_USER_ERROR);

		$q_start = "";
		$q_where = "";
		$q = "";
		$return = "";
		
		if ($this->id == 0)
		{
			// new
			$is_new = true;
			
			$q_start .= "INSERT INTO ";
			$return = 'insert';
		} else if (is_numeric($this->id) && $this->id > 0)
		{
			$this->id = (int) $this->id;
			
			// update
			$is_new = false;
			
			$q_start .= "UPDATE ";
			$q_where .= " WHERE id='".$this->id."'";
			$return = 'update';
		}
		$q_start .= "`".$this->_table['client_users']."` SET ";
		
		foreach($this->_autostore as $key)
		{
			if ($this->$key == '' || is_null($this->$key))
			{
				$q .= $key."=NULL,";
			} else {
				$q .= $key."='".$this->_db->filter($this->$key)."',";
			}
		}
		$q = substr($q, 0, -1);

		$result = 0;
		$query = $q_start.$q.$q_where;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, TRUE);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		
		
		// everything done, goto photobook overview
#		$this->_system->relocate('section.php?aid='.$album->id.'&sid='.$section->id);
	}
	
	function countAssignedAlbums()
	{
		if ($this->albumCount == null)
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['albums']."` WHERE user_id='".$this->id."'";
			$this->_db->doQuery($query, $this->albumCount, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		}
		return $this->albumCount;
	}
	
	/**
	 * switch online status of item
	 *
	 * @uses $_db
	 * @uses _xml_photo_update()
	 *
	 * @access public
	 */
	function switchOnline()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['client_users']."` SET online=IF(online='Y','N','Y') WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	function destroy()
	{
		$result = null;
		$query = "UPDATE `".$this->_table['albums']."` SET user_id=NULL WHERE user_id=".$this->id.""; 
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		$query = "DELETE FROM `".$this->_table['client_users']."` WHERE id='".$this->id."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
}
?>