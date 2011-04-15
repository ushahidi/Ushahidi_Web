<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */

$config['_default'] = 'main';
$config['feed/atom'] = 'feed/index/atom';

// Action::config - Config Routes
Event::run('ushahidi_action.config_routes', $config);