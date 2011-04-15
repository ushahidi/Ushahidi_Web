<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Pagination
 *
 * Pagination configuration is defined in groups which allows you to easily switch
 * between different pagination settings for different website sections.
 * Note: all groups inherit and overwrite the default group.
 *
 * Group Options:
 *  directory      - Views folder in which your pagination style templates reside
 *  style          - Pagination style template (matches view filename)
 *  uri_segment    - URI segment (int or 'label') in which the current page number can be found
 *  query_string   - Alternative to uri_segment: query string key that contains the page number
 *  items_per_page - Number of items to display per page
 *  auto_hide      - Automatically hides pagination for single pages
 */
$config['default'] = array
(
	'directory'      => 'pagination',
	'style'          => 'classic',
	'uri_segment'    => 3,
	'query_string'   => '',
	'items_per_page' => 20,
	'auto_hide'      => FALSE,
);
