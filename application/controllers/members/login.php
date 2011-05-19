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
 * @module	   OpenID Login Controller	 
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
	
	public function index()
	{
		$openid_error = false;
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
		
		try {
			$openid = new OpenID;
			
			// Retrieve the Name (if available) and Email
			$openid->required = array("namePerson", "contact/email");
		
			if(!$openid->mode)
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
		
		$this->template->openid_error = $openid_error;
	}
}