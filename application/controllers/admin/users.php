<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to manage users
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

class Users_Controller extends Admin_Controller
{
    private $display_roles = false;
    
    function __construct()
    {
        parent::__construct();
        $this->template->this_page = 'users';
        
        // If user doesn't have access, redirect to dashboard
        if ( ! admin::permissions($this->user, "users"))
        {
            url::redirect(url::site().'admin/dashboard');
        }
        
        $this->display_roles = admin::permissions($this->user, 'manage_roles');
    }

    function index()
    {   
        $this->template->content = new View('admin/users');
        $this->template->js = new View('admin/users_js');
        
        // Check, has the form been submitted, if so, setup validation

		if ($_POST)
		{
			$post = Validation::factory(array_merge($_POST,$_FILES));

			// Add some filters

			$post->pre_filter('trim', TRUE);

			// As far as I know, the only time we submit a form here is to delete a user

			if ($post->action == 'd')
			{
				// We don't want to delete the first user

				if($post->user_id_action != 1)
				{
					// Delete the user

					$user = ORM::factory('user',$post->user_id_action)
								->delete();

					// Remove the roles assigned to the now deleted user to clean up

					$roles_user_model = new Roles_User_Model;
					$roles_user_model->delete_role($post->user_id_action);

				}

				$form_saved = TRUE;
				$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
			}
		}

        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
                            'total_items'  => ORM::factory('user')->count_all()
                        ));

        $users = ORM::factory('user')
                    ->orderby('name', 'asc')
                    ->find_all((int) Kohana::config('settings.items_per_page_admin'), 
                        $pagination->sql_offset);

        // Set the flag for displaying the roles link
        $this->template->content->display_roles = $this->display_roles;

        $this->template->content->pagination = $pagination;
        $this->template->content->total_items = $pagination->total_items;
        $this->template->content->users = $users;
    }
    
    /**
    * Edit a user
    * @param bool|int $user_id The id no. of the user
    * @param bool|string $saved
    */
    function edit( $user_id = false, $saved = false )
    {       
        $this->template->content = new View('admin/users_edit');
        
        if ($user_id)
        {
            $user_exists = ORM::factory('user')->find($user_id);
            if ( ! $user_exists->loaded)
            {
                // Redirect
                url::redirect(url::site().'admin/users/');
            }
        }
        
        
        // setup and initialize form field names
        $form = array
        (
            'username'  => '',
            'password'  => '',
            'password_again'  => '',
            'name'      => '',
            'email'     => '',
            'notify'    => '',
            'role'      => ''
        );
        
        //copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        $user = "";
        
        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            $post = Validation::factory($_POST);

            //  Add some filters
            $post->pre_filter('trim', TRUE);
    
            $post->add_rules('username','required','length[3,16]', 'alpha_numeric');
        
            //only validate password as required when user_id has value.
            $user_id == '' ? $post->add_rules('password','required',
                'length[5,16]','alpha_numeric'):'';
            $post->add_rules('name','required','length[3,100]');
        
            $post->add_rules('email','required','email','length[4,64]');
        
            $user_id == '' ? $post->add_callbacks('username',
                array($this,'username_exists_chk')) : '';
        
            $user_id == '' ? $post->add_callbacks('email',
                array($this,'email_exists_chk')) : '';

            // If Password field is not blank
            if (!empty($post->password))
            {
                $post->add_rules('password','required','length[5,30]'
                    ,'alpha_numeric','matches[password_again]');
            }
            
            $post->add_rules('role','required','length[3,30]', 'alpha_numeric');
            
            $post->add_rules('notify','between[0,1]');
            
            Event::run('ushahidi_action.user_submit_admin', $post);
            
            if ($post->validate())
            {
                $user = ORM::factory('user',$user_id);
                $user->name = $post->name;
                $user->email = $post->email;
                $user->notify = $post->notify;
                
                // Existing User??
                if ($user->loaded==true)
                {
                    // Prevent modification of the main admin account username or role
                    if ($user->id != 1)
                    {
                        $user->username = $post->username;
                        
                        // Remove Old Roles
                        foreach($user->roles as $role)
                        {
                            $user->remove($role); 
                        }
                        
                        // Add New Roles
                        $user->add(ORM::factory('role', 'login'));
                        $user->add(ORM::factory('role', $post->role));
                    }
                    
                    $post->password !='' ? $user->password=$post->password : '';
                }
                // New User
                else 
                {
                    $user->username = $post->username;
                    $user->password = $post->password;
                    
                    // Add New Roles
                    $user->add(ORM::factory('role', 'login'));
                    $user->add(ORM::factory('role', $post->role));
                }
                $user->save();
                Event::run('ushahidi_action.user_edit', $user);
                
                // Redirect
                url::redirect(url::site().'admin/users/');
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
            if ( $user_id )
            {
                // Retrieve Current Incident
                $user = ORM::factory('user', $user_id);
                if ($user->loaded == true)
                {
                    foreach ($user->roles as $user_role)
                    {
                         $role = $user_role->name;
                    }
                    
                    $form = array
                    (
                        'user_id'   => $user->id,
                        'username'  => $user->username,
                        'password'  => '',
                        'password_again'  => '',
                        'name'      => $user->name,
                        'email'     => $user->email,
                        'notify'    => $user->notify,
                        'role'      => $role
                    );
                }
            }
        }
        
        $roles = ORM::factory('role')
            ->where('id != 1')
            ->orderby('name', 'asc')
            ->find_all();
        
        $role_array = array("login" => "NONE");
        foreach ($roles as $role)
        {
            $role_array[$role->name] = strtoupper($role->name);
        }
        
        $this->template->content->id = $user_id;
        $this->template->content->display_roles = $this->display_roles;
        $this->template->content->user = $user;
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->yesno_array = array('1'=>strtoupper(Kohana::lang('ui_main.yes')),'0'=>strtoupper(Kohana::lang('ui_main.no')));        
        $this->template->content->role_array = $role_array;
    }
    
    public function roles()
    {
        $this->template->content = new View('admin/users_roles');
        
        $form = array
        (
            'role_id'   => '',
            'action'    => '',
            'name'  => '',
            'description'  => '',
            'reports_view' => '',
            'reports_edit' => '',
            'reports_evaluation' => '',
            'reports_comments' => '',
            'reports_download' => '',
            'reports_upload' => '',
            'messages' => '',
            'messages_reporters' => '',
            'stats' => '',
            'settings' => '',
            'manage' => '',
            'users' => '',
            'access_level' => ''
        );
        //copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
        $form_saved = FALSE;
        $form_action = "";
        
        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            $post = Validation::factory($_POST);
            
            //  Add some filters
            $post->pre_filter('trim', TRUE);
            
            if ($post->action == 'a')       // Add / Edit Action
            {
                $post->add_rules('name','required','length[3,30]', 'alpha_numeric');
                $post->add_rules('description','required','length[3,100]');
                $post->add_rules('access_level','required','between[0,100]', 'numeric');
                $post->add_rules('reports_view','between[0,1]');
                $post->add_rules('reports_edit','between[0,1]');
                $post->add_rules('reports_evaluation','between[0,1]');
                $post->add_rules('reports_comments','between[0,1]');
                $post->add_rules('reports_download','between[0,1]');
                $post->add_rules('reports_upload','between[0,1]');
                $post->add_rules('messages','between[0,1]');
                $post->add_rules('messages_reporters','between[0,1]');
                $post->add_rules('stats','between[0,1]');
                $post->add_rules('settings','between[0,1]');
                $post->add_rules('manage','between[0,1]');
                $post->add_rules('users','between[0,1]');
            
                if ($post->role_id == "3")
                {
                    $post->add_error('name', 'nomodify');
                }
                
                // Unique Role Name
                $post->role_id == '' ? $post->add_callbacks('name',
                    array($this,'role_exists_chk')) : '';
            }
            
            if ($post->validate())
            {
                $role = ORM::factory('role',$post->role_id);
                if ($post->action == 'a')               // Add/Edit Action
                {
                    $role->name = $post->name;
                    $role->description = $post->description;
                    $role->access_level = $post->access_level;
                    $role->reports_view = $post->reports_view;
                    $role->reports_edit = $post->reports_edit;
                    $role->reports_evaluation = $post->reports_evaluation;
                    $role->reports_comments = $post->reports_comments;
                    $role->reports_download = $post->reports_download;
                    $role->reports_upload = $post->reports_upload;
                    $role->messages = $post->messages;
                    $role->messages_reporters = $post->messages_reporters;
                    $role->stats = $post->stats;
                    $role->settings = $post->settings;
                    $role->manage = $post->manage;
                    $role->users = $post->users;
                    
                    $role->save();
                    
                    $form_saved = TRUE;
                    $form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
                }
                elseif ($post->action == 'd')           // Delete Action
                {
                    if($post->role_id != 1
                        AND $post->role_id != 2
                        AND $post->role_id != 3)
                    {   
                        // Delete the role
                        $role->delete();
                    }

                    $form_saved = TRUE;
                    $form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
                }
            }
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

               // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('roles'));
                $form_error = TRUE;
            }
        }
        
        
        $roles = ORM::factory('role')
            ->orderby('access_level', 'desc')
            ->find_all();
            
        $permissions = array(
            "reports_view" => "View Reports",
            "reports_edit" => "Create/Edit Reports",
            "reports_evaluation" => "Approve & Verify Reports",
            "reports_comments" => "Manage Report Comments",
            "reports_download" => "Download Reports",
            "reports_upload" => "Upload Reports",
            "messages" => "Manage Messages",
            "messages_reporters" => "Manage Message Reporters",
            "stats" => "View Stats",
            "settings" => "Modify Settings",
            "manage" => "Manage Panel",
            "users" => "Manage Users",
        );
        
        $this->template->content->display_roles = $this->display_roles;
        $this->template->content->roles = $roles;
        $this->template->content->permissions = $permissions;
        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
        $this->template->content->form_action = $form_action;
        $this->template->js = new View('admin/users_roles_js');         
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
     * Check if 
     */
    
    /**
     * Checks if email address is associated with an account.
     * @param Validation $post $_POST variable with validation rules 
     */
    public function email_exists_chk( Validation $post )
    {
        $users = ORM::factory('user');
        if (array_key_exists('email',$post->errors()))
            return;
            
        if ($users->email_exists( $post->email ) )
            $post->add_error('email','exists');
    }
    
    /**
     * Checks if role already exists.
     * @param Validation $post $_POST variable with validation rules 
     */
    public function role_exists_chk(Validation $post)
    {
        $roles = ORM::factory('role')
            ->where('name', $post->name)
            ->find();
            
        // If add->rules validation found any errors, get me out of here!
        if (array_key_exists('name', $post->errors()))
            return;
                
        if ($roles->loaded)
        {
            $post->add_error( 'name', 'exists');
        }
    }   
}
