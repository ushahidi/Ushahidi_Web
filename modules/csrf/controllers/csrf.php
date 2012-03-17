<?php defined('SYSPATH') or die('No direct script access');
/**
 * CSRF Controller for JS calls that require CSRF token to authenticate
 * requests
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://github.com/ushahidi/Ushahidi_web
 * @category   Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class CSRF_Controller extends Template_Controller {

	/**
	 * Disable auto rendering
	 * @var bool
	 */
	public $auto_render = FALSE;

	/**
	 * View template for the controller
	 * @var string
	 */
	public $template = '';

	/**
	 * Generates a CSRF token. Only honours AJAX requests
	 */
	public function generate_token()
	{
		header("Content-Type: application/json; charset=utf-8");

		$result = array('token' => '');

		if (request::is_ajax())
		{
			$token = csrf::token();

			$result['token'] = $token;

		}

		echo json_encode($result);
	}
}

?>