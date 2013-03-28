<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Shield class
 * handles login in and checking if login is valid
 *
 * @package yourportfolio
 * @subpackage Pages
 */
class Shield
{
	/**
	 * custom name for use of multiple shields on same server/site
	 * @var string
	 */
	var $shield_prefix = 'yourportfolio';
	
	/**
	 * database object
	 * @var object
	 */
	var $_db;
	
	/**
	 * database tables
	 * @var array
	 */
	var $_table;
	
	/**
	 * shield vars
	 * @var boolean $access
	 * @var string $feedback
	 */
	var $access;
	var $feedback;
	
	var $loggedIn = false; // set to true when user logged in
	
	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct()
	{
		$this->access = false;
		
		$this->shield_prefix = 'yp_'.DOMAIN;
		
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
#		global $system;
#		$this->_system = &$system;
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Shield()
	{
		$this->__construct();
	}
	
	/**
	 * check for user to login
	 *
	 * @param array $login containing login and pass
	 */
	function checkUser($login)
	{
		session_regenerate_id();
		
		$this->loadRuntime_ini();	
		
		// validate login and password
		// login - a-zA-Z 0-9 @
		// pass - ...?
		
		// check for correct login
		$challenge = array();
		
		$query = new Query("SELECT %s, %s FROM `%s` WHERE id='%u' LIMIT 1", $this->_table['challenges'], array('id', 'challenge'), array('id' => $login['challenge']), 'row');
		$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
		$this->_db->doQuery($query, $challenge);
		
		if (!empty($challenge))
		{
			$result = null;
			$query = "DELETE FROM `".$this->_table['challenges']."` WHERE id='".$challenge['id']."'";
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		}
		
		if (rand() < 0.2)
		{
			$this->clearChallenges();
		}
		
		$user = array();
		
		// uses user input
		$query = new Query("SELECT %s, %s, %s, %s, %s FROM `%s` WHERE login='%s' LIMIT 1", $this->_table['users'], array('id', 'password', 'master', 'online', 'last_login'), array('login' => $login['login']), 'row');
		$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
		$this->_db->doQuery($query, $user);
		
		$table = $this->_table['users'];
		
		if (md5($user['password'].$challenge['challenge']) !== $login['password'])
		{
			$user = false;
		}
		
		// new feature: check to see if it is a sub user, with limited access to albums
		if (!$user)
		{
			$user = array();
			$query = new Query("SELECT %s, %s, %s, %s, %s FROM `%s` WHERE login='%s' LIMIT 1", $this->_table['subusers'], array('id', 'password', 'site_user_id', 'online', 'last_login'), array('login' => $login['login']), 'row');
			$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
			$this->_db->doQuery($query, $user);
			
			if (md5(md5($user['password']).$challenge['challenge']) !== $login['password'])
			{
				$user = false;
			}
			
			if (!$user)
			{
				// still no user found
				$this->access = false;
				$this->feedback = _('login of wachtwoord is verkeerd');
				
				return;
			}
			
			$user['master'] 		= 'N';
			$user['limited']		= true;
			$user['limited_id']	= $user['id'];
			$user['id']			= $user['site_user_id'];
			
			$table = $this->_table['subusers'];
		} else {
			$user['limited']	= false;
		}
		
		// user found but is stated offline
		if ($user['online'] == 'N')
		{
			$this->access = false;
			$this->feedback = _('uw login staat uitgeschakeld');
			return;
		}
		
		// if user is master, all domains are valid
		if ($user['master'] == 'Y')
		{
			// let the VIP enter!
			$user['master_id'] = $user['id'];
			
			// remap master to domainowner
			$user['id'] = $this->user_id;
		} else if ($user['limited'])
		{
			// limited user
		} else {
			if ($user['id'] != $this->user_id)
			{
				$this->access = false;
				$this->feedback = _('u kunt niet inloggen op dit domein, log in via uw eigen domein');
				return;
			}
		}
		
		// user found and correct, update login data
		if ($user['master'] == 'Y')
		{
			$query = "UPDATE `".$this->_table['users']."` SET previous_login='".$user['last_login']."', last_login=NOW() WHERE id='".$user['master_id']."'";
		} else {
			if (!$user['limited'])
			{
				$query = "UPDATE `".$table."` SET previous_login='".$user['last_login']."', last_login=NOW() WHERE id='".$user['id']."'";
			} else {
				$query = "UPDATE `".$table."` SET previous_login='".$user['last_login']."', last_login=NOW() WHERE id='".$user['limited_id']."'";
			}
		}
		
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', FALSE);

		$this->access = true;
		$_SESSION['shielding'] 					= $this->shield_prefix.session_id();
		$_SESSION['session_shield']['id']			= $user['id'];
		$_SESSION['session_shield']['limited']		= $user['limited'];
		if ($user['limited'])
		{
			$_SESSION['session_shield']['limited_id']	= $user['limited_id'];
		}
		if ($user['master'] == 'Y')
		{
			$_SESSION['session_shield']['master'] = true;
			$_SESSION['session_shield']['master_id'] = $user['master_id'];
		} else {
			$_SESSION['session_shield']['master'] = false;
		}
		$_SESSION['shielding_check'] = md5($this->shield_prefix.serialize($_SESSION['session_shield']));
		
		$this->loggedIn = true;
	}
	
	/**
	 * called after authentication of an user
	 *
	 */
	function onLogin()
	{
		// clean items for random_ids
		$result = null;
		$query = "UPDATE `".$this->_table['items']."` SET random_id=NULL WHERE random_id IS NOT NULL";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		// relocate because of old post data
		global $system;
		$system->relocate($system->url);
		
	}

	/**
	 * checks if access is still ok
	 *
	 */
	function checkAuth()
	{
		if (isset($_SESSION['shielding']) && $_SESSION['shielding'] == $this->shield_prefix.session_id())
		{
			// $_SESSION['shielding_check'] = md5($this->shield_prefix.serialize($_SESSION['session_shield']));
			if ($_SESSION['shielding_check'] == md5($this->shield_prefix.serialize($_SESSION['session_shield'])))
			{
				$this->access = true;
			} else {
				trigger_error('shielding check failed..', E_USER_WARNING);
				$this->access = false;
			}
		} else {
			$this->access = false;
		}
	}
	
	/**
	 * destroys and unsets the session
	 *
	 */
	function logOut()
	{
		// insert log entry (action = 1)
#		$query = "INSERT ".$this->_table['logs']." SET user_id='".$_SESSION['session_shield']['id']."', log_date=NOW(), action=2, hidden='".$_SESSION['session_shield']['hidden']."'";
#		$this->_db->doQuery_old($query, $result, "query error in ".__FILE__." on line ".__LINE__." (function: ".__FUNCTION__." of class: ".__CLASS__.")", "insert", FALSE);
	
//		unset($_SESSION['shielding']);
//		unset($_SESSION['session_shield']);
		
		// remove the session cookie
		if (isset($_COOKIE[session_name()]))
		{
			setcookie(session_name(), '', time() - 42000, '/');
		}
		
		session_unset();
		session_destroy();
		
		// there is (now) no need to create a new session, as we are to be redirected to index.php
//		session_start();
//		session_regenerate_id();
	}
	
	/**
	 * this function is called from startup.php for handling forms
	 * it can also trigger errors, but doesn't mean it contains a bug
	 * read the message in the log file to find the reason for the error
	 *
	 */
	function handleInput($input)
	{
		switch( isset($input['action']) ? $input['action'] : 'none' ) // checks to see if action is given, else it defaults to 'none'
		{
			case('login'):
				$this->checkUser($input['login']);
				break;
			default:
				// no action or an unknown action is given, trigger error to the log
				trigger_error("Unknown action given (".$input['action'].").\n".__CLASS__."::".__FUNCTION__." > ".__LINE__.".", E_USER_NOTICE);
		}
	}
	
	function loadRuntime_ini()
	{
		// load runtime vars needed
		if (!file_exists(SETTINGS.'runtime.ini'))
		{
			trigger_error('runtime.ini file is missing!', E_USER_ERROR);
		}
		
		foreach (parse_ini_file(SETTINGS.'runtime.ini') as $key => $value)
		{
			$this->{$key} = $value;
		}
	}
	
	function createChallenge()
	{
		$challenge = array();
		$challenge['id']     = 0;
		$challenge['string'] = md5(uniqid("yp"));
		$query = "INSERT INTO `".$this->_table['challenges']."` SET challenge='".$challenge['string']."', created=NOW()";
		$this->_db->doQuery($query, $challenge['id'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		
		return $challenge;
	}
	
	function clearChallenges()
	{
		$result = null;
		$query = "DELETE FROM `".$this->_table['challenges']."` WHERE created < DATE_SUB(NOW(), INTERVAL 1 DAY)";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
}
?>