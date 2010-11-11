<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Admin helper class.
 *
 * @package    Admin
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class admin_Core {

	/**
	 * Generate Main Tab Menus
	 */
	public static function main_tabs()
	{
		// Change tabs for MHI
		if (Kohana::config('config.enable_mhi') == TRUE AND Kohana::config('settings.subdomain') == '')
		{
	    	// Start from scratch on admin tabs since most are irrelevant

			return array(
				'mhi' => Kohana::lang('ui_admin.mhi'),
				'stats' => Kohana::lang('ui_admin.stats'),
			);
		}
		else
		{
			return array(
				'dashboard' => Kohana::lang('ui_admin.dashboard'),
				'reports' => Kohana::lang('ui_admin.reports'),
				'messages' => Kohana::lang('ui_admin.messages'),
				'stats' => Kohana::lang('ui_admin.stats'),
				'addons' => Kohana::lang('ui_admin.addons')
			);
		}
	}


	/**
	 * Generate Main Tab Menus (RIGHT SIDE)
     */
	public static function main_right_tabs($auth = FALSE)
	{
		$main_right_tabs = array();

		// Change tabs for MHI
		if (Kohana::config('config.enable_mhi') == TRUE AND Kohana::config('settings.subdomain') == '')
		{
			$main_right_tabs = array(
				'users' => Kohana::lang('ui_admin.users'),
				'mhi/settings' => Kohana::lang('ui_admin.settings')
			);
		}
		else
		{
			
			if ($auth AND $auth->logged_in('superadmin'))
			{
				$main_right_tabs = array(
					'settings/site' => Kohana::lang('ui_admin.settings'),
					'manage' => Kohana::lang('ui_admin.manage'),
					'users' => Kohana::lang('ui_admin.users')
				);
			}
			elseif ($auth AND $auth->logged_in('admin'))
			{
				$main_right_tabs = array(
					'manage' => Kohana::lang('ui_admin.manage'),
					'users' => Kohana::lang('ui_admin.users')
				);
			}
		}

		return $main_right_tabs;
	}

	/**
	 * Generate MHI Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function mhi_subtabs($this_sub_page = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "deployments") ? "Deployments" : "<a href=\"".url::base()."admin/mhi/\">Deployments</a>";

		$menu .= ($this_sub_page == "activity") ? "Activity Stream" : "<a href=\"".url::base()."admin/mhi/activity\">Activity Stream</a>";

		$menu .= ($this_sub_page == "updatelist") ? "Update List" : "<a href=\"".url::base()."admin/mhi/updatelist\">Update List</a>";

		echo $menu;
	}

	/**
	 * Generate Report Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function reports_subtabs($this_sub_page = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "view") ? Kohana::lang('ui_main.view_reports') : "<a href=\"".url::base()."admin/reports\">".Kohana::lang('ui_main.view_reports')."</a>";

		$menu .= ($this_sub_page == "edit") ? Kohana::lang('ui_main.create_report') : "<a href=\"".url::base()."admin/reports/edit\">".Kohana::lang('ui_main.create_report')."</a>";

		$menu .= ($this_sub_page == "comments") ? Kohana::lang('ui_main.comments') : "<a href=\"".url::base()."admin/comments\">".Kohana::lang('ui_main.comments')."</a>";

		$menu .= ($this_sub_page == "download") ? Kohana::lang('ui_main.download_reports') : "<a href=\"".url::base()."admin/reports/download\">".Kohana::lang('ui_main.download_reports')."</a>";

		$menu .= ($this_sub_page == "upload") ? Kohana::lang('ui_main.upload_reports') : "<a href=\"".url::base()."admin/reports/upload\">".Kohana::lang('ui_main.upload_reports')."</a>";

		echo $menu;
		
		// Action::nav_admin_reports - Add items to the admin reports navigation tabs
		Event::run('ushahidi_action.nav_admin_reports', $this_sub_page);
	}


	/**
	 * Generate Messages Sub Tab Menus
     * @param int $service_id
	 * @return string $menu
     */
	public static function messages_subtabs($service_id = FALSE)
	{
		$menu = "";
		foreach (ORM::factory('service')->find_all() as $service)
		{
			if ($service->id == $service_id)
			{
				$menu .= $service->service_name;
			}
			else
			{
				$menu .= "<a href=\"" . url::site() . "admin/messages/index/".$service->id."\">".$service->service_name."</a>";
			}
		}
		
		echo $menu;
		
		// Action::nav_admin_messages - Add items to the admin messages navigation tabs
		Event::run('ushahidi_action.nav_admin_messages', $service_id);
	}


	/**
	 * Generate Settings Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function settings_subtabs($this_sub_page = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "site") ? Kohana::lang('ui_main.site') : "<a href=\"".url::site()."admin/settings/site\">".Kohana::lang('ui_main.site')."</a>";

		$menu .= ($this_sub_page == "map") ? Kohana::lang('ui_main.map') : "<a href=\"".url::site()."admin/settings\">".Kohana::lang('ui_main.map')."</a>";

		$menu .= ($this_sub_page == "sms") ? Kohana::lang('ui_main.sms') : "<a href=\"".url::site()."admin/settings/sms\">".Kohana::lang('ui_main.sms')."</a>";

		$menu .= ($this_sub_page == "email") ? Kohana::lang('ui_main.email') : "<a href=\"".url::site()."admin/settings/email\">".Kohana::lang('ui_main.email')."</a>";

		$menu .= ($this_sub_page == "themes") ? Kohana::lang('ui_main.themes') : "<a href=\"".url::site()."admin/settings/themes\">".Kohana::lang('ui_main.themes')."</a>";

		// We cannot allow cleanurl settings to be changed if MHI is enabled since it modifies a file in the config folder
		if (Kohana::config('config.enable_mhi') == FALSE)
		{
			$menu .= ($this_sub_page == "cleanurl") ? Kohana::lang('ui_main.cleanurl'):  "<a href=\"".url::site() ."admin/settings/cleanurl\">".Kohana::lang('ui_main.cleanurl')."</a>";
		}
		
        $menu .= ($this_sub_page == "api") ? Kohana::lang('ui_main.api') : "<a href=\"".url::site()."admin/settings/api\">".Kohana::lang('ui_main.api')."</a>";        
		
		echo $menu;
		
		// Action::nav_admin_settings - Add items to the admin settings navigation tabs
		Event::run('ushahidi_action.nav_admin_settings', $this_sub_page);
	}


	/**
	 * Generate SMS Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function settings_sms_subtabs($this_sub_page = FALSE)
	{
		$menu = "";
		$menu .= ($this_sub_page == "sms") ? Kohana::lang('ui_main.sms') : "<a href=\"".url::base()."admin/settings/sms\">".Kohana::lang('settings.sms.option_1')."</a>";
		$menu .= ($this_sub_page == "smsglobal") ? Kohana::lang('ui_main.sms') : "<a href=\"".url::base()."admin/settings/smsglobal\">".Kohana::lang('settings.sms.option_2')."</a>";
		
		echo $menu;
		
		// Action::nav_admin_settings_sms - Add items to the settings sms  navigation tabs
		Event::run('ushahidi_action.sub_nav_admin_settings_sms', $this_sub_page);
	}




	/**
	 * Generate Manage Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function manage_subtabs($this_sub_page = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "categories") ? Kohana::lang('ui_main.categories') : "<a href=\"".url::site()."admin/manage\">".Kohana::lang('ui_main.categories')."</a>";

		$menu .= ($this_sub_page == "forms") ? Kohana::lang('ui_main.forms') : "<a href=\"".url::site()."admin/manage/forms\">".Kohana::lang('ui_main.forms')."</a>";

		//** Not sure Organizations is necessary any more?
		//$menu .= ($this_sub_page == "organizations") ? Kohana::lang('ui_main.organizations')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/organizations\">".Kohana::lang('ui_main.organizations')."</a>";
		
		$menu .= ($this_sub_page == "sharing") ? Kohana::lang('ui_main.sharing') : "<a href=\"".url::site()."admin/manage/sharing\">".Kohana::lang('ui_main.sharing')."</a>";

		$menu .= ($this_sub_page == "pages") ? Kohana::lang('ui_main.pages') : "<a href=\"".url::site()."admin/manage/pages\">".Kohana::lang('ui_main.pages')."</a>";

		$menu .= ($this_sub_page == "feeds") ? Kohana::lang('ui_main.news_feeds') : "<a href=\"".url::site()."admin/manage/feeds\">".Kohana::lang('ui_main.news_feeds')."</a>";

		$menu .= ($this_sub_page == "layers") ? Kohana::lang('ui_main.layers') : "<a href=\"".url::site()."admin/manage/layers\">".Kohana::lang('ui_main.layers')."</a>";

		$menu .= ($this_sub_page == "scheduler") ? Kohana::lang('ui_main.scheduler') : "<a href=\"".url::site()."admin/manage/scheduler\">".Kohana::lang('ui_main.scheduler')."</a>";

		echo $menu;
		
		// Action::nav_admin_manage - Add items to the admin manage navigation tabs
		Event::run('ushahidi_action.nav_admin_manage', $this_sub_page);
	}
	
	
	/**
	 * Generate User Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function user_subtabs($this_sub_page = FALSE)
	{
		$menu = "";
		
		$menu .= ($this_sub_page == "users") ? Kohana::lang('ui_admin.manage_users') : "<a href=\"".url::site()."admin/users/\">".Kohana::lang('ui_admin.manage_users')."</a>";
		
		$menu .= ($this_sub_page == "users_edit") ? Kohana::lang('ui_admin.manage_users_edit') : "<a href=\"".url::site()."admin/users/edit/\">".Kohana::lang('ui_admin.manage_users_edit')."</a>";
		
		$menu .= ($this_sub_page == "roles") ? Kohana::lang('ui_admin.manage_roles') : "<a href=\"".url::site()."admin/users/roles/\">".Kohana::lang('ui_admin.manage_roles')."</a>";
		
		echo $menu;
		
		// Action::nav_admin_users - Add items to the admin manage navigation tabs
		Event::run('ushahidi_action.nav_admin_users', $this_sub_page);
	}
	
	public static function permissions($user = FALSE, $section = FALSE)
	{
		if ($user AND $section)
		{
			$access = FALSE;
			foreach ($user->roles as $user_role)
			{
				if ($user_role->$section == 1)
				{
					$access = TRUE;
				}
			}
			
			return $access;
		}
		else
		{
			return false;
		}
	}
}
