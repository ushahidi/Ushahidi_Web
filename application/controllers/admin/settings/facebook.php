<?php defined('SYSPATH') or die('No direct script access allowed.');

/**
 * This controller is used to manage Facebook Settings
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Facebook_Controller extends Admin_Controller {

	/**
	 * Handles SMS Settings
	 */
	function index()
	{
		$this->template->content = new View('admin/settings/facebook/main');
		$this->template->content->title = Kohana::lang('ui_admin.settings');

		// setup and initialize form field names
		$form = array
		(
			'facebook_appid' => '',
			'facebook_appsecret' => ''
		);
		//	Copy the form as errors, so the errors will be stored with keys
		//	corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Instantiate Validation, use $post, so we don't overwrite $_POST
			// fields with our own things
			$post = new Validation($_POST);

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order

			$post->add_rules('facebook_appid', 'length[1,150]');
			$post->add_rules('facebook_appsecret', 'length[1,150]');
			
			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid
				Settings_Model::save_setting('facebook_appid', $post->facebook_appid);
				Settings_Model::save_setting('facebook_appsecret', $post->facebook_appsecret);

				// Delete Settings Cache
				$this->cache->delete('settings');
				$this->cache->delete_tag('settings');

				// Everything is A-Okay!
				$form_saved = TRUE;

				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

			}

			// No! We have validation errors, we need to show the form again,
			// with the errors
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('settings'));
				$form_error = TRUE;
			}
		}
		else
		{
			$form = array
			(
				'facebook_appid' => Settings_Model::get_setting('facebook_appid'),
				'facebook_appsecret' => Settings_Model::get_setting('facebook_appsecret')
			);
		}
		
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
	}
	
}
