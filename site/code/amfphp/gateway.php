<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2008 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * AMFPHP gateway setup for use with Yourportfolio.
 * 
 * @package yourportfolio
 * @subpackage DataCommunication
 */

if (!defined('DEBUG_AMFPHP'))
{
	if (file_exists(SETTINGS.'../yourportfolio_debug'))
	{
		define('DEBUG_AMFPHP', true);
	} else {
		// load runtime vars needed
		if (!file_exists(SETTINGS.'runtime.ini'))
		{
			trigger_error('runtime.ini file is missing!', E_USER_ERROR);
		}
		
		$runtime = parse_ini_file(SETTINGS.'runtime.ini', true);
		if (isset($runtime['debug']['amfphp']))
		{
			define('DEBUG_AMFPHP', $runtime['debug']['amfphp']);
		} else {
			define('DEBUG_AMFPHP', false);
		}
		
		unset($runtime);
	}
}

// start the program
require(CODE.'program/startup.php');

/*
The gateway is a customized entry point to your flash services. 

Things you can set here:

  - setBaseClassPath(string path) The absolute path to your services on the server

  - setLooseMode(bool mode) If true, output buffering is enabled and error_reporting
	is lowered to circumvent a number of documented NetConnection.BadVersion errors

  - setCharsetHandler(string mode, string phpCharset, string sqlCharset)

	mode can be one of
	  - none        don't do anything
	  - iconv       uses the iconv libray for reencoding
	  - mbstring    uses the mbstring library for reencoding
	  - recode      uses the recode library for reencoding
	  - utf8_decode uses the XML function utf8_decode and encode for
						reencoding - ISO-8859-1 only

	phpCharset is the charset that the system assumes the PHP strings will be in.

	sqlCharset is the charset of sql result sets used (only when outputting results
	  to flash client)

	wsCharset (web service charset) has been eliminated from this release, UTF-8 
	is assumed as the remote encoding. When using PHP5 SoapClient, the SoapClient 
	object will be initialized with "encoding" => phpCharset. When using nusoap, 
	soapclient->soap_defencoding will be initialized with phpCharset.

	The following settings are recommended (try the first setting appropriate for 
	your language, if it doesn't work try the second):
	
	* English:
	
		$gateway->setCharsetHandler( "none", "ISO-8859-1", "ISO-8859-1" );
		
	* Western european languages (French, Spanish, German, etc.):
	
		$gateway->setCharsetHandler( "iconv", "ISO-8859-1", "ISO-8859-1" );
		$gateway->setCharsetHandler( "utf8_decode", "ISO-8859-1", "ISO-8859-1" );
		
	* Eastern european languages (Russian and other slavic languages):
	
		$gateway->setCharsetHandler( "none", "ISO-8859-1", "ISO-8859-1" );
		$gateway->setCharsetHandler( "iconv", "your codepage", "your codepage" );
		
	* Oriental languages (Chinese, japanese, korean):
	
		$gateway->setCharsetHandler( "none", "ISO-8859-1", "ISO-8859-1" );
		$gateway->setCharsetHandler( "iconv", "big5", "big5" );
		$gateway->setCharsetHandler( "iconv", "CP950", "CP950" );
		$gateway->setCharsetHandler( "iconv", "Shift_JIS", "Shift_JIS" );
		$gateway->setCharsetHandler( "iconv", "CP932", "CP932" );
		$gateway->setCharsetHandler( "iconv", "CP949", "CP949" );
		
	* Other languages:
	
		$gateway->setCharsetHandler( "none", "ISO-8859-1", "ISO-8859-1" );
		
	See all the possible codepages for iconv here:
		
	http://www.gnu.org/software/libiconv/
	
	iconv is included by default in php5, but not in php4 although most
	hosts have it installed. utf8_decode is of some use for Western European languages,
	but please remember that it won't work with settings other than ISO-8859-1.
	The other methods also require seldom-used extensions but were included 
	just in case your particular host only supports them.
		
  - setWebServiceHandler(string handler)
	Handler can be one of:
	  - php5 (that is, PHP5 SoapClient)
	  - pear
	  - nusoap
	This is used for webservices when working with http:// service names in
	new Service(). For php5 and pear, you will need to have it installed on your 
	server. For nusoap, you need nusoap.php instead in ./lib relative to this file.
	 
	If you have PHP5 and the SOAP extension installed it is highly recommended that
	you use it as it is _much_ faster than NuSOAP or PEAR::SOAP
	
Things you may want to disable for production environments:

  - disableStandalonePlayer()
	Disables the standalone player by filtering out its User-Agent string
	
  - disableServiceDescription()
	Disable service description from Macromedia's service browser
  
  - disableTrace()
	Disables remote tracing
	
  - disableDebug()
	Stops debug info from being sent (independant of remote trace setting)

*/

//Include things that need to be global, for integrating with other frameworks
//include "globals.php";
//This file is intentionally left blank so that you can add your own global settings
//and includes which you may need inside your services. This is generally considered bad
//practice, but it may be the only reasonable choice if you want to integrate with
//frameworks that expect to be included as globals, for example TextPattern or WordPress

//Set start time before loading framework
list($usec, $sec) = explode(" ", microtime());
$amfphp['startTime'] = ((float)$usec + (float)$sec);

// if there is a custom services directory, use that one, otherwise use the default.
if (file_exists(SETTINGS.'services'))
{
	$servicesPath = SETTINGS.'services';
} else {
	$servicesPath = CODE.'amfphp/services';
}
$voPath = $servicesPath.'/vo';

//Include framework
require(CODE.'vendor/amfphp/core/amf/app/Gateway.php');

$gateway = new Gateway();

//Set where the services classes are loaded from, *with trailing slash*
//$servicesPath defined in globals.php
$gateway->setClassPath($servicesPath);

//Set where class mappings are loaded from (ie: for VOs)
//$voPath defined in globals.php
$gateway->setClassMappingsPath($voPath); 

//Read above large note for explanation of charset handling
//The main contributor (Patrick Mineault) is French, 
//so don't be afraid if he forgot to turn off iconv by default!
$gateway->setCharsetHandler('utf8_decode', 'ISO-8859-1', 'ISO-8859-1');

//Error types that will be rooted to the NetConnection debugger
$gateway->setErrorHandling(E_ALL ^ E_NOTICE);

if (!DEBUG_AMFPHP)
{
	//Disable profiling, remote tracing, and service browser
	$gateway->disableDebug();
	// Keep the Flash/Flex IDE player from connecting to the gateway. Used for security to stop remote connections. 
	$gateway->disableStandalonePlayer();
}

//If you are running into low-level issues with corrupt messages and 
//the like, you can add $gateway->logIncomingMessages('path/to/incoming/messages/');
//and $gateway->logOutgoingMessages('path/to/outgoing/messages/'); here
//$gateway->logIncomingMessages('in/');
//$gateway->logOutgoingMessages('out/');

//Explicitly disable the native extension if it is installed
//$gateway->disableNativeExtension();

//Enable gzip compression of output if zlib is available, 
//beyond a certain byte size threshold
$gateway->enableGzipCompression(25 * 1024);

//Service now
$gateway->service();

?>