<?php defined('SYSPATH') or die('No direct script access.');

/**
* REQUIREMENTS CONFIGURATION
*/

/**
 * Do we want requirements to suffix onto the requirement link
 * tags for caching or is it disabled.
 * */
$config['suffix_requirements'] = TRUE;

/**
 * Enable combining of css/javascript files.
 **/
$config['combined_files_enabled'] = TRUE;

/**
 * Put all javascript includes at the bottom of the template
 * before the closing <body> tag instead of the <head> tag.
 * This means script downloads won't block other HTTP-requests,
 * which can be a performance improvement.
 * @see Requirements_Backend::$write_js_to_body for details
 **/
$config['write_js_to_body'] = FALSE;

/**
 * Using the JSMin library to minify any
 * javascript file passed to {@link combine_files()}.
 **/
$config['combine_js_with_jsmin'] = TRUE;