<?php defined('SYSPATH') or die('No direct script access.');

/**
* REQUIREMENTS CONFIGURATION
*/

/**
 * Do we want requirements to suffix modification time onto file names
 * */
$config['suffix_requirements'] = TRUE;

/**
 * Enable combining of css/javascript files.
 **/
$config['combined_files_enabled'] = FALSE;

/**
 * Using the JSMin library to minify any
 * javascript file passed to {@link combine_files()}.
 **/
$config['combine_js_with_jsmin'] = TRUE;

/**
 * Using the CSSMin library to minify any
 * css file passed to {@link combine_files()}.
 **/
$config['combine_css_with_cssmin'] = TRUE;

/**
 * Enable auto uploading combined css/js to CDN
 **/
$config['cdn_store_combined_files'] = TRUE;

/**
 * Put all javascript includes at the bottom of the template
 * before the closing <body> tag instead of the <head> tag.
 * This means script downloads won't block other HTTP-requests,
 * which can be a performance improvement.
 * @see Requirements_Backend::$write_js_to_body for details
 **/
$config['write_js_to_body'] = FALSE;
