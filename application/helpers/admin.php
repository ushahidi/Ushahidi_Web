<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Admin helper class.
 *
 * $Id: valid.php 3917 2009-01-21 03:06:22Z zombor $
 *
 * @package    Distance
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
	    if(Kohana::config('config.enable_mhi') == TRUE && Kohana::config('settings.subdomain') == '')
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
				'stats' => Kohana::lang('ui_admin.stats')
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
        if(Kohana::config('config.enable_mhi') == TRUE AND Kohana::config('settings.subdomain') == '')
		{
			$main_right_tabs = array(
        		'users' => Kohana::lang('ui_admin.users')
        	);
        }
		else
		{
			if($auth AND $auth->logged_in('superadmin'))
			{
	        	$main_right_tabs = array(
	        		'settings/site' => Kohana::lang('ui_admin.settings'),
	        		'manage' => Kohana::lang('ui_admin.manage'),
	        		'users' => Kohana::lang('ui_admin.users')
	        	);
	        }
			elseif($auth AND $auth->logged_in('admin'))
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

		$menu .= ($this_sub_page == "sharing") ? Kohana::lang('ui_main.sharing') : "<a href=\"".url::site()."admin/settings/sharing\">".Kohana::lang('ui_main.sharing')."</a>";

		$menu .= ($this_sub_page == "email") ? Kohana::lang('ui_main.email') : "<a href=\"".url::site()."admin/settings/email\">".Kohana::lang('ui_main.email')."</a>";

		$menu .= ($this_sub_page == "themes") ? Kohana::lang('ui_main.themes') : "<a href=\"".url::site()."admin/settings/themes\">".Kohana::lang('ui_main.themes')."</a>";

		$menu .= ($this_sub_page == "cleanurl") ? Kohana::lang('ui_main.cleanurl'):  "<a href=\"".url::site() ."admin/settings/cleanurl\">".Kohana::lang('ui_main.cleanurl')."</a>";
		echo $menu;
	}


	/**
	 * Generate Manage Sub Tab Menus
     * @param string $this_sub_page
	 * @return string $menu
     */
	public static function manage_subtabs($this_sub_page = FALSE)
	{
		$menu = "";

		$menu .= ($this_sub_page == "categories") ? Kohana::lang('ui_main.categories')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage\">".Kohana::lang('ui_main.categories')."</a>";

		$menu .= ($this_sub_page == "forms") ? Kohana::lang('ui_main.forms')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/forms\">".Kohana::lang('ui_main.forms')."</a>";

		$menu .= ($this_sub_page == "organizations") ? Kohana::lang('ui_main.organizations')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/organizations\">".Kohana::lang('ui_main.organizations')."</a>";

		$menu .= ($this_sub_page == "pages") ? Kohana::lang('ui_main.pages')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/pages\">".Kohana::lang('ui_main.pages')."</a>";

		$menu .= ($this_sub_page == "feeds") ? Kohana::lang('ui_main.news_feeds')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/feeds\">".Kohana::lang('ui_main.news_feeds')."</a>";

		$menu .= ($this_sub_page == "layers") ? Kohana::lang('ui_main.layers')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/layers\">".Kohana::lang('ui_main.layers')."</a>";

		$menu .= ($this_sub_page == "reporters") ? Kohana::lang('ui_main.reporters')."&nbsp;<span>(<a href=\"#add\">Add New</a>)</span>" : "<a href=\"".url::site()."admin/manage/reporters\">".Kohana::lang('ui_main.reporters')."</a>";

		$menu .= ($this_sub_page == "scheduler") ? Kohana::lang('ui_main.scheduler') : "<a href=\"".url::site()."admin/manage/scheduler\">".Kohana::lang('ui_main.scheduler')."</a>";

		echo $menu;
	}

}