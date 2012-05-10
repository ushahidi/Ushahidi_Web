<?php defined('SYSPATH') or die('No direct script access.');

/**
* RIVERID CONFIGURATION
*/

/**
 * Enable RiverID, bool
 * If this is set to true, you must define an API endpoint
 */

$config['enable'] = false;

/**
 * RiverID Server HTTP API endpoint
 * ie https://crowdmapid.com/api (no trailing slash!)
 */

$config['endpoint'] = '';

/**
 * RiverID API Key
 * A registered API key is necessary to communicate with an endpoint.
 */

$config['api_key'] = '';

/**
 * RiverID user exemption list
 * Performs authentication locally instead of on the defined RiverID server
 *
 * Array of integer userids to be exempted from being authenticated
 *   through the RiverID server. You may want to consider doing
 *   this with the main administrator account (usually ID 1).
 */
$config['exempt'] = array();

/**
 * Cache lifetime in seconds.
 * We cache some variables like server name, version and URL. This is
 * the life of that cache. These variables rarely change.
 */

$config['cache_lifetime'] = 86400; // Default: 1 Day = 86400 Seconds

?>