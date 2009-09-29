<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This main controller for the Admin section 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Admin Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Admin_Controller extends Template_Controller
{
    public $auto_render = TRUE;
	
    // Main template
    public $template = 'admin/layout';
	
    // Cache instance
    protected $cache;

    // Enable auth
    protected $auth_required = FALSE;
	
    protected $user;

    public function __construct()
    {
        parent::__construct();	

	// Load cache
	$this->cache = new Cache;

	// Load session
	$this->session = new Session;

	// Load database
	$this->db = new Database();
		
	$this->auth = new Auth();
	$this->session = Session::instance();
	$this->auth->auto_login();
		
	if (!$this->auth->logged_in('admin')
	    && !$this->auth->logged_in('login'))
	{
	    url::redirect('login');
        }

        //fetch latest version of ushahidi
        $version_number = $this->_fetch_core_version();
        
        $version = $this->_find_core_version($version_number);        
	// Get Session Information
	$user = new User_Model($_SESSION['auth_user']->id);
		
	$this->template->admin_name = $user->name;

        $this->template->version = $version;        
	// Retrieve Default Settings
	$this->template->site_name = Kohana::config('settings.site_name');
	$this->template->mapstraction = Kohana::config('settings.mapstraction');
	$this->template->api_url = Kohana::config('settings.api_url');
		
	// Javascript Header
	$this->template->map_enabled = FALSE;
	$this->template->flot_enabled = FALSE;
	$this->template->colorpicker_enabled = FALSE;
	$this->template->js = '';
		
	// Load profiler
	// $profiler = new Profiler;		
		
    }

    public function index()
    {
        // Send them to the right page
	url::redirect('admin/dashboard');
    }

    public function log_out()
    {
	$auth = new Auth;
	$auth->logout(TRUE);

	url::redirect('login');
    }

    /**
     * find ushahidi core version details
     */
    function _find_core_version($version) {
        if($version > Kohana::config('version.ushahidi_version')){
            return $version;
        
        } else {
            return "";
        }
    }

    /**
     * Fetch latest ushahidi version from a remote instance
     */
    function _fetch_core_version() {
        $version_api = url::base()."/api?task=version&resp=json";
        $json_string = file_get_contents($version_api);
        $json_obj = json_decode($json_string);
        $version_number = $json_obj->payload->version[0]->version;
        return $latest_version = $version_number;
    }
} // End Admin
