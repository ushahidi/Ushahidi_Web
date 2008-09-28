<?php defined('SYSPATH') or die('No direct script access.');

/**
* Users Controller
*/
class Users_Controller extends Admin_Controller
{
	public $roles_users;
	function __construct()
	{
		parent::__construct();
		$this->roles_users = new Roles_User_Model();
		$this->template->this_page = 'users';
				
	}
	
	
	function index()
	{	
		$this->template->content = new View('admin/users');
		$this->template->content->title = 'Manage Users';
		
		
		// setup and initialize form field names
		$form = array
	    (
	        'user_id'   => '',
			'action'	=> '',
			'username' 	=> '',
			'password'  => '',
			'name'  	=> '',
			'email' 	=> '',
			'role'  	=> ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		
		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        $post = Validation::factory($_POST);
			
	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('username','required','length[3,16]', 'alpha');
			
			//only validate password as required when user_id has value.
			$post->user_id == ''? $post->add_rules('password','required',
			'length[5,16]'):'';
			$post->add_rules('name','required','length[3,100]');
			
			$post->add_rules('email','required','email','length[4,64]');
			
			$post->user_id == '' ? $post->add_callbacks('username',
				array($this,'username_exists_chk')) : '';
			
			$post->user_id == '' ? $post->add_callbacks('email',
				array($this,'email_exists_chk')) : '';
			
			if ($post->validate())
	        {
				//check the actions being taken.
				//add action
				if($post->user_id == '' ) {
					
					//Getting familiar with ORM. Correct me if I'm doing 
					//something wrong.
					
					$add_user = ORM::factory('user');
					$add_user->username = $post->username;
					$add_user->name = $post->name;
					$add_user->password = $post->password;
					$add_user->email = $post->email;
					$add_user->save();
					
					//add role to role table.
					$user_role = ORM::factory('user')->where('username',$post->username )->find();
					
					$data = array('user_id' => $user_role->id,
						'role_id' => $post->role );
					$this->roles_users->insert_role($data);	
				
				} elseif( $post->action == 'd' ){ //delete action
					
					//print_r($post );
					ORM::factory('user')->delete($post->user_id);
					
					//update role table too.
					$data = array('user_id' => $post->user_id,
						'role_id' => $post->role );
						
					$this->roles_users->delete_role($post->user_id, $data);
					  
				} else { // edit action
					
					$update_user = ORM::factory('user',$post->user_id );
					$update_user->username = $post->username;
					$update_user->name = $post->name;
					$update_user->email = $post->email;
					$post->password !='' ? $update_user->password=$post->password : '';
					$update_user->save();
					
					//update role table too.
					$data = array('user_id' => $post->user_id,
						'role_id' => $post->role );
						
					$this->roles_users->update_role($post->user_id, $data);
					
				}
				
				$form_saved = TRUE;
			} else {
							
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
	    }
	
	
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'    => 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'    => ORM::factory('user')->count_all()
		));

		$users = ORM::factory('user')->orderby('name', 'asc')->find_all((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		
		// Get User Roles
		foreach (ORM::factory('role')->orderby('name', 'asc')->find_all() as $role)
		{
			$roles[$role->id] = $role->name;
		}
		
		
		
		$this->template->content->form = $form;
	    $this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->users = $users;
		$this->template->content->roles = $roles;
		
		
		// Javascript Header
		$this->template->colorpicker_enabled = TRUE;
		$this->template->js = new View('admin/users_js');
		
	}
	
	/**
	 * Checks if username already exists. 
	 */
	public function username_exists_chk(Validation $post)
	{
		$users = ORM::factory('user');
		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('username', $post->errors()))
			return;
				
		if( $users->username_exists($post->username) )
			$post->add_error( 'username', 'exists');
	}
	
	/**
	 * Checks if email address is associated with an account.
	 */	
	public function email_exists_chk( Validation $post )
	{
		$users = ORM::factory('user');
		if( array_key_exists('email',$post->errors()))
			return;
			
		if( $users->email_exists( $post->email ) )
			$post->add_error('email','exists');
	}
}