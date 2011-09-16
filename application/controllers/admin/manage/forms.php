<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller is used to add/ remove Custom Forms
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

class Forms_Controller extends Admin_Controller {

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
	* Lists the forms.
    */
	public function index()
	{
		$this->template->content = new View('admin/forms');
		
		// Setup and initialize form field names
		$form = array
	    (
			'action' => '',
	        'form_id' => '',
			'form_title' => '',
	        'form_description' => '',
	        'form_active' => '',
			'field_type' => ''
	    );
	
		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$form_saved = FALSE;
		$form_action = "";
		$form_id = "";
		
		if( $_POST ) 
		{
			$post = Validation::factory( $_POST );
			
			 //  Add some filters
	        $post->pre_filter('trim', TRUE);
	
			if ($post->action == 'a')		// Add Action
			{
				// Add some rules, the input field, followed by a list of checks, carried out in order
				$post->add_rules('form_title','required', 'length[1,1000]');
				$post->add_rules('form_description','required');
			}
			elseif ($post->action == 'd')
			{
				if ($_POST['form_id'] == 1)
				{ 
					// Default Form Cannot Be Deleted
					$post->add_error('form_id','default');
				}
			}
			
			if( $post->validate() )
			{
				$form_id = $post->form_id;
				
				$custom_form = new Form_Model($form_id);
				if ($post->action == 'd')
				{ 
					// Delete Action
					$custom_form->delete( $form_id );
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.deleted'));
				}
				elseif ($post->action == 'h')
				{ 
					// Active/Inactive Action
					if ($custom_form->loaded)
					{
						// @todo Doesn't make sense, find out what the logic for this is
						// Customary values for active and inactive are 1 and 0 respectively
						$custom_form->form_active = ($custom_form->form_active == 1)? 0: 1;
						$custom_form->save();
						$form_saved = TRUE;
						$form_action = strtoupper(Kohana::lang('ui_admin.modified'));
					}
				}
				else
				{
					// Save Action
					$custom_form->form_title = $post->form_title;
					$custom_form->form_description = $post->form_description;
					$custom_form->save();
					$form_saved = TRUE;
					$form_action = strtoupper(Kohana::lang('ui_admin.created_edited'));
				}
				
				// Empty $form array
				array_fill_keys($form, '');
				
			}
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('form'));
				$form_error = TRUE;
			}
		}
		
        // Pagination
        $pagination = new Pagination(array(
                            'query_string' => 'page',
                            'items_per_page' => $this->items_per_page,
                            'total_items' => ORM::factory('form')->count_all()
                        ));

		$forms = ORM::factory('form')
				->orderby('id', 'asc')
				->find_all($this->items_per_page, $pagination->sql_offset);
				
		// Form Field Types
		$form_field_types = array
		(
			'' => Kohana::lang('ui_admin.select_field_type'),
			1 => Kohana::lang('ui_admin.text_field'),
			2 => Kohana::lang('ui_admin.free_text_field'),
			3 => Kohana::lang('ui_admin.date_field'),
			5 => Kohana::lang('ui_admin.radio_field'),
			6 => Kohana::lang('ui_admin.checkbox_field'),
			7 => Kohana::lang('ui_admin.dropdown_field'),
			8 => Kohana::lang('ui_admin.divider_start_field'),
			9 => Kohana::lang('ui_admin.divider_end_field'),
			// 4 => 'Add Attachments'
		);
		
		$this->template->content->form = $form;
		$this->template->content->form_error = $form_error;
		$this->template->content->form_saved = $form_saved;
		$this->template->content->form_action = $form_action;
		$this->template->content->pagination = $pagination;
		$this->template->content->total_items = $pagination->total_items;
		$this->template->content->forms = $forms;
		$this->template->content->form_field_types = $form_field_types;
		$this->template->content->errors = $errors;

        // Javascript Header
        $this->template->js = new View('admin/forms_js');
		$this->template->js->form_id = $form_id;
		$this->template->form_error = $form_error;
	}

	
	/**
	* Generates Form Field Entry Form (Add/Edit) via Ajax Request
    */
	public function selector()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Seelctor ID
		$selector_id = (isset($_POST['selector_id']))? intval($_POST['selector_id']) : -1;
		
		// Form ID
		$form_id = (isset($_POST['form_id']))? intval($_POST['form_id']) : 0;
		
		// Field ID
		$field_id = (isset($_POST['field_id']))? intval($_POST['field_id']) : 0;
		
		$selector_content = "";
		
		if ($selector_id >= 0 AND $form_id > 0)
		{
			switch ($selector_id) {
			    case 0:
			        $selector_content = Kohana::lang('ui_main.select_item');
			        break;
			    case 1:
			        $selector_content = $this->_get_selector_text($form_id, $field_id);
			        break;
			    case 2:
			        $selector_content = $this->_get_selector_textarea($form_id, $field_id);
			        break;
				case 3:
		        	$selector_content = $this->_get_selector_date($form_id, $field_id);
		        	break;
				case 4:
		        	$selector_content = $this->_get_selector_multi($form_id, $field_id, $selector_id);
		        	break;
				case 5:
		        	$selector_content = $this->_get_selector_multi($form_id, $field_id, $selector_id);
		        	break;
				case 6:
		        	$selector_content = $this->_get_selector_multi($form_id, $field_id, $selector_id);
		        	break;
				case 7:
		        	$selector_content = $this->_get_selector_multi($form_id, $field_id, $selector_id);
		        	break;
				case 8:
		        	$selector_content = $this->_get_selector_div($form_id, $field_id, "start");
		        	break;
				case 9:
		        	$selector_content = $this->_get_selector_div($form_id, $field_id, "end");
		        	break;
			}
		}
		echo json_encode(array("status"=>"success", "message"=>$selector_content));
	}
	
	
	/**
	 * Create/Edit & Save New Form Field
	 */
	public function field_add()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// setup and initialize form field names
		$form = array
	    (
			'field_type' => '',
			'field_name' => '',
	        'field_default' => '',
	        'field_required' => '',
			'field_width' => '',
			'field_height' => ''
	    );
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		
		$field_add_status = "";
		$field_add_response = "";
		
		if ($_POST) 
		{
			// @todo Manually extract the data to be validated
			$form_field_data = arr::extract($_POST, 'form_id', 'field_type', 'field_name', 'field_default', 'field_required', 
				'field_width', 'field_height', 'field_isdate', 'field_ispublic_visible', 'field_ispublic_submit');
			
			// Sanitize the default value (if provided)
			$form_field_data['field_default'] = $this->input->xss_clean($form_field_data['field_default']);
			
			// Form_Field_Model instance
			$form_field = Form_Field_Model::is_valid_form_field($_POST['field_id'])
				? ORM::factory('form_field', $_POST['field_id'])
				: new Form_Field_Model();

			
			// Validate the form field data
			if ($form_field->validate($form_field_data))
			{
				// Validation succeeded, proceed...
				
				// Check for new form field entry
				$new_field = $form_field->loaded;
				
				// Save the new/modified form field entry
				$form_field->save();
				
				// Get the form field id
				$field_id = $form_field->id;
				
				// Save optional values
				if (isset($_POST['field_options']))
				{
					foreach ($_POST['field_options'] as $name => $value)
					{
						$option_exists = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name',$name)->find();
						
						$option_entry = ($option_exists->loaded == TRUE)
							? ORM::factory('form_field_option', $option_exists->id)
							: new Form_Field_Option_Model();
						
						$option_entry->form_field_id = $field_id;
						$option_entry->option_name = $name;
						$option_entry->option_value = $value;
						$option_entry->save();
					}
				}

				// If a new field, calculate the field position
				if (empty($new_field))
				{
					// Calculate the field position
					$field_position = ORM::factory('form_field')
										->where(array('form_id' => $form_field->form_id, 'id != ' => $field_id))
										->count_all() + 1;
					
					$form_field->field_position = $field_position;
					$form_field->save();
				}

				$field_add_status = "success";
				$field_add_response = rawurlencode(customforms::get_current_fields($form_field->form_id, $this->user));
				
			}
			else
			{
				// Repopulate the form fields
	            $form = arr::overwrite($form, $form_field_data->as_array());

               // Populate the error fields, if any
                $errors = arr::overwrite($errors, $form_field_data->errors('form'));
                
				// populate the response to this post request
				$field_add_status = "error";
				$field_add_response  = "";
				$field_add_response .= "<ul>";
				
				foreach ($errors as $error_item => $error_description)
				{
					$field_add_response .= (!$error_description) ? '' : "<li>" . $error_description . "</li>";
				}
				$field_add_response .= "</ul>";
			}
		}
		
		echo json_encode(array("status"=>$field_add_status, "response"=>$field_add_response));
	}
	
	
	/**
	* Delete Form Field
    */
	public function field_delete()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Get the form id
		$form_id = (isset($_POST['form_id']))? intval($_POST['form_id']) : 0;
		
		// Get the field id
		$field_id = (isset($_POST['field_id']))? intval($_POST['field_id']) : 0;
		
		// To hold the return content
		$return_content = "";
		
		if ($field_id > 0 AND $form_id > 0)
		{
			ORM::factory('form_field')->delete($field_id);
			$return_content = customforms::get_current_fields($form_id,$this->user);
		}
		
		echo json_encode(array("status"=>"success", "response"=>$return_content));
	}
	
	
	/**
	* Move Form Field Up or Down
	* Positioning in layout
    */
	public function field_move()
	{
		$this->template = "";
		$this->auto_render = FALSE;
		
		// Get the form id
		$form_id = (isset($_POST['form_id']) AND intval($_POST['form_id']) > 0)
			? intval($_POST['form_id'])
			: 0;
		
		// Get the field id
		$field_id = (isset($_POST['field_id']) AND intval($_POST['field_id']) > 0)
			? intval($_POST['field_id'])
			: 0;
		
		// Field position
		$field_position = (isset($_POST['field_position']))? $_POST['field_position'] : "";
		
		$return_content = "";
		
		if ($field_position == 'u' OR $field_position == 'd')
		{
			
			// Load This Field
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == TRUE)
			{
				// Get the total number of fields for the form
				$total_fields = ORM::factory('form_field')->where('form_id', $field->form_id)->count_all();
				
				// Get current position
			    $current_position = $field->field_position;
				
				if ($field_position == 'u' AND $current_position > 1)
				{
					// Move down the fields whose position value is greater
					// than that of the selected field 
					$sql = "UPDATE %sform_field SET field_position = field_position + 1 WHERE id != %d";
					$this->db->query(sprintf($sql, $this->table_prefix, $field_id));

					// Move the selected field upwards
					$field->field_position = $current_position - 1;
					$field->save();
				}
				elseif ($field_position == 'd' AND $current_position != $total_fields)
				{ 
					// Move all other form fields upwards
					$sql = "UPDATE %sform_field SET field_position = field_position - 1 WHERE id != %d";
					$this->db->query(sprintf($sql, $this->table_prefix, $field_id));
					
					// Move the selected field downwards - increase its field position in the database
					$field->field_position = $current_position + 1;
					$field->save();
				}
			}
			
		}
		
		$return_content = customforms::get_current_fields($form_id,$this->user);
		echo json_encode(array("status"=>"success", "response"=>$return_content));
	}

	/**
	* Generate Public Visible / Submit Dropdown Boxes
    * @param int $field_ispublic_submit If this can be submitted by anyone
    * @param int $field_ispublic_submit If answers this can be viewed by anyone
	*/
	private function _get_public_state($field_ispublic_submit, $field_ispublic_visible)
	{
		$visibility_selection = array('0' => Kohana::lang('ui_admin.anyone_role'));
		
		$roles = ORM::factory('role')->orderby('access_level','asc')->find_all();
		foreach($roles as $role)
		{
			$visibility_selection[$role->id] = ucfirst($role->name);
		}

		$html ="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.ispublic_submit').":</strong><br />";
		if (isset($field_ispublic_submit))
			$html .=  form::dropdown('field_ispublic_submit',$visibility_selection,$field_ispublic_submit);
		else
			$html .=  form::dropdown('field_ispublic_submit',$visibility_selection,'0');

		$html .="</div>";
		
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.ispublic_visible').":</strong><br />";
		if (isset($field_ispublic_visible))
			$html .=  form::dropdown('field_ispublic_visible',$visibility_selection,$field_ispublic_visible);
		else
			$html .=  form::dropdown('field_ispublic_visible',$visibility_selection,'0');
		$html .="</div>";

		return $html;
	}

	/**
	* Generate Div Fields
    * @param int $form_id The id no. of the form
    * @param int $field_id The id no. of the field
	* @param string $type "start" for start of a div "end" for the end
    */
	private function _get_selector_div($form_id = 0, $field_id = 0, $type = "")
	{
		if (intval($field_id) > 0)
		{
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == true)
			{
				$field_name = $field->field_name;
				$field_default = $field->field_default;
				$field_required = $field->field_required;
				$field_width = $field->field_width;
				$field_height = $field->field_height;
				$field_maxlength = $field->field_maxlength;
				$field_isdate = $field->field_isdate;
				$field_ispublic_visible = $field->field_ispublic_visible;
				$field_ispublic_submit = $field->field_ispublic_submit;
			}
		}
		else
		{
			$field_id = "";
			$field_name = "";
			$field_default = "";
			$field_required = "0";
			$field_width = "";
			$field_height = "";
			$field_maxlength = "";
			$field_isdate = "0";
			$field_ispublic_visible = "0";
			$field_ispublic_submit = "0";
		}
		
		$html = "";
		$html .="<input type=\"hidden\" name=\"form_id\" id=\"form_id\" value=\"".$form_id."\">";
		$html .="<input type=\"hidden\" name=\"field_id\" id=\"field_id\" value=\"".$field_id."\">";
		$html .="<div id=\"form_result_".$form_id."\" class=\"forms_fields_result\"></div>";
		if($type == "start")
		{
			$html .="<div class=\"forms_item\">"; 
			$html .="	<strong>".Kohana::lang('ui_admin.field_name').":</strong><br />"; 
			$html .= 	form::input('field_name', $field_name, ' class="text"');
			$html .="</div>"; 
			$html .="<div class=\"forms_item\">"; 
			$html .="	<strong>".Kohana::lang('ui_admin.divider_class').":</strong><br />"; 
			$html .= 	form::input('field_default', $field_default, ' class="text"');
			$html .="</div>"; 
		}else{
			$html .="<input type=\"hidden\" name=\"field_name\" id=\"field_name\" value=\"BLANKDIV\">";
			$html .="<input type=\"hidden\" name=\"field_default\" id=\"field_default\" value=\"BLANKDIV\">";
		}
		$html .="<input type=\"hidden\" name=\"field_required\" id=\"field_required\" value=\"FALSE\">";
		$html .= $this->_get_public_state($field_ispublic_submit, $field_ispublic_visible);
	
		// toggle options
		$toggle_default = '0';
		$toggle_check = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name','field_toggle')->find();
		if($toggle_check->loaded == TRUE)
			$toggle_default = $toggle_check->option_value;

		$toggle_options = array(
			'0' => Kohana::lang('ui_admin.field_toggle_no'),
			'1' => Kohana::lang('ui_admin.field_toggle_yes_open'),
			'2' => Kohana::lang('ui_admin.field_toggle_yes_close')
		);
		$html .="<div class=\"forms_item\">"; 
		$html .="<strong>" . Kohana::lang('ui_admin.field_toggle') . ":</strong><br />";
		$html .= form::dropdown('field_options[field_toggle]',$toggle_options,$toggle_default);
		$html .="</div>";

		$html .="<div style=\"clear:both;\"></div>";
		$html .="<div class=\"forms_item\">";
		$html .="	<div id=\"form_loading_".$form_id."\" class=\"forms_fields_loading\"></div>";
		$html .="	<input type=\"image\" src=\"".url::base()."media/img/admin/btn-save.gif\" />";
		$html .="</div>";
		$html .="<div style=\"clear:both;\"></div>";
		$html .=$this->_get_selector_js($form_id);
		
		return $html;
	}

	/**
	* Generate Text Field Entry Form
    * @param int $form_id The id no. of the form
    * @param int $field_id The id no. of the field
    */
	private function _get_selector_text($form_id = 0, $field_id = 0)
	{
		if (intval($field_id) > 0)
		{
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == true)
			{
				$field_name = $field->field_name;
				$field_default = $field->field_default;
				$field_required = $field->field_required;
				$field_width = $field->field_width;
				$field_height = $field->field_height;
				$field_maxlength = $field->field_maxlength;
				$field_isdate = $field->field_isdate;
				$field_ispublic_visible = $field->field_ispublic_visible;
				$field_ispublic_submit = $field->field_ispublic_submit;
			}
		}
		else
		{
			$field_id = "";
			$field_name = "";
			$field_default = "";
			$field_required = "0";
			$field_width = "";
			$field_height = "";
			$field_maxlength = "";
			$field_isdate = "0";
			$field_ispublic_visible = "0";
			$field_ispublic_submit = "0";
		}
		
		$html = "";
		$html .="<input type=\"hidden\" name=\"form_id\" id=\"form_id\" value=\"".$form_id."\">";
		$html .="<input type=\"hidden\" name=\"field_id\" id=\"field_id\" value=\"".$field_id."\">";
		$html .="<div id=\"form_result_".$form_id."\" class=\"forms_fields_result\"></div>";
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_name').":</strong><br />"; 
		$html .= 	form::input('field_name', $field_name, ' class="text"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_default').":</strong><br />"; 
		$html .= 	form::input('field_default', $field_default, ' class="text"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.required').":</strong><br />";
		if ($field_required != 1)
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', FALSE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', TRUE);
		}
		else
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', TRUE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', FALSE);
		}
		$html .="</div>";
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_maxlength').":</strong><br />"; 
		$html .= 	form::input('field_maxlength', $field_maxlength, ' class="text short"');
		$html .="</div>";
		
		// is_public additions by george
		$html .= $this->_get_public_state($field_ispublic_submit, $field_ispublic_visible);
		
		//datatype options
		$datatype_default = '0';
		$datatype_check = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name','field_datatype')->find();
		if($datatype_check->loaded == TRUE)
			$datatype_default = $datatype_check->option_value;

		$datatype_options = array(
			'text' => Kohana::lang('ui_admin.field_datatype_text'),
			'numeric' => Kohana::lang('ui_admin.field_datatype_numeric'),
			'email' => Kohana::lang('ui_admin.header_email'),
			'phonenumber' => Kohana::lang('ui_admin.phone') . " #" 
		);
		$html .="<div class=\"forms_item\">"; 
		$html .="<strong>" . Kohana::lang('ui_admin.field_datatype') . ":</strong><br />";
		$html .= form::dropdown('field_options[field_datatype]',$datatype_options, $datatype_default);
		$html .="</div>";	
		//hidden options
		$hidden_default = '0';
		$hidden_check = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name','field_hidden')->find();
		if($hidden_check->loaded == TRUE)
			$hidden_default = $hidden_check->option_value;

		$hidden_options = array(
			'0' => Kohana::lang('ui_main.no'),
			'1' => Kohana::lang('ui_main.yes')
		);
		$html .="<div class=\"forms_item\">"; 
		$html .="<strong>" . Kohana::lang('ui_admin.field_hidden') . ":</strong><br />";
		$html .= form::dropdown('field_options[field_hidden]',$hidden_options, $hidden_default);
		$html .="</div>";

		$html .="<div style=\"clear:both;\"></div>";
		$html .="<div class=\"forms_item\">";
		$html .="	<div id=\"form_loading_".$form_id."\" class=\"forms_fields_loading\"></div>";
		$html .="	<input type=\"image\" src=\"".url::base()."media/img/admin/btn-save.gif\" />";
		$html .="</div>";
		$html .="<div style=\"clear:both;\"></div>";
		$html .=$this->_get_selector_js($form_id);
		
		return $html;
	}
	
	
	/**
	* Generate TextArea Field Entry Form
    * @param int $form_id The id no. of the form
    * @param int $field_id The id no. of the field
    */
	private function _get_selector_textarea($form_id = 0, $field_id = 0)
	{
		if (intval($field_id) > 0)
		{
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == true)
			{
				$field_name = $field->field_name;
				$field_default = $field->field_default;
				$field_required = $field->field_required;
				$field_width = $field->field_width;
				$field_height = $field->field_height;
				$field_maxlength = $field->field_maxlength;
				$field_isdate = $field->field_isdate;
				$field_ispublic_visible = $field->field_ispublic_visible;
				$field_ispublic_submit = $field->field_ispublic_submit;
			}
		}
		else
		{
			$field_id = "";
			$field_name = "";
			$field_default = "";
			$field_required = "0";
			$field_width = "";
			$field_height = "";
			$field_maxlength = "";
			$field_isdate = "0";
			$field_ispublic_visible = "0";
			$field_ispublic_submit = "0";
		}
		
		$html = "";
		$html .="<input type=\"hidden\" name=\"form_id\" id=\"form_id\" value=\"".$form_id."\">";
		$html .="<input type=\"hidden\" name=\"field_id\" id=\"field_id\" value=\"".$field_id."\">";
		$html .="<input type=\"hidden\" name=\"field_isdate\" id=\"field_id\" value=\"0\">";
		$html .="<input type=\"hidden\" name=\"field_ispublic_visible\" id=\"field_id\" value=\"0\">";
		$html .="<input type=\"hidden\" name=\"field_ispublic_submit\" id=\"field_id\" value=\"0\">";
		$html .="<div id=\"form_result_".$form_id."\" class=\"forms_fields_result\"></div>";
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_name').":</strong><br />"; 
		$html .= 	form::input('field_name', $field_name, ' class="text"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_default').":</strong><br />"; 
		$html .= 	form::textarea('field_default', $field_default, ' class="text" style="width:438px;"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.required').":</strong><br />"; 
		if ($field_required != 1)
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', FALSE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', TRUE);
		}
		else
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', TRUE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', FALSE);
		}
		$html .="</div>";
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_height').":</strong><br />"; 
		$html .= 	form::input('field_height', $field_height, ' class="text short"');
		$html .="</div>";
		
		// is_public additions by george
		$html .= $this->_get_public_state($field_ispublic_submit, $field_ispublic_visible);
		
		//datatype options
		$datatype_default = '0';
		$datatype_check = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name','field_datatype')->find();
		if($datatype_check->loaded == TRUE)
			$datatype_default = $datatype_check->option_value;

		$datatype_options = array(
			'text' => Kohana::lang('ui_admin.field_datatype_text'),
			'markup' => Kohana::lang('ui_admin.field_datatype_markup'),
			// 'javascript' => Kohana::lang('ui_admin.field_datatype_javascript')
		);
		$html .="<div class=\"forms_item\">"; 
		$html .="<strong>" . Kohana::lang('ui_admin.field_datatype') . ":</strong><br />";
		$html .= form::dropdown('field_options[field_datatype]',$datatype_options, $datatype_default);
		$html .="</div>";

/*		Not sure this makes sense in the context of text areas
		//hidden options
		$hidden_default = '0';
		$hidden_check = ORM::factory('form_field_option')->where('form_field_id',$field_id)->where('option_name','field_hidden')->find();
		if($hidden_check->loaded == TRUE)
			$hidden_default = $hidden_check->option_value;

		$hidden_options = array(
			'0' => Kohana::lang('ui_main.no'),
			'1' => Kohana::lang('ui_main.yes')
		);
		$html .="<div class=\"forms_item\">"; 
		$html .="<strong>" . Kohana::lang('ui_admin.field_hidden') . ":</strong><br />";
		$html .= form::dropdown('field_options[field_hidden]',$hidden_options, $hidden_default);
		$html .="</div>";
*/
		$html .="<div style=\"clear:both;\"></div>";
		$html .="<div class=\"forms_item\">";
		$html .="	<div id=\"form_loading_".$form_id."\" class=\"forms_fields_loading\"></div>";
		$html .="	<input type=\"image\" src=\"".url::base()."media/img/admin/btn-save.gif\" />";
		$html .="</div>";
		$html .="<div style=\"clear:both;\"></div>";
		$html .=$this->_get_selector_js($form_id);
		
		return $html;
	}
		
	/**
	* Generate Date Field Entry Form
    * @param int $form_id The id no. of the form
    * @param int $field_id The id no. of the field
    */
	private function _get_selector_date($form_id = 0, $field_id = 0)
	{
		if (intval($field_id) > 0)
		{
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == TRUE)
			{
				$field_name = $field->field_name;
				$field_default = $field->field_default;
				$field_required = $field->field_required;
				$field_width = $field->field_width;
				$field_height = $field->field_height;
				$field_maxlength = $field->field_maxlength;
				$field_isdate = $field->field_isdate;
				$field_ispublic_visible = $field->field_ispublic_visible;
				$field_ispublic_submit = $field->field_ispublic_submit;
			}
		}
		else
		{
			$field_id = "";
			$field_name = "";
			$field_default = "";
			$field_required = "0";
			$field_width = "";
			$field_height = "";
			$field_maxlength = "";
			$field_isdate = "0";
			$field_ispublic_visible = "0";
			$field_ispublic_submit = "0";
		}
		
		$html = "";
		$html .="<input type=\"hidden\" name=\"form_id\" id=\"form_id\" value=\"".$form_id."\">";
		$html .="<input type=\"hidden\" name=\"field_id\" id=\"field_id\" value=\"".$field_id."\">";
		$html .="<div id=\"form_result_".$form_id."\" class=\"forms_fields_result\"></div>";
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_name').":</strong><br />"; 
		$html .= 	form::input('field_name', $field_name, ' class="text"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.field_default').":</strong><br />"; 
		$html .= 	form::input('field_default', $field_default, ' class="text"');
		$html .="</div>"; 
		$html .="<div class=\"forms_item\">"; 
		$html .="	<strong>".Kohana::lang('ui_admin.required').":</strong><br />";
		if ($field_required != 1)
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', FALSE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', TRUE);
		}
		else
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', TRUE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', FALSE);
		}
		$html .="</div>";
		
		// is_public additions by george
		$html .= $this->_get_public_state($field_ispublic_submit, $field_ispublic_visible);
		
		$html .="<div style=\"clear:both;\"></div>";
		$html .="<div class=\"forms_item\">";
		$html .="	<div id=\"form_loading_".$form_id."\" class=\"forms_fields_loading\"></div>";
		$html .="	<input type=\"image\" src=\"".url::base()."media/img/admin/btn-save.gif\" />";
		$html .="</div>";
		$html .="<div style=\"clear:both;\"></div>";
		$html .=$this->_get_selector_js($form_id);
		
		return $html;	
	}

	/**
	* Generate Multi-Selector Field Entry Form (radio, dropdown, checkbox)
    * @param int $form_id The id no. of the form
    * @param int $field_id The id no. of the field
    * @param int $type 5=radio, 6=checkbox, 7=dropdown
    */
	private function _get_selector_multi($form_id = 0, $field_id = 0, $type="")
	{
		if (intval($field_id) > 0)
		{
			$field = ORM::factory('form_field', $field_id);
			if ($field->loaded == true)
			{
				$field_name = $field->field_name;
				$field_default = $field->field_default;
				$field_required = $field->field_required;
				$field_ispublic_visible = $field->field_ispublic_visible;
				$field_ispublic_submit = $field->field_ispublic_submit;
			}
		}
		else
		{
			$field_id = "";
			$field_name = "";
			$field_default = "";
			$field_required = "0";
			$field_ispublic_visible = "0";
			$field_ispublic_submit = "0";
		}
		
		// Prompt/label for the values
		$values_prompt = (intval($type) == 7)? Kohana::lang('ui_admin.dropdown_choices') : Kohana::lang('ui_admin.field_choices');
		
		// Tooltip display value
		$tooltip = (intval($type) == 7)? Kohana::lang('tooltips.dropdown_choices') : Kohana::lang('tooltips.default_value');
		
		$html = "<input type=\"hidden\" name=\"form_id\" id=\"form_id\" value=\"".$form_id."\">"
			. "<input type=\"hidden\" name=\"field_id\" id=\"field_id\" value=\"".$field_id."\">"
			. "<input type=\"hidden\" name=\"field_ispublic_visible\" id=\"field_id\" value=\"0\">"
			. "<input type=\"hidden\" name=\"field_ispublic_submit\" id=\"field_id\" value=\"0\">"
			. "<div id=\"form_result_".$form_id."\" class=\"forms_fields_result\"></div>"
			. "<div class=\"forms_item\">"
			. "		<strong>".Kohana::lang('ui_admin.field_name').":</strong><br />" 
			.		form::input('field_name', $field_name, ' class="text"')
			. "</div>" 
			. "<div class=\"forms_item\">" 
			. "		<strong>".$values_prompt.":<a href=\"#\" class=\"tooltip\""
			. "		title=\"".$tooltip."\"></a><br />" 
			.		form::textarea('field_default', $field_default, ' class="text"')
			. "</div>" 
			. "<div class=\"forms_item\">" 
			. "		<strong>".Kohana::lang('ui_admin.required').":</strong><br />"; 
		
		if ($field_required != 1)
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', FALSE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', TRUE);
		}
		else
		{
			$html .= 	Kohana::lang('ui_admin.yes')." " . form::radio('field_required', '1', TRUE) . "&nbsp;&nbsp;";
			$html .= 	Kohana::lang('ui_admin.no')." " . form::radio('field_required', '0', FALSE);
		}
		$html .="</div>";
		$html .= $this->_get_public_state($field_ispublic_submit, $field_ispublic_visible);
		$html .="<div style=\"clear:both;\"></div>";
		$html .="<div class=\"forms_item\">";
		$html .="	<div id=\"form_loading_".$form_id."\" class=\"forms_fields_loading\"></div>";
		$html .="	<input type=\"image\" src=\"".url::base()."media/img/admin/btn-save.gif\" />";
		$html .="</div>";
		$html .="<div style=\"clear:both;\"></div>";
		$html .=$this->_get_selector_js($form_id);
		
		return $html;
	}

	/** 
	* Custom callback for testing the field_options array
	*
	* @param  Validation  $array   Validation object
	* @param  string      $field   name of field being validated
	* @param int	$field_type 	What type of field this is
	*/
	private function _options_validation( Validation $array, $options)
	{
		error_log('inside options validation');
		return;
	}

	/**
	* Generate Field Entry Form Javascript
	* For Ajax Requests
    * @param int $form_id The id no. of the form
    */
	private function _get_selector_js($form_id = 0)
	{
		$html = "";
		$html .="<script type=\"text/javascript\" charset=\"utf-8\">";
		$html .="$(document).ready(function(){";
		$html .="i=0;";
		$html .="var options = { ";
		$html .="    dataType:   'json',";
		$html .="    beforeSubmit:    function() { ";
		$html .="        $('#form_loading_".$form_id."').html('<img src=\"".url::base()."media/img/loading_g.gif\">');";
		$html .="    }, ";
		$html .="    success:    function(data) { ";
		$html .="        $('#form_loading_".$form_id."').html(''); ";
		$html .="        if(data.status != 'success'){";
		$html .="        	$('#form_result_".$form_id."').removeClass(\"forms_fields_result\").addClass(\"forms_fields_result_error\");";
		$html .="        	$('#form_result_".$form_id."').html(data.response);";
		$html .="        	$('#form_result_".$form_id."').show(); ";
		$html .="        } else { ";
		$html .="        	$('#form_result_".$form_id."').removeClass(\"forms_fields_result_error\").addClass(\"forms_fields_result\");";
		$html .="        	$('#formadd_".$form_id."').hide(300);";
		$html .="        	$('#form_fields_".$form_id."').hide();";
		$html .="        	$('#form_fields_current_".$form_id."').html('');";
		$html .="        	$('#form_fields_current_".$form_id."').html(unescape(data.response));";
		$html .="        	$('#form_fields_current_".$form_id."').effect(\"highlight\", {}, 2000);";
		$html .="        };";
		$html .="    } ";
		$html .="};";
		$html .="$(\"#form_field_".$form_id."\").ajaxForm(options);";
		$html .="$(\"#form_field_".$form_id."\").submit(function() {";
		$html .="return false;";
		$html .="});";		
		$html .="});";
		$html .="</script>";
		
		return $html;
	}
}
