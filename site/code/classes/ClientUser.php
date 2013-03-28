<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 *  */

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
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;
	var $_system;
	
	var $xml_albums;
	var $contents;
	
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
	
	function login($username, $password)
	{
		// check for correct login
		$user = array();
		
		// uses user input
		$query = new Query("SELECT %s, %s, %s FROM `%s` WHERE login='%s' AND password='%s'", $this->_table['client_users'], array('id', 'online', 'last_login'), array('login' => $username, 'password' => $password), 'row');
		$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
		$this->_db->doQuery($query, $user);
		
		// no user found
		if (!$user)
		{
			$this->access = false;
			$this->feedback = 'Login or password is incorrect.';
			return;
		}
		
		// user found but is stated offline
		if ($user['online'] == 'N')
		{
			$this->access = false;
			$this->feedback = 'Your login is disabled.';
			return;
		}
		
		foreach ($user as $key => $value)
		{
			$this->{$key} = $value;
		}
		
		$this->access = true;
		$this->feedback = 'logged in.';
		
		// login correct, update last_login value
		$query = "UPDATE `".$this->_table['client_users']."` SET previous_login='".$user['last_login']."', last_login=NOW() WHERE id='".$user['id']."'";
		
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "update", FALSE);
	}
	
	function loadAlbums()
	{
		global $canvas, $yourportfolio;
		
		$this->xml_albums = array();
		$this->_canvas = $canvas;
		$this->session = array('id' => $this->id);
		$this->settings = $yourportfolio->settings;
		$this->preferences = $yourportfolio->preferences;
		$this->preferences['title'] = 'login+albums';
		
		if (isset($yourportfolio->settings['unassigned_restricted_albums_for_all']) && $yourportfolio->settings['unassigned_restricted_albums_for_all'])
		{
			$query = "SELECT id, `restricted`, user_id, name, text, template, type, link FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='Y' AND (user_id='".$this->id."' OR user_id IS NULL) ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		} else {
			$query = "SELECT id, `restricted`, user_id, name, text, template, type, link FROM `".$this->_table['albums']."` WHERE online='Y' AND restricted='Y' AND user_id='".$this->id."' ORDER BY IF(position > 0, position, 999) ASC, id ASC";
		}
		$this->_db->doQuery($query, $this->xml_albums, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
		
		if (YP_MULTILINGUAL)
		{
			$xml_file = (file_exists(SETTINGS.'yourportfolio_multilingual.xml')) ? SETTINGS.'yourportfolio_multilingual.xml' : XML.'yourportfolio_multilingual.xml';
		} else {
			$xml_file = (file_exists(SETTINGS.'yourportfolio.xml')) ? SETTINGS.'yourportfolio.xml' : XML.'yourportfolio.xml';
		}
		
		$GLOBALS['GENERATING_XML'] = true;
		
		require_once(XML.'XMLUtil.php');
		
		ob_start();
		require($xml_file);
		$this->contents = ob_get_contents();
		ob_end_clean();
		
		unset($this->xml_albums);
		unset($this->_canvas);
		unset($this->session);
		unset($this->settings, $this->preferences);
	}
	
	function generateXML()
	{
		global $canvas;
		
		$xml = '';
		
		$xml .= '<client access="'.($this->access ? '1' : '0').'" id="'.$this->id.'">';
		if ($this->access)
		{
			$xml .= $this->contents;
			$xml .= '<message>'.$canvas->xml_filter($this->feedback).'</message>';
		} else {
			$xml .= '<message>'.$canvas->xml_filter($this->feedback).'</message>';
		}
		$xml .= '</client>';
		
		return $xml;
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
		{
			
		}
	}

	function handle($data = array())
	{
		$action = (isset($data['action'])) ? $data['action'] : 'undefined';
		$client = null;
		
		switch ($action)
		{
			case ('login'):
				$client = new ClientUser();
				$client->login($data['username'], $data['password']);
				
				if ($client->access)
				{
					$client->loadAlbums();
				}
				break;
			case ('logout'):
				
				break;
		}
		
		return $client;
	}
}
?>