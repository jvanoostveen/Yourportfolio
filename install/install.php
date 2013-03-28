#!/usr/bin/env php -Cq
<?PHP
require(dirname(__FILE__).'/../code/program/version.php');
require(dirname(__FILE__).'/Installer.php');

if ( !function_exists('file_put_contents') && !defined('FILE_APPEND') )
{
	define('FILE_APPEND', 1);
	function file_put_contents($n, $d, $flag = false)
	{
		$mode = ($flag == FILE_APPEND || strtoupper($flag) == 'FILE_APPEND') ? 'a' : 'w';
		$f = fopen($n, $mode);
		if ($f === false)
		{
			return 0;
		} else {
			if (is_array($d)) $d = implode($d);
			$bytes_written = fwrite($f, $d);
			fclose($f);
			return $bytes_written;
	    }
	}
}

$installer = new Installer();

// get the Yourportfolio code directory (BASE directory)
if ( !$installer->findYourportfolioDirectory(true) )
{
	exit("\nAborting installation...\nCouldn't locate Yourportfolio directory.\n");
}

if ( !$installer->setInstallDirectory() )
{
	exit("\ninstall directory not set.\n");
}

$installer->inputSettingsDirectory();

$installer->inputNewsletterOptions();

$installer->inputDataCommunication();
$installer->inputDebugMode();
$installer->inputDatabaseSettings();

$installer->databaseSetup();

$installer->adminSetup();
$installer->userSetup();

if ( $installer->confirmAll() )
{
	$installer->install();
} else {
	exit("\nAborting installation...\n");
}

$installer->linkSettings();
$installer->linkSite();

echo PHP_EOL."\033[1mInstallation was succesful!\033[0m".PHP_EOL.PHP_EOL;

echo "\n\n";
?>