<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This main controller for the Members section 
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Members_Controller extends Template_Controller
{
	public $auto_render = TRUE;

	// Main template
	public $template = 'members/layout';

	// Cache instance
	protected $cache;

	// Enable auth
	protected $auth_required = FALSE;
	
	// User Object
	protected $user;

	// Table Prefix
	protected $table_prefix;
    
    protected $release;

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
		
		if ( ! $this->auth->logged_in('login') OR ! $this->auth->logged_in('member'))
		{
			url::redirect('members/login');
		}

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Get Session Information
		$this->user = new User_Model($_SESSION['auth_user']->id);

		$this->template->admin_name = $this->user->name;
		
		// Retrieve Default Settings
		$this->template->site_name = Kohana::config('settings.site_name');
		$this->template->api_url = Kohana::config('settings.api_url');

		// Javascript Header
		$this->template->map_enabled = FALSE;
		$this->template->flot_enabled = FALSE;
		$this->template->treeview_enabled = FALSE;
		$this->template->protochart_enabled = FALSE;
		$this->template->colorpicker_enabled = FALSE;
		$this->template->editor_enabled = FALSE;
		$this->template->tablerowsort_enabled = FALSE;
		$this->template->autocomplete_enabled = FALSE;
		$this->template->json2_enabled = FALSE;
		$this->template->js = '';
		$this->template->form_error = FALSE;

		// Initialize some variables for raphael impact charts
		$this->template->raphael_enabled = FALSE;
		$this->template->impact_json = '';

		// Generate main tab navigation list.
		$this->template->main_tabs = members::main_tabs();

		$this->template->this_page = "";

		// Load profiler
		// $profiler = new Profiler;	
    }

	public function index()
	{
		url::redirect('members/dashboard');
	}

	public function log_out()
	{
		$auth = new Auth;
		$auth->logout(TRUE);

		url::redirect('members/login');
	}

} // End Admin

