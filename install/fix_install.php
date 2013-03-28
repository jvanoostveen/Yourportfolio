#!/usr/bin/env php -Cq
<?PHP
require(dirname(__FILE__).'/../code/program/version.php');
require(dirname(__FILE__).'/Installer.php');

$installer = new Installer();

// get the Yourportfolio code directory (BASE directory)
if ( !$installer->findYourportfolioDirectory(true) )
{
	exit("\nAborting installation...\nCouldn't locate Yourportfolio directory.\n");
}

$installer->fixExistingInstall();

print "\n\n";
?>