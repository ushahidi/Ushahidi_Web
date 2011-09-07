<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Addon Manager
 * Install new Plugins & Themes
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Admin
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Addons_Controller extends Admin_Controller {
    
    function __construct()
    {
        parent::__construct();
        $this->template->this_page = 'addons';

        // If this is not a super-user account, redirect to dashboard
        if(!$this->auth->logged_in('admin') && !$this->auth->logged_in('superadmin'))
        {
             url::redirect('admin/dashboard');
        }
    }
    
    public function index()
    {
        url::redirect(url::base().'admin/addons/plugins/');
    }
}
