<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

require(dirname(__FILE__).'/GuestbookMessage.php');

/**
 * Guestbook class
 *
 * @package yourportfolio
 * @subpackage Site
 */
class Guestbook
{
	/**
	 * variables from form
	 */
	var $album_id;
	var $name;
	var $email;
	var $message;
	var $language;
	
	var $allowed = array('album_id', 'name', 'email', 'message', 'language');
	
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
	function Guestbook()
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
		// translate post vars to array
		// ****TODO****: need to apply input filters!
		foreach ($_POST as $key => $value)
		{
			if (strpos($key, $_POST['formName'].'_') !== false && in_array(substr($key, strlen($_POST['formName']) + 1), $this->allowed))
			{
				$this->{substr($key, strlen($_POST['formName']) + 1)} = (is_numeric($value)) ? intval($value) : strip_tags($this->_canvas->flash_input_filter($value), '<br>');
				trigger_error(substr($key, strlen($_POST['formName']) + 1).': '.$this->{substr($key, strlen($_POST['formName']) + 1)});
			}
		}
		
		$this->message = str_replace("\r", "\n", $this->message);
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
//	function loadAlbumData()
//	{
//		if ( isset($_POST['form_album_id']) && ($album_id = (int) $_POST['form_album_id']) > 0 )
//		{
//			/* select the recipient name and email address from the database if specified */
//			$query = "SELECT value, parameter FROM ".$this->_table['parameters']." WHERE album_id='".$album_id."' AND parameter='name' OR parameter='mail'";
//			$result = null;
//			$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array', false, array('index_key' => 'parameter'));
//			
//			if (!empty($result))
//			{
//				$this->photographer_name = $result['name']['value'];
//				$this->photographer_email = $result['mail']['value'];
//			}
//		}
//	}
	
//	function sendEmail()
//	{
//		$this->saveMessage();
//		
//		$this->_mailToolkit->setEmailHeader('To', $this->photographer_email, $this->photographer_name);
//		$this->_mailToolkit->setEmailHeader('From', $this->email, $this->name);
//		$this->_mailToolkit->setEmailHeader('Reply-To', $this->email, $this->name);
//		$this->_mailToolkit->setEmailHeader('Errors-To', 'yourportfolio@webdebugger.nl', 'WebDebugger');
//		
//		$this->_mailToolkit->setHeader('Subject', $this->makeSubject());
//		
//		$mail = $this->retrieveMail();
//		
//		$this->_mailToolkit->addTextPart($mail);
//		
//		return $this->_mailToolkit->sendEmail();
//	}
	
	/**
	 * Stores the message in the database
	 * message date is stored as unix timestamp since this is easy to convert
	 */
	function saveMessage()
	{
		global $yourportfolio;
		
		$online = ($yourportfolio->settings['guestbook_approval']) ? 'Y' : 'N';
		
		$result = null;
		$query = "INSERT INTO `".$this->_table['guestbook']."` SET created=NOW(), modified=NOW(), online='".$online."', album_id='".$this->_db->filter($this->album_id)."', name='".$this->_db->filter($this->name)."', email='".$this->_db->filter($this->email)."', message='".$this->_db->filter($this->message)."', language='".$this->_db->filter($this->language)."'";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert');
		
		if ($yourportfolio->settings['guestbook_approval'])
		{
			$this->updateGuestbookXML();
		}
		
		return true;
	}
	
	function updateGuestbookXML()
	{
		$messages = null;
		$query = "SELECT created AS `date`, name, message, homepage, language FROM `".$this->_table['guestbook']."` WHERE album_id='".$this->_db->filter($this->album_id)."' AND online='Y' ORDER BY id";
		$this->_db->doQuery($query, $messages, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'GuestbookMessage'));

		$xml_file = (file_exists(SETTINGS.'guestbook.xml')) ? SETTINGS.'guestbook.xml' : XML.'guestbook.xml';
		
		$guestbook = &$this;
		ob_start();
		require($xml_file);
		$contents = ob_get_contents();
		ob_end_clean();
		
		$file = DATA_DIR.'guestbook_'.$this->album_id.'.xml';
		
		if (file_exists($file) && !is_writeable($file))
		{
			trigger_error('File is not writable ('.$file.'). '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		if (!$fp = fopen($file, 'w'))
		{
			trigger_error('Failed to create/open file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		@chmod($file, 0666);
		
		if (!fwrite($fp, $contents))
		{
			trigger_error('Failed to write to file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		fclose($fp);
	}
	
//	function makeSubject()
//	{
//		if (!empty($this->subject))
//		{
//			return $this->_canvas->text_filter($this->subject);
//		} else {
//			return 'Mail van '.$this->domain;
//		}
//	}
	
//	function retrieveMail()
//	{
//		if (file_exists(SETTINGS.'mail_message.txt'))
//		{
//			$mailfile = SETTINGS.'mail_message.txt';
//		} else {
//			$mailfile = CODE.'mail/default.txt';
//			if (file_exists(CODE.'mail/'.$this->domain.'.txt'))
//			{
//				$mailfile = CODE.'mail/'.$this->domain.'.txt';
//			}
//		}
//		
//		ob_start();
//		require($mailfile);
//		$mail = ob_get_contents();
//		ob_end_clean();
//		
//		return $mail;
//	}
	
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
