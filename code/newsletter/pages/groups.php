<?PHP

/**
 * Beheer groepen
 *
 * Project: Yourportfolio / newsletter
 *
 * @link http://www.yourportfolio.nl
 * @copyright Christiaan Ottow 2006
 * @author Christiaan Ottow <chris@6core.net>
 */

$page_name = 'groups/';

require(CODE . 'newsletter/code/startup.php');

$post_templates[] = 'components/newGroupDiv.php';
$post_templates[] = 'components/editGroupDiv.php';

$canvas->addScript('nl_common');
$canvas->addScript('nl_search');
$canvas->addScript('nl_groups');
$canvas->addScript('nl_groups_popups');

// voor zoeken
$filter = (isset($_GET['f']) ? (int) $_GET['f'] : 0);
$filter = (isset($_POST['filter']) ? (int) $_POST['filter'] : $filter);
$data['filter'] = $filter;

// ordering
if( isset($_SESSION['order_group'] ) )
{
	$ordering['field'] = $db->filter( $_SESSION['order_group']['field'] );
	$ordering['dir'] = $db->filter( $_SESSION['order_group']['direction'] );
} else {
	$ordering['field'] = 'name';
	$ordering['dir'] = 'ASC';
}
function build_menu()
{
	global $submenu, $db, $yourportfolio;
	
	$result = array();
	$query = sprintf("SELECT `name`, `group_id`, `visible` FROM `%s` ORDER BY `name` ASC", $yourportfolio->_table['nl_groups']);
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	
	$submenu = array();
	
	if (!empty($result))
	{
		$group_id = (isset($_GET['group'])) ? (int) $_GET['group'] : -1;
		
		foreach( $result as $row )
		{
			$submenu_entry = array (
				'name' 		=> $row['name'],
				'icon'		=> 'iconsets/default/users_filled.gif',
				'href'		=> 'newsletter_groups.php?case=show&group=' . $row['group_id'],
				'popup'		=> "editGroup(".$row['group_id'].", '".addslashes($row['name'])."', '".$row['visible']."')", 
				'active'	=> false
			);
			
			if ($row['group_id'] == $group_id)
			{
				$submenu_entry['active'] = true;
			}
			
			$submenu[] = $submenu_entry;
		}
	}
	
	$submenu[] = array (
			'name'	=> '<i>'._('nieuwe groep...').'</i>',
			'icon'	=> 'img/btn_new_album.gif',
			'href'	=> 'javascript:newGroup()',
			'id'	=> 'newGroupLink',
			'type'	=> 'event'
		);
}

build_menu();

if( !isset( $_GET['case']) )
{
	$case = '';
} else {
	$case = $_GET['case'];
}

if( isset($_POST['case'] ) )
{
	$case = $_POST['case'];
}

switch( $case )
{
	case 'show':
		if( isset( $_GET['group'] ) )
		{
			showGroup( (int) $_GET['group']);
		} else {
			showGroup();
		}
		break;
	case 'new':
		newGroup();
		break;
	case 'saveMembers':
		saveGroupMembers();
		break;
	case 'saveMeta':
		saveGroupMeta();
		break;
	case 'delete':
		deleteGroup();
		break;
	case 'search':
		search($filter);
		break;
	case 'load':
		if( isset($_GET['group']) )
		{
			$group = (int) $_GET['group'];
		} else {
			$group = (int) $_POST['group'];
		}
		
		load($group);
		break;
	default:
		listGroups();
		break;
}

function showGroup($gid = 0)
{
	global $yourportfolio, $db, $templates, $data, $settings, $page_name, $components, $ordering;
	
	$group_only = isset($_GET['group_only']) ? $_GET['group_only'] : 1;
	$group_only = isset($_POST['group_only']) ? $_POST['group_only'] : $group_only;
	$data['group_only'] = $group_only;
	$data['currentView'] = ($group_only == 1 ? 'group' : 'all');
	
	$components['bottomBar'][]= 'download_link.php';

	$components['topBar'][] = 'save_link.php';
	$components['topBar'][] = 'search_bar.php';
	$components['bottomBar'][] = 'save_link.php';
	$components['bottomBar'][] = 'group_view_switcher.php';
	
	// pagination
	$components['bottomBar'][] = 'num_pages.php';
	$components['bottomBar'][] = 'paginator_groups.php';
		
	$query = "SELECT `group_id`, `name` FROM `".$yourportfolio->_table['nl_groups']."`";
	$db->doQuery( $query, $data['groups'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	
	if( isset($_GET['page']) )
	{
		$data['page'] = (int)$_GET['page'];
	} else {
		$data['page'] = 1;
	}
	$data['selected_app'] = $_COOKIE['app'];
	$data['params'] = 'case=show&group='.((int) $_GET['group']).'&group_only='.$group_only;
	
	$query = "SELECT COUNT(*) FROM `".$yourportfolio->_table['nl_addresses']."`";
	if ($group_only)
	{
		$query .= " WHERE address_id IN (SELECT address_id FROM `".$yourportfolio->_table['nl_bindings']."` WHERE group_id='".$gid."')";
	}
	$total = 0;
	$db->doQuery( $query, $total, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	
	if( $_COOKIE['app'] > 0 )
	{
		$data['num_pages'] =  ceil( $total / $_COOKIE['app']);
	} else {
		$data['num_pages'] = 0;
	}
	
	// einde pagination

	if ($gid == 0)
	{
		$gid = 1;
	}
	

	/* groep informatie zoeken */
	$query = sprintf("SELECT `group_id`, `name` FROM `%s` WHERE `group_id` = '$gid'", $yourportfolio->_table['nl_groups']);
	$data['group'] = null;
	$db->doQuery($query, $data['group'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	$query = sprintf("SELECT COUNT(*) FROM `%s` a, `%s` b WHERE a.`group_id`='$gid' AND a.address_id=b.address_id AND b.status IN ( ".AddressStatus::OK().", ".AddressStatus::ERROR().")", $yourportfolio->_table['nl_bindings'], $yourportfolio->_table['nl_addresses']);
	$num = null;
	$db->doQuery($query, $num, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
	
	$settings['page_title'] = $data['group'][0]['name'];
	$settings['page_center_title'] = $num.' '._('adressen');
	$settings['page_icon'] = 'design/iconsets/default/item_white.gif';
	
	$page_name .= $settings['page_title'].'/';
	
	$templates[] = 'group_edit.php';
	
}

function load( $gid )
{
	global $yourportfolio, $db, $templates, $data, $settings, $ordering, $system;
	
	if( isset($_GET['group_only'] ) )
	{
		$group_only = ($_GET['group_only'] == 1 ) ? true : false;
	} else if( isset($_POST['group_only']) ) {
		$group_only = ($_POST['group_only'] == 1 ) ? true : false;
	}		
	
	
	/* groep informatie zoeken */
	$query = sprintf("SELECT `group_id`, `name` FROM `%s` WHERE `group_id` = '$gid'", $yourportfolio->_table['nl_groups']);
	$data['group'] = null;
	$db->doQuery($query, $data['group'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
		
	/* groepsleden zoeken */
	$query = sprintf("SELECT u.address_id FROM `%s` u, `%s` g WHERE g.group_id = '".$gid."' AND g.address_id = u.address_id ORDER BY ".$ordering['field']." ".$ordering['dir']."", 
	$yourportfolio->_table['nl_addresses'],
	$yourportfolio->_table['nl_bindings'] );
	$members = array();
	$db->doQuery($query, $members, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false );
	
	if( isset($_GET['page']) )
	{
		$page = (int) $_GET['page'];
	} else if( isset($_POST['page']) )
	{
		$page = (int) $_POST['page'];	
	} else {
		$page = 1;
	}
	
	if( isset($_COOKIE['app']) )
	{
		$app = $_COOKIE['app'];
	} else {
		$app = 0;
	}
	
	if( $group_only) 
	{
		$go = '&group_only=1';
	} else {
		$go = '&group_only=0';
	}
	
	$extra = '&group='. $data['group'][0]['group_id'] . '&case=load'.$go;

	
	if (!$group_only)
	{
		// check of pagina binnen bereik is
		$query = sprintf("SELECT COUNT(*) FROM `%s` WHERE status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")", $yourportfolio->_table['nl_addresses']);
		$num = 0;
		$db->doQuery($query, $num, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
		$real_pages = ceil($num / $app);
		
		if( $page > $real_pages && $real_pages > 0 )
		{
			if( $settings['debug'] == true)
			{
				trigger_error("Page out of reach ( $page > $real_pages), redirecting");
			}
			echo 'ERROR:'._('page out of reach');
		}

		/* alle adressen zoeken */
		$query = sprintf("SELECT `address_id`, `name`, `address`, `status`, `created`, `recv_count` FROM `%s` WHERE status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().") ORDER BY %s %s", $yourportfolio->_table['nl_addresses'], $ordering['field'], $ordering['dir']);
		
		// pagination
		if( $app > 0 )
		{
			$from = ($page-1) * $app;
			$query .= " LIMIT $from, $app";
		}
		$users = null;
		$db->doQuery( $query, $users, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	} else {
		// check of pagina binnen bereik is
		$query = sprintf("SELECT COUNT(*) FROM `%s` u, `%s` g WHERE u.status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().") AND g.group_id = '".$gid."' AND g.address_id = u.address_id", $yourportfolio->_table['nl_addresses'], $yourportfolio->_table['nl_bindings']);
		$num = 0;
		$db->doQuery($query, $num, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false );
		$real_pages = ceil($num / $app);
		
		if( $page > $real_pages && $real_pages > 0)
		{
			if( $settings['debug'] == true)
			{
				trigger_error("Page out of reach ( $page > $real_pages), redirecting");
			}
			echo 'ERROR:page out of reach';
		}

		$query = sprintf("SELECT u.address_id, u.name, u.address, u.status, u.created, u.recv_count FROM `%s` u, `%s` g WHERE u.status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().") AND g.group_id = '".$gid."' AND g.address_id = u.address_id ORDER BY %s %s", 
		$yourportfolio->_table['nl_addresses'], $yourportfolio->_table['nl_bindings'], $ordering['field'], $ordering['dir'] );
		
		// pagination
		if( $app > 0 )
		{
			$from = ($page-1) * $app;
			$query .= " LIMIT $from, $app";
		}
		$users = null;
		$db->doQuery($query, $users, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	}
	
	header("Content-Type: text/html; charset=ISO-8859-1");
	
	// doorlopen en printen
	if (!empty($users))
	{
		require(NL_TEMPLATES.'components/group_address_start.php');
		
		foreach ($users as $u)
		{
			if( $members && in_array( $u['address_id'], $members ) )
			{
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}
			
			require(NL_TEMPLATES.'components/group_address_row.php');
			
		}
		require(NL_TEMPLATES.'components/group_address_end.php');		
	}
	
	exit();
			
}


function newGroup()
{
	global $templates, $db, $yourportfolio;
	
	
	$group_name = $db->filter($_POST['name']);
	$visible_i = $_POST['visible'];
	if( $visible_i == 'true' )
	{
		$visible = 'Y';
	} else {
		$visible = 'N';
	}
	
	if( empty($group_name) )
	{
		echo _('Een groep naam mag niet leeg zijn');
		exit();
	}
	
	if( group_exists($group_name) )
	{
		echo _('Er bestaat al een groep met deze naam');
		exit();
	}
	
	if( strlen($group_name) > 40 )
	{
		echo _('De groep naam is te lang');
		exit();
	}

	$query = sprintf("INSERT INTO `%s` SET `name`='$group_name', `visible`='$visible'", $yourportfolio->_table['nl_groups']);
	$result = null;
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false );
	
	log_message( 'event', "Group $group_name created", debug_backtrace() );
	echo 'OK';
	exit();
}

function saveGroupMembers()
{
	global $db, $yourportfolio, $_POST;
	
	$gid = (int) $_POST['id'];
	
	if ($gid == 0)
	{
		trigger_error("Group id is 0, POST id: ".$_POST['id'].", dump: ".print_r(debug_backtrace(), true));
		log_message( 'error', "Group id 0 found.", debug_backtrace() );
	}
	
	// get group members
	$query = sprintf("SELECT `address_id` FROM `%s` WHERE `group_id` = '$gid'", $yourportfolio->_table['nl_bindings'] );
	$members = array();
	$db->doQuery( $query, $members, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false );

	if( !$members )
	{
		$members = array();
	}
	$dataset = explode(',', $_POST['serialized_dataset']);
	$input_members = explode(',', $_POST['serialized_members']);
	$void = '';
	
	foreach( $dataset as $address )
	{
		if ((int) $address == 0)
		{
			trigger_error("(int) address id is 0 (actual: ".$address."), dump: ".print_r(debug_backtrace(), true));
			log_message( 'error', "(int) address id 0 found (actual: ".$address.".", debug_backtrace() );
		}
		
		$query = '';
		
		if( in_array( $address, $input_members ) )
		{
			// volgens input is hij member
			if( !in_array($address, $members) )
			{
				$query = sprintf("INSERT INTO `%s` SET `address_id` = '$address', `group_id` = '$gid'", $yourportfolio->_table['nl_bindings'] );
				$type = 'insert';
				$queries[] = $query;
			}
		} else {
			// volgens input geen member
			if( in_array($address, $members) )
			{
				$query = sprintf("DELETE FROM `%s` WHERE `address_id` = '$address' AND `group_id` = '$gid'", $yourportfolio->_table['nl_bindings'] );
				$type = 'delete';
				$queries[] = $query;
			}
		}
		
		if( !empty($query) )
		{
			$db->doQuery( $query, $void, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', $type, false );
		}

	}
	
	
	$url = "Location: newsletter_groups.php?case=show&group=$gid";
	
	if( isset($_POST['goto_page']) && !empty($_POST['goto_page']) )
	{
		$url .= "&page=".(int)$_POST['goto_page'];
	}
	if (isset($_POST['group_only']))
	{
		$url .= '&group_only='.((int) $_POST['group_only']);
	}
	
	log_message( 'event', "Group members for group $gid saved", debug_backtrace() );
	header($url);		
	exit();	
	
}

function saveGroupMeta()
{
	global $db, $yourportfolio, $settings;
	
	$id = (int)$_POST['id'];
	$null = 0;	
	$name = $db->filter($_POST['name'] );
	$visible = $_POST['visibility'];
	
	if( $visible == 'true' )
	{
		$visibility = 'Y';
	} else {
		$visibility = 'N';
	}

	if( $settings['debug'] == true)
	{
		trigger_error("Input: ".$visible.", parsed: ".$visibility);
	}
		
	$query = sprintf("UPDATE `%s` SET `name` = '$name', `visible` = '$visibility' WHERE `group_id` = '$id'", $yourportfolio->_table['nl_groups']);
	$db->doQuery( $query, $null, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false );
	
	log_message( 'event', "Group information for group $name(id=$id) updated", debug_backtrace() );
	header("Location: newsletter_groups.php?case=show&group=$id");
	exit();
}

function deleteGroup()
{
	global $yourportfolio, $db, $data, $templates;
	$id = (int) $_POST['id'];
	$del_contents = $_POST['del_contents'];
	if( $id != 0 )	
	{
		$res = null;
		if( $del_contents == 'yes' )
		{
			// door een mysql bug kan er geen exists in delete worden gebruikt
			// we halen dus eerst alle adressen op om ze vervolgens weg te gooien
			$query = sprintf('SELECT address_id FROM `%s` WHERE group_id=%d', $yourportfolio->_table['nl_bindings'], $id);
			$db->doQuery( $query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'flat_array', false);
			if( is_array($res) && count($res) > 0 )
			{
				$list = '('.$res[0];
				foreach( $res as $r )
				{
					$list .= ', '.$r;
				}
				$list .= ')';
				$query = sprintf("DELETE FROM `%s` WHERE address_id IN $list", $yourportfolio->_table['nl_addresses']); 
				$db->doQuery( $query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
			}
		}

		$query = sprintf("DELETE FROM `%s` WHERE `group_id` = '$id'", $yourportfolio->_table['nl_bindings']);
		$db->doQuery( $query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
		$query = sprintf("DELETE FROM `%s` WHERE `group_id` = '$id'", $yourportfolio->_table['nl_groups']);
		$db->doQuery( $query, $res, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);
	}
	
	$query = "SELECT `group_id`, `name` FROM `".$yourportfolio->_table['nl_groups']."`";
	$db->doQuery( $query, $data['groups'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );

	$group = $data['groups'][0]['name'];
	log_message( 'event', "Group $group(id=$id) deleted", debug_backtrace() );
	
	header("Location: newsletter_groups.php");
	exit();
}

function listGroups()
{
	global $yourportfolio, $db, $data, $templates;
	
	$query = sprintf("SELECT `group_id`, `name` FROM `%s` ORDER BY `name` ASC", $yourportfolio->_table['nl_groups'] );
	$data = array('groups' => null);
	$db->doQuery( $query, $data['groups'],  __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	$templates[] = 'group_list.php';
}

function search($filter = 0)
{
	global $yourportfolio, $db, $canvas, $settings, $ordering;

	$orderfield = 'u.name';
	$direction = 'ASC';	

	if( isset( $_POST['param'] ) )
	{
		$param = $db->filter($_POST['param']);
	} else {
		exit();
	}
	
	if( isset( $_POST['group_only'] ) )
	{
		$group_only = $db->filter($_POST['group_only']);
	} else {
		$group_only = false;
	}
	
	if( isset($_POST['gid']) )
	{
		$gid = (int)$_POST['gid'];
	} else {
		$gid = 0;
	}
	
	if( isset($_GET['page']) )
	{
		$page = (int)$_GET['page'];
	} else if( isset($_POST['page']) ) {
		$page = (int) $_POST['page'];	
	} else {
		$page = 1;
	}
	
	if( isset($_COOKIE['app']) )
	{
		$app = $_COOKIE['app'];
	} else {
		$app = 0;
	}

	if( $group_only) 
	{
		$go = '&group_only=1';
	} else {
		$go = '&group_only=0';
	}
	
	$extra = '&group='. $gid . '&case=load'.$go;
	
	$regex = '%'.str_replace('_', '\_', $db->filter($param)).'%';
	if( $regex == '' )
	{
		$regex = '.*';
	}
	
	// query constructie
	
	if( $group_only )
	{
		$query = sprintf("SELECT u.address_id, u.name, u.address, u.status, u.created, u.recv_count FROM `%s` u, `%s` g WHERE g.group_id = '".$gid."' AND g.address_id = u.address_id AND ", $yourportfolio->_table['nl_addresses'], $yourportfolio->_table['nl_bindings']);
	} else {
		$query = "SELECT `address_id`, `address`, `name`, `status`, `created`, `status`, `status_param` FROM `".$yourportfolio->_table['nl_addresses']."` WHERE ";
	}
	
	if ($filter == 0)
	{
		$query .= "status IN (".AddressStatus::OK().", ".AddressStatus::ERROR().")";
	} else {
		$query .= "status = '".$db->filter($filter)."'";
	}
	
	if( $group_only )
	{ 
		$query .= sprintf(" AND (u.address LIKE '%s' OR u.name LIKE '%s') ORDER BY u.%s %s", utf8_decode($regex), utf8_decode($regex), $ordering['field'], $ordering['dir']);
	} else {
		$query .= sprintf(" AND (`address` LIKE '%s' OR `name` LIKE '%s') ORDER BY `%s` %s", utf8_decode($regex), utf8_decode($regex), $ordering['field'], $ordering['dir']);
	}
	

	$result = array();
	// pagination: alleen pagineren als zoekterm leeg is
	if( $app > 0 && $param == '')
	{
		$from = ($page-1) * $app;
		$query .= " LIMIT $from, $app";
	}	
	
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	
	$data['users'] = $result;
	
	header("Content-Type: text/html; charset=ISO-8859-1");
	
	print(count($result).'|');
	require(NL_TEMPLATES . 'components/group_address_start.php');
	$data['error_threshold'] = $settings['error_threshold'];
		
	// alle group members halen
	$gid = (int)$_POST['gid'];
	$query = sprintf("SELECT u.address_id, u.name, u.address, u.status, u.created, u.recv_count FROM `%s` u, `%s` g WHERE g.group_id = '".$gid."' AND g.address_id = u.address_id ORDER BY ".$orderfield." ".$direction."", 
	$yourportfolio->_table['nl_addresses'],
	$yourportfolio->_table['nl_bindings'] );
	$data['members'] = array();
	$members = array();
	$db->doQuery($query, $members, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false );
	
	if (!empty($members))
	{
		foreach ($members as $m)
		{
			$data['members'][] = $m['address_id'];
		}
	}
	
	if( is_array( $result ) && count( $result ) > 0 )
	{
		foreach( $result as $row )
		{
			$u = $row;
			if( in_array( $u['address_id'], $data['members'] ) )
			{
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}			
			
			require(NL_TEMPLATES . 'components/group_address_row.php');
		}
	}
	require(NL_TEMPLATES . 'components/group_address_end.php');
	
	exit();
}
do_output();

require(NL_CODE . 'shutdown.php');

?>