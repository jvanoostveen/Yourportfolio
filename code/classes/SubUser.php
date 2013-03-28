<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * SubUser class
 * for managing subusers which can be assigned to one or more albums
 *
 * @package yourportfolio
 * @subpackage Core
 */
class SubUser
{
	/**
	 * vars available from database
	 */
	var $id;
	var $site_user_id;
	var $online;
	var $name;
	var $login;
	var $password;
	var $last_login;
	
	/**
	 * vars used in class etc
	 * don't save them to database
	 */
	var $_autostore = array(	'site_user_id', 'online', 'name', 'login', 'password', 'last_login' );
	var $album_ids = array();
	var $albumCount;
	
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
	function SubUser($data = null)
	{
		$this->__construct($data);
	}
	
	/**
	 * give object some default values
	 */
	function init()
	{
		$this->id			= 0;
		$this->online		= 'Y';
		$this->last_login	= '0000-00-00 00:00:00';
		$this->album_ids		= array();
	}
	
	/**
	 * load data needed for editing
	 * or when item can't be found in database, init default values
	 *
	 */
	function load()
	{
		$query = "SELECT online, name, login, password, last_login FROM `".$this->_table['subusers']."` WHERE id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'object', false) )
		{
			$this->init();
			return;
		}
		
		$query = "SELECT album_id FROM `".$this->_table['subuser_album']."` WHERE subuser_id='".$this->id."'";
		if ( !$this->_db->doQuery($query, $this->album_ids, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false) )
		{
			$this->album_ids = array();
		}
	}

	function save($data)
	{
		// parse data to object
		foreach($data as $key => $value)
		{
			$this->$key = $value;
		}
		
		if (!isset($this->id))
		{
			trigger_error('$id is missing', E_USER_ERROR);
		}
		
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
		$q_start .= "`".$this->_table['subusers']."` SET ";
		
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
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $return, TRUE);
		if (empty($this->id))
		{
			$this->id = $result;
		}
		
		// store related album ids
		// first, remove all old ones if any
		$query = "DELETE FROM `".$this->_table['subuser_album']."` WHERE subuser_id=".$this->id.""; 
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
//		trigger_error($this->album_ids)
		
		if (!empty($this->album_ids))
		{
			foreach ($this->album_ids as $album_id)
			{
				$query = "INSERT INTO `".$this->_table['subuser_album']."` SET subuser_id='".$this->id."', album_id='".$album_id."'";
				$result = null;
				$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '','insert', true);
			}
		}
	}
	
	function countAssignedAlbums()
	{
		if ($this->albumCount == null)
		{
			$query = "SELECT COUNT(*) FROM `".$this->_table['subuser_album']."` WHERE subuser_id='".$this->id."'";
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
		$query = "UPDATE `".$this->_table['subusers']."` SET online=IF(online='Y','N','Y') WHERE id='".$this->id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	}
	
	/**
	 * removes all references to this subuser in all tables
	 */
	function destroy()
	{
		$query = "DELETE FROM `".$this->_table['subuser_album']."` WHERE subuser_id=".$this->id.""; 
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		
		$query = "DELETE FROM `".$this->_table['subusers']."` WHERE id='".$this->id."' LIMIT 1";
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
}
?>