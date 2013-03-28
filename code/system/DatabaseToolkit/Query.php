<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * class: Query
 * 
 * data object for a database query
 * the object stores only the requested fields etc,
 * the databaseobject should generate the query according to it's standards
 *
 * @sample
 *
 * $query = new Query();
 * $query->setErrorControl(__FILE__, __LINE__, __FUNCTION__, __CLASS__, 'optional message');
 * $query->setTable($tablename);
 * $query->addSelect('field1', 'field2');
 * $query->setQuery("SELECT %s, %s FROM %s LIMIT 1");
 * $query->setReturn('array');
 * 
 * $result = array();
 * $db->doQuery($query, $result);
 * print_r($result);
 *
 * @package yourportfolio
 * @subpackage Database
 */
class Query
{
	/**
	 * query type
	 * can be SELECT, INSERT, UPDATE or DELETE
	 * @var string $type
	 * @var array $_types
	 */
	var $type;
	var $_types = array('SELECT', 'INSERT', 'UPDATE', 'DELETE');
	
	/**
	 * the table(s) to apply the query to
	 * @var array $tables
	 */
	var $tables = array();
	
	/**
	 * fields to be retrieved by a select statement
	 * @var array $select
	 */
	var $select = array();
	
	/**
	 * the data to be stored in the database
	 * @var array $data
	 */
	var $data = array();
	
	/**
	 * the where statements
	 * @var array $where
	 * @var array $wheres	for the short sql
	 */
	var $where = array();
	var $wheres = array();
	
	/**
	 * what to return after query is executed
	 * @var string $return
	 * @var array $_return_types
	 * @var array $return_options
	 */
	var $return;
	var $_return_types = array('value', 'array', 'index_array', 'sort_array', 'row', 'row_merge', 'object', 'insert_id');
	var $return_options;
	
	/**
	 * limit number of affected rows
	 * @var integer $limit
	 */
	var $limit;
	
	/**
	 * the sql query when too complex for this object (or when not implemented yet)
	 * the values must still be passed by the addValues() function!
	 * can also be used for prepared queries (ie. queries that have to run multiple times, with different values)
	 * @var string $sql		format: 'UPDATE %s SET field=%s, field=%s WHERE field=%s AND field IN (%s, %s, %s)'
	 */
	var $sql;
	
	/**
	 * error control
	 * contains the line, file etc data for bug tracking
	 * @var array $_error_control
	 */
	var $_error_control = array();
	
	/**
	 * constructor (php5)
	 *
	 */
	function __construct()
	{
		if (func_num_args() > 0)
		{
			$arg = func_get_arg(0);
			if (is_array($arg))
				$args = func_get_arg(0);
			else
				$args = func_get_args();
			
			// translate arguments to properties
			
			// the sql query
			$this->sql = $args[0];
			
			// table(s)
			if (is_array($args[1]))
				$this->tables = array_merge($this->tables, $args[1]);
			else
				$this->setTable($args[1]);
			
			// select data or storage data
			switch(count($args))
			{
				case(4): // update query
					$this->addValues($args[2]);
					break;
				case(5): // select query
					$this->addSelect($args[2]);
					$this->setReturn($args[4]);
					break;
			}
			
			// where value
			$this->addWheres($args[3]);
			
		} else {
			$this->setType = 'SELECT';
		}
	}

	/**
	 * constructor (php4)
	 *
	 */
	function Query()
	{
		if (func_num_args() > 0)
		{
			$args = func_get_args();
			$this->__construct($args);
		} else {
			$this->__construct();
		}
	}
	
	/** 
	 * sets type of query
	 *
	 * @param string $type
	 */
	function setType($type)
	{
		$type = strtoupper($type);
		if (in_array($type, $this->_types))
			$this->type = $type;
	}
	
	/**
	 * set the table to which the query applies
	 * can also be multiple tables
	 *
	 * @param string $table 	* n
	 */
	function setTable($table)
	{
		$tables = func_get_args();
		$this->tables = array_merge($this->tables, $tables);
	}
	
	/**
	 * add select fields
	 * can also include the 'field AS name'
	 *
	 * @param string $select	* n
	 */
	function addSelect()
	{
		$selects = func_get_args();
		if (is_array($selects[0]))
			$selects = $selects[0];
		$this->select = array_merge($this->select, $selects);
	}
	
	/**
	 * add a single value to be stored
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	function addValue($value)
	{
		if (func_num_args() == 1)
			$this->data[] = $value;
		else
			$this->data[$value] = func_get_arg(1); // $value is here $key
	}
	
	/**
	 * add multiple values at once
	 *
	 * @param array $values		array(key => value)
	 */
	function addValues($values)
	{
		$this->data = array_merge($this->data, $values);
	}
	
	/**
	 * add a single where clause
	 *
	 * @param string $field		name of the field
	 * @param mixed $value		value to be compared against
	 * @param string $operator	context operator, can be =, >, <, IN, etc
	 */
	function addWhere($field, $value, $operator = '=')
	{
		$this->where[] = array('field' => $field, 'value' => $value, 'operator' => $operator);
		$this->wheres[] = $value;
	}
	
	/**
	 * add multiple where clauses
	 * first argument can be array($field => $value) using the default operator
	 * or
	 * an array containing all array('field' => $field, 'value' => $value, 'operator' => $operator)s
	 * 
	 * @param array $wheres
	 * @param string $operator	used when changing the default operator on the first procedure
	 */
	function addWheres($wheres, $operator = '=')
	{
		if (!empty($wheres))
		{
			if (isset($wheres[0])) // extended type
			{
				foreach($wheres as $where)
				{
					$this->where[] = $where;
					$this->wheres[] = $where['value'];
				}
			} else {
				foreach($wheres as $field => $value)
				{
					$this->addWhere($field, $value, $operator);
				}
			}
		}
	}
	
	/**
	 * what format should the query return
	 *
	 * @param string $return
	 * @param array $options	optional return options
	 */
	function setReturn($return, $options = array())
	{
		if (!in_array($return, $this->_return_types) && $return !== false)
			return false;
		
		$this->return = $return;
		$this->return_options = $options;
	}
	
	/**
	 * the sql query when too complex for this object (or when not implemented yet)
	 * the values must still be passed by the addValues() function!
	 * can also be used for prepared queries (ie. queries that have to run multiple times, with different values)
	 * @param string $sql		format: 'UPDATE %s SET field=%s, field=%s WHERE field=%s AND field IN (%s, %s, %s)'
	 */
	function setQuery($sql)
	{
		$this->sql = $sql;
	}
	
	/**
	 * set error position
	 * gives the real position of the wrong query when it failes
	 *
	 * @param string $file
	 * @param integer $line
	 * @param string $function
	 * @param string $class
	 * @param string $message
	 */
	function setErrorLocation($file = null, $line = null, $function = null, $class = null, $message = null)
	{
		$this->_error_control = array('file' => $file, 'line' => $line, 'function' => $function, 'class' => $class, 'message' => $message);
	}
}
?>
