<?php defined('SYSPATH') or die('No direct script access');
/**
 * A helper class for generating CSRF-enabled forms - forms with
 * a hidden field that contains a randomly generated token that
 * is used for CSRF protection
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://github.com/ushahidi/Ushahidi_Web
 * @category   Helpers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class form extends form_Core {

	/**
	 * Generates an opening HTML form tag.
	 *
	 * @param   string  form action attribute
	 * @param   array   extra attributes
	 * @param   array   hidden fields to be created immediately after the form tag
	 * @return  string
	 */
	public static function open($action = NULL, $attr = array(), $hidden = NULL)
	{
		// Make sure that the method is always set
		empty($attr['method']) and $attr['method'] = 'post';

		if ($attr['method'] !== 'post' AND $attr['method'] !== 'get')
		{
			// If the method is invalid, use post
			$attr['method'] = 'post';
		}

		if ($action === NULL)
		{
			// Use the current URL as the default action
			$action = url::site(Router::$complete_uri);
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the action URI into a URL
			$action = url::site($action);
		}

		// Set action
		$attr['action'] = $action;

		// Only show the CSRF field when form method is POST
		$hidden_field = ($attr['method'] === 'post')
		    ? form::hidden('form_auth_token', csrf::token())."\n"
		    : '';

		// Form opening tag
		$form = '<form'.form::attributes($attr).'>'."\n"
		    . $hidden_field;

		// Add hidden fields immediate after opening tag
		empty($hidden) or $form .= form::hidden($hidden);

		return $form;
	}
}
?>