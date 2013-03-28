<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

require(CODE.'classes/GuestbookMessage.php');

/**
 * Guestbook
 *
 * @package yourportfolio
 * @subpackage Core
 */
class Guestbook
{
	var $album_id = 0;

	var $messages = array();
	
	/**
	 * objects needed to run this component
	 * @var object $_db
	 * @var array $_table
	 * @var object $_system
	 */
	var $_db;
	var $_table;
	var $_system;
	var $_canvas;
	var $_yourportfolio;
	
	/**
	 * constructor (PHP5)
	 *
	 * @param array $data can contain data needed to create an album without fetching from database itself
	 */
	function __construct($id)
	{
		global $db;
		$this->_db = &$db;
		$this->_table = &$db->_table;
		
		global $system;
		$this->_system = &$system;

		global $canvas;
		$this->_canvas = &$canvas;
		
		global $yourportfolio;
		$this->_yourportfolio = &$yourportfolio;
		
		$id = (int) $id;
		if ($id > 0)
		{
			$this->album_id = $id;
		}
	}
	
	/**
	 * constructor (PHP4)
	 *
	 */
	function Guestbook($id)
	{
		$this->__construct($id);
	}
	
	function loadMessages($online = false)
	{
		if ($this->album_id == 0)
		{
			return;
		}
		
		$query  = "SELECT id, online, name, message FROM `".$this->_table['guestbook']."` WHERE album_id='".$this->album_id."'";
		if ($online)
		{
			$query .= " AND online='Y'";
		}
		$query .= " ORDER BY id DESC";
		$this->_db->doQuery($query, $this->messages, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	}
	
	function setMessagesOnline($ids)
	{
		$result = null;
		$query = "UPDATE `".$this->_table['guestbook']."` SET online='N' WHERE album_id='".$this->album_id."'";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		$query = "UPDATE `".$this->_table['guestbook']."` SET online='Y' WHERE id IN ('".implode("','", $ids)."') AND album_id='".$this->album_id."'";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		$this->updateGuestbookXML();
	}
	
	function deleteMessage($id)
	{
		if (empty($id))
		{
			return;
		}
		
		$result = null;
		$query = "DELETE FROM `".$this->_table['guestbook']."` WHERE id='".$this->_db->filter($id)."' LIMIT 1";
		$this->_db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	function updateGuestbookXML()
	{
		$messages = null;
		$query = "SELECT created AS `date`, name, message, homepage, language FROM `".$this->_table['guestbook']."` WHERE album_id='".$this->_db->filter($this->album_id)."' AND online='Y' ORDER BY id";
		$this->_db->doQuery($query, $messages, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'GuestbookMessage'));

		$xml_file = (file_exists(SETTINGS.'guestbook.xml')) ? SETTINGS.'guestbook.xml' : XML.'guestbook.xml';
		
		$guestbook = &$this;
		ob_start();
		require($xml_file);
		$contents = ob_get_contents();
		ob_end_clean();
		
		$file = DATA_DIR.'guestbook_'.$this->album_id.'.xml';
		
		if (file_exists($file) && !is_writeable($file))
		{
			trigger_error('File is not writable ('.$file.'). '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		if (!$fp = fopen($file, 'w'))
		{
			trigger_error('Failed to create/open file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		@chmod($file, 0666);
		
		if (!fwrite($fp, $contents))
		{
			trigger_error('Failed to write to file. '.__FILE__.' at '.__LINE__, E_USER_ERROR);
		}
		
		fclose($fp);
	}
}
?>