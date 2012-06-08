<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to view/remove Alerts
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

class Alerts_Controller extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'manage';
		
		// If user doesn't have access, redirect to dashboard
		if ( ! admin::permissions($this->user, "manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}

	/**
	* Lists all alerts in the system
	* @return void
    */
	public function index()
	{
		$this->template->content = new View('admin/manage/alerts/main');
		$this->template->content->title = Kohana::lang('ui_admin.alerts');

		// Is this an SMS or Email Filter?
		if (!empty($_GET['type']))
		{
			$type = $_GET['type'];

			if ($type == '1')
			{ // SMS
				$filter = 'alert_type=1';
			}
			elseif ($type == '2')
			{ // EMAIL
				$filter = 'alert_type=2';
			}
			else
			{ // ALL
				$filter = '1=1';
			}
		}
		else
		{
			$type = "0";
			$filter = '1=1';
		}

		// Are we using an Alert Keyword?
		if (isset($_GET['ak']) AND !empty($_GET['ak']))
		{
			$table_prefix = Kohana::config('database.default.table_prefix');

			//	Brute force input sanitization
			// Phase 1 - Strip the search string of all non-word characters
			$keyword = $_GET['ak'];
			$keyword_raw = preg_replace('#/\w+/#', '', $keyword);

			// Strip any HTML tags that may have been missed in Phase 1
			$keyword_raw = strip_tags($keyword_raw);

			// Phase 3 - Invoke Kohana's XSS cleaning mechanism just incase an outlier wasn't caught
			// in the first 2 steps
			$keyword_raw = $this->input->xss_clean($keyword_raw);
			$keyword_raw = $this->db->escape_str($keyword_raw);

			$filter .= " AND ".$table_prefix."alert_recipient LIKE '%".$keyword_raw."%'";
		}
		else
		{
			$keyword = '';
		}

		// setup and initialize form field names
		$form = array
		(
			'action' => ''
		);
		//	copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";

		if ( $_POST )
		{
			$post = Validation::factory($_POST);

			 //	 Add some filters
			$post->pre_filter('trim', TRUE);

			// Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('action','required', 'alpha', 'length[1,1]');
			if ($post->action =='d')
			{
				$post->add_rules('alert_id.*','required','numeric');
			}
			
			if ($post->validate())
			{
				// Delete Alert
				if ($post->action =='d')
				{

					foreach ($post->alert_id as $item)
					{
						$update = new Alert_Model($item);
						if ($update->loaded)
						{
							$update->delete();
						}
					}

					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
			}
			else
			{
				$errors = arr::overwrite($errors, $post->errors('alerts'));
				$form_error = TRUE;
			}
		}
		
		// Pagination
		$pagination = new Pagination(array(
			'query_string'   => 'page',
			'items_per_page' => $this->items_per_page,
			'total_items'    => ORM::factory('alert')
				->where($filter)
			    ->count_all()
			));

		$alerts = ORM::factory('alert')
			->where($filter)
		    ->find_all($this->items_per_page, $pagination->sql_offset);

		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->alerts = $alerts;
		$this->template->content->type = $type;
		$this->template->content->keyword = $keyword;

		// Javascript Header
		$this->template->js = new View('admin/manage/alerts/alerts_js');
	}
}