<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

require(dirname(__FILE__).'/DatabaseToolkit/Query.php');

/**
 * class: DatabaseToolkit
 * 
 * a MySQL database toolkit, can be feeded with Query objects
 * as a temporary extra, it will also work with the old dbhandler class functions
 *
 * @package yourportfolio
 * @subpackage Database
 */
class DatabaseToolkit
{
	/**
	 * general database info
	 * @var integer $_id
	 * @var string $_dbhost
	 * @var string $_dbuser
	 * @var string $_dbpass
	 * @var string $_dbname
	 * @access private
	 */
	var $_id;
	var $_host;
	var $_user;
	var $_pass;
	var $_name;
	
	/**
	 * magic_quotes_gpc status
	 * @var boolean magic_quotes
	 */
	var $magic_quotes;
	
	/**
	 * query counter
	 * @var integer
	 */
	var $queries = 0;
	
	/**
	 * query list
	 * @var array $queries_full
	 */
	var $queries_full = array();

	/**
	 * container holding mysql specific commands that don't need or want a ' ' around them
	 * @var array
	 * @access private
	 */
	var $_mysql_tags = array('NOW()', 'NULL');

	/**
	 * constructor (php5)
	 *
	 * @param array $dbinfo containing db info
	 */
	function __construct($dbinfo = null, $table = null)
	{
		if (!is_null($dbinfo) && is_array($dbinfo))
		{
			$this->setDatabaseSettings($dbinfo);
		} else {
			// parse ini files
			
			// load default settings
			if (DEBUG)
			{
				if (file_exists(SETTINGS.'database_debug.ini'))
				{
					$ini = SETTINGS.'database_debug.ini';
				} else {
					$ini = CORE_SETTINGS.'database_debug.ini';
				}
			} else {
				$ini = CORE_SETTINGS.'database.ini';
			}
			
			foreach( parse_ini_file($ini) as $key => $setting )
			{
				$this->{'_'.$key} = $setting;
			}
			
			// load site specific settings
			if (!DEBUG && file_exists(SETTINGS.'database.ini'))
			{
				foreach( parse_ini_file(SETTINGS.'database.ini') as $key => $setting )
				{
					$this->{'_'.$key} = $setting;
				}
			}
		}
		
		if (!is_null($table) && is_array($table))
		{
			$this->_table = $table;
		} else {
			// default
			$this->_table = parse_ini_file(CORE_SETTINGS.'tables.ini');
				
			// load sqlite3 db
			if (file_exists(SETTINGS.'db/config.db'))
			{
				$config = new SQLite3(SETTINGS.'db/config.db', SQLITE3_OPEN_READONLY);
				$results = $config->query('SELECT "name", "realname" FROM "tables"');
				
				while ($row = $results->fetchArray(SQLITE3_ASSOC))
				{
					$this->_table[$row['name']] = $row['realname'];
				}
				
				$config->close();
			} else {
				// parse ini files
				
				// load site specific settings
				if (!file_exists(SETTINGS.'tables.ini'))
				{
					trigger_error('Missing specific tables.ini in '.SETTINGS.' !!', E_USER_ERROR);
				}
				$this->_table = array_merge($this->_table, parse_ini_file(SETTINGS.'tables.ini'));
			}
		}
		
		$this->magic_quotes = (get_magic_quotes_gpc()) ? true : false;
	}

	/**
	 * constructor (php4)
	 *
	 * @param array $dbinfo containing db info
	 */
	function DatabaseToolkit($dbinfo = null, $table = null)
	{
		$this->__construct($dbinfo, $table);
	}
	
	/**
	 * Sets the connect settings.
	 * 
	 * @param array $dbinfo	contains db info
	 */
	function setDatabaseSettings($dbinfo)
	{
		$this->_host = $dbinfo['host'];
		$this->_user = $dbinfo['user'];
		$this->_pass = $dbinfo['pass'];
		$this->_name = $dbinfo['name'];
	}
	
	/**
	 * checks if there is a connection
	 * if there is, check it
	 * otherwise, connect
	 * @return void
	 */
	function checkConnection()
	{
		if ($this->_id)
		{
			$this->ping();
		} else {
			if(!$this->connect() && !defined('FRONTEND')) {
				noDBConnection(mysql_error());
			}
		}
	}
	
	/**
	 * Check if we're connected. If not, no action is taken
	 * @return boolean true if connected
	 */
	function isConnected() 
	{
		return is_resource($this->_id) && mysql_ping($this->_id);
	}
	
	/**
	 * makes a connection to the database
	 * and selects the database
	 * @return boolean true on success, otherwise false
	 *
	 */
	function connect()
	{
		if ( $this->_id = @mysql_connect($this->_host, $this->_user, $this->_pass) )
		{
			mysql_query("SET CHARACTER SET 'latin1'", $this->_id);
			mysql_query("SET NAMES 'latin1'", $this->_id);
			
			return mysql_select_db($this->_name, $this->_id);
		} else {
			return false;
		}
	}
	
	/**
	 * checks if connection is still alive, if not, try to reconnect
	 *
	 */
	function ping()
	{
		if (!mysql_ping($this->_id))
		{
			trigger_error('Lost connection to the database!', E_USER_ERROR);
			mysql_close($this->_id);
		}
	}
	
	/**
	 * disconnects from the database if there is a connection
	 *
	 */
	function disconnect()
	{
		if ($this->_id)
		{
			mysql_close($this->_id);
			$this->_id = null;
		}
	}
	
	/**
	 * Create database.
	 * 
	 */
	function createDatabase()
	{
		$result = null;
		$query = "CREATE DATABASE `".$this->filter($this->_name)."`";
		$this->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
	}
	
	/**
	 * wrapper function for the doQuery
	 *
	 */
	function executeQuery($query, &$result)
	{
		if (!is_object($query)) // wrong parameter, could be the previous version
		{
			$func_args = func_get_args();
			if (!isset($func_args[2]))
				$func_args[2] = 'Unknown file';
			if (!isset($func_args[3]))
				$func_args[3] = '0';
			if (!isset($func_args[4]))
				$func_args[4] = 'Unknown function';
			if (!isset($func_args[5]))
				$func_args[5] = 'Unknown class';
			if (!isset($func_args[6]))
				$func_args[6] = '';
			if (!isset($func_args[7]))
				$func_args[7] = 'resource';
			if (!isset($func_args[8]))
				$func_args[8] = false;
			if (!isset($func_args[9]))
				$func_args[9] = null;
			return $this->doQuery_old($query, $result, $func_args[2], $func_args[3], $func_args[4], $func_args[5], $func_args[6], $func_args[7], $func_args[8], $func_args[9]);
		} else
			return $this->doQuery($query, $result);
	}
	
	/**
	 * executes a query object, passes the result in the $result parameter
	 *
	 * @param Query $query		can also be used to parse the old sql string
	 * @param mixed $result		the target variable in which the result must be stored
	 *
	 * @retun mixed
	 */
	function doQuery($query, &$result)
	{
		if (!is_object($query)) // wrong parameter, could be the previous version
		{
			$func_args = func_get_args();
			if (!isset($func_args[2]))
				$func_args[2] = 'Unknown file';
			if (!isset($func_args[3]))
				$func_args[3] = '0';
			if (!isset($func_args[4]))
				$func_args[4] = 'Unknown function';
			if (!isset($func_args[5]))
				$func_args[5] = 'Unknown class';
			if (!isset($func_args[6]))
				$func_args[6] = '';
			if (!isset($func_args[7]))
				$func_args[7] = 'resource';
			if (!isset($func_args[8]))
				$func_args[8] = false;
			if (!isset($func_args[9]))
				$func_args[9] = null;
			return $this->doQuery_old($query, $result, $func_args[2], $func_args[3], $func_args[4], $func_args[5], $func_args[6], $func_args[7], $func_args[8], $func_args[9]);
		}
		
		if (!empty($query->sql))
		{
			// has a prepared sql statement
			
			// collect all the values into one array
			$values = array();
			
			// **** build in some checks whether all needed data is available ****
			
			$values = array_merge($values, $query->select, $query->tables, array_values($query->data), $query->wheres);
			$code = 'return sprintf($query->sql, ';
			foreach($values as $key => $value)
			{
				$code .= '$this->filter($values['.$key.']), ';
			}
			$code = substr($code, 0, -2);
			$code .= ');';
			
			$query->query = eval($code);
			
		} else {
			
			// abort for now
			trigger_error('Not yet implemented.... '.__LINE__, E_USER_ERROR);
			return;
		}
		
		if (empty($query->query))
			trigger_error('Query was empty.', E_USER_ERROR);
		
		$this->queries++;
		$this->queries_full[] = $query->query;
		
		$this->checkConnection();
		
		$temp_result = mysql_query($query->query, $this->_id)
					   or 
					   $this->queryError($query, mysql_error());
		
		// return the result
		if ($query->return !== false)
			return $this->returnValue($temp_result, $result, $query->return, $query->return_options);
	}
	
	/**
	 * Returns an array containing all tables in the database.
	 * 
	 * @return array
	 */
	function getTables()
	{
		$this->checkConnection();

		$tables = array();
		
		$query = "SHOW TABLES FROM `".$this->_name."`";
		$this->doQuery($query, $tables, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
		
		if ($tables === false)
		{
			$tables = array();
		}
		
		return $tables;
	}
	
	/**
	 * returns list of columns of $table
	 * 
	 * @param string $table
	 * @return array
	 */
	function getColumns($table)
	{
		if (empty($table))
		{
			trigger_error('please specify table', E_USER_ERROR);
		}
		$this->checkConnection();
		
		$columns = array();
		
		$query = "SHOW COLUMNS FROM ".$table;
		$result = null;
		$this->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'resource', false);
		while($row = mysql_fetch_assoc($result))
		{
			$columns[] = $row['Field'];
		}
		
		return $columns;
	}
	
	/**
	 * a return results function
	 * based on the given $return value, the caller gets the wanted data instead of filtering itself
	 *
	 * @param resource_id $temp_result		a valid mysql resource id
	 * @param mixed $result					the target variable
	 * @param string $return				return type switch
	 * @param array $options				needed with some return types
	 */
	function returnValue(&$temp_result, &$result, $return, $options = null)
	{
		// what return do we want?
		switch($return)
		{
			case('value'):
				$result = "";
				$row = mysql_fetch_row($temp_result);
				$result = $row[0];
				break;
			case('array'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					$result[] = $row;
				}
				return mysql_num_rows($temp_result);
				//break;
			case('index_array'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					$result[$row[$options['index_key']]] = $row;
				}
				return mysql_num_rows($temp_result);
			case('index_array_value'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					if (!isset($result[$row[$options['index_key']]]))
					{
						$result[$row[$options['index_key']]] = array();
					}
					
					$result[$row[$options['index_key']]][] = $row[$options['value']];
				}
				return mysql_num_rows($temp_result);
			case('multi_index_array_value'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					$result[$row[$options['index_key_1']]][$row[$options['index_key_2']]] = $row[$options['value']];
				}
				return mysql_num_rows($temp_result);
			case('multi_index_array'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					$result[$row[$options['index_key_1']]][$row[$options['index_key_2']]] = $row;
				}
				return mysql_num_rows($temp_result);
			case('sort_array'):
				$result = array();
				while($row = mysql_fetch_assoc($temp_result))
				{
					$result[$row[$options['sort_key']]][] = $row;
				}
				return mysql_num_rows($temp_result);
				//break;
			case('flat_array'):
				$result = array();
				while ($row = mysql_fetch_row($temp_result))
				{
					$result[] = $row[0];
				}
				return true;
				//break;
			case('array_of_objects'):
				$result = array();
				while ($row = mysql_fetch_assoc($temp_result))
				{
					$result[] = new $options['object']($row);
				}
				break;
			case('index_array_of_objects'):
				$result = array();
				while ($row = mysql_fetch_assoc($temp_result))
				{
					$result[$row[$options['index_key']]] = new $options['object']($row);
				}
				break;
			case('row'):
				$result = array();
				$row = mysql_fetch_assoc($temp_result);
				$result = $row;
				break;
			case('row_merge'):
			case('merge_row'):
				$row = mysql_fetch_assoc($temp_result);
				$result = array_merge($result, $row);
				break;
			case('object'):
				$row = mysql_fetch_assoc($temp_result);
				foreach($row as $key => $value)
				{
					$result->$key = $value;
				}
				return true;
				//break;
			case('new_object'):
				$row = mysql_fetch_assoc($temp_result);
				$result = new $options['object']($row);
				return true;
				//break;
			case('insert'):
				// need to be recoded to go beyond the bigint bug/limit
				$result = mysql_insert_id();
				break;
			case('resource'):
			default:
				$result = $temp_result;
		}
		return;
	}
	
	/**
	 * report an error when the sql failed to execute
	 * 
	 * @param Query $query
	 * @param string $error
	 */
	function queryError($query, $error)
	{
		$error_string = "The database returned an error in the query:\n%s\nin %s on line %d (function: %s of class: %s)\n%s";
		if (!empty($query->_error_control['message']))
			$error_string .= "\nMessage: %s";
		
		trigger_error(sprintf($error_string, $query->query, $query->_error_control['file'], $query->_error_control['line'], $query->_error_control['function'], $query->_error_control['class'], mysql_error(), $query->_error_control['message']), E_USER_ERROR);
	}
	
	/**
	 * Last error.
	 * 
	 * @return string
	 */
	function lastError()
	{
		return mysql_error();
	}
	
	/**
	 * Last error in numeric value.
	 *
	 * @return Number
	 */
	function lastErrorNo()
	{
		return mysql_errno();
	}
	
	/**
	 * removes the automatic addslashes functions and applies the better mysql_real_escape_string()
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function filter($value)
	{
		if (is_array($value))
		{
			trigger_error('Filtered value contains an array: '.print_r($value, true), E_USER_WARNING);
			return '';
		}
		
		if ($this->magic_quotes)
		{
			$value = stripslashes($value);
		}
		
		if ( in_array($value, $this->_mysql_tags) )
		{
			return $value;
		} else {
			// null values
			if ( is_null($value) )
			{
				return 'NULL';
			}
			
			// numbers
			if ( is_int($value) || is_numeric($value) )
			{
				return $value;
			}
		}
		
		// strip \r to prevent double line endings.
		$value = str_replace("\r", '', $value);
		
		// the rest
		$this->checkConnection();
		
		if ($this->isConnected())
		{
			return mysql_real_escape_string($value, $this->_id);
		} else {
			return mysql_escape_string($value);
		}
	}
	
	/**
	 * handles (mysql) database queries
	 *
	 * @param string $query
	 * @param mixed $result where the result will be in
	 * @param string $message log message when query fails
	 * 				example: "\nin ".__FILE__." on line ".__LINE__." (function: ".__FUNCTION__." of class: ".__CLASS__.")"
	 * @param string $return [optional] wanted format
	 * @return mixed
	 */
	function doQuery_old($query, &$result, $file = null, $line = null, $function = null, $class = null, $message = '', $return = 'resource', $ifnull = FALSE, $options = array() )
	{
		$this->queries++;
		$this->queries_full[] = $query;
		
		$this->checkConnection();
		
		if(defined('FRONTEND'))
		{
			$temp_result = mysql_query($query, $this->_id) or trigger_error("error in query:\n".$query."\nin ".$file." on line ".$line." (function: ".$function." of class: ".$class.") ".$message."\n".mysql_error(), E_USER_WARNING);
			if(!$temp_result)
			{
				return false; // direct return seems neccessary here since $temp_result may not be a resouce and this will break returnValue
			}
		} else {
			$temp_result = mysql_query($query, $this->_id) or trigger_error("error in query:\n".$query."\nin ".$file." on line ".$line." (function: ".$function." of class: ".$class.") ".$message."\n".mysql_error(), E_USER_ERROR);
		}

		$mysql_special = array('insert', 'update', 'delete', 'create', 'resource');
		if (!in_array($return, $mysql_special) && mysql_num_rows($temp_result) == 0) // no results found...
		{
			if ($ifnull == true)
				trigger_error("No results found\n".$query."\nin ".$file." on line ".$line." (function: ".$function." of class: ".$class.") ".$message."", E_USER_ERROR);
			else if ($return == 'object' || $return == 'new_object')
			{
				return false;
			} else {
				$result = false;
				return;
			}
		}

		// what return do we want?
		return $this->returnValue($temp_result, $result, $return, $options);
	}
}
?>
