<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Admin helper class.
 *
 * @package    Admin
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class members_Core {

	/**
	 * Generate Main Tab Menus
	 * @return array array of all the main tabs
	 */
	public static function main_tabs()
	{
		return array(
			'dashboard' => Kohana::lang('ui_admin.dashboard'),
			'reports' => Kohana::lang('ui_admin.my_reports'),
			'checkins' => Kohana::lang('ui_admin.my_checkins'),
			'alerts' => Kohana::lang('ui_admin.my_alerts'),
			'private' => Kohana::lang('ui_admin.private_messages')
		);
	}
	
	/**
     * Generate Report Sub Tab Menus
     * @param string $this_sub_page
     * @return string $menu
     */
    public static function reports_subtabs($this_sub_page = FALSE)
    {
        $menu = "";

        $menu .= ($this_sub_page == "view") ? Kohana::lang('ui_main.view_reports') : "<a href=\"".url::base()."members/reports\">".Kohana::lang('ui_main.view_reports')."</a>";

        $menu .= ($this_sub_page == "edit") ? Kohana::lang('ui_main.create_report') : "<a href=\"".url::base()."members/reports/edit\">".Kohana::lang('ui_main.create_report')."</a>";

        echo $menu;
        
        // Action::nav_admin_reports - Add items to the admin reports navigation tabs
        Event::run('ushahidi_action.nav_members_reports', $this_sub_page);
    }


	/**
     * Generate Private Messages Sub Tab Menus
     * @param string $this_sub_page
     * @return string $menu
     */
    public static function private_subtabs($this_sub_page = FALSE)
    {
        $menu = "";

        $menu .= ($this_sub_page == "view") ? Kohana::lang('ui_admin.view_private') : "<a href=\"".url::base()."members/private\">".Kohana::lang('ui_admin.view_private')."</a>";

        $menu .= ($this_sub_page == "new") ? Kohana::lang('ui_admin.new_private') : "<a href=\"".url::base()."members/private/send\">".Kohana::lang('ui_admin.new_private')."</a>";

        echo $menu;
        
        // Action::nav_members_private - Add items to the members private messages navigation tabs
        Event::run('ushahidi_action.nav_members_private', $this_sub_page);
    }


	/**
     * Generate Alerts Sub Tab Menus
     * @param string $this_sub_page
     * @return string $menu
     */
    public static function alerts_subtabs($this_sub_page = FALSE)
    {
        $menu = "";

        $menu .= ($this_sub_page == "view") ? Kohana::lang('ui_admin.my_alerts') : "<a href=\"".url::base()."members/alerts\">".Kohana::lang('ui_admin.my_alerts')."</a>";

        //$menu .= ($this_sub_page == "edit") ? Kohana::lang('ui_admin.new_alert') : "<a href=\"".url::base()."members/alerts/edit\">".Kohana::lang('ui_admin.new_alert')."</a>";

        echo $menu;
        
        // Action::nav_members_alerts - Add items to the members alerts navigation tabs
        Event::run('ushahidi_action.nav_members_alerts', $this_sub_page);
    }
	
	
	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	public function gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() )
	{
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img )
		{
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}
}