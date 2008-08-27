<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Kohana Controller class. The controller class must be extended to work
 * properly, so this class is defined as abstract.
 *
 * $Id: Controller.php 3281 2008-08-06 16:13:58Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Controller_Core {

	// Allow all controllers to run in production by default
	const ALLOW_PRODUCTION = TRUE;

	/**
	 * Loads URI, and Input into this controller.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		if (Kohana::$instance == NULL)
		{
			// Set the instance to the first controller loaded
			Kohana::$instance = $this;
		}

		// URI should always be available
		$this->uri = URI::instance();

		// Input should always be available
		$this->input = Input::instance();
	}

	/**
	 * Handles methods that do not exist.
	 *
	 * @param   string  method name
	 * @param   array   arguments
	 * @return  void
	 */
	public function __call($method, $args)
	{
		// Default to showing a 404 page
		Event::run('system.404');
	}

	/**
	 * Includes a View within the controller scope.
	 *
	 * @param   string  view filename
	 * @param   array   array of view variables
	 * @return  string
	 */
	public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
	{
		if ($kohana_view_filename == '')
			return;

		// Buffering on
		ob_start();

		// Import the view variables to local namespace
		extract($kohana_input_data, EXTR_SKIP);

		// Views are straight HTML pages with embedded PHP, so importing them
		// this way insures that $this can be accessed as if the user was in
		// the controller, which gives the easiest access to libraries in views
		include $kohana_view_filename;

		// Fetch the output and close the buffer
		return ob_get_clean();
	}

} // End Controller Class