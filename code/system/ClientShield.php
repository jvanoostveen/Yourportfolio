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
class ClientShield
{
	/**
	 * custom name for use of multiple shields on same server/site
	 * @var string
	 */
	var $shield_prefix = 'yourportfolio_';
	
	var $must_relocate = false;
	
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
	
	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct()
	{
		$this->access = false;
		
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		$this->shield_prefix = DOMAIN.'sess_';

#		global $system;
#		$this->_system = &$system;
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function ClientShield()
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
		#$login['md5pass'] = md5($login['pass']);
		
		// check for correct login
		$user = array();
		
		// uses user input
		$query = new Query("SELECT %s, %s, %s FROM `%s` WHERE login='%s' AND password='%s'", $this->_table['client_users'], array('id', 'online', 'last_login'), array('login' => $login['login'], 'password' => $login['password']), 'row');
		$query->setErrorLocation(__FILE__, __LINE__, __FUNCTION__, __CLASS__);
		$this->_db->doQuery($query, $user);
		
		// no user found
		if (!$user)
		{
			$this->access = false;
			$this->feedback = _('login of wachtwoord is verkeerd');
			return;
		}
		
		// user found but is stated offline
		if ($user['online'] == 'N')
		{
			$this->access = false;
			$this->feedback = _('uw login staat uitgeschakeld');
			return;
		}
		
		// retrieve domain info (seperate from what the user[domain_id] says)
		// check if domain is valid
		if (!SUB_DOMAIN) // site is located on it's own domain
			$query = "SELECT id, tablename, first_domain FROM `".$this->_table['domains']."` WHERE domain='".DOMAIN."' AND subdomain IS NULL LIMIT 1";
		else
			$query = "SELECT id, tablename, first_domain FROM `".$this->_table['domains']."` WHERE domain='".DOMAIN."' AND subdomain='".SUB_DOMAIN."' LIMIT 1";
		
#		$query = 'SELECT id, tablename FROM '.$this->_table['domains'].' WHERE domain="'.DOMAIN.'" LIMIT 1';
#		$query = 'SELECT id, domain, tablename FROM '.$this->_table['domains'].' WHERE id="'.$user['domain'].'" LIMIT 1';
		$domain = array();
		$this->_db->doQuery($query, $domain, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'row', false);
		
		// unknown domain
		if (!$domain)
		{
			$this->access = false;
			$this->feedback = _('dit domein is niet geregistreerd bij het systeem');
			return;
		}
		
		if (!is_null($domain['first_domain']))
			$domain['id'] = $domain['first_domain'];
		
		if (!defined('DOMAIN_ID'))
			define('DOMAIN_ID', $domain['id']);
		
		// user found and correct, update login data
		$query = "UPDATE `".$this->_table['client_users']."` SET previous_login='".$user['last_login']."', last_login=NOW() WHERE id='".$user['id']."'";
		
		$result = null;
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', "update", FALSE);


		$this->access = true;
		
		$_SESSION['client_shielding'] 				 = $this->shield_prefix.session_id();
		$_SESSION['session_client_shield']['id']	 = $user['id'];
#		$_SESSION['session_client_shield']['domain'] = $domain['id'];
	
		$this->logOn();
	}
	
	/**
	 * called after authentication of an user
	 *
	 */
	function logOn()
	{
		// show the restricted albums for this user on the login spot.. relocate to the first album
		$this->must_relocate = true;
	}

	/**
	 * checks if access is still ok
	 *
	 */
	function checkAuth()
	{
		if (isset($_SESSION['client_shielding']) && $_SESSION['client_shielding'] == $this->shield_prefix.session_id())
			$this->access = true;
		else
			$this->access = false;
		
		return $this->access;
	}
	
	function checkPrivileges($album)
	{
		if ($this->checkAuth())
		{
			return ($album->user_id == $_SESSION['session_client_shield']['id']);
		} else {
			return false;
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
	
		unset($_SESSION['client_shielding']);
		unset($_SESSION['session_client_shield']);
		session_destroy();
		session_unset();
#		session_start();
#		if (function_exists('session_regenerate_id'))
#			session_regenerate_id();
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
			case('client_login'):
				$this->checkUser($input['login']);
				break;
			default:
				// no action or an unknown action is given, trigger error to the log
				trigger_error("Unknown action given (".$input['action'].").\n".__CLASS__."::".__FUNCTION__." > ".__LINE__.".", E_USER_NOTICE);
		}
	}
}
?>