<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MHI en_US locale file
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     MHI Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

$lang = array
(
    'contact_email' => array
	(
	    'required'    => 'Please provide a valid email address',

		'email'     => 'The Email field does not appear to contain a valid email address?',

	),

    'contact_subject' => array
	(
		'required'	=> 'The subject field is required.',
		'length'    => 'The subject field must be at least 3 characters long.'
	),
	
	'contact_message' => array
	(
		'required'  => 'The message field is required.'
	),

    'contact_captcha' => array
    (
        'required'  => 'Please enter the security code.',
        'default'   => 'Please enter a valid security code.'
    )

);
