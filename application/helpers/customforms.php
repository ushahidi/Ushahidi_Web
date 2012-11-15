<?php
/**
 * Custom Forms Helper
 * Functions to pull in the custom form fields and display them
 *
 * @package    Custom Forms
 * @author     The Konpa Group - http://konpagroup.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */
class customforms_Core {

	/**
	 * Retrieve Custom Forms
	 * @param bool $active_only Whether or not to limit to active forms only
	 * @return ORM_Iterator
	 */
	public static function get_custom_forms($active_only = TRUE)
	{
		$custom_forms = ORM::factory('form');
		if ($active_only)
		{
			$custom_forms->where('form_active',1);
		}
		return $custom_forms->find_all();
	}

	/**
	 * Retrieve Custom Form Fields
	 * @param bool|int $incident_id The unique incident_id of the original report
	 * @param int $form_id The unique form_id. If none selected, retrieve custom form fields from ALL custom forms
	 * @param bool $data_only Whether or not to include just data
	 * @param string $action If this is being used to grab fields for submit or view of data
	 */
	public static function get_custom_form_fields($incident_id = FALSE, $form_id = NULL, $data_only = FALSE, $action = "submit")
	{
		$fields_array = array();

		// If we have a form id but its invalid, return empty
		if( ! empty($form_id) AND ! Form_Model::is_valid_form($form_id))
			return $fields_array;

		// Database table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Get field we'll check permissions against
		$ispublic_field = ($action == "view") ? 'field_ispublic_visible' : 'field_ispublic_submit';

		// NOTE will probably need to add a user_level variable for non-web based requests
		$user_level = self::get_user_max_auth();
		
		// Check if incident is valid
		// Have to do this early since we can't build 2 ORM queries at once.
		$valid_incident = Incident_Model::is_valid_incident($incident_id, FALSE);

		// Check if the provided incident exists, then fill in the data
		if ($valid_incident)
		{
			$sql = "SELECT ff.*, fr.form_response
			FROM `{$table_prefix}form_field` ff
			LEFT JOIN `{$table_prefix}roles` r ON (r.id = field_ispublic_visible)
			LEFT JOIN
				`{$table_prefix}form_response` fr ON (
					fr.form_field_id = ff.id AND
					fr.incident_id = :incident_id
				)
			WHERE (access_level <= :user_level OR access_level IS NULL) "
			. ( ! empty($form_id) ? "AND form_id = :form_id " : '')
			. "ORDER BY field_position ASC";
		}
		else
		{
			$sql = "SELECT ff.*
			FROM `{$table_prefix}form_field` ff
			LEFT JOIN `{$table_prefix}roles` r ON (r.id = field_ispublic_visible)
			WHERE (access_level <= :user_level OR access_level IS NULL) "
			. ( ! empty($form_id) ? "AND form_id = :form_id " : '')
			. "ORDER BY field_position ASC";
		}
		
		$form_fields = Database::instance()->query($sql, array(
			':form_id' => $form_id,
			':user_level' => $user_level,
			':incident_id' => $incident_id
		));

		foreach ($form_fields as $custom_formfield)
		{
			if ($data_only)
			{
				// Return Data Only
				$fields_array[$custom_formfield->id] = isset($custom_formfield->form_response) ? $custom_formfield->form_response : '';
			}
			else
			{
				// Return Field Structure
				$fields_array[$custom_formfield->id] = array(
					'field_id' => $custom_formfield->id,
					'field_name' => $custom_formfield->field_name,
					'field_type' => $custom_formfield->field_type,
					'field_default' => $custom_formfield->field_default,
					'field_required' => $custom_formfield->field_required,
					'field_maxlength' => $custom_formfield->field_maxlength,
					'field_height' => $custom_formfield->field_height,
					'field_width' => $custom_formfield->field_width,
					'field_isdate' => $custom_formfield->field_isdate,
					'field_ispublic_visible' => $custom_formfield->field_ispublic_visible,
					'field_ispublic_submit' => $custom_formfield->field_ispublic_submit,
					'field_response' => isset($custom_formfield->form_response) ? $custom_formfield->form_response : '',
				);
			}
		}

		// Garbage collection
		unset ($form_fields);

		// Return
		return $fields_array;
	}

	/**
	 * Returns a list of the field names and values for a given userlevel
	 *
	 * @param int $id incident id
	 * @param int $user_level the user's role level
	 * @return Result
	 */
	public static function view_everything($id, $user_level)
	{
		$db = new Database();
		$db->select('form_response.form_response', 'form_field.field_name');
		$db->from('form_response');
		$db->join('form_field','form_response.form_field_id','form_field.id');
		$db->where(array('form_response.incident_id'=>$id,'form_field.field_ispublic_visible <='=>$user_level));
		$db->orderby('form_field.field_position');

		return $db->get();
	}

	/**
	 * Returns the user's maximum role id number
	 *
	 * @param array $user the current user object
	 * @return int
	 */
	public static function get_user_max_auth(){
		if( ! isset($_SESSION['auth_user']))
			return 0;

		$user = new User_Model($_SESSION['auth_user']->id);

		if ($user->loaded == TRUE)
		{
			$r = array();
			foreach($user->roles as $role)
			{
				array_push($r,$role->access_level);
			}
			return max($r);
		}
		return 0;
	}

	/**
	 * Validate Custom Form Fields
	 * @param array $custom_fields Array
	 * XXX This whole function is being done backwards
	 * Need to pull the list of custom form fields first
	 * Then look through them to see if they're set, not the other way around.
	 */
	public static function validate_custom_form_fields(&$post)
	{
		$errors = array();
		$custom_fields = array();

		if (!isset($post->custom_field))
			return;

		/* XXX Checkboxes hackery
			 Checkboxes are submitted in the post as custom_field[field_id-boxnum]
			 This foreach loop consolidates them into one variable separated by commas.
			 If no checkboxes are selected then the custom_field[] for that variable is not sent
			 To get around that the view sets a hidden custom_field[field_id-BLANKHACK] field that
			 ensures the checkbox custom_field is there to be tested.
		*/
		foreach ($post->custom_field as $field_id => $field_response)
		{
			$split = explode("-", $field_id);
			if (isset($split[1]))
			{
				// The view sets a hidden field for blankhack
				if ($split[1] == 'BLANKHACK')
				{
					if(!isset($custom_fields[$split[0]]))
					{
						// then no checkboxes were checked
						$custom_fields[$split[0]] = '';
					}
					// E.Kala - Removed the else {} block; either way continue is still invoked
					continue;
				}

				if (isset($custom_fields[$split[0]]))
				{
					$custom_fields[$split[0]] .= ",$field_response";
				}
				else
				{
					$custom_fields[$split[0]] = $field_response;
				}
			}
			else
			{
				$custom_fields[$split[0]] = $field_response;
			}
		}

		$post->custom_field = $custom_fields;
		// Kohana::log('debug', Kohana::debug($custom_fields));

		foreach ($post->custom_field  as $field_id => $field_response)
		{

			$field_param = ORM::factory('form_field',$field_id);
			$custom_name = $field_param->field_name;

			// Validate that this custom field already exists
			if ( ! $field_param->loaded)
			{
				// Populate the error field
				$errors[$custom_name] = "The $custom_name field does not exist";
				return $errors;
			}

			$max_auth = self::get_user_max_auth();
			if ($field_param->field_ispublic_submit > $max_auth)
			{
				// Populate the error field
				$errors[$custom_name] = "The $custom_name field cannot be edited by your account";
				return $errors;
			}

			// Validate that the field is required
			if ( $field_param->field_required == 1 AND $field_response == "")
			{
				$errors[$custom_name] = "The $custom_name field is required";
				return $errors;
			}

			// Grab the custom field options for this field
			$field_options = self::get_custom_field_options($field_id);

			// Validate Custom fields for text boxes
			if ($field_param->field_type == 1 AND isset($field_options) AND $field_response != '')
			{
				foreach ($field_options as $option => $value)
				{
					if ($option == 'field_datatype')
					{
						if ($value == 'email' AND !valid::email($field_response))
						{
							$errors[$custom_name] = "The $custom_name field requires a valid email address";
						}

						if ($value == 'phonenumber' AND !valid::phone($field_response))
						{
							$errors[$custom_name] = "The $custom_name field requires a valid email address";
						}

						if ($value == 'numeric' AND !valid::numeric($field_response))
						{
							$errors[$custom_name] = "The $custom_name field must be numeric";
						}
					}
				}
			}

			// Validate for date
			if ($field_param->field_type == 3 AND $field_response != "")
			{
				$field_default = $field_param->field_default;
				if ( ! valid::date_mmddyyyy($field_response))
				{
					$errors[$custom_name] = "The $custom_name field is not a valid date (MM/DD/YYYY)";
				}
			}

			// Validate multi-value boxes only have acceptable values
			if ($field_param->field_type >= 5 AND $field_param->field_type <=7)
			{
				$defaults = explode('::',$field_param->field_default);
				$options = array();
				if (preg_match("/[0-9]+-[0-9]+/",$defaults[0]) AND count($defaults) == 1)
				{
					$dashsplit = explode('-',$defaults[0]);
					$start = $dashsplit[0];
					$end = $dashsplit[1];
					for($i = $start; $i <= $end; $i++)
					{
						array_push($options,$i);
					}
				}
				else
				{
					$options = explode(',',$defaults[0]);
				}

				$responses = explode(',',$field_response);
				foreach ($responses as $response)
				{
					if ( ! in_array($response, $options) AND $response != '')
					{
						$errors[$custom_name] = "The $custom_name field does not include $response as an option";
					}
				}
			}

			// Validate that a required checkbox is checked
			if ($field_param->field_type == 6 AND $field_response == 'BLANKHACK' AND $field_param->field_required == 1)
			{
				$errors[$custom_name] = "The $custom_name field is required";
			}
		}

		return $errors;
	}

	/**
	 * Generate list of currently created Form Fields for the admin interface
	 * @param int $form_id The id no. of the form
	 * @return string
	 */
    public static function get_current_fields($form_id = 0)
    {
		$form_fields = form::open(NULL, array('method' => 'get'));
		$form = array();
		$form['custom_field'] = self::get_custom_form_fields('',$form_id, true);
		$form['id'] = $form_id;
		$custom_forms = new View('reports/submit_custom_forms');
		$disp_custom_fields = self::get_custom_form_fields('', $form_id,false);
		$custom_forms->disp_custom_fields = $disp_custom_fields;
		$custom_forms->form = $form;
		$custom_forms->editor = true;
		$form_fields.= $custom_forms->render();
		$form_fields .= form::close();

		return $form_fields;
	}

	/**
	 * Generates the html that's passed back in the json switch_Action form switcher
	 * @param int $incident_id The Incident Id
	 * @param int $form_id Form Id
	 * @param int $public_visible If this form should be publicly visible
	 * @param int $pubilc_submit If this form is allowed to be submitted by anyone on the internets.
	 * @return string
	 */
	public static function switcheroo($incident_id = '', $form_id = '')
	{
		$form_fields = '';

		$fields_array = self::get_custom_form_fields($incident_id, $form_id, TRUE);

		$form = array();
		$form['custom_field'] = self::get_custom_form_fields($incident_id,$form_id, TRUE);
		$form['id'] = $form_id;
		$custom_forms = new View('reports/submit_custom_forms');
		$disp_custom_fields = self::get_custom_form_fields($incident_id,$form_id, FALSE);
		$custom_forms->disp_custom_fields = $disp_custom_fields;
		$custom_forms->form = $form;
		$form_fields.= $custom_forms->render();

		return $form_fields;
	}

	/**
	 * Generates an array of fields that an admin can see but can't edit
	 *
	 * @param int $form_id The form id
	 * @return array
	 */
	public static function get_edit_mismatch($form_id = 0)
	{
		$user_level = self::get_user_max_auth();
		$public_state = array('field_ispublic_submit >'=>$user_level, 'field_ispublic_visible <='=>$user_level);
		$custom_form = ORM::factory('form', $form_id)->where($public_state)->orderby('field_position','asc');
		$mismatches = array();
		foreach ($custom_form->form_field as $custom_formfield)
		{
			$mismatches[$custom_formfield->id] = 1;
		}
		return $mismatches;
	}

	/**
	 * Checks if a field type has multiple values
	 *
	 * @param array $field
	 * @return bool
	 */
	public static function field_is_multi_value($field){
		$is_multi = FALSE;

		switch ($field["field_type"])
		{
			case 5: //Radio
				$is_multi = TRUE;
			break;

			case 6: // Checkbox
				$is_multi = TRUE;
			break;

			case 7: // Dropdown
				$is_multi = TRUE;
			break;

			default:
			$is_multi = FALSE;
		}
		return $is_multi;
	}

	/**
	 * Returns the form field options associated with this form field
	 *
	 * @param int $field_id The Field Id
	 * @return array
	 */
	public static function get_custom_field_options($field_id)
	{
		//XXX should be able to use the model for this, right?
		$field_options = array();
		$field_option_query = ORM::factory('form_field_option')->where('form_field_id',$field_id)->find_all();
		foreach($field_option_query as $option)
		{
			$field_options[$option->option_name] = $option->option_value;
		}

		return $field_options;
	}
}
