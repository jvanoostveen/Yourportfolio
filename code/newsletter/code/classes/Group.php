<?PHP
class Group
{
	function getGroups()
	{
		global $db, $yourportfolio;
		
		$groups = '';
		$query = sprintf("SELECT `group_id` AS id, `name` FROM `%s`", $yourportfolio->_table['nl_groups']);
		$db->doQuery($query, $groups, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
		return $groups;
	}
	
	function getName($id)
	{
		global $db, $yourportfolio;
		$query = sprintf("SELECT `name` FROM `%s` WHERE `group_id` = $id", $yourportfolio->_table['nl_groups']);
		$result = '';
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		if( empty($result)) 
		{
			return "-";
		} else {
			return $result;
		}
	}
}
?>