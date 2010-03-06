<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Default language locale name(s).
 * First item must be a valid i18n directory name, subsequent items are alternative locales
 * for OS's that don't support the first (e.g. Windows). The first valid locale in the array will be used.
 * @see http://php.net/setlocale
 */
$config['language'] = array('en_US', 'English_United States');

/**
 * All Available Languages
 * Activate new languages by adding them to this array
 */
$config['all_languages'] = array ( 'en_US'=>'English (US)', 'fr_FR'=>'Français' );

/**
 * Locale timezone. Defaults to use the server timezone.
 * @see http://php.net/timezones
 */
$config['timezone'] = '';