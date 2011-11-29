<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base path of the web site. If this includes a domain, eg: localhost/ushahidi/
 * then a full URL will be used, eg: http://localhost/ushahidi/. If it only includes
 * the path, and a site_protocol is specified, the domain will be auto-detected.
 */
$config['site_domain'] = '/';

/**
 * Force a default protocol to be used by the site. If no site_protocol is
 * specified, then the current protocol is used, or when possible, only an
 * absolute path (with no protocol/domain) is used.
 */
$config['site_protocol'] = 'http';

/**
 * Name of the front controller for this application. Default: index.php
 *
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = 'index.php';

/**
 * Whether or not you want to have the auto upgrader enabled.
 * TRUE will ping Ushahidi.com for the latest version.
 * FALSE will require you to check manually and do upgrades by hand.
 */
$config['enable_auto_upgrader'] = TRUE;

/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 */
$config['url_suffix'] = '';

/**
 * Length of time of the internal cache in seconds. 0 or FALSE means no caching.
 * The internal cache stores file paths and config entries across requests and
 * can give significant speed improvements at the expense of delayed updating.
 */
$config['internal_cache'] = TRUE;

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = TRUE;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = TRUE;

/**
 * Enable or disable hooks. Setting this option to TRUE will enable
 * all hooks. By using an array of hook filenames, you can control
 * which hooks are enabled. Setting this option to FALSE disables hooks.
 */
$config['enable_hooks'] = TRUE;

/**
 * Log thresholds:
 *  0 - Disable logging
 *  1 - Errors and exceptions
 *  2 - Warnings
 *  3 - Notices
 *  4 - Debugging
 */
$config['log_threshold'] = 1;

/**
 * Message logging directory.
 */
$config['log_directory'] = APPPATH.'logs';

if (@!is_writable($config["log_directory"])) {
	$config["log_threshold"] = 0;
}

/**
 * Enable or disable displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = TRUE;

/**
 * Enable or disable statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://docs.kohanaphp.com/general/configuration
 */
$config['render_stats'] = TRUE;

/**
 * Turn MHI on or off. This is an advanced feature that will drastically alter
 * the way your instance works. Please read documentation before proceeding.
 *
 * @see [A URL not yet created]
 */
$config['enable_mhi'] = FALSE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 */
$config['extension_prefix'] = 'MY_';

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array
(
	MODPATH.'auth',      // Authentication
	// MODPATH.'forge',     // Form generation
	// MODPATH.'formation',     // Form generation
	// MODPATH.'kodoc',     // Self-generating documentation
	// MODPATH.'media',     // Media caching and compression
	// MODPATH.'archive',   // Archive utility
	// MODPATH.'unit_test', // Unit testing
);
