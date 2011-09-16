<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles login requests.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   Ushahidi - http://source.ushahididev.com
 * @subpackage Members
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Login_Controller extends Template_Controller {
	
	public $auto_render = TRUE;
	
	protected $user;
	
	// Session Object
	protected $session;
	
	// Main template
	public $template = 'members/login';
	

	public function __construct()
	{
		parent::__construct();
		
		$this->session = new Session();
		// $profiler = new Profiler;
	}
	
	public function index($user_id = 0)
	{
		$auth = Auth::instance();
		
		// If already logged in redirect to user account page
		// Otherwise attempt to auto login if autologin cookie can be found
		// (Set when user previously logged in and ticked 'stay logged in')
		if ($auth->logged_in() OR $auth->auto_login())
		{
			if ($user = Session::instance()->get('auth_user',FALSE) AND
				$auth->logged_in('member') )
			{
				url::redirect('members/dashboard');
			}
		}

		// setup and initialize form field names
		$form = array
		(
			'action'	=> '',
			'username'	=> '',
			'password'	=> '',
			'password_again'  => '',
			'name'		=> '',
			'email'		=> '',
			'resetemail' => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$openid_error = FALSE;
		$success = FALSE;
		$action = (isset($_POST["action"])) ? $_POST["action"] : "";
		
		// Is this a password reset request?
		if (isset($_GET["reset"]))
		{
			$this->_new_password($user_id, $this->uri->segment(5));
			$success = TRUE;
		}
		
		// Regular Form Post for Signin
		// check, has the form been submitted, if so, setup validation
		if ($_POST AND isset($_POST["action"])
			AND $_POST["action"] == "signin")
		{
			$post = Validation::factory($_POST);
			$post->pre_filter('trim');
			$post->add_rules('username', 'required');
			$post->add_rules('password', 'required');
			
			if ($post->validate())
			{
				// Sanitize $_POST data removing all inputs without rules
				$postdata_array = $post->safe_array();

				// Load the user
				$user = ORM::factory('user', $postdata_array['username']);

				// If no user with that username found
				if ( ! $user->id)
				{
					$post->add_error('username', 'login error');
				}
				else
				{
					$remember = (isset($post->remember))? TRUE : FALSE;

					// Attempt a login
					if ($auth->login($user, $postdata_array['password'], $remember))
					{
						// Exists Redirect to Dashboard
						url::redirect("members/dashboard");
					}
					else
					{
						$post->add_error('password', 'login error');
					}
				}
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				// We need to already have created an error message file, for Kohana to use
				// Pass the error message file name to the errors() method			
				$errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
			else
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				// We need to already have created an error message file, for Kohana to use
				// Pass the error message file name to the errors() method			
				$errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
		}
		elseif ($_POST AND isset($_POST["action"])
			AND $_POST["action"] == "new")
		{
			$post = Validation::factory($_POST);

			//	Add some filters
			$post->pre_filter('trim', TRUE);
	
			$post->add_rules('username','required','length[3,16]', 'alpha_numeric');
			$post->add_rules('password','required', 'length[5,30]','alpha_numeric');
			$post->add_rules('name','required','length[3,100]');
			$post->add_rules('email','required','email','length[4,64]');
			$post->add_callbacks('username', array($this,'username_exists_chk'));
			$post->add_callbacks('email', array($this,'email_exists_chk'));

			// If Password field is not blank
			if (!empty($post->password))
			{
				$post->add_rules('password','required','length[5,16]'
					,'alpha_numeric','matches[password_again]');
			}
			
			if ($post->validate())
			{
				$user = ORM::factory('user');
				$user->name = $post->name;
				$user->email = $post->email;
				$user->username = $post->username;
				$user->password = $post->password;
				
				// Add New Roles
				$user->add(ORM::factory('role', 'login'));
				$user->add(ORM::factory('role', 'member'));
				$user->save();
				
				// Send Confirmation email
				$this->_send_email_confirmation($user);
				
				$success = TRUE;
				$action = "";
			}
			else 
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
		}
		elseif ($_POST AND isset($_POST["action"])
			AND $_POST["action"] == "forgot")
		{
			$post = Validation::factory($_POST);

			//	Add some filters
			$post->pre_filter('trim', TRUE);
			$post->add_callbacks('resetemail', array($this,'email_exists_chk'));
			
			if ($post->validate())
			{
				$user = ORM::factory('user',$post->resetemail);
				
				// Existing User??
				if ($user->loaded==true)
				{
					// Secret consists of email and the last_login field.
					// So as soon as the user logs in again, 
					// the reset link expires automatically.
					$secret = $auth->hash_password($user->email.$user->last_login);
					$secret_link = url::site('members/login/index/'.$user->id.'/'.$secret."?reset");
					
					$details_sent = $this->_email_resetlink($post->resetemail,$user->name,$secret_link);
					if( $details_sent )
					{
						$password_reset = TRUE;
					}
					
					$success = TRUE;
					$action = "";	
				}
			}
			else 
			{
				// repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
		}
		
		// OpenID Post
		try {
			$openid = new OpenID;
			
			// Retrieve the Name (if available) and Email
			$openid->required = array("namePerson", "contact/email");
		
			if( ! $openid->mode)
			{
				if(isset($_POST["openid_identifier"]))
				{
					$openid->identity = $_POST["openid_identifier"];
					header("Location: " . $openid->authUrl());
				}
			}
			elseif($openid->mode == "cancel")
			{
				$openid_error = "You have canceled authentication!";
			}
			else
			{
				if ($openid->validate())
				{
					// Does User Exist?
					$openid_user = ORM::factory("openid")
						->where("openid", $openid->identity)
						->find();
						
					if ($openid_user->loaded AND $openid_user->user)
					{
						// First log all other sessions out
						$auth->logout();
						
						// Initiate Ushahidi side login + AutoLogin
						$auth->force_login($openid_user->user->username);
						
						// Exists Redirect to Dashboard
						url::redirect("members/dashboard");
					}
					else
					{	
						// Does this openid have the required email??
						$new_openid = $openid->getAttributes();
						if ( ! isset($new_openid["contact/email"]) OR 
							empty($new_openid["contact/email"]))
						{
							$openid_error = $openid->identity . " has not been logged in. No Email Address Found.";
						}
						else
						{
							// Create new User and save OpenID
							$user = ORM::factory("user");
							
							// But first... does this email address already exist
							// in the system?
							if ($user->email_exists($new_openid["contact/email"]))
							{
								$openid_error = $new_openid["contact/email"] . " is already registered in our system.";
							}
							else
							{
								$username = "user".time(); // Random User Name from TimeStamp - can be changed later
								$password = text::random("alnum", 16); // Create Random Strong Password
								
								// Name Available?
								$user->name = (isset($new_openid["namePerson"]) AND ! empty($new_openid["namePerson"]))
									? $new_openid["namePerson"]
									: $username;
								$user->username = $username;
								$user->password = $password;
								$user->email = $new_openid["contact/email"];

								// Add New Roles
								$user->add(ORM::factory('role', 'login'));
								$user->add(ORM::factory('role', 'member'));

								$user->save();

								// Save OpenID and Association
								$openid_user->user_id = $user->id;
								$openid_user->openid = $openid->identity;
								$openid_user->openid_email = $new_openid["contact/email"];
								$openid_user->openid_server = $openid->server;
								$openid_user->openid_date = date("Y-m-d H:i:s");
								$openid_user->save();
								
								// Initiate Ushahidi side login + AutoLogin
								$auth->login($username, $password, TRUE);
								
								// Redirect to Dashboard
								url::redirect("members/dashboard");
							}
						}
					}
				}
				else
				{
					$openid_error = $openid->identity . "has not been logged in.";
				}
			}
		}
		catch(ErrorException $e)
		{
			$openid_error = $e->getMessage();
		}
		
		$this->template->errors = $errors;
		$this->template->success = $success;
		$this->template->form = $form;
		$this->template->form_error = $form_error;
		$this->template->openid_error = $openid_error;
		
		$this->template->site_name = Kohana::config('settings.site_name');
		$this->template->site_tagline = Kohana::config('settings.site_tagline');
		
		// Javascript Header
		$this->template->js = new View('members/login_js');
		$this->template->js->action = $action;
	}
	
	/**
	 * Confirms user registration
	 */
	public function verify()
	{
		$auth = Auth::instance();
		
		$code = (isset($_GET['c']) AND ! empty($_GET['c'])) ? $_GET['c'] : "";
		$email = (isset($_GET['e']) AND ! empty($_GET['e'])) ? $_GET['e'] : "";
		
		$user = ORM::factory("user")
			->where("code", $code)
			->where("email", $email)
			->where("confirmed != 1")
			->find();
			
		if ($user->loaded)
		{
			$user->confirmed = 1;
			$user->save();
			
			// First log all other sessions out
			$auth->logout();
			
			// Initiate Ushahidi side login + AutoLogin
			$auth->force_login($user->username);
			
			// Redirect to Dashboard
			url::redirect("members/dashboard");
		}
		else
		{
			// Redirect to Dashboard
			url::redirect("members/login");
		}
	}
	
	/**
	 * Facebook connect function
	 */
	public function facebook()
	{
		$auth = Auth::instance();
		
		$this->template = "";
		$this->auto_render = FALSE;
		
		$settings = ORM::factory("settings")->find(1);
		
		$appid = $settings->facebook_appid;
		$appsecret = $settings->facebook_appsecret;
		$next_url = url::site()."members/login/facebook";
		$cancel_url = url::site()."members/login";
		
		// Create our Application instance.
		$facebook = new Facebook(array(
			'appId'  => $appid,
			'secret' => $appsecret,
			'cookie' => true
		));
		
		// Get User ID
		$fb_user = $facebook->getUser();
		if ($fb_user)
		{
			try
			{
		    	// Proceed knowing you have a logged in user who's authenticated.
				$new_openid = $facebook->api('/me');
				
				// Does User Exist?
				$openid_user = ORM::factory("openid")
					->where("openid", "facebook_".$new_openid["id"])
					->find();
					
				if ($openid_user->loaded AND $openid_user->user)
				{
					// First log all other sessions out
					$auth->logout();
					
					// Initiate Ushahidi side login + AutoLogin
					$auth->force_login($openid_user->user->username);
					
					// Exists Redirect to Dashboard
					url::redirect("members/dashboard");
				}
				else
				{
					// Does this login have the required email??
					if ( ! isset($new_openid["email"]) OR 
						empty($new_openid["email"]))
					{
						$openid_error = "User has not been logged in. No Email Address Found.";

						// Redirect back to login
						url::redirect("members/login");
					}
					else
					{
						// Create new User and save OpenID
						$user = ORM::factory("user");

						// But first... does this email address already exist
						// in the system?
						if ($user->email_exists($new_openid["email"]))
						{
							$openid_error = $new_openid["email"] . " is already registered in our system.";

							// Redirect back to login
							url::redirect("members/login");
						}
						else
						{
							$username = "user".time(); // Random User Name from TimeStamp - can be changed later
							$password = text::random("alnum", 16); // Create Random Strong Password

							// Name Available?
							$user->name = (isset($new_openid["name"]) AND ! empty($new_openid["name"]))
								? $new_openid["name"]
								: $username;
							$user->username = $username;
							$user->password = $password;
							$user->email = $new_openid["email"];

							// Add New Roles
							$user->add(ORM::factory('role', 'login'));
							$user->add(ORM::factory('role', 'member'));

							$user->save();

							// Save OpenID and Association
							$openid_user->user_id = $user->id;
							$openid_user->openid = "facebook_".$new_openid["id"];
							$openid_user->openid_email = $new_openid["email"];
							$openid_user->openid_server = "http://www.facebook.com";
							$openid_user->openid_date = date("Y-m-d H:i:s");
							$openid_user->save();

							// Initiate Ushahidi side login + AutoLogin
							$auth->login($username, $password, TRUE);

							// Redirect to Dashboard
							url::redirect("members/dashboard");
						}
					}
				}
			}
			catch (FacebookApiException $e)
			{
				error_log($e);
				$user = null;
			}
		}
		else
		{
			$login_url = $facebook->getLoginUrl(
				array(
					'canvas' => 1,
					'fbconnect' => 0,
					'scope' => "email,publish_stream",
					'next' => $next_url,
					'cancel' => $cancel_url
				)
			);

			url::redirect($login_url);
		}
	}
	
	/**
	 * Checks if username already exists.
	 * @param Validation $post $_POST variable with validation rules 
	 */
	public function username_exists_chk(Validation $post)
	{
		$users = ORM::factory('user');
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('username', $post->errors()))
			return;

		if ($users->username_exists($post->username))
			$post->add_error( 'username', 'exists');
	}	
	
	/**
	 * Checks if email address is associated with an account.
	 * @param Validation $post $_POST variable with validation rules 
	 */
	public function email_exists_chk( Validation $post )
	{
		$users = ORM::factory('user');
		if ($post->action == "new")
		{
			if (array_key_exists('email',$post->errors()))
				return;

			if ($users->email_exists( $post->email ) )
				$post->add_error('email','exists');
		}
		elseif($post->action == "forgot")
		{
			if (array_key_exists('resetemail',$post->errors()))
				return;

			if ( ! $users->email_exists( $post->resetemail ) )
				$post->add_error('resetemail','invalid');
		}
	}
	
    /**
     * Create New password upon user request.
     */
    private function _new_password($user_id = 0, $secret)
    {
    	$auth = Auth::instance();
		$user = ORM::factory('user',$user_id);
		if ($user->loaded == true && 
			$auth->hash_password($user->email.$user->last_login, $auth->find_salt($secret)) == $secret)
		{ // Email New Password
			$new_password = $this->_generate_password();
			$user->password = $new_password;
			$user->save();
			
			$this->_email_newpassword($user->email, $user->name, $user->username, $new_password);
		}	
	}	
	
	/**
	 * Sends an email confirmation
	 */
	private function _send_email_confirmation($user)
	{
		$email = $user->email;
		$code = text::random('alnum', 20);
		$user->code = $code;
		$user->save();
		
		$url = url::site()."members/login/verify/?c=$code&e=$email";
		
		$settings = kohana::config('settings');

		$to = $email;
		$from = array($settings['site_email'], $settings['site_name']);
		$subject = $settings['site_name']." "
			.Kohana::lang('ui_main.login_signup_confirmation_subject');
		$message = Kohana::lang('ui_main.login_signup_confirmation_message',
			array($settings['site_name'], $url));
		
		email::send($to, $from, $subject, $message, FALSE);
	}
	
	/**
	 * Generate random password for the user.
	 *
 	 * @return the new password
	 */
	private function _generate_password()
	{
		$password_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$chars_length = strlen( $password_chars ) - 1;
		$password = NULL;
		for( $i = 0; $i < 8; $i++ )
		{
			$position = mt_rand(0,$chars_length);
			$password .= $password_chars[$position];
		}
		return $password;
	}	
	
	/**
	 * Email reset link to the user.
	 * 
	 * @param the email address of the user requesting a password reset.
	 * @param the username of the user requesting a password reset.
	 * @param the new generated password.
	 * 
	 * @return void.
	 */
	private function _email_resetlink( $email, $name, $secret_url )
	{
		$to = $email;
		$from = Kohana::lang('ui_admin.password_reset_from');
		$subject = Kohana::lang('ui_admin.password_reset_subject');
		$message = Kohana::lang('ui_admin.password_reset_message_line_1').' '.$name.",\n";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_2').' '.$name.". ";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_3')."\n\n";
		$message .= $secret_url."\n\n";
		
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}
	
	private function _email_newpassword( $email, $name, $username, $password )
	{
		$to = $email;
		$from = Kohana::lang('ui_admin.password_reset_from');
		$subject = Kohana::lang('ui_admin.password_reset_subject');
		
		$message = Kohana::lang('ui_admin.password_reset_message_line_1').' '.$name.",\n";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_4').":\n\n";
		$message .= Kohana::lang('ui_admin.label_username').": ".$username."\n";
		$message .= Kohana::lang('ui_admin.password').": ".$password;
		
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}	
}
