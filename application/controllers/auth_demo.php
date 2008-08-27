<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Auth module demo controller. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 *
 * $Id: auth_demo.php 3267 2008-08-06 03:44:02Z Shadowhand $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Auth_Demo_Controller extends Template_Controller {

	// Do not allow to run in production
	const ALLOW_PRODUCTION = TRUE;

	public function __construct()
	{
		parent::__construct();

		if (KOHANA::config('auth.driver') != 'ORM')
		{
			throw new Kohana_User_Exception('Auth Demo (ORM)', 'Config Error : modules/auth/config driver set to - '.KOHANA::config('auth.driver'));
		}

		$this->profiler = new Profiler;

		$this->template->title = 'Kohana Auth (ORM driver) Demo';
	}

	public function index()
	{
		// Display the install page
		$this->template->title   = 'Auth Module Installation';
		$this->template->content = View::factory('auth/install');
	}

	public function create()
	{
		$_POST = Validation::factory($_POST)
			   ->pre_filter('trim')
			   ->add_rules('name',	'required', 'length[3,100]', 'standard_text')
			   ->add_rules('email',	'required', 'length[4,64]', 'email')
			   ->add_rules('username', 'required', 'length[2,16]', 'standard_text')
			   ->add_rules('password', 'required', 'length[5,16]')
			   ->add_rules('roles',	'required');

		if ($_POST->validate())
		{

			// Create user model instance
			$user = ORM::factory('user');

			// Sanitize $_POST data removing all inptus without rules
			$postdata_array = $_POST->safe_array();

			// Check to ensure that none of the submitted fields
			// matches any of the fields set as unique in the db 'users' table.
			// Optionally pass a reference to the validation object
			// so error messages can be added to the appropriate form fields
			if ( ! $user->exists($postdata_array, $_POST))
			{

				foreach (arr::extract($postdata_array, 'name', 'email', 'username', 'password') as $key => $val)
				{
					// Set user data
					$user->$key = $val;
				}

				// Save the user
				if ($user->save())
				{
					// Add roles
					foreach ($postdata_array['roles'] as $rolename)
					{
						$role = ORM::factory('role', $rolename);

						$user->add($role);
					}

					Auth::instance()->login($user, $user->password);

					// Redirect to the login page
					url::redirect('auth_demo/login');
				}
			}
		}

		// Populate $roleoptions for the form checklist
		$roleoptions = ORM::factory('role')->find_all();

		// Pass a view object to the template->content
		// Setting the error messages to the ones listed in the i18n directory
		// inside auth.php $lang
		$this->template->content = View::factory('auth/create')
								 ->bind('roleoptions', $roleoptions)
								 ->bind('formerrors', $_POST->errors('auth'));
	}

	public function login()
	{
		if (Auth::instance()->logged_in())
		{
			$this->template->title = 'User Logout';

			$form = new Forge('auth_demo/logout');
			$form->submit('Logout Now');
		}
		else
		{
			$this->template->title = 'User Login';

			$form = new Forge;
			$form->input('username')->label(TRUE)->rules('required|length[4,32]');
			$form->password('password')->label(TRUE)->rules('required|length[5,40]');
			$form->submit('Attempt Login');

			if ($form->validate())
			{
				// Load the user
				$user = ORM::factory('user', $form->username->value);

				if (Auth::instance()->login($user, $form->password->value))
				{
					// Login successful, redirect
					url::redirect('auth_demo/login');
				}
				else
				{
					$form->password->add_error('login_failed', 'Invalid username or password.');
				}
			}
		}

		// Display the form
		$this->template->content = $form->render();
	}

	public function logout()
	{
		// Force a complete logout
		Auth::instance()->logout(TRUE);

		// Redirect back to the login page
		url::redirect('auth_demo/login');
	}

} // End Auth Controller