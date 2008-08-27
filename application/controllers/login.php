<?php

/**
* Login Form
*/
class Login_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	
	// Session Object
	protected $session;
	
	// Main template
	public $template = 'admin/login';
	

	public function __construct()
	{
		parent::__construct();
		
		$this->session = new Session();
		
		if (KOHANA::config('auth.driver') != 'ORM')
		{
			throw new Kohana_User_Exception('Auth Demo (ORM)', 'Config Error : modules/auth/config driver set to - '.KOHANA::config('auth.driver'));
		}		
				
		$this->profiler = new Profiler;
	}
	
	public function index()
	{
		$auth = Auth::instance();
		
		// If already logged in redirect to user account page
		// Otherwise attempt to auto login if autologin cookie can be found
		// (Set when user previously logged in and ticked 'stay logged in')
		if ($auth->logged_in() OR $auth->auto_login())
		{
			if ($user = Session::instance()->get('auth_user',FALSE))
			{
				// url::redirect('admin/dashboard');
			}
		}
				
		
	    $form = array
	    (
	        'username'  => '',
	        'password'  => '',
	    );

	    //  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		
		
		// Set up the validation object
		$_POST = Validation::factory($_POST)
			   ->pre_filter('trim')
			   ->add_rules('username', 'required')
			   ->add_rules('password', 'required');
		
		if ($_POST->validate())
		{
			// Sanitize $_POST data removing all inptus without rules
			$postdata_array = $_POST->safe_array();

			// Load the user
			$user = ORM::factory('user', $postdata_array['username']);

			// If no user with that username found
			if ( ! $user->id)
			{
				$_POST->add_error('username', 'login error');
			}
			else
			{
				$remember = (isset($_REQUEST['remember']))? TRUE : FALSE;

				// Attempt a login
				if ($auth->login($user, $postdata_array['password'], $remember))
				{
					url::redirect('admin/dashboard');
				}
				else
				{
					$_POST->add_error('password', 'login error');
				}
			}
			// repopulate the form fields
			$form = arr::overwrite($form, $_POST->as_array());
			
			// populate the error fields, if any
				// We need to already have created an error message file, for Kohana to use
				// Pass the error message file name to the errors() method			
			$errors = arr::overwrite($errors, $_POST->errors('auth'));
		}
		
		$this->template->errors = $errors;
		$this->template->form = $form;
		// $this->template->form_extra = $form_extra;
	}	
}