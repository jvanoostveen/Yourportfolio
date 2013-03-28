<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * Contact class
 *
 * @package yourportfolio
 * @subpackage Site
 */
class Contact
{
	/**
	 * variables from form
	 */
	var $album_id;
	var $photographer_id;
	var $name;
	var $email;
	var $message;
	// var .... (dynamicly added from form)
	
	/**
	 * variables from db
	 */
	var $photographer_name;
	var $photographer_email;
	var $domain;
	
	var $bcc_emails = array();
	
	/**
	 * runtime vars
	 */
	var $success;
	var $feedback;
	
	/**
	 * objects needed to run this component
	 */
	var $_db;
	var $_table;
	var $_system;
	var $_canvas;
	var $_mailToolkit;

	/**
	 * constructor (PHP5)
	 *
	 */
	function __construct()
	{
		global $db;
		
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		
		$this->_system = &$system;
		
		global $canvas;
		$this->_canvas = &$canvas;
		
		$this->_mailToolkit = $system->getModule('MailToolkit');
		
		$this->domain = DOMAIN;
		
		$this->getForm();
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Contact()
	{
		$this->__construct();
	}
	
	/**
	 * Get the form data <br />
	 * Also loads the default recipient data from cache, this can be overridden 
	 * by he loadAlbumData() function
	 * @return void
	 */
	function getForm()
	{
		// needed for the cached data
		global $yourportfolio;

		// translate post vars to array
		// ****TODO****: need to apply input filters!
		if (isset($_POST['contactForm']['contact']))
		{
			$this->email = $_POST['contactForm']['contact']['email'];
			$this->name = $_POST['contactForm']['contact']['name'];
			$this->message = $_POST['contactForm']['contact']['message'];
			
		} else {
			// form sent from flash
			foreach ($_POST as $key => $value)
			{
				if (strpos($key, $_POST['formName'].'_') !== false)
				{
					$this->{substr($key, strlen($_POST['formName']) + 1)} = (is_numeric($value)) ? intval($value) : $this->_canvas->flash_input_filter($value);
				}
			}
		}
		
		
		// the yourportfolio object contains the contact info which is loaded from
		// the cache, or when unavailable the database
/*
		$this->photographer_name = $yourportfolio->firstname . ' ' . $yourportfolio->lastname;
		$this->photographer_id = $yourportfolio->user_id;
		
		
		$mail = explode(',', $yourportfolio->email);
		$this->photographer_email = array_shift($mail);
		foreach ($mail as $key => $value)
		{
			$this->bcc_emails[] = trim($value);
		}
*/
	}
	
	/**
	 * Checks whether an email address is correct
	 * @return boolean
	 */
	function validateEmailAddress()
	{
		return $this->_mailToolkit->validateEmailAddress($this->email);
	}
	
	/**
	 * Loads the album contact data if specified
	 * @return void
	 */
	function loadAlbumData()
	{
		if ( isset($_POST['form_album_id']) && ($album_id = (int) $_POST['form_album_id']) > 0 )
		{
			/* select the recipient name and email address from the database if specified */
			$query = "SELECT value, parameter FROM `".$this->_table['parameters']."` WHERE album_id='".$album_id."'";
			$result = null;
			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array', false, array('index_key' => 'parameter'));
			
			if (!empty($result))
			{
				$this->photographer_name = $result['name']['value'];
				
				$mail = explode(',', $result['mail']['value']);
				$this->photographer_email = array_shift($mail);
				foreach ($mail as $key => $value)
				{
					$this->bcc_emails[] = trim($value);
				}
				
				if (isset($result['subject']) && !empty($result['subject']['value']))
				{
					$this->subject = $result['subject']['value'];
				}
			}
		}
	}
	
	/**
	 * Send mail.
	 * 
	 * @return Void
	 */
	function sendEmail()
	{
		$this->saveMessage();
		
		$this->_mailToolkit->setEmailHeader('To', $this->photographer_email, $this->photographer_name);
		$this->_mailToolkit->setEmailHeader('From', $this->email, $this->name);
		$this->_mailToolkit->setEmailHeader('Reply-To', $this->email, $this->name);
		foreach ($this->bcc_emails as $bcc_email)
		{
			$this->_mailToolkit->setEmailHeader('Bcc', $bcc_email, $bcc_email);
		}
		
		$this->_mailToolkit->setEmailHeader('Errors-To', 'yourportfolio@webdebugger.nl', 'WebDebugger');
		
		$this->_mailToolkit->setHeader('Subject', $this->makeSubject());
		
		$mail = $this->retrieveMail();
		
		$this->_mailToolkit->addTextPart($mail);
		
		$result = $this->_mailToolkit->sendEmail();
		
		return $result;
	}
	
	/**
	 * Stores the message in the database
	 * message date is stored as unix timestamp since this is easy to convert
	 */
	function saveMessage()
	{
		$result = null;
		$query = "INSERT INTO `".$this->_table['contact']."` SET date=NOW(), name='".$this->_db->filter($this->name)."', address='".$this->_db->filter($this->email)."', message='".$this->_db->filter($this->message)."'";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert');
	}
	
	function makeSubject()
	{
		if (!empty($this->subject))
		{
			return $this->_canvas->text_filter($this->subject);
		} else {
			return 'Mail van '.$this->domain;
		}
	}
	
	function retrieveMail()
	{
		if (file_exists(SETTINGS.'mail_message.txt'))
		{
			$mailfile = SETTINGS.'mail_message.txt';
		} else {
			$mailfile = SITE_MAIL;
		}
		
		ob_start();
		require($mailfile);
		$mail = ob_get_contents();
		ob_end_clean();
		
		return $mail;
	}
	
	/**
	 * Sends the response back to the flash frontend
	 * @return string
	 */
	function generateOutput()
	{
		$success = ($this->success) ? 'true' : 'false';
		return '&success='.$success.'&feedback='.urlencode($this->feedback).'';
	}
	
}
?>