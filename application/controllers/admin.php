<?php defined('SYSPATH') or die('No direct script access.');
/**
* ADMIN CONTROLLER
*/

class Admin_Controller extends Template_Controller {

	public $auto_render = TRUE;
	
	// Main template
	public $template = 'admin/layout';
	
	// Cache instance
	protected $cache;

	// Enable auth
	protected $auth_required = FALSE;
	
	protected $user;


	public function __construct()
	{
		parent::__construct();	

		// Load cache
		$this->cache = new Cache;

		// Load session
		$this->session = new Session;

		// Load database
		$this->db = new Database();
		
			
 		if ( ! Auth::instance()->logged_in('admin')) 
		{
			url::redirect('login');
		}
		
		// Get Session Information
		$user = new User_Model($_SESSION['auth_user']->id);
		
		$this->template->admin_name = $user->name;
		$this->template->site_name = Kohana::config('settings.site_name');	//Retrieve Default Settings
		
		
		// Load profiler
		$profiler = new Profiler;		
		
	}

	public function index()
	{
		// Send them to the right page
		url::redirect('admin/dashboard');
	}

	public function log_out()
	{
		$auth = new Auth;
		$auth->logout(TRUE);

		url::redirect('login');
	}

	public function manage_users($id = FALSE)
	{
		if ($id === FALSE)
		{
			$this->template->title = Kohana::lang('admin.users_title');

			$this->template->content = View::factory('admin/edit_list')
				->set('edit_action', 'admin/manage_users')
				->set('delete_action', 'admin/delete_user')
				->bind('items', $items);

			if ($this->user->has_role('admin'))
			{
				$this->template->content->set('new', Kohana::lang('admin.add_user'));

				foreach (ORM::factory('user')->find(ALL) as $user)
				{
					// Create a list of all users
					$items[$user->username] = $user->username;
				}
			}
			else
			{
				// Show only this user
				$items[$this->user->username] = $this->user->username;
			}
		}
		else
		{
			// Reset the id for new users
			($id === 'new') and $id = FALSE;

			// Load the user
			$user = new User_Model($id);

			$roles = array();
			foreach (ORM::factory('role')->find(ALL) as $role)
			{
				// Create a checklist option array
				$roles[$role->name] = array($role->name, $user->has_role($role->id));
			}

			// Create user editing form
			$form = new Forge(NULL, $this->template->title = ($user->username ? Kohana::lang('admin.edit_user', $user->username) : Kohana::lang('admin.new_user')));
			$form->input('username')->label(TRUE)->rules('required|length[2,32]')->value($user->username);
			$form->input('email')->label(TRUE)->rules('required|length[4,127]|valid_email')->value($user->email);
			$form->password('password')->label(TRUE)->rules('length[4,64]');
			$form->password('passconf')->label('Confirm')->matches($form->password);
			$form->checklist('roles')->label(TRUE)->options($roles);
			$form->submit(Kohana::lang('admin.save_button'));

			if ($id === FALSE)
			{
				// New users must have a password
				$form->password->rules('+required');
			}

			if ( ! $this->user->has_role('admin'))
			{
				// Only admins are allowed to change user roles
				$form->roles->disabled(TRUE);
			}

			if ($form->validate() AND $data = $form->as_array())
			{
				// Extract the roles from the data
				$set_roles = arr::remove('roles', $data);

				if (empty($data['passconf']))
				{
					// Do not reset the password to nothing
					unset($data['password'], $data['passconf']);
				}

				foreach ($data as $key => $val)
				{
					// Set new values
					$user->$key = $val;
				}

				// Save the user and set the message
				$user->save() and $this->session->set_flash('message', Kohana::lang('admin.user_added'));

				// Only admins are allowed to change user roles
				if ($this->user->has_role('admin'))
				{
					foreach (array_diff($user->roles, $set_roles) as $role)
					{
						// Remove roles that were unchecked
						$user->remove_role($role);
					}

					foreach (array_diff($set_roles, $user->roles) as $role)
					{
						// Add new roles
						$user->add_role($role);
					}
				}

				// Redirect the the dashboard
				url::redirect('admin/dashboard');
			}

			$this->template->content = $form->html();
		}
	}

	public function delete_user($id = FALSE)
	{
		// Confirmation
		$confirm = $this->input->get('confirm');

		// Load the user
		$user = new User_Model($id);

		if (! $this->user->has_role('admin') OR $confirm === 'no' OR $user->id == 0)
		{
			// Go back the to the management page
			url::redirect('admin/manage_users');
		}

		// Set the template title
		$this->template->title = Kohana::lang('admin.delete_user', $user->username);

		if ($user->id AND $confirm === 'yes')
		{
			// Delete the user
			$user->delete();

			// Go back to the user management
			url::redirect('admin/manage_users');
		}

		$this->template->content = View::factory('admin/confirm')->set('action', 'admin/delete_user/'.$id);
	}
		

} // End Admin