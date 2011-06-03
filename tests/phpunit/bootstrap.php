<?php
/**
 * PHPUnit bootstrap/test helper
 * This file is a variation of the index.php file found in the Kohana 2.3.x DOCROOT
 * and is used to invoke the process control file for the application
 */

/**
 * Define the website environment status. When this flag is set to TRUE, some
 * module demonstration controllers will result in 404 errors. For more information
 * about this option, read the documentation about deploying Kohana.
 *
 * @see http://docs.kohanaphp.com/installation/deployment
 */
define('IN_PRODUCTION', TRUE);

/**
 * Website application directory. This directory should contain your application
 * configuration, controllers, models, views, and other resources.
 *
 * This path can be absolute or relative to this file.
 */
$kohana_application = 'application';

/**
 * Kohana modules directory. This directory should contain all the modules used
 * by your application. Modules are enabled and disabled by the application
 * configuration file.
 *
 * This path can be absolute or relative to this file.
 */
$kohana_modules = 'modules';

/**
 * Kohana system directory. This directory should contain the core/ directory,
 * and the resources you included in your download of Kohana.
 *
 * This path can be absolute or relative to this file.
 */
$kohana_system = 'system';

/**
 * Themes directory.
 *
 * This path can be absolute or relative to this file.
 */
$kohana_themes = 'themes';

/**
 * Plugin directory.
 *
 * This path can be absolute or relative to this file.
 */
$kohana_plugins = 'plugins';

/**
 * Location of the PHPUnit unit tests
 */
$phpunit_tests = 'tests/phpunit';

/**
 * Test to make sure that Kohana is running on PHP 5.2 or newer. Once you are
 * sure that your environment is compatible with Kohana, you can comment this
 * line out. When running an application on a new server, uncomment this line
 * to check the PHP version quickly.
 */
version_compare(PHP_VERSION, '5.2', '<') and exit('Kohana requires PHP 5.2 or newer.');

/**
 * Set the error reporting level. Unless you have a special need, E_ALL is a
 * good level for error reporting.
 */
error_reporting(E_ALL & ~E_STRICT);

/**
 * Turning off display_errors will effectively disable Kohana error display
 * and logging. You can turn off Kohana errors in application/config/config.php
 */
ini_set('display_errors', TRUE);

/**
 * If you rename all of your .php files to a different extension, set the new
 * extension here. This option can left to .php, even if this file has a
 * different extension.
 */
define('EXT', '.php');

//
// DO NOT EDIT BELOW THIS LINE, UNLESS YOU FULLY UNDERSTAND THE IMPLICATIONS.
// ----------------------------------------------------------------------------
// $Id: index.php 3168 2008-07-21 01:34:36Z Shadowhand $
//

// Get the current directory
$current_dir =  str_replace('\\', '/', dirname(realpath(__FILE__)));

// Define the front controller name and docroot
define('DOCROOT', substr($current_dir, 0, strlen($current_dir) - strlen($phpunit_tests)));
define('KOHANA',  basename(__FILE__));

// If the front controller is a symlink, change to the real docroot
is_link(KOHANA) and chdir(dirname(realpath(__FILE__)));

// Define application and system paths
define('APPPATH', str_replace('\\', '/', DOCROOT.$kohana_application).'/');
define('THEMEPATH', str_replace('\\', '/', DOCROOT.$kohana_themes).'/');
define('PLUGINPATH', str_replace('\\', '/', DOCROOT.$kohana_plugins).'/');
define('MODPATH', str_replace('\\', '/', DOCROOT.$kohana_modules).'/');
define('SYSPATH', str_replace('\\', '/', DOCROOT.$kohana_system).'/');
define('TESTS_PATH', str_replace('\\', '/', DOCROOT.$phpunit_tests).'/');

// Clean up
unset($kohana_application, $kohana_themes, $kohana_plugins, $kohana_modules, $kohana_system, $phpunit_tests);

// Bootstrap the Kohana project, Ushahidi_Web in this case
require TESTS_PATH.'testbootstrap'.EXT;