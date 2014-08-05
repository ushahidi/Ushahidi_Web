<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Addon Manager
 * Install new Plugins & Themes
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Themes_Controller extends Admin_Controller {

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'addons';

		// If this is not a super-user account, redirect to dashboard
		if (!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
		{
			url::redirect('admin/dashboard');
		}
	}

	function index()
	{
		$this->template->content = new View('admin/addons/themes');
		$this->template->content->title = 'Addons';

		// setup and initialize form field names
		$form = array('site_style' => '');
		//  Copy the form as errors, so the errors will be stored with keys
		//  corresponding to the form field names
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

			$post->add_rules('site_style', 'length[1,50]');

			// Test to see if things passed the rule checks
			if ($post->validate())
			{
				// Yes! everything is valid
				Settings_Model::save_setting('site_style', $post->site_style);

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
			$site_style = Settings_Model::get_setting('site_style');
			// Retrieve Current Settings
			$form = array('site_style' => (! empty($site_style)) ? $site_style : 'default');
		}

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$themes = addon::get_addons('theme');	
		$this->template->content->themes = $themes;
		//delete cache to make sure theme is reloaded after change
  		$this->cache->delete_all();
	}

}
