<?php
/**
 * This file acts as the bootstrap for the installer.
 * It forwards all HTTP requests to the Install_Wizard class
 *
 * @copyright Ushahidi Inc <http://www.ushahidi.com>
 */

// Define absolute paths for the root and installation directories
define('ROOT_DIR', realpath(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR);
define('INSTALLER_DIR', ROOT_DIR.'installer'.DIRECTORY_SEPARATOR);
define('INSTALLER_PAGES', INSTALLER_DIR.'pages'.DIRECTORY_SEPARATOR);

require INSTALLER_DIR . 'wizard.php';
require INSTALLER_DIR . 'utils.php';

// Bootstrap the installer
Installer_Wizard::init();

if ($_POST)
{
	if  (isset($_POST['previous']))
	{
		Installer_Wizard::previous();
	}
	else
	{
		// Show the next step
		Installer_Wizard::next();
	}
}

?>