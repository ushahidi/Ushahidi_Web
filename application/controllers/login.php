<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles login requests.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Login_Controller extends Template_Controller {
	
    public $auto_render = TRUE;
	
    protected $user;
	
    // Session Object
    protected $session;
	
    // Main template
    public $template = 'login';
    
    protected $destination = 'admin/';
	

    public function __construct()
    {
        parent::__construct();
        $this->session = new Session();
		// $profiler = new Profiler;
    }
	
    public function index()
    {
        $auth = Auth::instance();

        // If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE) AND
				! $auth->logged_in('member') )
            {
                url::redirect($this->destination);
            }
        }
				
		
        $form = array(
	        'username' => '',
	        'password' => '',
                );

        //  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
		
		
        // Set up the validation object
        $_POST = Validation::factory($_POST)
            ->pre_filter('trim')
            ->add_rules('username', 'required')
            ->add_rules('password', 'required')
            ->add_rules('redirect_to', 'url');
		
        if ($_POST->validate())
        {
            // Sanitize $_POST data removing all inputs without rules
            $postdata_array = $_POST->safe_array();
            
            // Change redirect location if set in form
            if(isset($postdata_array['redirect_to']) AND $postdata_array['redirect_to'] != ''){
        		$this->destination = $postdata_array['redirect_to'];
            }

            // Load the user
            $user = ORM::factory('user')
            		->orwhere(array('username'=>$postdata_array['username'],'email'=>$postdata_array['username']))
            		->find();

            // If no user with that username found
            if (! $user->id)
            {
                $_POST->add_error('username', 'login error');
            }
            else
            {
                $remember = (isset($_POST['remember']))? TRUE : FALSE;

                // Attempt a login
                if ($auth->login($user, $postdata_array['password'], $remember))
                {
                    url::redirect($this->destination);
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
            $form_error = TRUE;
        }
        
        $this->template->site_name = Kohana::config('settings.site_name');
		$this->template->site_tagline = Kohana::config('settings.site_tagline');
		
        $this->template->errors = $errors;
        $this->template->form = $form;
        $this->template->form_error = $form_error;
    }
    
    
    public function front()
    {
    	/**
	     * If the login page is for the frontend, hit this function first.
	     */
		
		$this->destination = '/';
		$this->index();
		
    }
    
    /**
     * Reset password upon user request.
     */
    public function resetpassword()
    {
    	$auth = Auth::instance();
		
        // If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect($this->destination);
            }
        }
    	
    	$this->template = new View('reset_password');
		
		$this->template->title = Kohana::lang('ui_admin.password_reset');
		$form = array
	    (
			'resetemail' 	=> '',
	    );
		
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$password_reset = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        $post = Validation::factory($_POST);

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('resetemail','required','email','length[4,64]');
			
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
					$secret_link = url::site('login/newpassword/'.$user->id.'/'.$secret);
					
					$details_sent = $this->_email_resetlink($post->resetemail,$user->name,$secret_link);
					if( $details_sent )
					{
						$password_reset = TRUE;
					}		
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

        $this->template->form = $form;
	    $this->template->errors = $errors;
		$this->template->form_error = $form_error;
		$this->template->password_reset = $password_reset;
		$this->template->form_action = $form_action;

		// Javascript Header
		//TODO create reset_password js file.
		$this->template->js = new View('reset_password_js');
	}

    /**
     * Create New password upon user request.
     */
    public function newpassword($user_id = 0)
    {
    	$auth = Auth::instance();
		// If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect($this->destination);
            }
        }
    	
    	$this->template = new View('new_password');
		
		$this->template->title = Kohana::lang('ui_admin.new_password');
		
		$secret = $this->uri->segment(4);
		$user = ORM::factory('user',$user_id);
		if ($user->loaded == true && 
			$auth->hash_password($user->email.$user->last_login, $auth->find_salt($secret)) == $secret)
		{ // Email New Password
			$new_password = $this->_generate_password();
			$user->password = $new_password;
			$user->save();
			
			$this->_email_newpassword( $user->email, $user->name, $user->username, $new_password);
		}
		else
		{ // User doesn't exist or reset link expired - redirect to login
			url::redirect('admin/');
		}		
	}

	/**
	 * Checks if email address is associated with an account.
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function email_exists_chk( Validation $post )
	{
		$users = ORM::factory('user');
		if( array_key_exists('resetemail',$post->errors()))
			return;

		if( !$users->email_exists( $post->resetemail ) )
			$post->add_error('resetemail','invalid');
	}
	
	/**
	 * Generate random password for the user.
	 *
 	 * @return the new password
	 */
	public function _generate_password()
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
	public function _email_resetlink( $email, $name, $secret_url )
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
	
	public function _email_newpassword( $email, $name, $username, $password )
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
