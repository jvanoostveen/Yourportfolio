<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 */
 
/**
 * class: SiteSystem
 * handles common and systemwide settings
 * 
 * @package yourportfolio
 * @subpackage Core
 */
class SiteSystem
{
	/**
	 * current filename.ext
	 * @var string
	 */
	var $file;
	var $base_url;
	
	/**
	 * current url
	 * @var string
	 */
	var $url;
	
	var $query;
	
	var $browser;
	var $os;
	
	/**
	 * has there been a change to the data
	 */
	var $changed = false;
	var $force_update = false;
	var $sitemap_changed = false;
	
	/**
	 * loaded modules (start with no modules loaded, load them on demand)
	 * @var array $modules
	 */
	var $modules = array();
	
	/**
	 * list of known modules
	 * @var array $known_modules
	 */
	var $known_modules = array();
	
	/**
	 * class contructor (PHP5)
	 *
	 */
	function __construct()
	{
		$this->base_url = $this->rootUrl();
		
		if (defined('FRONTEND') && FRONTEND)
			$this->setupHTMLDefines();
		
		$this->file = $this->thisFile();
		$this->url = $this->thisUrl();
		$this->setEnvironment();
		
		$this->findModules();
	}
	
	/**
	 * class contructor (PHP4)
	 *
	 */
	function SiteSystem()
	{
		$this->__construct();
	}
	
	function setupHTMLDefines()
	{
		define('CSS', $this->base_url.'templates/css/');
		define('SCRIPTS', $this->base_url.'templates/scripts/');
		define('IMAGES', $this->base_url.'templates/images/');
	}
	
	function mem($msg = '')
	{
		if (!empty($msg))
			$msg .= ': ';
		
		if (function_exists('memory_get_usage') && function_exists('memory_get_peak_usage'))
		{
			trigger_error($msg.'mem curr/peak: '.round(memory_get_usage() / pow(1024, 2), 1).' / '.round(memory_get_peak_usage() / pow(1024, 2), 1).' MB');
		}
	}
	
	/**
	 * get to know the modules
	 *
	 * @access private
	 */
	function findModules()
	{
		if ($handle = opendir(MODULES))
		{
			while (false !== ($module = readdir($handle)))
			{
				if ($module{0} != '.' && is_file(MODULES.$module))
				{
					$this->known_modules[] = str_replace('.php', '', $module);
				}
			}
		}
	}
	
	/**
	 * return reference to the module
	 * create new instance if module has not yet been created
	 *
	 * @param string $module
	 *
	 * @return object
	 * @access public
	 */
	function getModule($module)
	{
		if (in_array($module, $this->known_modules))
		{
			if (!isset($this->modules[$module]))
			{
				require(MODULES.$module.'.php');
				$this->modules[$module] = new $module();
			}
			return $this->modules[$module];	
		} else {
			return false;
		}
	}
	
	/**
	 * header() wrapper
	 * TODO: remove the need for $_SERVER vars, because they can be manipulated
	 *
	 * @param string $location
	 * @access private
	 */
	function relocate($location, $intern = true, $status_code = '302')
	{
		$this->updateChanges();
		
		$status_codes = array(
						'301'	=> 'HTTP/1.1 301 Moved Permanently',
						'302'	=> 'HTTP/1.1 302 Found',
						);
		
//		if (DEBUG)
//			$this->mem("relocating");
		
		if ($intern)
		{
			$server = $_SERVER['HTTP_HOST'];
			$home = substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/') + 1);
			
			header($status_codes[$status_code]);
			header('Location: http://'.$server.$home.$location);
		} else {
			header($status_codes[$status_code]);
			header('Location: '.$location);
		}
		exit();
	}
	
	function updateChanges()
	{
		if ($this->changed)
		{
			global $yourportfolio;
			if ($yourportfolio->settings['autopublish'] || $this->force_update)
			{
				$yourportfolio->update_xml();
			}
		}
		
		if ($this->sitemap_changed)
		{
			global $yourportfolio;
			if ($yourportfolio->settings['autopublish'] || $this->force_update)
			{
				$yourportfolio->createGoogleSitemap();
			}
		}
	}
	
	/**
	 * flush the session and generate a new id
	 *
	 */
	function flushSession()
	{
		session_unset();
		session_destroy();
		session_start();
		session_regenerate_id();
	}
	
	function siteUrl()
	{
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']))
			$protocol = 'https';
		$server = $_SERVER['HTTP_HOST'];
		return $protocol.'://'.$server;
	}
	
	function rootUrl()
	{
		$script_name = $_SERVER['SCRIPT_NAME'];
		$tmp_url = dirname($script_name);
		
		if (strlen($tmp_url) > 0 && substr($tmp_url, -1, 1) != '/')
		{
			$tmp_url .= '/';
		}
		
		return $tmp_url;
	}
	
	/**
	 * gets the current uri including query
	 * 
	 * @uses thisFile()
	 * @uses thisQuery()
	 * @return string
	 */
	function thisUrl()
	{
		return $this->thisFile().$this->thisQuery();
	}
	
	/**
	 * get the current query
	 * 
	 * @uses subUri()
	 *
	 * @param boolean $inc
	 * @return string
	 */
	function thisQuery($inc=0)
	{
		if (strpos($_SERVER['REQUEST_URI'], '?'))
			return $this->subUri($_SERVER['REQUEST_URI'], '?', $inc);
	}

	/**
	 * gets the current uri (filename only)
	 * 
	 * @uses thisFile()
	 * @return string
	 */
	function baseUrl()
	{
		return $this->thisFile();
	}

	/**
	 * retrieve file name
	 * 
	 * @uses subUri()
	 *
	 * @param boolean $inc
	 * @return string
	 */
	function thisFile($inc=1)
	{
		return $this->subUri($_SERVER['SCRIPT_NAME'], '/', $inc);
	}

	/**
	 * gets a part from the current uri
	 * 
	 * @param string $string
	 * @param string $glue
	 * @param boolean $inc
	 * @return string
	 */
	function subUri($string, $glue, $inc)
	{
		return substr($string, strrpos($string, $glue)+$inc);
	}
	
	/**
	 * checks for file uploads and generates an array with file info
	 *
	 * @param string $formname first part of the form name
	 * @return array
	 * @access public
	 */
	function postedFiles($formname = '')
	{
		global $messages;
		
		if (!is_dir(SETTINGS.'tmp'))
		{
			trigger_error('need a tmp dir to write to.. make one in '.SETTINGS.'. Now switching to main tmp dir.', E_USER_NOTICE);
			$messages->add(sprintf(_('Een `tmp` directory is nodig in `%s`.'), SETTINGS), MESSAGE_ERROR);
			if (!is_dir(CODE.'tmp'))
			{
				trigger_error('need a tmp dir to write to.. make one in '.CODE, E_USER_ERROR);
			} else if (!is_writable(CODE.'tmp'))
			{
				trigger_error('directory '.CODE.'tmp is not writable.', E_USER_ERROR);
				$messages->add(sprintf(_('De directory `%s` is niet schrijfbaar.'), CODE.'tmp'), MESSAGE_ERROR);
			} else {
				$temp_dir = CODE.'tmp/';
			}
		} else if (!is_writable(SETTINGS.'tmp'))
		{
			trigger_error('directory '.SETTINGS.'tmp is not writable.', E_USER_ERROR);
			$messages->add(sprintf(_('De directory `%s` is niet schrijfbaar.'), SETTINGS.'tmp'), MESSAGE_ERROR);
		} else {
			$temp_dir = SETTINGS.'tmp/';
		}
		
		
		$files = array();
		if (isset($_FILES[$formname."Files"]) || isset($_FILES['files']))
		{
			$form = isset($_FILES[$formname."Files"]) ? $_FILES[$formname."Files"] : $_FILES['files'];
			
			$magic_quotes = get_magic_quotes_gpc();
			
			foreach($form['error'] as $key => $error)
			{
				if ( $error == UPLOAD_ERR_OK && !empty($form['name'][$key]) && !empty($form['size'][$key]) ) // no error, there was a file and it was uploaded
				{
					if ($magic_quotes)
					{
						$form['name'][$key] = stripslashes($form['name'][$key]);
					}
					
					$files[$key] = array(
						'name'	=> 		basename($form['name'][$key]),
						'type'	=> 		$form['type'][$key],
						'tmp_name'	=> 	$form['tmp_name'][$key],
						'size'	=> 		$form['size'][$key],
						);
					
					// use the fileinfo extension for type/mime checks
					// pear install fileinfo
					// ...
					
					$files[$key]['extension'] = $this->fileExtension($files[$key]['name']);
					
					if (!move_uploaded_file($files[$key]['tmp_name'], $temp_dir.substr($files[$key]['tmp_name'], strrpos($files[$key]['tmp_name'],'/') + 1)))
					{
						trigger_error('File transfer failed (/tmp -> '.$temp_dir, E_USER_WARNING);
						$messages->add(sprintf('Het bestand `%s` kon niet worden weggeschreven.', $files[$key]['name']), MESSAGE_WARNING);
					} else {
						$files[$key]['tmp_name'] = $temp_dir.substr($files[$key]['tmp_name'], strrpos($files[$key]['tmp_name'],'/') + 1);
						
						// check if file size is correct, could be tempered with while it was sitting in the tmp folder
						if (filesize($files[$key]['tmp_name']) != $files[$key]['size'])
						{
							// file size doesn't match, remove the file from temp and from the files array
							trigger_error('File size doesn\'t match, could be tempered with, removing file', E_USER_WARNING);
							$messages->add(sprintf('Bestandsgrootte komt niet overeen met wat verzonden is, bestand `%s` wordt overgeslagen.', $files[$key]['name']), MESSAGE_NOTICE);
							unlink($files[$key]['tmp_name']);
							unset($files[$key]);
						}
					}
				} else if ($error == UPLOAD_ERR_INI_SIZE || $error == UPLOAD_ERR_FORM_SIZE)
				{
					trigger_error('File upload was too large', E_USER_NOTICE);
					$messages->add(sprintf('Bestand `%s` is te groot. Maximale bestandsgrootte is `%s`B.', $form['name'][$key], $this->maxFileUpload()), MESSAGE_NOTICE);
				} else if ($error == UPLOAD_ERR_PARTIAL)
				{
					trigger_error('File only partial uploaded', E_USER_NOTICE);
					$messages->add(sprintf('Bestand `%s` is slechts voor een deel geupload, deze wordt overgeslagen.', $form['name'][$key]), MESSAGE_NOTICE);
				}
				
				/*
				UPLOAD_ERR_OK
				Value: 0; There is no error, the file uploaded with success.
				
				UPLOAD_ERR_INI_SIZE
				Value: 1; The uploaded file exceeds the  upload_max_filesize directive in php.ini.
				
				UPLOAD_ERR_FORM_SIZE
				Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
				
				UPLOAD_ERR_PARTIAL
				Value: 3; The uploaded file was only partially uploaded.
				
				UPLOAD_ERR_NO_FILE
				Value: 4; No file was uploaded.
				*/
			}
		}
		unset($_FILES, $form); // remove _FILES data
		
		return $files;
	}
	
	/**
	 * removes files from custom temp directory
	 *
	 * @param array $files containing the files to be deleted
	 *
	 * @access private
	 */
	function cleanPostedFiles($files)
	{
		// delete files after use
		foreach($files as $file)
		{
			if (file_exists($file['tmp_name']))
			{
				unlink($file['tmp_name']);
			} else {
				trigger_error('file: '.$file['tmp_name'].' didn\'t exist?', E_USER_NOTICE);
			}
		}
	}
	
	/**
	 * extracts extension from filename
	 *
	 * @param string $filename	name of the file
	 * @return string			returns the extension
	 */
	function fileExtension($filename)
	{
		$pos = strrpos($filename,".");
		if ($pos === false)
			$extension = null;
		else
			$extension = strtolower(substr($filename, $pos + 1));
		
		return $extension;
	}
	
	function setChanged()
	{
		$this->changed = true;
	}

	function forceUpdate()
	{
		$this->force_update = true;
	}

	function setSitemapChanged()
	{
		$this->sitemap_changed = true;
	}
	
	function setEnvironment()
	{
		$this->setOS();
		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			$this->browser = 4;
			return;
		}
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], "Mozilla/5.0") !== false)
			$this->browser = 5;
		else
			$this->browser = 4;
	}
	
	/**
	 * sets the OS the user is using
	 * for now, only switch to MacOS, otherwise asume it's Windows (or Windows' behavior)
	 */
	function setOS()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], "Macintosh") !== false)
		{
			$this->os = 'MacOS';
		} else {
			$this->os = 'Windows';
		}
	}
	
	function isIE()
	{
		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}
		
		// MSIE
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false)
		{
			return true;
		} else {
			return false;
		}		
	}
	
	function isSafari()
	{
		if (!isset($_SERVER['HTTP_USER_AGENT']))
		{
			return false;
		}
		
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false)
		{
			return true;
		} else {
			return false;
		}
	}
	
	function getFileInfo($file, $name = '')
	{
		if (!is_dir(CODE.'tmp'))
		{
			trigger_error('need a tmp dir to write to.. make one in '.CODE, E_USER_ERROR);
		}
		
		if (!is_writable(CODE.'tmp'))
		{
			trigger_error('directory '.CODE.'tmp is not writable.', E_USER_ERROR);
		}
		
		$info = array(
					'name'	=> 		substr($file, strrpos($file,'/') + 1),
#					'type'	=> 		$form['type'][$key],
#					'tmp_name'	=> 	$form['tmp_name'][$key], // will be supplied later
					'size'	=> 		filesize($file),
				);
		
		$info['extension'] = $this->fileExtension($info['name']);
		
		if ($info['extension'] == 'jpg')
		{
			$info['type'] = 'image/jpeg';
		}
		
		if ( rename($file, CODE.'tmp/'.$info['name']) )
		{
			$info['tmp_name'] = CODE.'tmp/'.$info['name'];
		} else {
			trigger_error('Couldn\'t move file ('.$file.')', E_USER_NOTICE);
		}

		if (!empty($name))
		{
			$info['name'] = $name;
		}
		
		return $info;
	}
	
	function maxPostUpload()
	{
		return ini_get('post_max_size');
	}
	
	function maxFileUpload()
	{
		return ini_get('upload_max_filesize');
	}
	
	function isCVS($string)
	{
		if (strpos($string, '$Name:') !== false)
			return true;
		else
			return false;
	}
	
	/**
	 * Sets a given parameter to the url, and returns the new url.
	 * It accounts for any other parameters in the url, which will be kept, but when the new parameter is also set,
	 * it will be overwritten to the new value.
	 * 
	 * @param $url:String
	 * $param $param:String
	 * $param $value:Mixed
	 * @return String
	 */
	function setParameter($url, $param, $value)
	{
		$qstart = strpos($url, '?');
		if ($qstart === false)
		{
			$url .= '?'.$param.'='.$value;
			return $url;
		} else {
			$query = substr($url, $qstart + 1);
			$parameters = array();
			parse_str($query, $parameters);
			
			if (isset($parameters['switch']))
			{
				unset($parameters['switch']);
			}
			
			$parameters[$param] = $value;
			
			if (function_exists('http_build_query'))
			{
				return '?'.http_build_query($parameters);
			} else {
				return '?'.$this->http_build_query($parameters);
			}
		}
	}
	
	/**
	 * Custom http_build_query for when the build in function is not available.
	 * 
	 * @see http_build_query()
	 * @param $formdata:Array
	 * @param $numeric_prefix
	 * @param $key
	 * @return String
	 */
	function http_build_query( $formdata, $numeric_prefix = null, $key = null )
	{
		$res = array();
		foreach ( (array) $formdata as $k => $v)
		{
			$tmp_key = urlencode(is_int($k) ? $numeric_prefix.$k : $k);
			if ($key)
			{
				$tmp_key = $key.'['.$tmp_key.']';
			}
			if ( is_array($v) || is_object($v) )
			{
				$res[] = $this->http_build_query($v, $numeric_prefix, $tmp_key);
			} else {
				$res[] = $tmp_key."=".urlencode($v);
			}
		}
		$separator = ini_get('arg_separator.output');
		return implode($separator, $res);
	}
}
?>