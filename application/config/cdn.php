<?php defined('SYSPATH') or die('No direct script access.');

/**
* CDN CONFIGURATION
*/

/**
 * Content Distribution Network (CDN) Configuration
 * Use a content distribution network to serve up
 * static CSS, JS, and IMG files
 */

$config['cdn_css'] = "";
$config['cdn_js'] = "";
$config['cdn_img'] = "";

/**
 * Dynamic content CDN details
 *
 * Are you turning on dynamic uploaded file CDN support on
 * an active deployment? You may need to set 'cdn_gradual_upgrade'
 * to true so your server will upload a file at a random interval.
 * You may also choose to write your own script to do everything
 * at once but that could lead to significant downtime.
 */

// Set to true to store dynamic uploaded files on a CDN
$config['cdn_store_dynamic_content'] = false;

// Currently the only option here is "cloudfiles" from Rackspace
$config['cdn_provider'] = "cloudfiles";

// Details for your CDN account
$config['cdn_username'] = "";
$config['cdn_api_key'] = "";
$config['cdn_container'] = "";

// An integer value that corresponds to the number of visits before
//   a file gets passed on to the CDN if there are files that are left
//   to be uploaded to the CDN. This is important to turn off once the
//   the files are finished uploading to the CDN. For example, setting
//   this to 1 will upload a file every time someone visits the site
//   and setting it to 2 will be every other visitor and setting it to
//   10 will be one in every 10 visitors. Your deployment will continue
//   to operate normally with new files being pushed to the CDN and the
//   old ones updating over time with this method.
//
//   Set to false if you don't want to do this or if all of your files
//   have been moved to the CDN.
//
//   WARNING: Setting this means files will be deleted as they are
//   uploaded to the CDN. Please be smart and backup your deployment.
//
$config['cdn_gradual_upgrade'] = false;

// Avoid XSS problems with jwysiwyg in CDN environments.
$config['cdn_ignore_jwysiwyg'] = true;

?>
