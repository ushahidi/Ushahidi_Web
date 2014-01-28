<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Base Controller
 * Enforces basic access control, ie. for private deployments
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

abstract class Controller extends Controller_Core {

	/**
	 * ORM reference for the currently logged in user
	 * @var object
	 */
	protected $user;

	/**
	 * Reference to Auth object 
	 * @var object
	 */
	protected $auth;

	/**
	 * Reference to Database object 
	 * @var object
	 */
	protected $db;
	
	public function __construct()
	{
		parent::__construct();
		
		// Load profiler
		if (Kohana::config('config.enable_profiler'))
		{
			$this->profiler = new Profiler;
		}
		
		$this->auth = Auth::instance();
		
		$this->db = Database::instance();

		// Are we logged in? if not, do we have an auto-login cookie?
		if (! $this->auth->logged_in()) {
			// Try to login with 'remember me' token
			if (! $this->auth->auto_login())
			{
				// Login user in via HTTP AUTH
				$this->auth->http_auth_login();
			}
		}

		// Get session information
		$this->user = Auth::instance()->get_user();

		// Check private deployment access
		$controller_whitelist = array(
			'login',
			'riverid',
			'api',
			// Whitelist all known SMS plugins
			// @todo add hook for plugins to add themselves
			'frontlinesms',
			'smssync',
			'nexmo'
		);

		if (Kohana::config('settings.private_deployment'))
		{
			if (!$this->auth->logged_in('login') AND ! in_array(Router::$controller, $controller_whitelist))
			{
				// Redirect to login form
				url::redirect('login');
			}
		}
		
		// Set default content-type header
		header('Content-type: text/html; charset=UTF-8');
	}
}
