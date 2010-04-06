<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MHI Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Contact Us Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class MHI_Controller extends Template_Controller {

	// MHI template

	public $template = 'layout';

	function __construct()
	{
		parent::__construct();

		// Load Header & Footer

		$this->template->header  = new View('mhi_header');
		$this->template->footer  = new View('mhi_footer');

		$this->template->header->site_name = Kohana::config('settings.site_name');

		// Initialize JS variables. js_files is an array of ex: html::script('media/js/jquery.validate.min');

		$this->template->header->js = '';
		$this->template->header->js_files = array();

		// If we aren't at the top level MHI site or MHI isn't enabled, don't allow access to any of this jazz

		if (Kohana::config('config.enable_mhi') == FALSE OR Kohana::config('settings.subdomain') != '')
			throw new Kohana_User_Exception('MHI Access Error', "MHI disabled for this site.");

		// Login Form variables

		$session = Session::instance();
		$this->template->header->errors = '';
		$this->template->header->form = '';
		$this->template->header->form_error = '';
		$this->template->header->mhi_user_id = $session->get('mhi_user_id');

	}

	public function index()
	{
		$this->template->header->this_body = 'mhi-home';
		$this->template->content = new View('mhi');
		$this->template->header->js = new View('mhi_js');
		$this->template->header->js_files = array(html::script('media/js/mhi/jquery.cycle.min'));

		$session = Session::instance();
		$mhi_user_id = $session->get('mhi_user_id');

		$form = array(
			'username' => '',
			'password' => '',
			);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names

		$errors = $form;
		$form_error = FALSE;

		// Set up the validation object

		$_POST = Validation::factory($_POST)
			->pre_filter('trim')
			->add_rules('username', 'required')
			->add_rules('password', 'required');

		if ($_POST->validate() OR $mhi_user_id != FALSE)
		{
			// Sanitize $_POST data removing all inputs without rules

			$postdata_array = $_POST->safe_array();

			// MHI user not already logged in, so do it

			if ($mhi_user_id == FALSE)
			{
				$mhi_user = new Mhi_User_Model;
				$mhi_user_id = $mhi_user->login($postdata_array['username'],$postdata_array['password']);
			}

			// If success (already logged in or login successful), move on

			if ($mhi_user_id != FALSE)
			{

				url::redirect('mhi/manage');

			}else{

				$_POST->add_error('username', 'Login Error');

				// Repopulate the form fields

				$form = arr::overwrite($form, $_POST->as_array());

				// Populate the error fields, if any
				// We need to already have created an error message file, for Kohana to use
				// Pass the error message file name to the errors() method

				$errors = arr::overwrite($errors, $_POST->errors('auth'));
				$form_error = TRUE;

			}
		}

		$this->template->header->errors = $errors;
		$this->template->header->form = $form;
		$this->template->header->form_error = $form_error;
	}

	public function manage()
	{
		// If not logged in, go back to the start

		$session = Session::instance();
		$mhi_user_id = $session->get('mhi_user_id');

		if ($mhi_user_id == FALSE)
		{
			// If the user is not logged in, go home.
			url::redirect('/');
		}

		$this->template->header->this_body = '';
		$this->template->content = new View('mhi_manage');

		$this->template->content->domain_name = $_SERVER['HTTP_HOST'].Kohana::config('config.site_domain');

		$mhi_site = new Mhi_Site_Model;
		$this->template->content->sites = $mhi_site->get_user_sites($mhi_user_id);
	}

	public function about()
	{
		$this->template->header->this_body = 'mhi-about';
		$this->template->content = new View('mhi_about');
		$this->template->header->js = new View('mhi_about_js');
	}

	public function account()
	{
		// If not logged in, go back to the start

		$session = Session::instance();
		$mhi_user_id = $session->get('mhi_user_id');
		if ($mhi_user_id == FALSE)
		{
			// If the user is not logged in, go home.
			url::redirect('/');
		}

		$this->template->header->this_body = '';
		$this->template->content = new View('mhi_account');
		$this->template->header->js = new View('mhi_account_js');

		$mhi_user = new Mhi_User_Model;

		// Get user info

		$this->template->content->user = $mhi_user->get($mhi_user_id);

		$form = array(
			'username' => '',
			'password' => '',
			);

		$form_error = FALSE;
		$errors = FALSE;

		// Set up the validation object

		$_POST = Validation::factory($_POST)
			->pre_filter('trim')
			->add_rules('firstname', 'required')
			->add_rules('lastname', 'required')
			->add_rules('email', 'required')
			->add_rules('password', 'required');

		if ($_POST->validate())
		{
			$mhi_user = new Mhi_User_Model;

			$postdata_array = $_POST->safe_array();

			$update = $mhi_user->update($mhi_user_id,array(
				'firstname'=>$postdata_array['firstname'],
				'lastname'=>$postdata_array['lastname'],
				'email'=>$postdata_array['email'],
				'password'=>$postdata_array['password']
			));

			// If update worked, go back to manage page

			if ($update != FALSE)
			{
				url::redirect('mhi/manage');
			}else{
				$errors = array('Something went wrong with form submission. Please try again.');
				$form_error = TRUE;
			}
		}
		$this->template->header->form_error = $form_error;
		$this->template->header->errors = $errors;

	}

	public function logout()
	{
		$mhi_user = new Mhi_User_Model;
		$mhi_user->logout();
		url::redirect('/');
	}

	public function signup()
	{
		$this->template->header->this_body = '';
		$this->template->content = new View('mhi_signup');
		$this->template->header->js = new View('mhi_signup_js');
		$this->template->header->js_files = array(html::script('media/js/mhi/initialize', true));

		$this->template->content->site_name = Kohana::config('settings.site_name');
		$this->template->content->domain_name = $_SERVER['HTTP_HOST'].Kohana::config('config.site_domain');

		$session = Session::instance();
		$this->template->content->logged_in = $session->get('mhi_user_id');
	}

	public function create()
	{
		$this->template->header->this_body = '';
		$this->template->content = new View('mhi_create');

		// Process Form

		if ($_POST)
		{
			$post = Validation::factory($_POST);

			// Trim whitespaces

			$post->pre_filter('trim');

			$session = Session::instance();
			$mhi_user_id = $session->get('mhi_user_id');

			// These rules are only required if we aren't already logged in

			if ($mhi_user_id == FALSE)
			{
				$post->add_rules('signup_first_name','required','alpha_dash');
				$post->add_rules('signup_last_name','required','alpha_dash');
				$post->add_rules('signup_email', 'required','email');
				$post->add_rules('signup_password','required');
			}else{
				$post->add_rules('verify_password','required');
			}

			$post->add_rules('signup_subdomain','required','alpha_dash');
			$post->add_rules('signup_instance_name','required');
			$post->add_rules('signup_instance_tagline','required');

			if ($post->validate())
			{

				$mhi_user = new Mhi_User_Model;
				$db_genesis = new DBGenesis;
				$mhi_site_database = new Mhi_Site_Database_Model;
				$mhi_site = new Mhi_Site_Model;

				// Check passwords if logged in and create user if not

				if ($mhi_user_id != FALSE)
				{

					// Get user info

					$user = $mhi_user->get($mhi_user_id);

					$salt = Kohana::config('auth.salt_pattern');
					$verify_password = sha1($post->verify_password.$salt);

					if ($verify_password != $user->password)
						throw new Kohana_User_Exception('Password Match Error', "Passwords do not match. Dev TODO: Come back later and clean up validation!");

					$user_id = $mhi_user_id;
					$email = $user->email;
					$name = $user->firstname.' '.$user->lastname;
					$password = $post->verify_password;

				}else{

					// Save new user

					$user_id = $mhi_user->save_user(array(
						'firstname'=>$post->signup_first_name,
						'lastname'=>$post->signup_last_name,
						'email'=>$post->signup_email,
						'password'=>$post->signup_password
					));

					$email = $post->signup_email;
					$name = $post->signup_first_name.' '.$post->signup_last_name;
					$password = $post->signup_password;
				}

				// Set up DB and Site

				$base_db = $db_genesis->current_db();

				$new_db_name = $base_db.'_'.$post->signup_subdomain;

				// Do some not so graceful validation

				if ($mhi_site_database->db_assigned($new_db_name) OR $db_genesis->db_exists($new_db_name))
					throw new Kohana_User_Exception('MHI Site Setup Error', "Database already exists and/or is already assigned in the MHI DB.");

				if ($mhi_site->domain_exists($post->signup_subdomain))
					throw new Kohana_User_Exception('MHI Site Setup Error', "Domain already assigned in MHI DB.");

				// Create site

				$site_id = $mhi_site->save_site(array(
					'user_id'=>$user_id,
					'site_domain'=>$post->signup_subdomain,
					'site_privacy'=>1,	// TODO: 1 is the hardcoded default for now. Needs to be changed?
					'site_active'=>1	// TODO: 1 is the default. This needs to be a config item since this essentially "auto-approves" sites
				));

				// Set up database and save details to MHI DB

				$db_genesis->create_db($new_db_name);
				$mhi_site_database->assign_db($new_db_name,$site_id);
				$db_genesis->populate_db($new_db_name,
					array(
						'username'=>$email,
						'name'=>$name,
						'password'=>$password,
						'email'=>$email),
					array(
						'site_name'=>$post->signup_instance_name,
						'site_tagline'=>$post->signup_instance_tagline));

			}else{
				throw new Kohana_User_Exception('Validation Error', "Form not validating. Dev TODO: Come back later and clean up validation!");
			}

		}else{
			// If the form was never posted, we need to complain about it.

			throw new Kohana_User_Exception('Incomplete Form', "Form not posted.");
		}
	}
}