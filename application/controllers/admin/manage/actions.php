<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Actions Triggers Controller
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

class Actions_Controller extends Admin_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->template->this_page = 'actions';

		// If user doesn't have access, redirect to dashboard
		if ( ! $this->auth->has_permission("manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}


	function index()
	{
		$this->template->content = new View('admin/manage/actions/main');
		$this->template->content->title = Kohana::lang('ui_admin.actions');

		$this->template->map_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;

		$this->template->js = new View('admin/manage/actions/actions_js');
		$this->template->js->default_map = Kohana::config('settings.default_map');
		$this->template->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->template->js->latitude = Kohana::config('settings.default_lat');
		$this->template->js->longitude = Kohana::config('settings.default_lon');

		// TODO: Figure out what to do with this
		$this->template->js->incident_zoom = array();
		$this->template->js->geometries = array();

		$trigger_options = $this->_trigger_options();
		$response_options = $this->_response_options();
		$trigger_advanced_options = $this->_trigger_advanced_options();
		$advanced_option_areas = $this->_advanced_option_areas();
		$response_advanced_options = $this->_response_advanced_options();
		$response_advanced_option_areas = $this->_response_advanced_option_areas();
		$trigger_allowed_responses = $this->_trigger_allowed_responses();

		// Setup and initialize form field names
		$form = array(
			'geometry' => '',
			'action_trigger' => '',
			'action_user' => '',
			'action_location_specific' => '',
			'action_keywords' => '',
			'action_category' => array(),
			'action_on_specific_count' => '',
			'action_on_specific_count_collective' => '',
			'action_days_of_the_week' => array(),
			'action_specific_days' => array(),
			'action_between_times_hour_1' => '',
			'action_between_times_hour_2' => '',
			'action_between_times_minute_1' => '',
			'action_between_times_minute_2' => '',
			'action_response' => '',
			'action_email_send_address' => '',
			'action_email_send_address_specific' => '',
			'action_email_subject' => '',
			'action_email_body' => '',
			'action_add_category' => array(),
			'action_verify' => '',
			'action_badge' => ''
			);
			
		// Process form submission
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			// Trim all of the fields to get rid of errant spaces
			$post->pre_filter('trim', TRUE);
			
			$expected_qualifier_fields = $trigger_advanced_options[$post['action_trigger']];
			$expected_response_fields = $response_advanced_options[$post['action_response']];
			$expected_fileds = array_merge($expected_qualifier_fields,$expected_response_fields);

			// Since our form is dynamic, we need to set validation dynamically
			foreach($expected_fileds as $field)
			{
				$this->_form_field_rules($field,$post);
			}

			if( $post->validate() )
			{
				$qualifiers = array();
				foreach($expected_qualifier_fields as $field){
					$form_field = 'action_'.$field;

					// 1. Standard field population
					if( isset($post->$form_field) )
					{
						$qualifiers[$field] = $post->$form_field;
					}

					// 2. Check additional field population

					// Populate additional geometry field
					if($field == 'location' && $post->$form_field == 'specific')
					{
						// Add geometry if this is a specific location
						$qualifiers['geometry'] = $post->geometry;
					}

					// Populate additional specific count collective boolean
					if($field == 'on_specific_count')
					{
						// Grab if we are counting everyone or just the individual users themselves
						$qualifiers['on_specific_count_collective'] = $post->action_on_specific_count_collective;
					}

					// Change the specific_days field to an array of timestamps
					if($field == 'specific_days')
					{
						// Grab if we are counting everyone or just the individual users themselves
						$qualifiers['specific_days'] = explode(',',$qualifiers['specific_days']);
						foreach($qualifiers['specific_days'] as $key => $specific_day){
							$qualifiers['specific_days'][$key] = strtotime($specific_day);
						}
						if($qualifiers['specific_days'][0] == false) {
							// Just get rid of it if we aren't using it
							unset($qualifiers['specific_days']);
						}
					}

					// Grab dropdowns for between_times
					if($field == 'between_times')
					{
						// Do everything for between times here

						if($post->action_between_times_hour_1 != 0 OR $post->action_between_times_minute_1 != 0
							OR $post->action_between_times_hour_2 != 0 OR $post->action_between_times_minute_2 != 0)
						{
							// We aren't all zeroed out so the user is not ignoring between_times. Now we need
							//   to calculate seconds into the day for each and put the lower count in the first
							//   variable and the higher in the second so the check in the hook doesn't have to
							//   do so much work. Also, set between_times to true so the hook knows to check it.

							$qualifiers['between_times'] = 1;

							$time1 = ((int)$post->action_between_times_hour_1 * 3600) + ((int)$post->action_between_times_minute_1 * 60);
							$time2 = ((int)$post->action_between_times_hour_2 * 3600) + ((int)$post->action_between_times_minute_2 * 60);

							if($time1 < $time2){
								$qualifiers['between_times_1'] = $time1;
								$qualifiers['between_times_2'] = $time2;
							}else{
								$qualifiers['between_times_1'] = $time2;
								$qualifiers['between_times_2'] = $time1;
							}

						}else{
							// Between_times is being ignored, set it that way here
							$qualifiers['between_times'] = 0;
						}

					}

				}

				$qualifiers = serialize($qualifiers);

				$response_vars = array();
				foreach($expected_response_fields as $field){
					$form_field = 'action_'.$field;
					if( isset($post->$form_field) )
					{
						$r_var = $post->$form_field;

						if($field == 'email_send_address' AND $post->$form_field == '1'){
							// Then set as the specific email address so we know where to send it
							$r_var = $post->action_email_send_address_specific;
						}

						// This is the array we're building to pass on the data we need
						//  to perform the response when qualifiers are all passed
						$response_vars[$field] = $r_var;
					}
				}

				$response_vars = serialize($response_vars);

				$action = ORM::factory('actions', $post->id);
				$action->action = $post->action_trigger;
				$action->qualifiers = $qualifiers;
				$action->response = $post->action_response;
				$action->response_vars = $response_vars;
				$action->active = 1;
				$action->save();

			}else{
				// TODO: Proper Validation
				$errors = $post->errors();
				foreach ($errors as $key => $val)
				{
					echo $key.' failed rule '.$val.'<br />';
				}
			}

		}

	    // Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$sharing_id = "";

		// Defined actions by the user that already exist in the system
		$this->template->content->actions = $this->_get_actions();
		$this->template->content->total_items = $this->template->content->actions->count();

		$this->template->content->trigger_options = $trigger_options;
		$this->template->content->response_options = $response_options;

		$this->template->content->trigger_advanced_options = $trigger_advanced_options;
		$this->template->content->advanced_option_areas = $advanced_option_areas;
		$this->template->content->response_advanced_options = $response_advanced_options;
		$this->template->content->response_advanced_option_areas = $response_advanced_option_areas;
		$this->template->content->trigger_allowed_responses = $trigger_allowed_responses;

		// Build user options list
		$this->template->content->user_options = $this->_user_options();

		// Grab badges for dropdown
		$this->template->content->badges = Badge_Model::badge_names();

		// Grab feeds for dropdown
		$this->template->content->feeds = ORM::factory('feed')->find_all()->select_list('id','feed_name');

		// Timezone
		$this->template->content->site_timezone = Kohana::config('settings.site_timezone');

		// Days of the week
		$this->template->content->days = array('mon' => Kohana::lang('datetime.monday.full'),
												'tue' => Kohana::lang('datetime.tuesday.full'),
												'wed' => Kohana::lang('datetime.wednesday.full'),
												'thu' => Kohana::lang('datetime.thursday.full'),
												'fri' => Kohana::lang('datetime.friday.full'),
												'sat' => Kohana::lang('datetime.saturday.full'),
												'sun' => Kohana::lang('datetime.sunday.full'));

		$this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->errors = $errors;

		// Enable date picker
		$this->template->datepicker_enabled = TRUE;
	}

	function changestate(){
		if ($_POST)
		{
			$post = Validation::factory($_POST);

			// Trim all of the fields to get rid of errant spaces
			$post->pre_filter('trim', TRUE);
			$post->add_rules('action_id','required', 'digit');
			$post->add_rules('action_switch_to','required');

			if( $post->validate())
			{
				if ($post->action_switch_to == 'de')
				{
					ORM::factory('actions',$post->action_id)->delete();
				}
				else
				{
					$active = (int)($post->action_switch_to);
					
					$action = ORM::factory('actions',$post->action_id);
					$action->active = $active;
					$action->save();
				}
			}
		}

		// This controller doesn't display anything so send the user back

		url::redirect(url::site().'admin/manage/actions');

	}

	public function _form_field_rules($field,&$post){
		switch ($field) {
			case 'user':
				$post->add_rules('action_user','required', 'digit');
				break;
			case 'location':
				$post->add_rules('action_location','required');

				if($post->action_location == 'specific')
				{
					$post->add_rules('geometry','required');
				}
				break;
			case 'email_send_address':
				$post->add_rules('action_email_send_address','required','digit');
				break;
			case 'on_specific_count':
				$post->add_rules('action_on_specific_count', 'digit');
				break;
			default:
				return false;
		}
	}

	public function _advanced_option_areas()
	{
		return Kohana::config('actions.advanced_option_areas');
	}

	public function _trigger_advanced_options()
	{
		return Kohana::config('actions.trigger_advanced_options');
	}

	public function _response_advanced_option_areas()
	{
		return Kohana::config('actions.response_advanced_option_areas');
	}

	public function _response_advanced_options()
	{
		return Kohana::config('actions.response_advanced_options');
	}

	public function _trigger_options()
	{
		$trigger_options = array('0'=>Kohana::lang('ui_admin.please_select'));
		return array_merge($trigger_options,Kohana::config('actions.trigger_options'));
	}

	public function _trigger_allowed_responses(){
		return Kohana::config('actions.trigger_allowed_responses');
	}

	public function _response_options()
	{
		return Kohana::config('actions.response_options');
	}

	public function _user_options()
	{
		$users = ORM::factory('user')
					->orderby('name', 'asc')
					->find_all();
		$user_options = array('0'=>'Anyone');
		foreach($users as $user)
		{
			$user_options[$user->id] = $user->name;
		}
		return $user_options;
	}

	public function _get_actions()
	{
		return $this->db->from('actions')->get();
	}

}
