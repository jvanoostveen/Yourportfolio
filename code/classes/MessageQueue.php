<?PHP
/**
 * Project:			yourportfolio
 * 
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

define('MESSAGE_ERROR', 1);
define('MESSAGE_WARNING', 2);
define('MESSAGE_NOTICE', 4);

/**
 * class: MessageQueue
 * 
 * Show messages created during runtime at the next page shown to the user
 * 
 * @package yourportfolio
 * @subpackage Core
 */
class MessageQueue
{
	var $severity;
	var $messages;
	var $title;
	
	/**
	 * MessageQueue.
	 * 
	 * @return MessageQueue
	 */
	function __construct()
	{
		$this->severity = MESSAGE_ERROR;
		$this->messages = array();
		$this->title = '';
	}
	
	/**
	 * MessageQueue
	 *
	 * @return MessageQueue
	 */
	function MessageQueue()
	{
		$this->__construct();
	}
	
	/**
	 * Add message to the session queue.
	 *
	 * @param String $message
	 * @param Number $severity
	 */
	function add($message, $severity = MESSAGE_NOTICE)
	{
		if (!isset($_SESSION['messages_queue']) || !is_array($_SESSION['messages_queue']))
		{
			$_SESSION['messages_queue'] = array();
		}
		
		// message is the same as last message.
		if (count($_SESSION['messages_queue']) > 0 && $_SESSION['messages_queue'][count($_SESSION['messages_queue']) - 1] == $message)
		{
			return;
		}
		
		$_SESSION['messages_queue'][] = $message;
		
		if (!isset($_SESSION['messages_severity']) || $severity < $_SESSION['messages_severity'])
		{
			$_SESSION['messages_severity'] = $severity;
		}
	}
	
	/**
	 * Load messages from session queue to be displayed.
	 * 
	 * @return Void
	 */
	function load()
	{
		if (!empty($_SESSION['messages_queue']))
		{
			$this->messages = $_SESSION['messages_queue'];
			$this->severity = $_SESSION['messages_severity'];
			
			$_SESSION['messages_queue'] = array();
			$_SESSION['messages_severity'] = MESSAGE_NOTICE;
		}
	}
	
	function getTitle()
	{
		if (!empty($this->title))
			return $this->title;
		
		switch ($this->severity)
		{
			case MESSAGE_ERROR:
				return _('Er is een fout opgetreden.');
			case MESSAGE_WARNING:
			case MESSAGE_NOTICE:
			default:
				return _('Attentie vereist');
		}
	}
	
	function getIcon()
	{
		$icon = '';
		switch ($this->severity)
		{
			case MESSAGE_ERROR:
			case MESSAGE_WARNING:
			case MESSAGE_NOTICE:
			default:
				$icon = 'header_error';
		}
		
		return $icon;
	}
}
?>