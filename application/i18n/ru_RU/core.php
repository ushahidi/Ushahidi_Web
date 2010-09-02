<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * FRONT END USER INTERFACE INTERNATIONALIZATION
 * Strings associated with the front end UI
 *
 * Misprints, mistypes, language errors and mistakes, new vars are
 * fixed by Sergei 'the_toon' Plaxienko sergei.plaxienko@gmail.com
 *
 * ru_RU
 *
 */

/**
* Errors associated with the core of the system
*/
$lang = array
(
	'there_can_be_only_one' => 'There can be only one deployment of Ushahidi per page request',
	'uncaught_exception' => 'Uncaught %s: %s in file %s on line %s',
	'invalid_method' => 'Invalid method %s called in %s',
	'invalid_property' => 'The %s property does not exist in the %s class.',
	'log_dir_unwritable' => 'The log directory is not writable: %s',
	'resource_not_found' => 'The requested %s, %s, could not be found',
	'invalid_filetype' => 'The requested filetype, .%s, is not allowed in your view configuration file',
	'view_set_filename' => 'You must set the the view filename before calling render',
	'no_default_route' => 'Please set a default route in config/routes.php',
	'no_controller' => 'Ushahidi was not able to determine a controller to process this request: %s',
	'page_not_found' => 'The page you requested, %s, could not be found.',
	'stats_footer' => 'Loaded in {execution_time} seconds, using {memory_usage} of memory. Generated by Ushahidi v%s.',
	'report_bug' => '<a href="%s" id="show_bugs">Report This Issue To Ushahidi</a>',
	'error_file_line' => '<tt>%s <strong>[%s]:</strong></tt>',
	'stack_trace' => 'Stack Trace',
	'generic_error' => 'Unable to Complete Request',
	'errors_disabled' => 'You can go to the <a href="%s">home page</a> or <a href="%s">try again</a>.',

	// Drivers
	'driver_implements' => 'The %s driver for the %s library must implement the %s interface',
	'driver_not_found' => 'The %s driver for the %s library could not be found',

	// Resource names
	'config' => 'config file',
	'controller' => 'controller',
	'helper' => 'helper',
	'library' => 'library',
	'driver' => 'driver',
	'model' => 'model',
	'view' => 'view',
);
?>