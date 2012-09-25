<?php defined('SYSPATH') or die('No direct script access.');
/**
 * "My Profile" - allows admin to configure their settings
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

class Profile_Controller extends Admin_Controller
{
    protected $user_id;

    function __construct()
    {
        parent::__construct();
        $this->template->this_page = 'profile';

        $this->user_id = $_SESSION['auth_user']->id;
    }

    public function index()
    {
        $this->template->content = new View('admin/profile');

        // setup and initialize form field names
        $form = array
        (
            'current_password'  => '',
            'new_password'  => '',
            'password_again'  => '',
            'name'      => '',
            'email'     => '',
            'notify'    => ''
        );

        //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
			$post = Validation::factory($_POST);

			 //  Add some filters
			$post->pre_filter('trim', TRUE);
			$post->add_rules('name','required','length[3,100]');
			$post->add_rules('email','required','email','length[4,64]');
			$post->add_rules('current_password','required');
			$post->add_callbacks('email',array($this,'email_exists_chk'));
			$post->add_callbacks('current_password',array($this,'current_pw_valid_chk'));

            // If Password field is not blank
            if ( ! empty($post->new_password))
            {
                $post->add_rules('new_password','required','length[5,30]','alpha_dash','matches[password_again]');
            }
		//for plugins that'd like to know what the user has to say about their profile
		Event::run('ushahidi_action.profile_add_admin', $post);
			if ($post->validate())
			{
				$user = ORM::factory('user',$this->user_id);
				if ($user->loaded)
				{
					$user->name = $post->name;
					$user->email = $post->email;
					$user->notify = $post->notify;
					if ($post->new_password != '')
                    {
                        $user->password = $post->new_password;
                    }
					$user->save();
					
					
					
					Event::run('ushahidi_action.profile_edit', $user);
						

	                // We also need to update the RiverID server with the new password if
	                //    we are using RiverID and a password is being passed
	                if (kohana::config('riverid.enable') == TRUE
	                	AND ! empty($user->riverid)
	                	AND $post->new_password != '')
					{
						$riverid = new RiverID;
						$riverid->email = $user->email;
						$riverid->password = $post->current_password;
						$riverid->new_password = $post->new_password;
						if ($riverid->changepassword() == FALSE)
						{
							// TODO: Something went wrong. Tell the user.
						}
					}
				}

                $form_saved = TRUE;

                // Repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());
                $form['new_password'] = "";
                $form['password_again'] = "";
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
        else
        {
            $user = ORM::factory('user',$this->user_id);
            $form['username'] = $user->email;
            $form['name'] = $user->name;
            $form['email'] = $user->email;
            $form['notify'] = $user->notify;
        }

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->yesno_array = array('1'=>utf8::strtoupper(Kohana::lang('ui_main.yes')),'0'=>utf8::strtoupper(Kohana::lang('ui_main.no')));

        // Javascript Header
    }


    /**
     * Checks if email address is associated with an account.
     * @param Validation $post $_POST variable with validation rules
     */
    public function email_exists_chk( Validation $post )
    {
		if (array_key_exists('email',$post->errors()))
			return;

		$users = ORM::factory('user')
		    ->where('id <> '.$this->user_id);

		if ($users->email_exists( $post->email ))
		{
			$post->add_error('email','exists');
		}
	}

	/**
	 * Checks if current password being passed is correct
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function current_pw_valid_chk( Validation $post )
	{
		if (array_key_exists('current_password',$post->errors()))
			return;

		$user = User_Model::get_user_by_email($post->email);

		if ( ! User_Model::check_password($user->email,$post->current_password) )
		{
			$post->add_error('current_password','incorrect');
		}
	}
}
