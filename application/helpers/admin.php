<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Admin helper class.
 *
 * @package	   Admin
 * @author	   Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
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
				'manage/pages' => Kohana::lang('ui_main.pages')
			);
		}
		else
		{
			$tabs = array();
			$tabs['dashboard'] = Kohana::lang('ui_admin.dashboard');
			$tabs['reports'] = Kohana::lang('ui_admin.reports');

			if(Kohana::config('settings.checkins'))
			{
				$tabs['checkins'] = Kohana::lang('ui_admin.checkins');
			}

			$tabs['messages'] = Kohana::lang('ui_admin.messages');
			$tabs['stats'] = Kohana::lang('ui_admin.stats');
			$tabs['addons'] = Kohana::lang('ui_admin.addons');
			Event::run('ushahidi_action.nav_admin_main_top', $tabs);
			return $tabs;
		}
	}


	/**
	 * Generate Main Tab Menus (RIGHT SIDE)
	 */
	public static function main_right_tabs($user = FALSE)
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
			// Build the tabs array depending on the role permissions for each section
			if ($user)
			{
				// Check permissions for settings panel
				$main_right_tabs = (Auth::instance()->has_permission('settings', $user))
					? arr::merge($main_right_tabs, array('settings/site' => Kohana::lang('ui_admin.settings')))
					: $main_right_tabs;

				// Check permissions for the manage panel
				$main_right_tabs = (Auth::instance()->has_permission('manage', $user))
					? arr::merge($main_right_tabs, array('manage' => Kohana::lang('ui_admin.manage')))
					: $main_right_tabs;

				// Check permissions for users panel
				$main_right_tabs = (Auth::instance()->has_permission('users', $user))
					? arr::merge($main_right_tabs, array('users' => Kohana::lang('ui_admin.users')))
					: $main_right_tabs;
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

		// We cannot allow cleanurl settings to be changed if MHI is enabled since it modifies a file in the config folder
		if (Kohana::config('config.enable_mhi') == FALSE)
		{
			$menu .= ($this_sub_page == "cleanurl") ? Kohana::lang('ui_main.cleanurl'):	 "<a href=\"".url::site() ."admin/settings/cleanurl\">".Kohana::lang('ui_main.cleanurl')."</a>";

			// SSL subtab
			$menu .= ($this_sub_page == "https") ? Kohana::lang('ui_main.https'):  "<a href=\"".url::site() ."admin/settings/https\">".Kohana::lang('ui_main.https')."</a>";
		}

		$menu .= ($this_sub_page == "api") ? Kohana::lang('ui_main.api') : "<a href=\"".url::site()."admin/settings/api\">".Kohana::lang('ui_main.api')."</a>";

		$menu .= ($this_sub_page == "facebook") ? "Facebook" : "<a href=\"".url::site()."admin/settings/facebook\">Facebook</a>";

		$menu .= ($this_sub_page == "externalapps") ? Kohana::lang('ui_main.external_apps') : "<a href=\"".url::site()."admin/settings/externalapps\">".Kohana::lang('ui_main.external_apps')."</a>";

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

		$menu .= ($this_sub_page == "blocks") ? Kohana::lang('ui_admin.blocks') : "<a href=\"".url::site()."admin/manage/blocks\">".Kohana::lang('ui_admin.blocks')."</a>";

		$menu .= ($this_sub_page == "forms") ? Kohana::lang('ui_main.forms') : "<a href=\"".url::site()."admin/manage/forms\">".Kohana::lang('ui_main.forms')."</a>";

		$menu .= ($this_sub_page == "pages") ? Kohana::lang('ui_main.pages') : "<a href=\"".url::site()."admin/manage/pages\">".Kohana::lang('ui_main.pages')."</a>";

		$menu .= ($this_sub_page == "feeds") ? Kohana::lang('ui_main.news_feeds') : "<a href=\"".url::site()."admin/manage/feeds\">".Kohana::lang('ui_main.news_feeds')."</a>";

		$menu .= ($this_sub_page == "layers") ? Kohana::lang('ui_main.layers') : "<a href=\"".url::site()."admin/manage/layers\">".Kohana::lang('ui_main.layers')."</a>";

		$menu .= ($this_sub_page == "scheduler") ? Kohana::lang('ui_main.scheduler') : "<a href=\"".url::site()."admin/manage/scheduler\">".Kohana::lang('ui_main.scheduler')."</a>";

		$menu .= ($this_sub_page == "publiclisting") ? Kohana::lang('ui_admin.public_listing') : "<a href=\"".url::site()."admin/manage/publiclisting\">".Kohana::lang('ui_admin.public_listing')."</a>";

		$menu .= ($this_sub_page == "actions") ? Kohana::lang('ui_admin.actions') : "<a href=\"".url::site()."admin/manage/actions\">".Kohana::lang('ui_admin.actions')."</a>";

		$menu .= ($this_sub_page == "badges") ? Kohana::lang('ui_main.badges') : "<a href=\"".url::site()."admin/manage/badges\">".Kohana::lang('ui_main.badges')."</a>";

		$menu .= ($this_sub_page == "alerts") ? Kohana::lang('ui_admin.alerts') : "<a href=\"".url::site()."admin/manage/alerts\">".Kohana::lang('ui_admin.alerts')."</a>";

		echo $menu;

		// Action::nav_admin_manage - Add items to the admin manage navigation tabs
		Event::run('ushahidi_action.nav_admin_manage', $this_sub_page);
	}


	/**
	 * Generate User Sub Tab Menus
	 * @param string $this_sub_page
	 * @param boolean $display_roles
	 * @return string $menu
	 */
	public static function user_subtabs($this_sub_page = FALSE, $display_roles = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "users") ? Kohana::lang('ui_admin.manage_users') : "<a href=\"".url::site()."admin/users/\">".Kohana::lang('ui_admin.manage_users')."</a>";

		$menu .= ($this_sub_page == "users_edit") ? Kohana::lang('ui_admin.manage_users_edit') : "<a href=\"".url::site()."admin/users/edit/\">".Kohana::lang('ui_admin.manage_users_edit')."</a>";

		// Only display the link for roles where $display_roles = TRUE
		if ($display_roles)
		{
			$menu .= ($this_sub_page == "roles") ? Kohana::lang('ui_admin.manage_roles') : "<a
			href=\"".url::site()."admin/users/roles/\">".Kohana::lang('ui_admin.manage_roles')."</a>";
		}

		echo $menu;

		// Action::nav_admin_users - Add items to the admin manage navigation tabs
		Event::run('ushahidi_action.nav_admin_users', $this_sub_page);
	}
	
	/**
	 * Legacy permissions check
	 * @deprecated Use Auth::has_permission() instead.
	 */
	public function permissions($user = FALSE, $permission = FALSE)
	{
		Kohana::log('alert', 'admin::permissions() in deprecated and replaced by Auth::has_permission()');
		return Auth::instance()->has_permission($permission, $user);
	}
	
	/**
	 * Legacy admin access check
	 * @deprecated Use Auth::admin_access() instead.
	 */
	public function admin_access($user = FALSE)
	{
		Kohana::log('alert', 'admin::admin_access() in deprecated and replaced by Auth::admin_access()');
		return Auth::instance()->admin_access($user);
	}
}
