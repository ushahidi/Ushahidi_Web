<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @package Session
 *
 * Session driver name.
 */
$config['driver'] = 'database';

/**
 * Session storage parameter, used by drivers.
 */
//$config['storage'] = '';

/**
 * Session name.
 * It must contain only alphanumeric characters and underscores. At least one letter must be present.
 */
$config['name'] = 'ushahidi';

/**
 * Session parameters to validate: user_agent, ip_address, expiration.
 */
$config['validate'] = array('user_agent', 'expiration');

/**
 * Enable or disable session encryption.
 */
$config['encryption'] = TRUE;

/**
 * Session lifetime. Number of seconds that each session will last.
 * A value of 0 will keep the session active until the browser is closed (with a limit of 24h).
 */
//$config['expiration'] = 7200;

/**
 * Number of page loads before the session id is regenerated.
 * A value of 0 will disable automatic session id regeneration.
 */
$config['regenerate'] = 0;

/**
 * Percentage probability that the gc (garbage collection) routine is started.
 */
//$config['gc_probability'] = 2;
