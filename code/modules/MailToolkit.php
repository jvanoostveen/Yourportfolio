<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

require(VENDOR.'PHPMailer/class.phpmailer.php');

/**
 * class: MailToolkit
 * handles mails by composition using the email_message class
 * this class requires PHP 4.3.0 or greater
 * 
 * @package yourportfolio
 * @subpackage Toolkits
 */
class MailToolkit
{
	/**
	 * the email_message object
	 * @var object $email_message
	 */
	var $mailer;
	var $email_regular_expression = "/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i";
	
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		if (phpversion() < '4.3.0')
		{
			trigger_error(__CLASS__.' requires PHP 4.3.0 or greater', E_USER_ERROR);
		}
		
		$this->mailer = new PHPMailer();
		$this->mailer->IsMail();
		$this->mailer->IsHTML(false);
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function MailToolkit()
	{
		$this->__construct();
	}
	
	/**
	 * Set the value of an header that is meant to represent the
	 * e-mail address of a person or entity with a known name. This is
	 * meant mostly to set the From, To, Cc and Bcc headers.
	 *
	 * @param string $header
	 * @param string $address
	 * @param string $name
	 */
	function setEmailHeader($header, $address, $name)
	{
		switch ($header)
		{
			case ('To'):
				$this->mailer->AddAddress($address, $name);
				break;
			case ('From'):
				$this->mailer->SetFrom($address, $name, false);
				break;
			case ('Reply-To'):
				$this->mailer->AddReplyTo($address, $name);
				break;
			case ('Bcc'):
				$this->mailer->AddBCC($address, $name);
				break;
			case ('Errors-To'):
				$this->mailer->AddCustomHeader("Errors-To: ".$address);
				break;
		}
	}
	
	/**
	 * Use this function to set the values of the headers of the
	 * message that may be needed. There are some message headers that are
	 * automatically set by the class when the message is sent. Others
	 * must be defined before sending.
	 *
	 * @param string $header
	 * @param string $value
	 */
	function setHeader($header, $value)
	{
		switch ($header)
		{
			case('Subject'):
				$this->mailer->Subject = $value;
				break;
		}
	}
	
	/**
	 * Add a text part to the message that may contain non-ASCII characters (8 bits or more).
	 *
	 * @param string $message
	 */
	function addTextPart($message)
	{
		$this->mailer->Body = $message;
	}
	
	/**
	 * Determine whether a given e-mail address may be valid.
	 *
	 * @param string $address
	 * @return boolean			true if address is valid
	 */
	function validateEmailAddress($address)
	{
		return preg_match($this->email_regular_expression, $address);
	}
	
	/**
	 * Send the email
	 *
	 * @return string			contains error(s)
	 */
	function sendEmail()
	{
		$return = $this->mailer->Send();
		
		if ($return)
		{
			return '';
		}
		
		return $this->mailer->ErrorInfo;
	}
	
	/**
	 * Reset mail message.
	 *
	 */
	function reset()
	{
		$this->mailer->ClearAllRecipients();
		$this->mailer->ClearReplyTos();
		$this->mailer->ClearAttachments();
		$this->mailer->Body = '';
	}
}
?>