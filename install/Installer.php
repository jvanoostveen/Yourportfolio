<?PHP
/**
 * Project:			yourportfolio
 *
 * @link http://www.yourportfolio.nl
 * @copyright 2007 Furthermore
 * @copyright 2010 Axis media-ontwerpers
 * @author Joeri van Oostveen <joeri@furthermore.nl>
 * @author Joeri van Oostveen <joeri@axis.fm>
 */

/**
 * class: Installer
 * 
 * an interactive Yourportfolio installer
 *
 * @package yourportfolio
 * @subpackage Install
 */
class Installer
{
	var $core_dir;
	var $core_install_dir;
	var $install_dir;
	var $settings_dir;
	
	var $install_db		= false;
	var $debug_install	= false;
	//var $insert_user	= false;
	
	var $code_locations = array('./yourportfolio',
								'../yourportfolio', 
								'../../yourportfolio', 
								'../../../yourportfolio',
								);
	
	var $db;
	var $db_settings	= array();
	var $db_override	= false;
	var $db_override_type;
	
	var $table_prefix		= 'yp_';
	var $core_tables		= array('photographers', 'photographer_data', 'challenges');
	var $user_tables		= array('albums', 'album_files', 'items', 'item_files', 'parameters', 'sections', 'section_files', 'users', 'subusers', 'subuser_album', 'language_strings', 'links', 'statistics', 'views', 'contact', 'guestbook', 'tags', 'tag_groups', 'album_tags', 'section_tags', 'item_tags', 'metadata');
	var $newsletter_tables	= array('nl_addresses', 'nl_templates', 'nl_letter_items', 'nl_item_files', 'nl_letters', 'nl_maillog', 'nl_groups', 'nl_address_group', 'nl_recipients', 'nl_queue', 'nl_settings', 'nl_log', 'nl_incoming', 'nl_links', 'nl_letter_stats');
	var $newsletter_settings = array('from_name' => '', 'mbox_address' => '', 'mbox_host' => '', 'mbox_user' => '', 'mbox_pass' => '', 'mbox_method' => 'APOP', 'mbox_port' => 110, 'error_threshold' => 4, 'batch_size' => 25, 'mail_method' => 'smtp', 'smtp_host' => '', 'smtp_username' => '', 'smtp_password' => '', 'unsubscribe_mode' => 'systeem');
	
	var $install_core			= false;
	var $install_core_tables	= array();
	
	var $install_user			= true;
	var $install_user_tables	= array();
	
	var $install_newsletter 	= false;
	
	var $install_xml			= true;
	var $install_amfphp			= false;
	
	var $core_version	= CORE_VERSION;
	var $user_version	= USER_VERSION;
	
	var $admin_settings = array(
							'id'		=> 0,
							'firstname'	=> 'Administrator',
							'lastname'	=> '',
							'login'		=> '',
							'password'	=> ''
							);
	
	var $user_settings	= array(
							'id'		=> 0,
							'firstname'	=> '',
							'lastname'	=> '',
							'login'		=> '',
							'password'	=> ''
							);
	
	var $sql			= array();
	
	/**
	 * Create a new Installer object.
	 */
	function __construct()
	{
		print "\n";
		print "\033[1mYourportfolio v".$this->core_version." installer\033[0m\n";
		print "(c) Furthermore\n";
		print "joeri@furthermore.nl\n\n";
	}
	
	/**
	 * PHP4 wrapper for construct.
	 */
	function Installer()
	{
		$this->__construct();
	}
	
	function findYourportfolioDirectory($cwd = false)
	{
		if ($cwd)
		{
			$path = realpath(dirname(__FILE__).'/../');
		} else {
			foreach($this->code_locations as $location)
			{
				if (file_exists($location) && $this->validYourportfolioDirectory($location))
				{
					$path = realpath($location);
					break;
				} else {
					$path = false;
				}
			}
		}
		
		if ($path === false)
		{
			print "Unable to find Yourportfolio code directory.\n";
			print "Please specify code directory:\n";
			return $this->inputYourportfolioDirectory();	
		} else {
			return $this->confirmYourportfolioDirectory($path);
		}
	}
	
	function validYourportfolioDirectory($path)
	{
		if ( ($path = $this->validDirectory($path)) !== false && $this->validDirectory($path.'/code/') !== false )
		{
			return $path;
		} else {
			return false;
		}
	}
	
	function inputYourportfolioDirectory()
	{
		$path = false;
		print "Current working directory: ".getcwd()."\n";
		while ( $path === false )
		{
			print "> ";
			$tmp_core_dir = trim(fgets(STDIN));
			if (substr($tmp_core_dir, -1, 1) == '/')
			{
				$tmp_core_dir = substr($tmp_core_dir, 0, -1);
			}
			print "\n";
			$path = $this->validYourportfolioDirectory($tmp_core_dir);
			if ($path === false)
			{
				print "Incorrect Yourportfolio folder, please try again.\nSpecify path of folder containing the 'code' folder.\n";
			}
		}
		
		return $this->confirmYourportfolioDirectory($path);
	}
	
	function confirmYourportfolioDirectory($path)
	{
		print "Please confirm the Yourportfolio directory:\n";
		print $path;
		print "\n\nIs this folder correct?\n";

		if ($this->confirm(array('yes', 'no'), true))
		{
			$this->core_dir = $path;
			if (!defined('CODE'))
			{
				define('CODE', $this->core_dir.'/code/');
			}
			
			$this->core_install_dir = realpath($path.'/../');
			return true;	
		} else {
			print "Please specify code directory:\n";
			return $this->inputYourportfolioDirectory();
		}
	}
	
	function setInstallDirectory()
	{
		$path = getcwd();
		return $this->confirmInstallDirectory($path);
	}
	
	function confirmInstallDirectory($path)
	{
		if (substr($path, -7, 7) == 'install')
		{
			print "\nWhere do you want the (public) site to be installed?\n";
			return $this->inputInstallDirectory();
		}
		
		print "\nDo you want to install the (public) site in this directory?\n";
		print $path;
		print "\n";
		
		if ($this->confirm(array('yes', 'no'), true))
		{
			$this->install_dir = $path;
			return true;
		} else {
			print "Please specify install directory:\n";
			return $this->inputInstallDirectory();
		}
	}
	
	function inputInstallDirectory()
	{
		$path = false;
		while ( $path === false )
		{
			print "> ";
			$tmp_install_dir = trim(fgets(STDIN));
			print "\n";
			$path = $this->validDirectory($tmp_install_dir);
			if ($path === false)
			{
				print "Incorrect install directory.\nMake sure your path points to an existing directory.\n";
			}
		}
		
		return $this->confirmInstallDirectory($path);	
	}
	
	function inputSettingsDirectory()
	{
		print "\nSpecify settings directory: (name or domain of site, no spaces or other strange characters)\n";
		while ( empty($this->settings_dir) )
		{
			print "> ";
			$this->settings_dir = trim(fgets(STDIN));
			$filtered = preg_replace("/([^A-Za-z0-9_-]+)/", '', $this->settings_dir);
			if ($filtered != $this->settings_dir)
			{
				echo 'Unsupported characters found, using: '.$filtered.PHP_EOL;
			}
			$this->settings_dir = $filtered;
		}
		print "\n";
	}
	
	function inputDebugMode()
	{
		$debug_file = $this->core_install_dir.'/yourportfolio-settings/yourportfolio_debug';
		if (file_exists($debug_file))
		{
			echo PHP_EOL.'Debug mode is already enabled.'.PHP_EOL;
			$this->debug_install = true;
			return;
		}
		
		echo PHP_EOL.'Enable debug mode on this installation?'.PHP_EOL;
		if ($this->confirm(array('yes', 'no'), false))
		{
			$this->debug_install = true;
		}
	}
	
	function inputDatabaseSettings()
	{
		$this->inputCustomDatabaseSettings();
		$this->testDatabaseConnection();
	}
	
	function testDatabaseConnection()
	{
		print "\nTesting database connection ..... ";
		if ( empty($this->db) )
		{
			if (!file_exists($this->core_dir.'/code/system/DatabaseToolkit.php'))
			{
				exit("\nCan't load DatabaseToolkit class.\n");
			}
			require($this->core_dir.'/code/system/DatabaseToolkit.php');
			$this->db = new DatabaseToolkit($this->db_settings, array());
		} else {
			$this->db->setDatabaseSettings($this->db_settings);
		}
		
		// make sure we have a fresh connection.
		if ($this->db->isConnected())
		{
			$this->db->disconnect();
		}
		
		if ( $this->db->connect() )
		{
			print "\033[32m\033[1mok\033[0m\n\n";
		} else {
			
			// connection has been made, but there was an other error.
			// test to check if it was the database which doesn't exist so we can try to create it.
			switch ($this->db->lastErrorNo())
			{
				// unknown database
				case (1049):
					// CREATE DATABASE  `mydatabase`;
					print "\nDatabase doesn't exist, try to created it?\n";
					if ($this->confirm(array('yes', 'no'), true))
					{
						$this->db->createDatabase();
						$this->testDatabaseConnection();
						return;
					}
					print "\nTesting database connection ..... ";
					break;
			}
			print "\033[31m\033[1mfailed (couldn't connect to database)\033[0m\n".$this->db->lastError()."\n\n";
			$this->inputDatabaseSettings();
		}
	}
	
	function inputCustomDatabaseSettings()
	{
		$type = 'live';
		if ($this->debug_install)
		{
			$type = 'debug';
		}
		
		$this->db_override = true;
		$this->db_override_type = $type;
		
		$this->db_settings['host'] = null;
		$this->db_settings['user'] = null;
		$this->db_settings['pass'] = null;
		$this->db_settings['name'] = null;
		
		print "\nSpecify database settings:\n";
		while ( empty($this->db_settings['host']) )
		{
			print "host     : ";
			$this->db_settings['host'] = trim(fgets(STDIN));
		}
		while ( empty($this->db_settings['user']) )
		{
			print "user     : ";
			$this->db_settings['user'] = trim(fgets(STDIN));
		}
		while ( empty($this->db_settings['pass']) )
		{
			print "password : ";
			$this->db_settings['pass'] = trim(fgets(STDIN));
		}
		while ( empty($this->db_settings['name']) )
		{
			print "name     : ";
			$this->db_settings['name'] = trim(fgets(STDIN));
		}
	}
	
	function inputNewsletterOptions()
	{
		print "Do you want to install the newsletter?\n";
		
		$this->install_newsletter = $this->confirm(array('yes', 'no'), false);
		
		if ($this->install_newsletter)
		{
			$padding = 0;
			foreach ($this->newsletter_settings as $setting => $value)
			{
				if (strlen($setting) > $padding)
				{
					$padding = strlen($setting);
				}
			}
			
			$padding += 2;
			
			print "\nSpecify settings:\n";
			foreach ($this->newsletter_settings as $setting => $value)
			{
				do
				{
					$default = $value;
					
					print $setting.str_repeat(' ', $padding - strlen($setting)).(!empty($default) ? '('.$default.')' : '').': ';
					$this->newsletter_settings[$setting] = trim(fgets(STDIN));
					
					if (empty($this->newsletter_settings[$setting]) && !empty($default))
					{
						$this->newsletter_settings[$setting] = $default;
					}
				} while ( empty($this->newsletter_settings[$setting]) );
			}
		}
	}
	
	function inputDataCommunication()
	{
		print PHP_EOL."Pick the data communication method: (default is to use XML)".PHP_EOL;
		print "1) XML".PHP_EOL;
		print "2) XML & AMFPHP hybrid".PHP_EOL;
		print "3) AMFPHP".PHP_EOL;
		print PHP_EOL."> ";
		
		$respons = trim(fgets(STDIN));
		
		switch($respons)
		{
			case(''):
			case('1'):
				$this->install_xml = true;
				$this->install_amfphp = false;
				break;
			case('2'):
				$this->install_xml = true;
				$this->install_amfphp = true;
				break;
			case('3'):
				$this->install_xml = false;
				$this->install_amfphp = true;
				break;
			default:
				print "\n\033[31mPick one of the options!\033[0m";
				$this->inputDataCommunication();
		}
	}
		
	function databaseSetup()
	{
		$tables = $this->db->getTables();
		
		// add newsletter tables to list of to be installed tables
		$this->user_tables = array_merge($this->user_tables, $this->newsletter_tables);
		
		$padding = 0;
		foreach ($this->core_tables as $table)
		{
			if (strlen($table) > $padding)
			{
				$padding = strlen($table);
			}
		}
		$padding += 6;
		
		print "Checking existence of core tables:\n";
		foreach($this->core_tables as $table)
		{
			print $this->table_prefix.$table;
			print ' '.str_repeat('.', $padding - strlen($table)).' ';
			if (in_array($this->table_prefix.$table, $tables))
			{
				print "\033[32m\033[1mok\033[0m\n";
			} else {
				print "\033[31m\033[1mmissing\033[0m\n";
				$this->install_core_tables[] = $table;
				$this->install_core = true;
			}
		}
		
		$padding = 0;
		foreach ($this->user_tables as $table)
		{
			if (strlen($table) > $padding)
			{
				$padding = strlen($table);
			}
		}
		$padding += 6;
		
		print "\n";
		print "Checking for existence of user tables:\n";
		foreach($this->user_tables as $table)
		{
			print $this->table_prefix.$this->settings_dir.'_'.$table;
			print ' '.str_repeat('.', $padding - strlen($table)).' ';
			if (in_array($this->table_prefix.$this->settings_dir.'_'.$table, $tables))
			{
				print "\033[32m\033[1mok\033[0m\n";
			} else {
				print "\033[31m\033[1mmissing\033[0m\n";
				$this->install_user_tables[] = $table;
				$this->install_user = true;
			}
		}
		
		if (!empty($this->install_core_tables) || !empty($this->install_user_tables))
		{
			print "\n";
			print "\033[1mMissing tables will be created.\033[0m\n";
		}
	}
	
	function adminSetup()
	{
		if (!$this->install_core)
			return;
		
		print "\nSpecify admin settings:\n";
		while (empty($this->admin_settings['login']))
		{
			print "login     : ";
			$this->admin_settings['login'] = trim(fgets(STDIN));
		}
		while (empty($this->admin_settings['password']))
		{
			print "password  : ";
			$this->admin_settings['password'] = trim(fgets(STDIN));
		}
	}
	
	
	function userSetup()
	{
#		$tables = $this->db->getTables();
		
		print "\nSpecify user settings:\n";
		while (empty($this->user_settings['firstname']))
		{
			print "firstname : ";
			$this->user_settings['firstname'] = trim(fgets(STDIN));
		}
		while (empty($this->user_settings['lastname']))
		{
			print "lastname  : ";
			$this->user_settings['lastname'] = trim(fgets(STDIN));
		}
		while (empty($this->user_settings['login']))
		{
			print "login     : ";
			$this->user_settings['login'] = trim(fgets(STDIN));
			
			// check if user exists
			if (!$this->install_core)
			{
				$query = "SELECT id FROM ".$this->table_prefix.$this->core_tables[0]." WHERE login='".$this->db->filter($this->user_settings['login'])."'";
				$result = null;
				$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'value', false);
				
				if (!empty($result))
				{
					print "\033[31m\033[1mUser already exists...\033[0m\n";
					$this->user_settings['login'] = "";
				}
			}
		}
		while (empty($this->user_settings['password']))
		{
			print "password  : ";
			$this->user_settings['password'] = trim(fgets(STDIN));
		}
		
	}
	
	function confirm($options, $default)
	{
		$true	= array(strtolower($options[0]), substr($options[0], 0, 1));
		$false	= array(strtolower($options[1]), substr($options[1], 0, 1));
		
		if ($default)
		{
			$true[] = '';
		} else {
			$false[] = '';
		}
		
		$query = '';
		
		if ($default)
		{
			$query .= strtoupper($options[0]).' / '.$options[1];
		} else {
			$query .= $options[0].' / '.strtoupper($options[1]);
		}
		$query .= ' > ';
		
		print $query;
		
		$validInput = false;
		while ( !$validInput )
		{
			$input = strtolower(trim(fgets(STDIN)));
			if ( in_array($input, $true) || in_array($input, $false) )
			{
				$validInput = true;
			} else {
				print "\nInvalid answer, please try again:\n".$query;
			}
		}
		
		return in_array($input, $true) ? true : false;
	}
	
	function confirmAll()
	{
		print "\nPlease confirm all settings:\n\n";
		print "Yourportfolio directory : ".$this->core_dir."\n";
		print "Install directory       : ".$this->install_dir."\n";
		print "\n";
		print "Install core tables : ".(($this->install_core) ? 'yes' : 'no')."\n";
		print "Install user tables : ".(($this->install_user) ? 'yes' : 'no')."\n";
		print "\n";
		print "Core version : ".$this->core_version."\n";
		print "User version : ".$this->user_version."\n";
		print "\n";
		print "Install newsletter module : yes (can be enabled in the settings)\n";
		print "\n";
		print "name     : ".$this->user_settings['firstname'].' '.$this->user_settings['lastname']."\n";
		print "login    : ".$this->user_settings['login']."\n";
		print "password : ".$this->user_settings['password']."\n";
		
		
		print "\nIs this correct? (no will abort the installation)\n";
		return $this->confirm(array('yes', 'no'), true);
	}
	
	function validDirectory($path)
	{
		return (file_exists($path)) ? realpath($path) : false;
	}
	
	function createDirectory($path, $chmod = 0755)
	{
		if ( !file_exists($path) )
		{
			mkdir($path);
			chmod($path, $chmod);
		}
	}
	
	function createSymlink($target, $link)
	{
		if (!is_link($link) && !is_file($link))
		{
			return symlink($target, $link);
		} else {
			return true;
		}
	}
	
	function createFile($path, $data)
	{
		if (is_link($path))
		{
			unlink($path);
		}
		$fp = fopen($path, 'w');
		fwrite($fp, $data);
		fclose($fp);
	}
	
	function getFiles($path, $pattern)
	{
		$old_dir = getcwd();
		chdir($path);
		$files = glob($pattern);
		chdir($old_dir);
		return $files;
	}
	
	/**
	 * performs checks and recreates symlinks when a new version is uploaded in a new folder
	 */
	function fixExistingInstall()
	{
		if (empty($this->core_dir))
		{
			$this->inputYourportfolioDirectory();
		}
		
		print "\n\n";
		
		// check tmp writeable
		if ( substr(sprintf('%o', fileperms($this->core_dir.'/code/tmp')), -4) != 0777)
		{
			chmod($this->core_dir.'/code/tmp', 0777);
		}
	}
	
	function installCore()
	{
		
	}
	
	function installUser()
	{
		
	}
	
	function install()
	{
		print "\nStarting installation...\n";
		
		$dir = dirname(__FILE__);
		
		// core
		// create code/settings folder
		$this->createDirectory($this->core_install_dir.'/yourportfolio-settings');
		$this->createDirectory($this->core_install_dir.'/yourportfolio-settings/'.$this->settings_dir);
		$this->createDirectory($this->core_install_dir.'/yourportfolio-settings/'.$this->settings_dir.'/tmp', 0777);
		$this->createDirectory($this->core_install_dir.'/yourportfolio-settings/'.$this->settings_dir.'/cache', 0777);
		$this->createDirectory($this->core_install_dir.'/yourportfolio-settings/'.$this->settings_dir.'/cache/service', 0777);
		
		// add debug file if install is debug
		if ($this->debug_install)
		{
			$debugFile = $this->core_install_dir.'/yourportfolio-settings/yourportfolio_debug';
			if (!file_exists($debugFile))
			{
				file_put_contents($debugFile, '');
			}
		}
		
		// save us some typing
		$this->settings_path = $this->core_install_dir.'/yourportfolio-settings/'.$this->settings_dir;
		
		$this->createDirectory($this->settings_path.'/icons');
		$this->createDirectory($this->settings_path.'/newsletter');
		$this->createDirectory($this->settings_path.'/newsletter/template', 0777);
		$this->createDirectory($this->settings_path.'/newsletter/content', 0777);
		$this->createDirectory($this->settings_path.'/newsletter/cache', 0777);
		
		$this->fixExistingInstall();
		
		// if (install_core) insert core sql
		require($dir.'/installer_data/sql_core.php');
		
		if ($this->install_core)
		{
			foreach($this->install_core_tables as $table)
			{
				$result = null;
				$this->db->doQuery($this->sql[$table], $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'create', false);
			}
			
			if (in_array('photographers', $this->install_core_tables))
			{
				$this->db->doQuery($this->sql['admin'], $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			}
		}
		
		// insert user rows
		if ($this->install_user)
		{
			$this->user_settings['id'] = 0;
			$this->db->doQuery($this->sql['user'], $this->user_settings['id'], __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			if (intval($this->user_settings['id']) == 0)
			{
				exit("\nFatal error: new user didn't return id value.\n");
			}
		}
		
		// site
		// create folders
		$this->createDirectory($this->install_dir.'/assets');
		$this->createDirectory($this->install_dir.'/assets/movies', 0777);
		$this->createDirectory($this->install_dir.'/assets/music', 0777);
		$this->createDirectory($this->install_dir.'/assets/original', 0777);
		$this->createDirectory($this->install_dir.'/assets/preview', 0777);
		$this->createDirectory($this->install_dir.'/assets/thumbs', 0777);
		$this->createDirectory($this->install_dir.'/assets/yourportfolio', 0777);
		$this->createDirectory($this->install_dir.'/assets/cache_upload', 0777);
		$this->createDirectory($this->install_dir.'/assets/downloads', 0777);
		$this->createDirectory($this->install_dir.'/assets/cache', 0777);
		$this->createDirectory($this->install_dir.'/edit');
		$this->createDirectory($this->install_dir.'/templates');
		$this->createDirectory($this->install_dir.'/templates/shared');
		$this->createDirectory($this->install_dir.'/templates/desktop');
		$this->createDirectory($this->install_dir.'/templates/tablet');
		$this->createDirectory($this->install_dir.'/templates/mobile');
		
		// data directory or amfphp directory
		if ($this->install_xml)
			$this->createDirectory($this->install_dir.'/data', 0777);
		
		if ($this->install_amfphp)
			$this->createDirectory($this->install_dir.'/amfphp');
		
		// create symlinks
		$links = $this->getFiles($this->core_dir.'/site/source/', '*.php');
		if (!empty($links))
		{
			foreach($links as $link)
			{
				$this->createSymlink($this->core_dir.'/site/source/'.$link, $this->install_dir.'/'.$link);
			}
		}
		
		if ($this->install_amfphp)
			$this->createSymlink($this->core_dir.'/site/source/amfphp/gateway.php', $this->install_dir.'/amfphp/gateway.php');
		
		$this->createSymlink($this->core_dir.'/site/design', $this->install_dir.'/design');
		
		$links = $this->getFiles($this->core_dir.'/site/source/edit/', '*.php');
		if (!empty($links))
		{
			foreach($links as $link)
			{
				$this->createSymlink($this->core_dir.'/site/source/edit/'.$link, $this->install_dir.'/edit/'.$link);
			}
		}
		$this->createSymlink($this->settings_path.'/icons', $this->install_dir.'/edit/icons');
		
		$this->createSymlink($this->core_dir.'/design', $this->install_dir.'/edit/design');
		
		$this->createSymlink($this->settings_path.'/newsletter', $this->install_dir.'/newsletter_images');
		$this->createSymlink($this->settings_path.'/newsletter', $this->install_dir.'/edit/newsletter');
		
		// create site/base.php
		// create site/edit/base.php
		$base = '';
		require($dir.'/installer_data/base.php');
		$this->createFile($this->install_dir.'/base.php', $base);
		
		require($dir.'/installer_data/base_edit.php');
		$this->createFile($this->install_dir.'/edit/base.php', $base);
		
		// place default robots.txt
		$this->createSymlink($dir.'/installer_data/robots.txt', $this->install_dir.'/robots.txt');
		
		// place default .htaccess file
		copy($dir.'/installer_data/htaccess', $this->install_dir.'/.htaccess');
		
		// if (install_user) insert user sql
		if ($this->install_user)
		{
			require($dir.'/installer_data/sql_user.php');
			
			// load sql for newsletter
			require($dir.'/installer_data/sql_newsletter.php');
			
			$result = null;
			$this->db->doQuery($this->sql['user_data'], $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			
			foreach($this->install_user_tables as $table)
			{
				if (!isset($this->sql[$table]))
				{
					trigger_error('There was supposed to be a table named: '.$table.' but there is no sql for it.', E_USER_WARNING);
					continue;
				}
				
				if (empty($this->sql[$table]))
				{
					continue;
				}
				
				$this->db->doQuery($this->sql[$table], $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'create', false);
			}
			
			// setup default newsletter settings and default template
			// always install newsletter, so activating later is easy.
			
			// setup settings
			$result = null;
			$this->db->doQuery($this->sql['newsletter_settings'], $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
			
			foreach ($this->newsletter_settings as $setting => $value)
			{
				$result = null;
				$query = "UPDATE `".$this->table_prefix.$this->settings_dir."_nl_settings` SET value='".$this->db->filter($value)."' WHERE name='".$setting."'";
				$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'update', false);
			}
			
			// install default template
			require($dir.'/installer_data/template/template.php');
			$t = get_template($this->db);
			$query = "INSERT INTO `".$this->table_prefix.$this->settings_dir."_nl_templates` SET `name`='".$t['name']."', `default_title`='".$t['default_title']."', `header`='".$t['header']."', `item`='".$t['item']."', `footer`='".$t['footer']."', `header_text`='".$t['header_text']."', `item_text`='".$t['item_text']."', `footer_text`='".$t['footer_text']."', `online`='Y', `created`=NOW(), `modified`=NOW();";
			$this->db->doQuery($query, $result, __FILE__, __LINE__, __FUNCTION__, __CLASS__, '', 'insert', false);
		}
		
		// create settings/runtime.ini
		$runtime = '';
		require($dir.'/installer_data/runtime.php');
		$this->createFile($this->settings_path.'/runtime.ini', $runtime);

		// create settings/site.ini
# ** =>	// (no config for this file during installer yet)
#		require('installer_data/site.php');
#		$this->createFile($this->settings_dir.'/site.ini', $site);
		
		// create settings/tables.ini
		if (class_exists('SQLite3'))
		{
			$this->createDirectory($this->settings_path.'/db', 0777);
			
			$config = new SQLite3($this->settings_path.'/db/config.db', SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);
			chmod($this->settings_path.'/db/config.db', 0666);
			
			$config->exec('CREATE TABLE IF NOT EXISTS "settings" ("name" STRING, "value" STRING)');
			$stmt = $config->prepare('INSERT INTO "settings" ("name", "value") VALUES (:name, :value)');
			
			$data = array();
			$data[] = array('prefix', substr($this->table_prefix, 0, -1));
			$data[] = array('settings', $this->settings_dir);
			$data[] = array('divider', '_');
			
			$config->exec('BEGIN TRANSACTION');
			foreach ($data as $row)
			{
				$stmt->bindValue(':name', $row[0], SQLITE3_TEXT);
				$stmt->bindValue(':value', $row[1], SQLITE3_TEXT);
				
				$result = $stmt->execute();
			}
			$config->exec('COMMIT TRANSACTION');
			
			
			$config->exec('CREATE TABLE IF NOT EXISTS "tables" ("name" STRING, "realname" STRING)');
			$stmt = $config->prepare('INSERT INTO "tables" ("name", "realname") VALUES (:name, :realname)');
			
			$data = array();
			$prefix = $this->table_prefix.$this->settings_dir.'_';
			$data[] = array('albums', $prefix.'albums');
			$data[] = array('album_files', $prefix.'album_files');
			$data[] = array('sections', $prefix.'sections');
			$data[] = array('section_files', $prefix.'section_files');
			$data[] = array('items', $prefix.'items');
			$data[] = array('item_files', $prefix.'item_files');
			$data[] = array('parameters', $prefix.'parameters');
			$data[] = array('client_users', $prefix.'users');
			$data[] = array('subusers', $prefix.'subusers');
			$data[] = array('subuser_album', $prefix.'subuser_album');
			$data[] = array('strings', $prefix.'language_strings');
			$data[] = array('links', $prefix.'links');
			$data[] = array('contact', $prefix.'contact');
			$data[] = array('guestbook', $prefix.'guestbook');
			
			// reserved
			$data[] = array('statistics', $prefix.'statistics');
			$data[] = array('views', $prefix.'views');
			
			// tag support
			$data[] = array('tags', $prefix.'tags');
			$data[] = array('tag_groups', $prefix.'tag_groups');
			$data[] = array('item_tags', $prefix.'item_tags');
			
			// metadata
			$data[] = array('metadata', $prefix.'metadata');
			
			// newsletter
			$data[] = array('nl_addresses', $prefix.'nl_addresses');
			$data[] = array('nl_letters', $prefix.'nl_letters');
			$data[] = array('nl_letter_items', $prefix.'nl_letter_items');
			$data[] = array('nl_item_files', $prefix.'nl_item_files');
			$data[] = array('nl_templates', $prefix.'nl_templates');
			$data[] = array('nl_images', $prefix.'nl_images');
			$data[] = array('nl_maillog', $prefix.'nl_maillog');
			$data[] = array('nl_pending', $prefix.'nl_pending');
			$data[] = array('nl_groups', $prefix.'nl_groups');
			$data[] = array('nl_address_group', $prefix.'nl_address_group');
			$data[] = array('nl_bindings', $prefix.'nl_address_group');
			$data[] = array('nl_recipients', $prefix.'nl_recipients');
			$data[] = array('nl_queue', $prefix.'nl_queue');
			$data[] = array('nl_settings', $prefix.'nl_settings');
			$data[] = array('nl_log', $prefix.'nl_log');
			$data[] = array('nl_links', $prefix.'nl_links');
			$data[] = array('nl_letter_stats', $prefix.'nl_letter_stats');
			$data[] = array('nl_incoming', $prefix.'nl_incoming');
			$data[] = array('nl_optinlog', $prefix.'nl_optinlog');
			
			$config->exec('BEGIN TRANSACTION');
			foreach ($data as $row)
			{
				$stmt->bindValue(':name', $row[0], SQLITE3_TEXT);
				$stmt->bindValue(':realname', $row[1], SQLITE3_TEXT);
				
				$result = $stmt->execute();
			}
			$config->exec('COMMIT TRANSACTION');
			
			$config->close();
		} else {
			$tables = array();
			require($dir.'/installer_data/tables.php');
			$this->createFile($this->settings_path.'/tables.ini', $tables);
		}
		
		// if custom db/debug, create database.ini / database_debug.ini
		if ($this->db_override)
		{
			$database = '';
			require($dir.'/installer_data/database.php');
			$database_override = ($this->db_override_type == 'live') ? 'database.ini' : 'database_debug.ini';
			$this->createFile($this->settings_path.'/'.$database_override, $database);
		}
		
		$this->addNoRewrite($this->install_dir.'/assets');
		$this->addNoRewrite($this->install_dir.'/edit');
		$this->addNoRewrite($this->install_dir.'/templates');
		if (!$this->install_amfphp)
		{
			$this->addNoRewrite($this->install_dir.'/data');
		} else {
			$this->addNoRewrite($this->install_dir.'/amfphp');
		}
	}
	
	function addNoRewrite($directory)
	{
		if (substr($directory, -1, 1) != '/')
		{
			$directory .= '/';
		}
		$path = $directory.'.htaccess';
		
		if (!file_exists($path))
		{
			$content = 'RewriteEngine Off';
			file_put_contents($path, $content);
		}
	}
	
	function linkSettings()
	{
		$parts = explode('/', $this->install_dir);
		
		if ($parts[count($parts) - 1] != 'deploy')
		{
			return;
		}
		
		$settings = realpath($this->install_dir.'/../settings');
		if (!file_exists($settings))
			return;
		
		$settings .= '/';
		$inis = $this->getFiles($settings, '*.ini');
		if (!empty($inis))
		{
			echo 'Linking settings...'.PHP_EOL;
			
			foreach($inis as $ini)
			{
				$this->createSymlink($settings.$ini, $this->settings_path.'/'.$ini);
			}
		}
	}
	
	function linkSite()
	{
		$parts = explode('/', $this->install_dir);
		
		if ($parts[count($parts) - 1] != 'deploy')
		{
			return;
		}
		
		$parts2 = array();
		$parts2[] = $parts[0];
		$parts2[] = $parts[1];
		$parts2[] = $parts[2];
		$parts2[] = 'Sites';
		
		$path = realpath(implode('/', $parts2));
		
		if ($path && file_exists($path))
		{
			echo 'Creating symlink to webroot...'.PHP_EOL;
			echo "Website can - depends on your setup - be reached at \033[1mhttp://localhost/".$this->settings_dir."\033[0m.".PHP_EOL;
			
			$this->createSymlink($this->install_dir, $path.'/'.$this->settings_dir);
		} else {
			echo 'Unable to create symlink to webroot, add it manually...'.PHP_EOL;
		}
	}
}
?>