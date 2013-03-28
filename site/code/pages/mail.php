<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * page handling mail call from a yourportfolio site
 *
 * @package yourportfolio
 * @subpackage Site
 */

// start the program
require(CODE.'program/startup.php');

// capture the form when it is from the contact form

/*
Array
(
	[targetObj] => contact
	[source] => flash
	[action] => sendMail
	[formName] => form

	[form_album_id] => 3
	[form_photographer_id] => 8
	[form_name] => 
	[form_email] => 
	[form_message] => 
	[form_address] => 
	[form_city] => 
	[form_country] => 
)
*/

if ( empty($_POST) || empty($_POST['targetObj']) || $_POST['targetObj'] != 'contact' )
{
	// no correct $_POST vars, or not for this script
	exit('success=0&feedback='.urlencode('Wrong input.'));
}
//

$language = (isset($_POST['lang']) && ctype_alpha($_POST['lang'])) ? $_POST['lang'] : 'en';

$feedback_strings = array();
$feedback_strings['invalid_email']['nl'] = 'Uw e-mailadres is niet correct.';
$feedback_strings['invalid_email']['en'] = 'Your email address is not correct.';
$feedback_strings['invalid_email']['de'] = 'Ihre Emailadresse ist nicht korrekt.';
$feedback_strings['error']['nl'] = 'Uw bericht is niet verstuurd, er is een fout opgetreden.';
$feedback_strings['error']['en'] = 'Your message has not been sent, an error occurred.';
$feedback_strings['error']['de'] = 'Ihre Nachricht wurde nicht versendet, da ein Fehler aufgetreten ist.';
$feedback_strings['success']['nl'] = 'Bedankt voor uw bericht.';
$feedback_strings['success']['en'] = 'Thank you for your message.';
$feedback_strings['success']['de'] = 'Danke fur Ihre Nachricht.'; // 'Danke für Ihre Nachricht.';

#$mailToolkit = &$system->getModule('MailToolkit');
if (file_exists(SETTINGS.'Contact.php'))
{
	require(SETTINGS.'Contact.php');
} else {
	require(CODE.'classes/Contact.php');
}
$contact = new Contact();

// collect mail info from album
//		if no info is available, fetch default
/*
data needed:
db:
photographer name
photographer/contact email address (to address)

form:
from email address
from name
message
*/

if ( !$contact->validateEmailAddress() )
{
	$success = 0;
	$feedback = $feedback_strings['invalid_email'][$language];
} else {
	
	$contact->loadAlbumData();
	
	$error = $contact->sendEmail();
	
	if(strcmp($error,''))
	{
		$success = 0;
		$feedback = $feedback_strings['error'][$language].'\n('.$error.')';
		trigger_error('Problem sending mail:\n'.$error, E_USER_NOTICE);
	} else {
		$success = 1;
		$feedback = $feedback_strings['success'][$language];
	}	
}

// generate the output string
$output = 'success='.$success.'&feedback='.urlencode($feedback);
echo $output;

// end program
require(CODE.'program/shutdown.php');
?>