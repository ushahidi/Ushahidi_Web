<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Sharing Controller
 * Add/Edit Ushahidi Instance Shares
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Forms Controller  
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
		if ( ! admin::permissions($this->user, "manage"))
		{
			url::redirect(url::site().'admin/dashboard');
		}
	}
	
	
	function index()
	{
		$this->template->content = new View('admin/actions');
		$this->template->content->title = Kohana::lang('ui_admin.actions');
		
		$this->template->map_enabled = TRUE;
		$this->template->treeview_enabled = TRUE;
		
		$this->template->js = new View('admin/actions_js');
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
		$form = array
	    (
			'geometry' => '',
			'action_trigger' => '',
			'action_user' => '',
			'action_location_specific' => '',
			'action_keywords' => '',
			'action_response' => '',
			'action_email_send_address' => '',
			'action_email_subject' => '',
			'action_email_body' => '',
			'action_add_category' => array()
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
	        
	        // Adding new action
	        if ($post->form_action == 'a')
			{
				// TODO: Put add specific data here
			}
			
			// Since our form is dynamic, we need to set validation dynamically
			foreach($expected_fileds as $field)
			{
				$this->_form_field_rules($field,&$post);
			}				

			if( $post->validate() )
			{	
				$qualifiers = array();
				foreach($expected_qualifier_fields as $field){
					$form_field = 'action_'.$field;
					$qualifiers[$field] = $post->$form_field;
					if($field == 'location' && $post->$form_field == 'specific')
					{
						// Add geometry if this is a specific location
						$qualifiers['geometry'] = $post->geometry;
					}
				}
				
				$qualifiers = serialize($qualifiers);
				
				$response_vars = array();
				foreach($expected_response_fields as $field){
					$form_field = 'action_'.$field;
					$response_vars[$field] = $post->$form_field;
				}
				
				$response_vars = serialize($response_vars);
				
				$action = ORM::factory('actions');
				$action->action = $post->action_trigger;
				$action->qualifiers = $qualifiers;
				$action->response = $post->action_response;
				$action->response_vars = $response_vars;
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
		
		// Grab categories for category advanced options
		$this->template->content->categories = Category_Model::get_categories();

		$this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
        $this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->errors = $errors;
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
				$post->add_rules('action_email_send_address','required','email');
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
		$response_options = array('0'=>Kohana::lang('ui_admin.please_select'));
		return array_merge($response_options,Kohana::config('actions.response_options'));
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