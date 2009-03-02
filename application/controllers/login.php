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
 * @module     Login Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Login_Controller extends Template_Controller {
	
    public $auto_render = TRUE;
	
    protected $user;
	
    // Session Object
    protected $session;
	
    // Main template
    public $template = 'admin/login';
	

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
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect('admin/dashboard');
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
            ->add_rules('password', 'required');
		
        if ($_POST->validate())
        {
            // Sanitize $_POST data removing all inputs without rules
            $postdata_array = $_POST->safe_array();

            // Load the user
            $user = ORM::factory('user', $postdata_array['username']);

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
            $form_error = TRUE;
        }
		
        $this->template->errors = $errors;
        $this->template->form = $form;
        $this->template->form_error = $form_error;
    }	
}
