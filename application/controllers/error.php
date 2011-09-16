<?php defined('SYSPATH') or die('No direct script access.');
/**
* Custom 404 Error Page Controller
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

class Error_Controller extends Controller
{
    /**
     * Render Custom 404 Error Page
     */
    public function error_404()
    {
        Header("HTTP/1.0 404 Not Found");
		
        $this->layout = new View('error');
        $this->layout->title = Kohana::lang('ui_admin.page_not_found');
        $this->layout->content = Kohana::lang('ui_admin.page_not_found_message');
        $this->layout->render(true);
    }
}
