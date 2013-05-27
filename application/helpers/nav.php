<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Front-End Nav helper class.
 *
 * @package    Nav
 * @author     Ushahidi Team
 * @copyright  (c) 2008 Ushahidi Team
 * @license    http://www.ushahidi.com/license.html
 */
class nav_Core {
	
	/**
	 * Generate Main Tabs
     * @param string $this_page
     * @param array $dontshow
     */
	public static function main_tabs($this_page = FALSE, $dontshow = FALSE)
	{
		$menu_items = array();

		if( ! is_array($dontshow))
		{
			// Set $dontshow as an array to prevent errors
			$dontshow = array();
		}
		
		// Home
		if( ! in_array('home',$dontshow))
		{
			$menu_items[] = array( 
				'page' => 'home',
				'url' => url::site('main'),
				'name' => Kohana::lang('ui_main.home')
			);
		}

		// Reports List
		if( ! in_array('reports',$dontshow))
		{
			$menu_items[] = array( 
				'page' => 'reports',
				'url' => url::site('reports'),
				'name' => Kohana::lang('ui_main.reports')
			);
		 }
		
		// Reports Submit
		if( ! in_array('reports_submit',$dontshow))
		{
			if (Kohana::config('settings.allow_reports'))
			{
				$menu_items[] = array( 
					'page' => 'reports_submit',
					'url' => url::site('reports/submit'),
					'name' => Kohana::lang('ui_main.submit')
				);
			}
		}
		
		// Alerts
		if(! in_array('alerts',$dontshow))
		{
			if(Kohana::config('settings.allow_alerts'))
			{
				$menu_items[] = array( 
					'page' => 'alerts',
					'url' => url::site('alerts'),
					'name' => Kohana::lang('ui_main.alerts')
				);
			}
		}
		
		// Contacts
		if( ! in_array('contact',$dontshow))
		{
			if (Kohana::config('settings.site_contact_page') AND Kohana::config('settings.site_email') != "")
			{
				$menu_items[] = array( 
					'page' => 'contact',
					'url' => url::site('contact'),
					'name' => Kohana::lang('ui_main.contact')
				);	
			}
		}
		
		// Custom Pages
		
		if( ! in_array('pages',$dontshow))
		{
			$pages = ORM::factory('page')->where('page_active', '1')->find_all();
			foreach ($pages as $page)
			{
				if( ! in_array('page/'.$page->id,$dontshow))
				{
					$menu_items[] = array( 
						'page' => 'page_'.$page->id,
						'url' => url::site('page/index/'.$page->id),
						'name' => $page->page_tab
					);
				}
			}
		}

		Event::run('ushahidi_filter.nav_main_tabs', $menu_items);

		foreach( $menu_items as $item )
		{
			$active = ($this_page == $item['page']) ? ' class="active"' : '';
			echo '<li><a href="'.$item['url'].'"'.$active.'>'.$item['name'].'</a></li>';
		}

		// Action::nav_admin_reports - Add items to the admin reports navigation tabs
		Event::run('ushahidi_action.nav_main_top', $this_page);
	}
	
	
}
