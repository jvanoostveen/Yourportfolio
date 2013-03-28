<?PHP
/*
 * Project: yptrunk
 *
 * @author Christiaan Ottow
 * @created May 4, 2007
 */

$page_name = 'templates';
$page_title = 'Templates';

require(CODE.'newsletter/code/startup.php');
require_once(NL_CODE.'classes/NewsletterTemplate.php');
//require_once(NL_CODE.'classes/TemplateParser.php');

// check of admin is ingelogd
if( !$yourportfolio->session['master'] )
{
	ob_end_clean();
	header("Location: newsletter_start.php");
	exit();
}

$canvas->addScript('nl_common');
$canvas->addScript('nl_templates');

$Template = new NewsletterTemplate();

$case = 'list';

if( isset($_POST['case']) )
{
	$case = $_POST['case'];
} else if( isset($_GET['case']) ) {
	$case = $_GET['case'];
}

function build_menu()
{
	global $submenu, $db, $yourportfolio;
	
	$result = array();
	$query = sprintf("SELECT `template_id`, `name`, `online` FROM `%s` ORDER BY `name` ASC", $yourportfolio->_table['nl_templates']);
	$db->doQuery( $query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array', false);
	
	$submenu = array();
	
	if (!empty($result))
	{
		$template_id= (isset($_GET['id'])) ? (int) $_GET['id'] : -1;
		
		foreach( $result as $row )
		{
			$submenu_entry = array (
				'name' 		=> $row['name'],
				'icon'		=> 'iconsets/default/users_filled.gif',
				'href'		=> 'newsletter_templates.php?case=edit&id=' . $row['template_id'],
				'active'	=> false
			);
			
			if ($row['template_id'] == $template_id)
			{
				$submenu_entry['active'] = true;
			}
			
			$submenu[] = $submenu_entry;
		}
	}
	
	$submenu[] = array (
			'name'	=> '<i>'._('nieuwe template...').'</i>',
			'icon'	=> 'img/btn_new_album.gif',
			'href'	=> 'newsletter_templates.php?case=edit',
			'id'	=> 'newTemplateLink',
		);
}

switch($case)
{
	case 'list':
		t_list();
		break;
	case 'edit':
		t_edit();
		break;
	case 'save':
		t_save();
		break;
	case 'delete':
		t_delete();
		break;
	case 'img_upload':
		img_upload();
		break;
	case 'img_delete':
		img_delete();
		break;
//	case 'export':
//		t_export();
//		break;
	default:
		t_list();
		break;
}

function t_list()
{
	global $Template, $db, $yourportfolio, $data, $templates;
	
	$data['templates'] = $Template->getTemplates(true);
	$templates[] = 'template_list.php';
}

function t_edit( $id = null )
{
	global $Template, $db, $yourportfolio, $data, $templates, $components;

	$components['topBar'][] = 'save_link.php';
	$components['bottomBar'][] = 'save_link.php';
	
	$data['images'] = list_images();
	
	if( $id == null )
	{
		if( isset($_GET['id']) && $_GET['id'] > 0 )
		{
			$id = (int)$_GET['id'];
		}
		
		if( isset($_POST['id']) && $_POST['id'] > 0 )
		{
			$id = (int)$_POST['id'];
		}
	}	
	
	if( isset($id) )
	{
		$Template->id = $id;
		$Template->load();
		$Template->loadDesign();
		
		$data['template'] = $Template;
	}
	
	$templates[] = 'template_edit.php';
}

function t_save( $show_output=true )
{
	global $Template, $db, $yourportfolio;
	
	$input = $_POST['template'];
	if( isset($input['id']) )
	{
		$Template->id 				= (int)$input['id'];
	}
	
	$Template->name 			= $db->filter($input['name']);
	$Template->default_title 	= $db->filter($input['default_title']);
	$Template->itemimage_width	= (int)$input['itemimage_width'];
	$Template->itemimage_height	= (int)$input['itemimage_height'];
	$Template->online			= ($input['online'] == 'Y' ? 'Y' : 'N');
	$Template->header			= $db->filter($input['header']);
	$Template->header_text		= $db->filter($input['header_text']);
	$Template->item				= $db->filter($input['item']);
	$Template->item_text		= $db->filter($input['item_text']);
	$Template->footer			= $db->filter($input['footer']);
	$Template->footer_text		= $db->filter($input['footer_text']);
	
	if( $Template->name == '' )
	{
		$Template->name = _('nieuwe template');
	}
	
	$Template->save();
	
	if( $show_output )
	{
		t_edit( $Template->id );
	}
	log_message( 'event', "Template ".$input['name']."(id=".$input['id'].")", debug_backtrace() );
}

function t_delete()
{
	global $db, $yourportfolio, $components, $templates;
	
	$id = (int)$_POST['template']['id'];
	if( $id > 0 )
	{
		// get lowest template ID that is not the template to be deleted
		$query = sprintf("SELECT MIN(`template_id`) FROM `%s` WHERE `template_id`!=%d", $yourportfolio->_table['nl_templates'], $id);
		$result = '';
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
		
		// update letters using the template that will be deleted
		$query = sprintf("UPDATE `%s` SET `template_id`=%d WHERE `template_id`=%d", $yourportfolio->_table['nl_letters'], $result, $id);
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
		
		// delete the template
		$query = sprintf("DELETE FROM `%s` WHERE `template_id`=%d", $yourportfolio->_table['nl_templates'], $id);
		$db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'delete', false);		
		
		log_message( 'event', "Template $id deleted", debug_backtrace() );
	}
	
	t_list();
}

function img_delete()
{
	global $db, $yourportfolio, $components, $templates;
	
	$img = parse_filename($_POST['deleteImgName']);
	if( !empty($img) && file_exists(SETTINGS.'newsletter/template/'.$img))
	{
		unlink(SETTINGS.'newsletter/template/'.$img);
	}
	
	t_save(false);
	t_edit($_POST['template']['id']);
	
}

function img_upload()
{
	global $db, $yourportfolio, $components, $templates, $data;
	t_save(false);
	
	$file_name = $_FILES['imageFile']['name'];
	$tmp_name = $_FILES['imageFile']['tmp_name'];
	
	if( !empty($file_name) && file_exists($tmp_name))
	{
		if( !move_uploaded_file($tmp_name, SETTINGS.'newsletter/template/'.$file_name) )
		{
			$data['errors']['general'] = _('Er is een fout opgetreden bij het wegschrijven van het bestand.');
		} else {
			chmod(SETTINGS.'newsletter/template/'.$file_name, 0666);
		}
	}
	
	t_edit($_POST['template']['id']);
}

/*function t_export()
{
	global $db, $yourportfolio, $templates;
	
	$tp = new TemplateParser();
	$output = $tp->createXMLFile((int)$_GET['id']);
	echo $output;
	die();	
}
*/
/**
 * Geef een lijst van geuploade afbeeldingen
 * @return array
 */
function list_images()
{
	$result = array();
	$dh = opendir(SETTINGS.'newsletter/template');
	while (($file = readdir($dh)) !== false) 
	{
		if( !(substr($file,0,1)=='.') )
		{
			$result[] = $file;
		}
	}
	
	closedir($dh);
	return $result;
}

/**
 * Maak een filename veilig door de punten weg te halen
 * @param $filename string de te parsen filename
 * @return string de veilige filename
 */
function parse_filename($filename)
{
	return str_replace('/','',$filename);
}

build_menu();


do_output();

require(CODE.'newsletter/code/shutdown.php');
?>
