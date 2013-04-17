<?php defined('SYSPATH') or die('No direct script access.');

$config['directory'] = DOCROOT.'media/uploads';
$config['relative_directory'] = 'media/uploads';
$config['create_directories'] = TRUE;
$conif['remove_spaces'] = TRUE;

// Action::config - Config Upload
Event::run('ushahidi_action.config_upload', $config);