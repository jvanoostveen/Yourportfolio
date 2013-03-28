<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2006 Triple Egg
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @release $Name$
 */

/**
 * page handling mail call from a yourportfolio site
 *
 * @package yourportfolio
 * @subpackage Site
 */

// start the program
require(CODE.'program/startup.php');


if ( empty($_POST) || empty($_POST['targetObj']) || $_POST['targetObj'] != 'guestbook' )
{
	// no correct $_POST vars, or not for this script
	exit();
}
//

$language = (isset($_POST['lang']) && ctype_alpha($_POST['lang'])) ? $_POST['lang'] : 'en';

$feedback_strings = array();
$feedback_strings['no_name']['nl'] = 'U heeft geen naam ingevuld.';
$feedback_strings['no_name']['en'] = 'You forgot to fill in your name.';
$feedback_strings['invalid_email']['nl'] = 'Uw e-mailadres is niet correct.';
$feedback_strings['invalid_email']['en'] = 'Your email address is not correct.';
$feedback_strings['error']['nl'] = 'Uw bericht is niet verstuurd, er is een fout opgetreden.';
$feedback_strings['error']['en'] = 'Your message has not been sent, an error occurred.';
if ($yourportfolio->settings['guestbook_approval'])
{
	$feedback_strings['success']['nl'] = 'Bedankt voor uw bericht.';
	$feedback_strings['success']['en'] = 'Thank you for your message.';
} else {
	$feedback_strings['success']['nl'] = 'Bedankt voor uw bericht. Deze zal op de website verschijnen nadat deze is goedgekeurd.';
	$feedback_strings['success']['en'] = 'Thank you for your message. Your message will be displayed on the site after approval.';
}

require(CODE.'classes/Guestbook.php');
$guestbook = new Guestbook();

if ( !$guestbook->validateEmailAddress() )
{
	$success = 0;
	$feedback = $feedback_strings['invalid_email'][$language];
} else if ( empty($guestbook->name) )
{
	$success = 0;
	$feedback = $feedback_strings['no_name'][$language];
} else {
	
	$error = '';
	$guestbook->saveMessage();
//	$error = $guestbook->sendEmail();
	
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
$output = '&success='.$success.'&feedback='.urlencode($feedback);

// end program
require(CODE.'program/shutdown.php');
?>