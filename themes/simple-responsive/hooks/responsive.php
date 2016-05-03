<?php defined('SYSPATH') or die('No direct script access.');

class responsive {
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
        Event::add('ushahidi_action.themes_add_requirements_pre_theme', array($this, 'add_js'));

		// Only add the events if we are on that controller
		if (Router::$controller == 'main')
		{
			switch (Router::$method)
			{
				case 'index':
					Event::add('ushahidi_action.themes_add_requirements_pre_theme', array($this, 'add_js'));
			}
		}
	}

	public function add_js()
	{
		$themes = Event::$data;

		// Add ushahidi.js extensions
        Requirements::js('themes/simple-responsive/assets/bootstrap/js/bootstrap.min.js');
        Requirements::js('themes/simple-responsive/js/jquery.browser.js');
        Requirements::css('themes/simple-responsive/assets/bootstrap/css/bootstrap.min.css');
        Requirements::css('themes/simple-responsive/assets/bootstrap/css/bootstrap-theme.min.css');
        if ($themes->map_enabled)
		{
		}
	}
}

new responsive;
