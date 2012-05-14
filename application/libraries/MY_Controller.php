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
	public function __construct()
	{
		parent::__construct();

		$this->auth = new Auth();
		// Are we logged in? if not, do we have an auto-login cookie?
		if (! $this->auth->logged_in()) {
			$this->auth->auto_login();
		}
		
		// Chceck private deployment access
		$controller_whitelist = array(
			'login',
			'riverid'
		);

		if (Kohana::config('settings.private_deployment'))
		{
			if (!$this->auth->logged_in('login') AND ! in_array(Router::$controller, $controller_whitelist))
			{
				url::redirect('login');
			}
		}
	}
}
