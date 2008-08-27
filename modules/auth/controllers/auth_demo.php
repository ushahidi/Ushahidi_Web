<?php
/**
 * Auth module demo controller for the Auth/ORM driver. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 *
 * $Id: auth_demo.php 3352 2008-08-18 09:43:56BST atomless $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class auth_demo_Controller extends Template_Controller {

	// Do not allow to run in production
	const ALLOW_PRODUCTION = FALSE;

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

	/**
	* Displays the auth install instructions
	*/
	public function index()
	{
		// Display the install page
		$this->template->content =  View::factory('auth/install');
	}


	/**
	* Example for site administor use - not for user facing pages.
	* Largely the same code used in the registration method
	* with the added ability to define the user permissions (roles).
	*/
	public function create()
	{
		$_POST = Validation::factory($_POST)
			   ->pre_filter('trim')
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

				foreach (arr::extract($postdata_array, 'email', 'username', 'password') as $key => $val)
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

	/**
	* Example user registration page
	*/
	public function register()
	{
		$_POST = Validation::factory($_POST)
			   ->pre_filter('trim')
			   ->add_rules('email', 'required', 'length[4,64]', 'email')
			   ->add_rules('username', 'required', 'length[2,32]', 'standard_text')
			   ->add_rules('password', 'required', 'length[5,64]')
			   ->add_rules('password_confirm', 'required', 'matches[password]');


		if ($_POST->validate())
		{
			// Create user and pending user model instances
			$user = ORM::factory('user');
			$p_user = ORM::factory('pending_user');

			// Sanitize $_POST data removing all inptus without rules
			$postdata_array = $_POST->safe_array();

			// Check to ensure that none of the submitted fields
			// matches any of the fields set as unique in the users and pending_users table.
			// Optionally pass a reference to the validation object
			// so error messages can be added to the appropriate form fields
			if ( ! ($user->exists($postdata_array, $_POST) OR
				  $p_user->exists($postdata_array, $_POST)))
			{
				foreach (arr::extract($postdata_array, 'email', 'username', 'password') as $key => $val)
				{
					// Set user data
					$p_user->$key = $val;
				}

				// Set a unique md5 key so the pending user can later be retrieved based on
				// the key in the url of the link sent to the user in the confirmation request email
				$p_user->set_encrypted_key();

				// Save the user to the pending_users table
				if ($p_user->save())
				{
					// Store the pending user key in the session to be retrieved in the
					// request_confirmation method
					Session::instance()->set('pendingkey', $p_user->key);

					url::redirect('auth_demo/request_confirmation');
				}
			}
		}

		// Pass a view object to the template->content
		// Setting the error messages to the ones listed in the i18n directory
		// inside auth.php for $lang['orm']
		$this->template->content = View::factory('auth/register')
								 ->bind('formerrors', $_POST->errors('auth'));

	}

	/**
	 * Send a registration confirmation request email to the pending user
	 */
	public function request_confirmation()
	{
		$key = Session::instance()->get('pendingkey', FALSE);

		if ($key === FALSE)
			url::redirect('auth_demo/register');

		// Find the pending user with the key passed
		$p_user = ORM::factory('pending_user')->where('key', $key)->find();

		// Email message view object
		$message = view::factory('auth/confirmationrequestemail')
				 ->bind('user', $p_user);

		try
		{
			// Send confirmation-request email
			// You'll need to set your email config for this to work!
			$mailsent = email::send
			(
				$p_user->email,
				array('robot@kohanaphp.com', 'Kohana Auth Demo'),
				'Please Confirm Your Registration',
				$message->render(),
				TRUE
			);
		}
		catch (Exception $e)
		{
			$mailsent = FALSE;

			KOHANA::log('failed to send confirmation email', 'error:'.$e);
		}

		$this->template->content = View::factory('auth/confirmationrequest')
								 ->set('mailsent', $mailsent);
	}

   /**
	* Confirm pending_user account - linked from confirmation email
	* - deletes from pending_users table and saves to users table
	*/
	public function confirm_user($key = FALSE)
	{
		if ($key === FALSE)
		{
			url::redirect('auth_demo/user_not_found');
		}

		// Find the pending user with the key passed
		$p_user = ORM::factory('pending_user')->where('key', $key)->find();

		if ($p_user === FALSE)
		{
			url::redirect('auth_demo/user_not_found');
		}
		else
		{
			$p_user->confirm();

			$this->template->content = View::factory('auth/confirmed');
		}
	}

   /**
	* Example user registration page
	* Set up to enable users to login with email + password
	* This can easily be changed to username + password
	*/
	public function login()
	{
		$auth = Auth::instance();

		// If already logged in redirect to user account page
		// Otherwise attempt to auto login if autologin cookie can be found
		// (Set when user previously logged in and ticked 'stay logged in')
		if ($auth->logged_in() OR $auth->auto_login())
		{
			if ($user = Session::instance()->get('auth_user',FALSE))
			{
				url::redirect('auth_demo/user/'.$user->username);
			}
		}

		// Set up the validation object
		$_POST = Validation::factory($_POST)
			   ->pre_filter('trim')
			   ->add_rules('email', 'required', 'length[4,64]', 'email')
			   ->add_rules('password', 'required', 'length[5,64]');

		if ($_POST->validate())
		{
			// Sanitize $_POST data removing all inptus without rules
			$postdata_array = $_POST->safe_array();

			// Load the user
			$user = ORM::factory('user', $postdata_array['email']);

			// If no user with that email address found
			if ( ! $user->id)
			{
				$_POST->add_error('email', 'login error');
			}
			else
			{
				$remember = (isset($_REQUEST['remember']))? TRUE : FALSE;

				// Attempt a login
				if ($auth->login($user, $postdata_array['password'], $remember))
				{
					url::redirect('auth_demo/user/'.$user->username);
				}
				else
				{
					$_POST->add_error('password', 'login error');
				}
			}
		}

		// Pass a view object to the template->content
		// Setting the error messages to the ones listed in the i18n directory
		// inside auth.php for $lang['orm']
		$this->template->content = View::factory('auth/login')
								 ->bind('formerrors', $_POST->errors('auth'));
	}

	/**
	 * Example private user account page
	 *
	 */
	public function user($username = FALSE)
	{
		// Find the user
		$user = ORM::factory('user', $username);

		if ($user->id == 0)
		{
			url::redirect('auth_demo/user_not_found');
		}

		$loggedinuser = Session::instance()->get('auth_user', FALSE);

		// Make sure the user is logged in and that their username matches.
		// You could ommit the username check for a page viewable by all members or
		// omit both the username and the logged_in checks for a public user page.
		if ( ! Auth::instance()->logged_in() OR $loggedinuser->username != $username)
		{
			url::redirect('auth_demo/access_denied');
		}

		$this->template->content = View::factory('auth/user')
								 ->bind('username', $username);

	}

	public function user_not_found()
	{
		// This should really be a page that offers the user a little more advice as to what to do :)
		$this->template->content = 'User not found!';
	}

	public function access_denied()
	{
		// This should really be a page that offers the user a little more advice as to what to do :)
		$this->template->content = 'Sorry you are not authorized to access the requested page.';
	}

	public function logout()
	{
		// Load auth and log out
		Auth::instance()->logout(TRUE);

		// Redirect back to the login page
		url::redirect('auth_demo/login');
	}

} // End Auth Controller