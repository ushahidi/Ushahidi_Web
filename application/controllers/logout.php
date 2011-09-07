<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles login requests.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Logout_Controller extends Controller {
	
	protected $destination = 'login';

    public function __construct()
    {
        parent::__construct();
    }
	
    public function index()
    {
		$auth = new Auth;
		$auth->logout(TRUE);

		url::redirect($this->destination);	
	}
	
	public function front()
    {
    	/**
	     * If the login page is for the frontend, hit this function first.
	     */
		
		$this->destination = '/';
		$this->index();
		
    }
}
