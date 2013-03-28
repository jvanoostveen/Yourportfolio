<?PHP
/*
 * Project: yptrunk
 *
 * API voor de frontend
 * Geeft een lijst van groepen weer of subscribed iemand (evt in een groep)
 * @author Christiaan Ottow
 * @created Mar 3, 2007
 */
 
define('NL_CODE', CODE.'newsletter/code/');
define('FORMAT_VARIABLES', 'variables');
define('FORMAT_XML', 'xml');
$available_formats = array(FORMAT_VARIABLES, FORMAT_XML);

require(CODE.'program/startup.php');
require(NL_CODE.'classes/AddressStatus.php');

$feedback_strings = array();
$feedback_strings['invalid_email']['nl'] = 'Uw e-mailadres is niet correct.';
$feedback_strings['invalid_email']['en'] = 'Your email address is not correct.';
$feedback_strings['invalid_email']['de'] = 'Ihre Emailadresse ist nicht korrekt.';
$feedback_strings['email_exists']['nl'] = 'Dit e-mailadres is reeds aangemeld.';
$feedback_strings['email_exists']['en'] = 'This email address is already on the list.';
$feedback_strings['email_exists']['de'] = 'This email address is already on the list.';
$feedback_strings['error']['nl'] = 'Uw aanmelding is niet verstuurd, er is een fout opgetreden.';
$feedback_strings['error']['en'] = 'Your application has not been sent, an error occurred.';
$feedback_strings['error']['de'] = 'Ihre Anmeldung wurde nicht versandt, da ein Fehler aufgetreten ist.';
$feedback_strings['nogroup']['nl'] = 'Uw aanmelding is niet verstuurd, deze groep bestaat niet.';
$feedback_strings['nogroup']['en'] = 'Your application has not been sent, group does not exist.';
$feedback_strings['nogroup']['de'] = 'Ihre Anmeldung wurde nicht versandt, Sektion existiert nicht';
$feedback_strings['success']['nl'] = 'Bedankt voor uw aanmelding.';
$feedback_strings['success']['en'] = 'Thank you for your application.';
$feedback_strings['success']['de'] = 'Danke fur Ihre Anmeldung.'; // 'Danke für Ihre Nachricht.';

if( isset($_GET['do']) )
{
	$do = $_GET['do'];
} else if( isset($_POST['do']) ) {
	$do = $_POST['do'];
} else {
	$do = 'groups';
}

$format = FORMAT_VARIABLES;
if (isset($_GET['f']))
	$format = $_GET['f'];
else if (isset($_POST['f']))
	$format = $_POST['f'];

if (!in_array($format, $available_formats))
	$format = FORMAT_VARIABLES;

switch( $do )
{
	case 'groups':
		showGroups();
		break;
	case 'subscribe':
		subscribe();
		break;
}

function showGroups()
{
	global $db, $yourportfolio;
	
	$query = sprintf("SELECT group_id, name FROM `%s` WHERE visible='Y'", $yourportfolio->_table['nl_groups']);
	$result = '';
	$db->doQuery($query,$result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	
	if (empty($result))
	{
		$result = array();
	}
	
	echo "<groups>\n";
	foreach($result as $g)
	{
		echo "<group id=\"".$g['group_id']."\">\n";
		echo "<name><![CDATA[".$g['name']."]]></name>\n";
		echo "</group>\n";
	}
	echo "</groups>\n";
}

function subscribe()
{
	// TODO: unsubscribed addresses weer kunnen toevoegen
	
	global $db, $yourportfolio, $canvas, $feedback_strings, $format;
	
	$language	= (isset($_REQUEST['lang']) && ctype_alpha($_REQUEST['lang'])) ? $_REQUEST['lang'] : 'en';
	
	$name		= $canvas->flash_input_filter($_REQUEST['name']);
	$name 		= $db->filter($name);
	
	$address	= $canvas->flash_input_filter($_REQUEST['address']);
	$address	= strtolower($address);
	$address 	= $db->filter($address);
	
	$groups		= array();
	if (isset($_REQUEST['groups']))
	{
		$groups = explode(',', $_REQUEST['groups']);
	} else if (isset($_REQUEST['group']))
	{
		$groups[] = (int) $_REQUEST['group'];
	} else {
		$query = sprintf("SELECT `group_id` FROM `%s` WHERE `visible`='Y' LIMIT 1", $yourportfolio->_table['nl_groups']);
		$db->doQuery($query, $group, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		$groups[] = $group;
	}
	
	if( empty( $name ) )
	{
		$name = $address;
	}
	
	if( empty( $address)  || !valid_address($address) )
	{
		$out  = '';
		switch ($format)
		{
			case FORMAT_XML:
				$out .= '<data success="0">';
				$out .= '<feedback><![CDATA['.$feedback_strings['invalid_email'][$language].']]></feedback>';
				$out .= '</data>';
				break;
			default:
				$out .= 'success=0&feedback='.$feedback_strings['invalid_email'][$language];
		}
		exit($out);
	}
	
	// bestaat hij maar is hij unsubscribed?
	$query2 = sprintf("SELECT EXISTS ( SELECT * FROM `%s` WHERE `address`='%s' AND `status`='%d')", $yourportfolio->_table['nl_addresses'], $address, AddressStatus::UNSUBSCRIBED());
	$exists = 0;
	$db->doQuery($query2, $exists, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	if( $exists == 1 )
	{
		// status terug naar OK
		$query3 = sprintf( "UPDATE `%s` SET `status`='%d' WHERE `address`='%s'", $yourportfolio->_table['nl_addresses'], AddressStatus::OK(), $address );
		$res = null;
		$db->doQuery($query3, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
	} else {
		$id = 0;
		
		// bestaat en is actief
		$query0 = sprintf("SELECT `address_id` FROM `".$yourportfolio->_table['nl_addresses']."` WHERE `address`='%s' AND `status`!='%d'", $address, AddressStatus::UNSUBSCRIBED());
		$db->doQuery($query0, $id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		$id = (int) $id;
		
		if ($id == 0)
		{
			// toevoegen
			$query4 = sprintf("INSERT INTO `%s` SET `name`='$name', `address`='$address', `status`=1, `verified`=1, created=NOW()", $yourportfolio->_table['nl_addresses']);
			$db->doQuery($query4, $id, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
	}
	
	foreach ($groups as $group)
	{
		$group = (int) $group;
		if ($group == 0)
			continue;
		
		// bestaat de groep?
		$query1 = sprintf("SELECT EXISTS ( SELECT * FROM `%s` WHERE `visible`='Y' AND `group_id`=$group)", $yourportfolio->_table['nl_groups']);
		$exists = 0;
		$db->doQuery($query1, $exists, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		if( $exists == 0 )
		{
			$out  = '';
			switch ($format)
			{
				case FORMAT_XML:
					$out .= '<data success="0">';
					$out .= '<feedback><![CDATA['.$feedback_strings['nogroup'][$language].']]></feedback>';
					$out .= '</data>';
					break;
				default:
					$out .= 'success=0&feedback='.$feedback_strings['nogroup'][$language];
			}
			exit($out);
		}
		
		// user al toegevoegd aan groep?
		$query11 = sprintf("SELECT EXISTS (SELECT * FROM `".$yourportfolio->_table['nl_bindings']."` WHERE `group_id`=%d AND `address_id`=%d LIMIT 1)", $group, $id);
		$exists = 0;
		$db->doQuery($query11, $exists, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		if ($exists == 1) // already subscribed to this group
			continue;
		
		// bewijsmateriaal
		$log = null;
		$query41 = sprintf("INSERT INTO `%s` SET `logstamp`=NOW(), `address`='%s', `address_id`=%d, `remoteip`='%s', `useragent`='%s', `method`='website'", $yourportfolio->_table['nl_optinlog'], $address, $id, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] );
		$db->doQuery($query41, $log, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		
		$binding = null;
		$query5 = sprintf("INSERT INTO `".$yourportfolio->_table['nl_bindings']."` SET `group_id`=%d, `address_id`=%d", $group, $id);
		$db->doQuery($query5, $binding, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
	}
	
	$out  = '';
	switch ($format)
	{
		case FORMAT_XML:
			$out .= '<data success="1">';
			$out .= '<feedback><![CDATA['.$feedback_strings['success'][$language].']]></feedback>';
			$out .= '</data>';
			break;
		default:
			$out .= 'success=1&feedback='.$feedback_strings['success'][$language];
	}
	exit($out);
}

function valid_address( $input )
{
	$email_regexp = '/[A-Za-z0-9._-]+\@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}/';
	return preg_match($email_regexp, $input);
}

?>
