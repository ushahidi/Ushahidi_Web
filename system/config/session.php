<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package Session
 *
 * Session driver name.
 */
$config['driver'] = 'cookie';

/**
 * Session storage parameter, used by drivers.
 */
$config['storage'] = '';

/**
 * Session name.
 * It must contain only alphanumeric characters and underscores. At least one letter must be present.
 */
$config['name'] = 'kohanasession';

/**
 * Session parameters to validate: user_agent, ip_address, expiration.
 */
$config['validate'] = array('user_agent');

/**
 * Enable or disable session encryption.
 * Note: this has no effect on the native session driver.
 * Note: the cookie driver always encrypts session data. Set to TRUE for stronger encryption.
 */
$config['encryption'] = FALSE;

/**
 * Session lifetime. Number of seconds that each session will last.
 * A value of 0 will keep the session active until the browser is closed (with a limit of 24h).
 */
$config['expiration'] = 7200;

/**
 * Number of page loads before the session id is regenerated.
 * A value of 0 will disable automatic session id regeneration.
 */
$config['regenerate'] = 3;

/**
 * Percentage probability that the gc (garbage collection) routine is started.
 */
$config['gc_probability'] = 2;