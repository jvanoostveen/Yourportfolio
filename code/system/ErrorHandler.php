<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * class: ErrorHandler
 * handles and responds to errors caused by the user and code
 * 
 * @package yourportfolio
 * @subpackage Error
 */

define('E_USER',    E_USER_NOTICE | E_USER_WARNING | E_USER_ERROR);
#define('E_NOTICE_ALL',  E_NOTICE | E_USER_NOTICE);
#define('E_WARNING_ALL', E_WARNING | E_USER_WARNING | E_CORE_WARNING | E_COMPILE_WARNING);
#define('E_ERROR_ALL',   E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
#define('E_NOTICE_NONE', E_ALL & ~E_NOTICE_ALL);
#define('E_DEBUG',       0x10000000);
#define('E_ALL',		    E_ERROR_ALL | E_WARNING_ALL | E_NOTICE_ALL | E_DEBUG);

class ErrorHandler
{
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function ErrorHandler()
	{
		$this->__construct();
	}
	
	function handle_error($level, $err_str, $file, $line, $context)
	{
		/*
		$t = compact('level', 'err_str', 'filename', 'line_no', 'context');
		print_r($t);
		exit();
		*/
		
		$m_start	= '';
		$m			= '';
		$m_end		= '';
		
		if ($level & ~E_USER) // error is not a user generated (trigger_error) error
		{
			// proceed like the normal php error handler
			// PHP Notice:  Undefined variable:  noarray in /Users/joeri/Sites/repository/yourportfolio/code/program/startup.php on line 58
			$m_start	.= '(Normal) PHP ';
			$m			.= $err_str;
			$m_end		.= 'in '.$file.' on line '.$line;
			
			switch ($level)
			{
				case E_NOTICE:
					$m_start .= 'Notice:';
					break;
				case E_WARNING:
					$m_start .= 'Warning:';
					break;
				case E_ERROR:
					$m_start .= 'Fatal error:';
					break;
			}
			
			$message = $m_start.'  '.$m.' '.$m_end;
			
			error_log($message, 0);
			
			return;
		}
		
		if ($level & E_USER) // error caused by trigger_error
		{
			
			//preg_match("(^[0-9]{0,})", $err_str, $matches);
			// preg_split($pattern, $str, -1, PREG_SPLIT_NO_EMPTY);
			$pattern = "/[:\[;\]]+/";
			$error = preg_split($pattern, $err_str, -1, PREG_SPLIT_NO_EMPTY);
			//list($code, $message) = preg_split($pattern, $err_str);
			
			list($code, $message) = $error;
			
			if (!is_numeric($code))
			{
				// error is old way
			}
			
			error_log('code: '.$code);
			error_log(print_r($error, true));
			
			$m_start	.= '(Custom) PHP ';
			$m			.= $err_str;
			$m_end		.= 'in '.$file.' on line '.$line;

			switch ($level)
			{
				case E_USER_NOTICE:
					$m_start .= 'Notice:';
					break;
				case E_USER_WARNING:
					$m_start .= 'Warning:';
					break;
				case E_USER_ERROR:
					$m_start .= 'Fatal error:';
					break;
			}
			
			$message = $m_start.'  '.$m.' '.$m_end;
			
			error_log($message, 0);

		}
		
		
		
	}
	
	// restore default php.ini setting possibly affected by ErrorHandler
	// this is a quick hack, it restores the values available at script
	// start-up not the values which is available ErrorHandler's startup
	function restore()
	{
		restore_error_handler();
		ini_restore('error_log');
		ini_restore('log_errors');
		ini_restore('display_errors');
	}

}

/*----------------------------------------------*\
|             AutoLaunch ErrorHandler            |
\*----------------------------------------------*/
/*
 * Lookup_ErrorHandler searches the first instance of class ErrorHandler
 * and calls its HandleError method, or return its name.
 */
function Lookup_ErrorHandler()
{
	static $EH_name = '', $OK = false;

	if ( empty($EH_name) )
	{
		foreach ( array_keys($GLOBALS) as $EH_name )
		{
			if ( is_object($GLOBALS[$EH_name]) && 'errorhandler' == get_class($GLOBALS[$EH_name]) )
			{
				$OK = TRUE;
				break;
			}
		}
	}
	
	// ErrorHandler not found
	if ( !$OK )
	{
		$EH_name = '';
	}
	
	if ( func_num_args() > 0 )
	{
		$args  = func_get_args();
		if ( $OK ){
			$error = &$GLOBALS[$EH_name];
			call_user_func_array(array(&$error, 'handle_error'), $args);
		} else {
			restore_error_handler();
			if ( function_exists(OLD_ERROR_HANDLER) )
			{
				call_user_func_array(OLD_ERROR_HANDLER, $args);
			}
			return false;
		}
	} else {
		return $OK ? $EH_name : false;
	}
}

define ('OLD_ERROR_HANDLER', set_error_handler('Lookup_ErrorHandler'));
?>