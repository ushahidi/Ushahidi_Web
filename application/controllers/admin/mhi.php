<?php defined('SYSPATH') or die('No direct script access.');
/**
 * MHI Controller.
 * This controller covers the MHI admin interface.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Reports Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Mhi_Controller extends Admin_Controller
{
	function __construct()
	{
		parent::__construct();

		$this->template->this_page = 'mhi';
	}


	/**
	* Lists the reports.
    * @param int $page
    */
	function index($page = 1)
	{
		$this->template->content = new View('admin/mhi');
		$this->template->content->title = Kohana::lang('ui_admin.multiple_hosted_instances');

		$this->template->content->domain_name = $_SERVER['HTTP_HOST'];

		// check, has the form been submitted?
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		if ($_POST)
		{
			$post = Validation::factory($_POST);

	         //  Add some filters
			$post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			$post->add_rules('instance_id.*','required','numeric');

			if ($post->validate()) {
				if ($post->action == 'a') { // Approve Action
					foreach($post->instance_id as $item) {
						$update = new Mhi_Site_Model($item);
						if ($update->loaded == true) {
							$update->site_active = '1';
							$update->save();
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.approved'));
				} elseif ($post->action == 'u') { // Unapprove Action
					foreach($post->instance_id as $item) {
						$update = new Mhi_Site_Model($item);
						if ($update->loaded == true) {
							$update->site_active = '0';
							$update->save();
						}
					}
					$form_action = strtoupper(Kohana::lang('ui_admin.unapproved'));
				} elseif ($post->action == 'd'){	// Delete Action
					foreach($post->instance_id as $item){
						$update = new Mhi_Site_Model($item);
						if ($update->loaded == true){
							$update->delete();
						}
					}
					$form_action = Kohana::lang('ui_admin.deleted');
				}
				$form_saved = TRUE;
			} else {
				$form_error = TRUE;
			}

		}
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;

		// Status is the "Show All/Pending/Approved tabs'
		if (!empty($_GET['status']))
		{
			$status = strtolower($_GET['status']);

			if ($status == 'a'){
				$filter = 'site_active = 1';
			}elseif ($status == 'p'){
				$filter = 'site_active = 0';
			}
		}else{
			$status = '0';
			$filter = '1=1'; // Using 1=1 is a way to preserve the "where" statement to reduce code complexity
		}
		$this->template->content->status = $status;

		// Pagination
		$pagination = new Pagination(array(
			'query_string'	=> 'page',
			'items_per_page' => (int) Kohana::config('settings.items_per_page_admin'),
			'total_items'	=> ORM::factory('mhi_site')->where($filter)->count_all()
		));
		$this->template->content->pagination = $pagination;

		$db = new Database();
		$db->select('mhi_site.*, mhi_users.email, mhi_users.firstname, mhi_users.lastname');
		$db->from('mhi_site');
		$db->join('mhi_users', 'mhi_users.id', 'mhi_site.user_id');
		$db->where($filter);
		$db->orderby('mhi_site.site_dateadd', 'desc');
		$db->limit((int) Kohana::config('settings.items_per_page_admin'), $pagination->sql_offset);
		$instances = $db->get();
		$this->template->content->instances = $instances;

		$this->template->content->total_items = $pagination->total_items;

		// Javascript Header
		$this->template->js = new View('admin/mhi_js');
	}

	/**
	* Lists the activity.
    * @param int $page
    */
	function activity($action_id=FALSE)
	{
		$this->template->content = new View('admin/mhi_activity');
		$this->template->content->activity = Mhi_Log_Model::get_actions(100,0,$action_id);
		$this->template->content->log_actions = Mhi_Log_Model::get_log_actions();
		$this->template->content->current_log_action_id = $action_id;
	}

	/**
	* Lists the reports.
    * @param int $page
    */
	function updatelist()
	{
		$this->template->content = new View('admin/mhi_updatelist');

		$settings = kohana::config('settings');

		if (isset($_POST['mhiupdatedb']))
		{
			Mhi_Site_Database_Model::update_db($_POST['db']);
		}

		if (isset($_GET['mhimassupdatedb']) AND isset($_GET['from_version']))
		{
			Mhi_Site_Database_Model::mass_update_db($_GET['mhimassupdatedb'],$_GET['from_version']);
		}

		$this->template->content->db_versions = Mhi_Site_Model::get_db_versions();
		asort($this->template->content->db_versions);
		$this->template->content->current_version = $settings['db_version'];
	}

	/**
	* MHI Settings.
    * @param int $page
    */
	function settings()
	{
		$this->template->content = new View('admin/mhi_settings');

		// setup and initialize form field names
		$this->template->content->form = array
		(
			'google_analytics' => ''
		);

	    //  Copy the form as errors, so the errors will be stored with keys
        //  corresponding to the form field names
		$this->template->content->errors = $this->template->content->form;
		$this->template->content->form_error = FALSE;
		$this->template->content->form_saved = FALSE;

		// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
            // Instantiate Validation, use $post, so we don't overwrite $_POST
            // fields with our own things
			$post = new Validation($_POST);

	        // Add some filters
			$post->pre_filter('trim', TRUE);

			// Validation Rules
			$post->add_rules('google_analytics','length[0,20]');

			// Test to see if things passed the rule checks
	        if ($post->validate())
	        {

	        	// Yes! everything is valid
				$settings = new Settings_Model(1);
	        	$settings->google_analytics = $post->google_analytics;
	        	$settings->date_modify = date("Y-m-d H:i:s",time());
				$settings->save();

				$this->template->content->form_saved = TRUE;

				$this->template->content->form = arr::overwrite($this->template->content->form, $post->as_array());

	        }else{

	        	// repopulate the form fields
	            $this->template->content->form = arr::overwrite($this->template->content->form, $post->as_array());

	            // populate the error fields, if any
	            $this->template->content->errors = arr::overwrite($this->template->content->errors, $post->errors('settings'));
				$this->template->content->form_error = TRUE;

	        }
		}else{

			// Retrieve Current Settings
			$settings = ORM::factory('settings', 1);
			$this->template->content->form = array
			(
				'google_analytics' => $settings->google_analytics
			);

		}

	}

}
