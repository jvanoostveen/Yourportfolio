<?PHP

class ContactController
{
	private $contact;
	private $feedback;
	private $language = 'en';
	
	public function __construct()
	{
		$this->initFeedback();
		
		if (isset($_POST['contactForm']['contact']['language']))
			$this->language = $_POST['contactForm']['contact']['language'];
		
		if (file_exists(SETTINGS.'Contact.php'))
		{
			require(SETTINGS.'Contact.php');
		} else {
			require(CODE.'classes/Contact.php');
		}
		
		global $dataprovider;
		$node = $dataprovider->currentNode();
		
		$_POST['form_album_id'] = $node->root->id;
		
		$this->contact = new Contact();
	}
	
	public function send()
	{
		$return = array();
		$return['name'] = $this->contact->name;
		$return['email'] = $this->contact->email;
		$return['message'] = $this->contact->message;
		$return['success'] = false;
		$return['feedback'] = '';
		
		if (!$this->contact->validateEmailAddress())
		{
			$return['feedback'] = $this->feedback['invalid_email'][$this->language];
			return $return;
		}
		
		$this->contact->loadAlbumData();
		
		$error = $this->contact->sendEmail();
		
		if (strcmp($error, ''))
		{
			$return['feedback'] = $this->feedback['error'][$this->language];
			return $return;
		} else {
			$return['success'] = true;
			$return['feedback'] = $this->feedback['success'][$this->language];
			
			$return['name'] = '';
			$return['email'] = '';
			$return['message'] = '';
			
			return $return;
		}
	}
	
	private function initFeedback()
	{
		$f = array();
		$f['invalid_email']['nl'] = 'Uw e-mailadres is niet correct.';
		$f['invalid_email']['en'] = 'Your email address is not correct.';
		$f['invalid_email']['de'] = 'Ihre Emailadresse ist nicht korrekt.';
		$f['error']['nl'] = 'Uw bericht is niet verstuurd, er is een fout opgetreden.';
		$f['error']['en'] = 'Your message has not been sent, an error occurred.';
		$f['error']['de'] = 'Ihre Nachricht wurde nicht versendet, da ein Fehler aufgetreten ist.';
		$f['success']['nl'] = 'Bedankt voor uw bericht.';
		$f['success']['en'] = 'Thank you for your message.';
		$f['success']['de'] = 'Danke fur Ihre Nachricht.'; // 'Danke fŸr Ihre Nachricht.';
		
		$this->feedback = $f;
	}
}