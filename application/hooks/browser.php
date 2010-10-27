<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Browser Checker Hook
 * Determines the capabilities of the users browser
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Browser Hoook
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

// Detect Gzip
$gz = "";
if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
{
	$gz  = strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false ? 'gz.' : '';
}
Kohana::config_set('settings.gz', $gz);