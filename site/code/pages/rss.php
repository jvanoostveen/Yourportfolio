<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */

/**
 * RSS feeds
 *
 * @package yourportfolio
 * @subpackage Site
 */

// start the program
require(CODE.'program/startup.php');

$vars		= array();
$query		= array();

$album_id	= null;
$section_id	= null;

$album		= null;
$section	= null;

$language	= null;

// show only a selection of the contents.
if (!empty($_GET['q']))
{
	$album_q	= null;
	$section_q	= null;
	
	$query_string = $_GET['q'];
	if (substr($query_string, 0, 4) == '.php')
	{
		// create a new query string but without /rss/.
		$query_string = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/rss/') + 5);
	}
	
	$query = explode('/', $query_string);
	
	// last entry is always nonsense (because of trailing slash)
	if (substr($query_string, -1, 1) == '/')
	{
		array_pop($query);
	}
	
	if (YP_MULTILINGUAL)
	{
		if (in_array($query[0], array_keys($GLOBALS['YP_LANGUAGES'])))
		{
			$language = array_shift($query);
			$GLOBALS['YP_CURRENT_LANGUAGE'] = $language;
		}
	}
	
	$vars[]	= &$album_q;
	$vars[]	= &$section_q;
	
	$i = 0;
	while (list($key, $value) = each($query))
	{
		if (!empty($value) && ctype_alnum($value))
		{
			$vars[$i] = $value;
		}
		$i++;
	}
	unset($vars);
	
	$album = $yourportfolio->searchAlbum($album_q);
	if (!is_null($album))
	{
		$album_id = $album->id;
		$section = $album->searchSection($section_q);
	}
}

if (is_null($language))
{
	$language = (YP_MULTILINGUAL) ? $GLOBALS['YP_DEFAULT_LANGUAGE'] : 'default';
}

// if section is set, show only items from section.
// if album is set, show only items from album (all sections) if album template is album.
// if album is set and album is news, show only newsitems.
$canvas->site_templates = true;
$canvas->template = 'empty';

$cache_name = '';

if (!is_null($album))
{
	$album->load();
	if ($album->restricted == 'Y')
	{
		print "no rss for secured albums.";
		exit();
	}
	
	$cache_name = 'rss_album_'.$album->id.'_'.$language.'.xml';
	
	switch ($album->template)
	{
		case ('news'):
			
			// try to show cache.
			showCache($cache_name, $album->modified);
			
			$album->loadSections();
			
			$canvas->template = 'news';
			break;
		case ('album'):
			
			$items = array();
			
			// first check section if we need to show only section
			if (!is_null($section))
			{
				$cache_name = 'rss_section_'.$album->id.'_'.$section->id.'_'.$language.'.xml';
				$section->load();
				
				showCache($cache_name, $section->modified);
				
				$section->loadItemsRSS();
				$items = $section->items;
				
				$canvas->template = 'items_section';
			} else {
				showCache($cache_name, $album->modified);
				
				$items = $album->loadItemsRSS();
				
				$sections = array();
				
				$canvas->template = 'items_album';
			}
			
			$yourportfolio->parseCustomFields();
			break;
		default:
			// not a news or album type of album...
			// could be a text album.
			print 'we don\'t have rss for this...';
			exit();
	}
} else {
	$cache_name = 'rss_all_'.$language.'.xml';
	
	// get last modification date
	$last_modified = '';
	$query = "SELECT UNIX_TIMESTAMP(MAX(modified)) FROM `".$db->_table['albums']."`";
	$db->doQuery($query, $last_modified, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
	
	showCache($cache_name, $last_modified);
	
	// select items for in rss feed.
	$items = array();
	$query = "SELECT id, album_id, section_id, type, name, text, link, custom_data, UNIX_TIMESTAMP(modified) AS modified FROM `".$db->_table['items']."` 
		WHERE 
			online='Y'
		AND album_id IN (SELECT id FROM `".$db->_table['albums']."` WHERE online='Y' AND restricted='N')
		AND section_id IN (SELECT id FROM `".$db->_table['sections']."` WHERE online='Y')
		ORDER BY modified DESC, id DESC LIMIT 500";
	$db->doQuery($query, $items, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'array_of_objects', false, array('object' => 'Item'));
	
	if (empty($items))
	{
		$items = array();
	}
	
	// select newsitems for in rss feed.
	$sections = array();
	
	if ($yourportfolio->settings['news_templates'])
	{
		$query = "SELECT id, album_id, type, name, text, link, UNIX_TIMESTAMP(section_date) AS modified FROM `".$db->_table['sections']."`
				WHERE
					online='Y' AND template='newsitem'
				AND album_id IN (SELECT id FROM `".$db->_table['albums']."` WHERE online='Y' AND restricted='N')
				ORDER BY section_date DESC, id DESC";
		$db->doQuery($query, $sections, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'index_array_of_objects', false, array('index_key' => 'id', 'object' => 'Section'));
		
		if (empty($sections))
		{
			$sections = array();
		}
	}
	
	// order items and newsitems so they are sorted on date.
	$rss_contents = array();
	if (count($items) > 0 && count($sections) > 0)
	{
		$rss_contents = array_merge($sections, $items);
		
		function compare_modified($a, $b)
		{
			if ($a->modified == $b->modified)
			{
				return 0;
			}
			
			return ($a->modified > $b->modified) ? -1 : 1;
		}
		
		usort($rss_contents, "compare_modified");
	} else {
		if (count($items) > 0)
		{
			$rss_contents = $items;
		} else if (count($sections) > 0)
		{
			$rss_contents = $sections;
		}
	}
	
	$albums = array();
	
	$canvas->template = 'mixed_site';
	
	$yourportfolio->parseCustomFields();
}

// show template.
if ($language == 'default')
{
	$language = '';
}
require($canvas->RSSTemplate($canvas->template));

$output = ob_get_contents();
ob_end_flush();

if (!function_exists('file_put_contents') && !defined('FILE_APPEND'))
{
	define('FILE_APPEND', 1);
	
	function file_put_contents($n, $d, $flag = false)
	{
		$mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
		$f = @fopen($n, $mode);
		if ($f === false)
		{
			return 0;
		} else {
			if (is_array($d))
			{
				$d = implode($d);
			}
			$bytes_written = fwrite($f, $d);
			fclose($f);
			return $bytes_written;
		}
	}
}

// write cache file
$cache_file = DATA_DIR.$cache_name;
file_put_contents($cache_file, $output, LOCK_EX);
chmod($cache_file, 0666);

// start the end of the program
$db->disconnect();
exit();

/**
 * Checks cache file against time, if cache file is expired it will be removed.
 * If a cache file is valid, it will be outputted to the user.
 * 
 * @return Boolean
 */
function showCache($cache_name, $time)
{
	$cache_file = DATA_DIR.$cache_name;
	
	if (file_exists($cache_file))
	{
		if (filectime($cache_file) < $time)
		{
			unlink($cache_file);
			
			return false;
		}
		
		if ( $fp = fopen($cache_file, 'rb') )
		{
			flock($fp, LOCK_SH);
			header("Cache-Control: must-revalidate");
			header("Pragma: cache");
			header("Last-Modified: ".date("r", $time));
			header('Content-Disposition: inline; filename="'.$cache_name.'"');
			header('Content-Type: text/xml; charset="iso-8859-1"; name="'.$cache_name.'"');
			header('Content-Length: '.(string) filesize($cache_file));
			
			while ( (!feof($fp)) && (connection_status() == 0) )
			{
				echo fread($fp, 1024 * 8);
				flush();
			}
			flock($fp, LOCK_UN);
			fclose($fp);
			
			exit();
		}
	} else {
		return false;
	}
}
?>