<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This main controller for the Admin section
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * Admin_Controller
 * @author	   Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Admin_Controller extends Template_Controller {
	/**
	 * Automatically display the views
	 * @var bool
	 */
	public $auto_render = TRUE;

	/**
	 * Path to the parent view for the pages in the admin console
	 * @var string
	 */
	public $template = 'admin/layout';

	/**
	 * Cache instance
	 * @var Cache
	 */
	protected $cache;

	/**
	 * Whether authentication is required
	 * @var bool
	 */
	protected $auth_required = FALSE;

	/**
	 * ORM reference for the currently logged in user
	 * @var object
	 */
	protected $user;

	/**
	 * Configured table prefix in the database config file
	 * @var string
	 */
	protected $table_prefix;

	/**
	 * Release name of the platform
	 * @var string
	 */
	protected $release;

	/**
	 * No. of items to display per page - to be used for paginating lists
	 * @var int
	 */
	protected $items_per_page;

	/**
	 * Auth instance for the admin controllers
	 * @var Auth
	 */
	protected $auth;


	public function __construct()
	{
		parent::__construct();

		// Load cache
		$this->cache = new Cache;

		// Load session
		$this->session = new Session;

		// Load database
		$this->db = new Database();

		$this->session = Session::instance();

		$this->auth = Auth::instance();

		// Themes Helper
		$this->themes = new Themes();

		// Admin is not logged in, or this is a member (not admin)
		if ( ! $this->auth->logged_in('login'))
		{
			url::redirect('login');
		}

		// Check if user has the right to see the admin panel
		if( ! $this->auth->admin_access())
		{
			// This user isn't allowed in the admin panel
			url::redirect('/');
		}

		// Get the authenticated user
		$this->user = $this->auth->get_user();

		// Set Table Prefix
		$this->table_prefix = Kohana::config('database.default.table_prefix');

		// Get the no. of items to display setting
		$this->items_per_page = (int) Kohana::config('settings.items_per_page_admin');

		$this->template->admin_name = $this->user->name;

		// Retrieve Default Settings
		$this->template->site_name = Kohana::config('settings.site_name');
		$this->template->mapstraction = Kohana::config('settings.mapstraction');
		$this->template->api_url = Kohana::config('settings.api_url');

		// Javascript Header
		$this->template->map_enabled = FALSE;
		$this->template->datepicker_enabled = FALSE;
		$this->template->flot_enabled = FALSE;
		$this->template->treeview_enabled = FALSE;
		$this->template->protochart_enabled = FALSE;
		$this->template->colorpicker_enabled = FALSE;
		$this->template->editor_enabled = FALSE;
		$this->template->tablerowsort_enabled = FALSE;
		$this->template->json2_enabled = FALSE;
		$this->template->js = '';
		$this->template->form_error = FALSE;

		// Initialize some variables for raphael impact charts
		$this->template->raphael_enabled = FALSE;
		$this->template->impact_json = '';

		// Generate main tab navigation list.
		$this->template->main_tabs = admin::main_tabs();

		// Generate sub navigation list (in default layout, sits on right side).
		$this->template->main_right_tabs = admin::main_right_tabs($this->user);

		$this->template->this_page = "";

		// Header Nav
		$header_nav = new View('header_nav');
		$this->template->header_nav = $header_nav;
		$this->template->header_nav->loggedin_user = $this->user;
		$this->template->header_nav->loggedin_role = $this->user->dashboard();
		$this->template->header_nav->site_name = Kohana::config('settings.site_name');

		// Header and Footer Blocks
		$this->template->header_block = $this->themes->admin_header_block();
		$this->template->footer_block = $this->themes->footer_block();

		// Language switcher
		$this->template->languages = $this->themes->languages();
	}

	public function index()
	{
		// Send them to the right page
		if (Kohana::config('config.enable_mhi') == TRUE && Kohana::config('settings.subdomain') == '')
		{
			url::redirect('admin/mhi');
		}
		else
		{
			url::redirect('admin/dashboard');
		}
	}


    /**
     * Checks version sequence parts
     *
     * @param string release_version - The version released.
     * @param string version_ushahidi - The version of ushahidi installed.
     *
     * @return boolean
     */
	private function _new_or_not($release_version=NULL, $version_ushahidi=NULL)
	{
		if ($release_version AND $version_ushahidi)
		{
			// Split version numbers xx.xx.xx
			$remote_version = explode(".", $release_version);
			$local_version = explode(".", $version_ushahidi);

			// Check first part .. if its the same, move on to next part
			if (isset($remote_version[0]) AND isset($local_version[0])
				AND (int) $remote_version[0] > (int) $local_version[0])
			{
				return TRUE;
			}

			// Check second part .. if its the same, move on to next part
			if (isset($remote_version[1]) AND isset($local_version[1])
				AND (int) $remote_version[1] > (int) $local_version[1])
			{
				return TRUE;
			}

			// Check third part
			if (isset($remote_version[2]) AND (int) $remote_version[2] > 0)
			{
				if ( ! isset($local_version[2]))
				{
					return TRUE;
				}
				elseif( (int) $remote_version[2] > (int) $local_version[2] )
				{
					return TRUE;
				}
			}
		}

		return TRUE;
	}


} // End Admin

