<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is used to view user profiles
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package	   Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Profile_Controller extends Main_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Displays the default "profile" page
	 */
	public function index()
	{
		// Cacheable Controller
		$this->is_cachable = TRUE;

		$this->template->header->this_page = 'profile';
		$this->template->content = new View('profile/main');

		$this->template->content->users = User_Model::get_public_users();

		$this->template->header->page_title .= Kohana::lang('ui_main.browse_profiles').Kohana::config('settings.title_delimiter');

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

	/**
	 * Displays a profile page for a user
	 */
	public function user()
	{
		// Cacheable Controller
		$this->is_cachable = TRUE;

		$this->template->header->this_page = 'profile';

		// Check if we are looking for a user. Argument must be set to continue.
		if( ! isset(Router::$arguments[0]))
		{
			url::redirect('profile');
		}

		$username = Router::$arguments[0];

		// We won't allow profiles to be public if the username is an email address
		if (valid::email($username))
		{
			url::redirect('profile');
		}

		$user = User_Model::get_user_by_username($username);

		// We only want to show public profiles here
		if($user->public_profile == 1)
		{
			$this->template->content = new View('profile/user');

			$this->template->content->user = $user;

			// User Reputation Score
			$this->template->content->reputation = reputation::calculate($user->id);

			// All users reports
			$this->template->content->reports = ORM::factory('incident')
				->where(array('user_id' => $user->id, 'incident_active' => 1))
				->with('incident:location')
				->find_all();

			// Get Badges
			$this->template->content->badges = Badge_Model::users_badges($user->id);

			// Logged in user id (false if not logged in)
			$logged_in_id = FALSE;
			if(isset(Auth::instance()->get_user()->id))
			{
				$logged_in_id = Auth::instance()->get_user()->id;
			}
			$this->template->content->logged_in_id = $logged_in_id;

			// Is this the logged in user?
			$logged_in_user = FALSE;
			if($logged_in_id == $user->id){
				$logged_in_user = TRUE;
			}
			$this->template->content->logged_in_user = $logged_in_user;
		}else{
			// this is a private profile so get out of here
			url::redirect('profile');
		}

		$this->template->header->page_title .= $user->name.Kohana::config('settings.title_delimiter');

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

} // End Profile
