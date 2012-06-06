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

class Users_Controller extends Admin_Controller {

	private $display_roles = FALSE;

	public function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'users';

		// If user doesn't have access, redirect to dashboard
		if (!$this->auth->has_permission("users"))
		{
			url::redirect(url::site() . 'admin/dashboard');
		}

		$this->display_roles = $this->auth->has_permission('manage_roles');
	}

	public function index()
	{
		$this->template->content = new View('admin/users/main');
		$this->template->js = new View('admin/users/users_js');

		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			$post = Validation::factory(array_merge($_POST, $_FILES));

			// Add some filters
			$post->pre_filter('trim', TRUE);

			// As far as I know, the only time we submit a form here is to delete a user

			if ($post->action == 'd')
			{
				// We don't want to delete the first user

				if ($post->user_id_action != 1)
				{
					// Delete the user

					$user = ORM::factory('user', $post->user_id_action)->delete();

				}

				$form_saved = TRUE;
				$form_action = utf8::strtoupper(Kohana::lang('ui_admin.deleted'));
			}
		}

		// Pagination
		$pagination = new Pagination( array('query_string' => 'page', 'items_per_page' => (int)Kohana::config('settings.items_per_page_admin'), 'total_items' => ORM::factory('user')->count_all()));

		$users = ORM::factory('user')->orderby('name', 'asc')->find_all((int)Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);

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
	public function edit($user_id = FALSE, $saved = FALSE)
	{
		$this->template->content = new View('admin/users/edit');

		if ($user_id)
		{
			$user_exists = ORM::factory('user')->find($user_id);
			if (!$user_exists->loaded)
			{
				// Redirect
				url::redirect(url::site() . 'admin/users/');
			}
		}

		// Setup and initialize form field names
		$form = array('username' => '', 'name' => '', 'email' => '', 'password' => '', 'notify' => '', 'role' => '');

		$this->template->content->user_id = $user_id;

		if ($user_id == FALSE)
		{
			// Tack this on when adding a new user
			$form['password'] = '';
			$form['password_again'] = '';
		}

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$user = "";

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			// Get the submitted data
			$post = $_POST;

			// Add the user_id to the $_POST data
			$user_id = ($user_id) ? $user_id : NULL;
			$post = array_merge($post, array('user_id' => $user_id));

			if (User_Model::custom_validate($post))
			{
				$user = ORM::factory('user', $user_id);
				$user->name = $post->name;
				$user->email = $post->email;
				$user->notify = $post->notify;
				if ($user_id == NULL)
				{
					$user->password = $post->password;
				}

				// We can only set a new password if we are using the standard ORM method,
				//    otherwise it won't actually change the password used for authentication
				if (isset($post->new_password) AND Kohana::config('riverid.enable') == FALSE AND strlen($post->new_password) > 0)
				{
					$user->password = $post->new_password;
				}

				// Existing User??
				if ($user->loaded)
				{
					// Prevent modification of the main admin account username or role
					if ($user->id != 1)
					{
						$user->username = $post->username;

						// Remove Old Roles
						foreach ($user->roles as $role)
						{
							$user->remove($role);
						}

						// Add New Roles
						if ($post->role != 'none')
						{
							$user->add(ORM::factory('role', 'login'));
							$user->add(ORM::factory('role', $post->role));
						}
					}
				}
				// New User
				else
				{
					$user->username = $post->username;

					// Add New Roles
					if ($post->role != 'none')
					{
						$user->add(ORM::factory('role', 'login'));
						$user->add(ORM::factory('role', $post->role));
					}
				}
				$user->save();

				//Event for adding user admin details
				Event::run('ushahidi_action.users_add_admin', $post);

				Event::run('ushahidi_action.user_edit', $user);

				// Redirect
				url::redirect(url::site() . 'admin/users/');
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
			if ($user_id)
			{
				// Retrieve Current Incident
				$user = ORM::factory('user', $user_id);
				if ($user->loaded)
				{
					// Some users don't have roles so we have this "none" role
					$role = 'none';
					foreach ($user->roles as $user_role)
					{
						$role = $user_role->name;
					}

					$form = array('user_id' => $user->id, 'username' => $user->username, 'name' => $user->name, 'email' => $user->email, 'notify' => $user->notify, 'role' => $role);
				}
			}
		}

		$roles = ORM::factory('role')->where('id != 1')->orderby('name', 'asc')->find_all();

		foreach ($roles as $role)
		{
			$role_array[$role->name] = utf8::strtoupper($role->name);
		}

		// Add one additional role for users with no role
		$role_array['none'] = utf8::strtoupper(Kohana::lang('ui_main.none'));

		$this->template->content->id = $user_id;
		$this->template->content->display_roles = $this->display_roles;
		$this->template->content->user = $user;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->yesno_array = array('1' => utf8::strtoupper(Kohana::lang('ui_main.yes')), '0' => utf8::strtoupper(Kohana::lang('ui_main.no')));
		$this->template->content->role_array = $role_array;
	}

	public function roles()
	{
		$this->template->content = new View('admin/users/roles');
		
		$permissions = ORM::factory('permission')->find_all()->select_list('id','name');

		$form = array('role_id' => '', 'action' => '', 'name' => '', 'description' => '', 'access_level' => '', 'permissions' => '');
		foreach($permissions as $permission)
		{
			$form[$permission] = '';
		}
		
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

			if ($post->action == 'a')// Add / Edit Action
			{
				$post->add_rules('name', 'required', 'length[3,30]', 'alpha_numeric');
				$post->add_rules('description', 'required', 'length[3,100]');
				$post->add_rules('access_level', 'required', 'between[0,100]', 'numeric');
				$post->add_rules('permissions[]', 'numeric');

				if ($post->role_id == "3" || $post->role_id == "1" || $post->role_id == "4")
				{
					$post->add_error('name', 'nomodify');
				}

				// Unique Role Name
				$post->role_id == '' ? $post->add_callbacks('name', array($this, 'role_exists_chk')) : '';
			}

			if ($post->validate())
			{
				$role = ORM::factory('role', $post->role_id);
				if ($post->action == 'a')// Add/Edit Action
				{
					// Remove non-existant permissions
					$perm_ids = array_keys($permissions);
					foreach ($post->permissions as $k => $perm)
					{
						if (! in_array($perm, $perm_ids))
						{
							unset($post->permissions[$k]);
						}
					}

					$role->name = $post->name;
					$role->description = $post->description;
					$role->access_level = $post->access_level;
					$role->permissions = array_unique($post->permissions);
					$role->save();

					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.added_edited'));
				}
				elseif ($post->action == 'd')// Delete Action
				{
					if ($post->role_id != 1 AND $post->role_id != 2 AND $post->role_id != 3)
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

		$roles = ORM::factory('role')->where('id != 1')->orderby('access_level', 'desc')->find_all();

		$this->template->content->display_roles = $this->display_roles;
		$this->template->content->roles = $roles;
		$this->template->content->permissions = $permissions;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->js = new View('admin/users/roles_js');
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
			$post->add_error('username', 'exists');
	}

	/**
	 * Checks if email address is associated with an account.
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function email_exists_chk(Validation $post)
	{
		$users = ORM::factory('user');
		if (array_key_exists('email', $post->errors()))
			return;

		if ($users->email_exists($post->email))
			$post->add_error('email', 'exists');
	}

	/**
	 * Checks if role already exists.
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function role_exists_chk(Validation $post)
	{
		$roles = ORM::factory('role')->where('name', $post->name)->find();

		// If add->rules validation found any errors, get me out of here!
		if (array_key_exists('name', $post->errors()))
			return;

		if ($roles->loaded)
		{
			$post->add_error('name', 'exists');
		}
	}

}
