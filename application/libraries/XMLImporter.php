<?php
/**
 * XML Report Importer Library
 *
 * Imports reports within XML file referenced by filehandle.
 * 
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 *
 */
class XMLImporter {
	
	/**
	 * Notices to be passed on successful data import
	 * @var array
	 */
	public $notices = array();
	
	/**
	 * Errors to be passed on failed data import
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * Total number of reports within XML file
	 * @var int
	 */		
	public $totalreports = 0;
	
	/**
	 * Total number of reports successfully imported
	 * @var int
	 */
	public $importedreports = 0;
	
	/**
	 * Total number of categories within XML file
	 * @var int
	 */
	private $totalcategories = 0;
	
	/**
	 * Total number of forms within XML file
	 * @var int
	 */
	private $totalforms = 0;
	
	/**
	 * Allowable database value options
	 * @var array
	 */
	private $allowable = array(0,1);
	
	/**
	 * Categories successfully imported
	 * @var array
	 */
	private $categories_added = array();
	
	/**
	 * Category Translations successfully imported
	 * @var array
	 */
	private $category_translations_added = array();
	
	/**
	 * Forms successfully imported
	 * @var array
	 */
	private $forms_added = array();
	
	/**
	 * Custom fields successfully imported
	 * @var array
	 */
	private $fields_added = array();
	
	/**
	 * Custom field options successfully imported
	 * @var array
	 */
	private $field_options_added = array();
	
	/**
	 * Reports successfully imported
	 * @var array
	 */
	private $incidents_added = array();
	
	/**
	 * Incident persons successfully imported
	 * @var array
	 */
	private $incident_persons_added = array();
	
	/**
	 * Custom form field responses successfully imported
	 * @var array
	 */
	private $incident_responses_added = array();
	
	/**
	 * Incident locations successfully imported
	 * @var array
	 */
	private $locations_added = array();
	
	/**
	 * Incident categories successfully imported
	 * @var array
	 */
	private $incident_categories_added = array();
	
	/**
	 * Incident Media successfully imported
	 * @var array
	 */
	private $incident_media_added = array();
	
	/**
	 * Function to import a report form a row in the CSV file
	 * @param file $file
	 * @return bool
	 */
	public function import($file)
	{
		/* For purposes of checking whether the data we're trying to import already exists */
		// Pick out existing categories 
		$this->existing_categories = ORM::factory('category')->select_list('category_title','id');
		$temp_cat = array(); 
		foreach ($this->existing_categories as $title => $id)
		{
			$temp_cat[utf8::strtoupper($title)] = $id;
		}
		$this->existing_categories = $temp_cat;
		
		// Pick out existing reports
		$this->incident_ids = ORM::factory('incident')->select_list('id','id'); 
	
		// Pick out existing forms
		$this->existing_forms = ORM::factory('form')->select_list('form_title', 'id');
		$temp_forms = array();
		foreach ($this->existing_forms as $title => $id)
		{
			$temp_forms[utf8::strtoupper($title)] = $id;
		}
		$this->existing_forms = $temp_forms;
	
		// Pick out existing form fields
		$form_fields = customforms::get_custom_form_fields(FALSE, '', FALSE);
		$temp_fields = array();
		foreach ($form_fields as $existing_field)
		{
			$field_name = $existing_field['field_name'];
			$form_id = $existing_field['form_id'];
			$field_id = $existing_field['field_id'];
			$temp_fields[utf8::strtoupper($field_name)][$form_id] = $field_id;
		}
		$this->existing_fields = $temp_fields;
		 
		// For purposes of adding location time
		$this->time = date("Y-m-d H:i:s",time());
		
		// Initialize DOMDocument
		$xml= new DOMDocument('1.0');
		
		// Make sure we're not trying to open an empty file
		if (@$xml->load($file) !== FALSE)
		{
			$depcategories = $xml->getElementsByTagName('categories');
			$depcustomforms = $xml->getElementsByTagName('custom_forms');
			$depreports = $xml->getElementsByTagName('reports');
			
			if ($depcategories->length == 0 AND $depcustomforms->length == 0 AND $depreports->length == 0)
			{
				$this->errors[] = Kohana::lang('import.xml.missing_elements');
			}
		
			// If we're importing categories
			if( $depcategories->length > 0)
			{	
				$categories = $depcategories->item(0);
				if ($categories->nodeValue != 'There are no categories on this deployment.')
				{
					if ($this->import_categories($categories) == false)
					{
						// Undo Data Import
						$this->rollback();
						return false;
					}
				}
				else
				{
					$this->notices[] = Kohana::lang('import.xml.no_categories');
				}	
			}
		
			// If we're importing custom forms
			if ($depcustomforms->length > 0)
			{
				$customforms = $depcustomforms->item(0);
				if ($customforms->nodeValue != 'There are no custom forms on this deployment.')
				{
					if ($this->import_customforms($customforms) == false)
					{
						// Undo Data Import
						$this->rollback();
						return FALSE;
					}
				}
				else
				{
					$this->notices[] = Kohana::lang('import.xml.no_custom_forms');
				}	
			}
		
			// If we are importing Reports 
			if ($depreports->length > 0)
			{
				$reports = $depreports->item(0);
				if ($reports->nodeValue != 'There are no reports on this deployment.')
				{
					if ($this->import_reports($reports) == false)
					{
						// Undo Data Import
						$this->rollback();
						return FALSE;						
					}
				}
				else
				{
					$this->notices[] = Kohana::lang('import.xml.no_reports');
				}
			}	
		}
		
		// The file we're trying to load is empty
		else
		{
			$this->errors[] = Kohana::lang('import.xml.file_empty');;
		}
		
		// If we have errors, return FALSE, else TRUE
		return count($this->errors) === 0;
	}
	
	/**
	 * Import categories via XML
	 * @param DOMNodeList Object $categories
	 * @return bool
	 */
	public function import_categories($categories)
	{
		/* Import individual categories*/
		foreach ($categories->getElementsByTagName('category') as $category)
		{
			// Increment category counter
			$this->totalcategories++;
			
			// Category Title
			$cat_title = xml::get_node_text($category, 'title');
			
			// Category Description
			$cat_description = xml::get_node_text($category, 'description');
				
			// If either the category title or description is not provided
			if ( ! $cat_title OR  ! $cat_description )
			{
				$this->errors[] = Kohana::lang('import.xml.category_error').$this->totalcategories;
			}
		
			// Both category title and descriptions exist
			else
			{
				// If this category does not already exist in the database	
				if ( ! isset($this->existing_categories[utf8::strtoupper($cat_title)]))
				{
					// Get category attributes
					$cat_color = xml::get_node_text($category, 'color', FALSE);
					$cat_visible = $category->getAttribute('visible');
					$cat_trusted = $category->getAttribute('trusted');
					
					/* Get other category elements */
					// Parent Category
					$cat_parent = xml::get_node_text($category, 'parent');
					if ($cat_parent)
					{
						$parent_id = isset($this->existing_categories[utf8::strtoupper($cat_parent)])
						 			? $this->existing_categories[utf8::strtoupper($cat_parent)] 
									: 0;
					}	 	
			
					// Save the Category
					$new_category = new Category_Model;
					$new_category->category_title = $cat_title;
					$new_category->category_description = $cat_description ? $cat_description : NULL;
					$new_category->parent_id = isset($parent_id) ? $parent_id : 0;
					$new_category->category_color = $cat_color ? $cat_color : '000000';
					$new_category->category_visible = ( isset($cat_visible) AND in_array($cat_visible, $this->allowable)) ? $cat_visible : 1;
					$new_category->category_trusted = ( isset($cat_trusted) AND in_array($cat_trusted, $this->allowable)) ? $cat_trusted : 0;
					$new_category->category_position = count($this->existing_categories);
					$new_category->save();
				
					// Add this new category to array of existing categories
					$this->existing_categories[utf8::strtoupper($cat_title)] = $new_category->id;
					
					// Also add it to the array of categories added during import
					$this->categories_added[] = $new_category->id;
					$this->notices[] = Kohana::lang('import.new_category').html::escape($cat_title);
				}

				/* Category Translations */
				$c_translations = $category->getElementsByTagName('translations');
				
				// Get the current category's id
				$cat_id = $this->existing_categories[utf8::strtoupper($cat_title)];
		
				// If category translations exist
				if ($c_translations->length > 0)
				{
					$cat_translations = $c_translations->item(0); 	
					foreach ($cat_translations->getElementsByTagName('translation') as $translation)
					{
						// Get Localization
						$locale = xml::get_node_text($translation,'locale', FALSE);
						
						// Does the locale attribute exist in the document? And is it empty?
						if ($locale)
						{	
							// Check if category translation exists for this localization
							$existing_translations = ORM::factory('category_lang')
													->where('category_id',$cat_id)
													->where('locale', $locale)
													->find_all();

							// If Category translation does not exist, save it
							if (count($existing_translations) == 0)
							{
								// Get category title for this localization
								$trans_title = xml::get_node_text($translation, 'translation_title');
								
								// Category Description
								$trans_description = xml::get_node_text($translation, 'translation_description');
								
								// If we're missing the translated category title
								if ( ! $trans_title)
								{
									$this->notices[] = Kohana::lang('import.xml.translation_title').$this->totalcategories
														.': '.utf8::strtoupper($locale);
								}
								else
								{
									// Save Category Translations
									$cl = new Category_Lang_Model();
									$cl->locale = $locale;
									$cl->category_id = $cat_id;
									$cl->category_title = $trans_title;
									$cl->category_description = $trans_description ? $trans_description : NULL;
									$cl->save();
									
									// Add this to array of category translations added during import
									$this->category_translations_added[] = $cl->id;
									$this->notices[] = Kohana::lang('import.xml.translation_added')
														.'"'.utf8::strtoupper($locale).'" for '.$cat_title;
								}
							}	
						}
						
						// Locale attribute does not exist
						else
						{
							$this->notices[] = Kohana::lang('import.xml.missing_localization').$this->totalcategories;
						}
					}	
				}
			}
		}
		// End individual category import
		
		// If we have errors, return FALSE, else TRUE
		return count($this->errors) === 0;
	}
	
	/**
	 * Import Custom Forms and their respective form fields via XML
	 * @param DOMNodeList Object $customforms
	 * @return bool
	 */
	public function import_customforms($customforms)
	{
		$forms = $customforms->getElementsByTagName('form');
		foreach ($forms as $form)
		{
			// Increment forms counter
			$this->totalforms++;
			$totalfields = 0;
			
			// Form Title
			$title = xml::get_node_text($form, 'title');
			
			// If the form title is missing
			if ( ! $title)
			{
				$this->errors[] = Kohana::lang('import.xml.missing_form_title').$this->totalforms;
			}
			
			// Form title exists, proceed
			else
			{
				// If the form does not already exist
				if ( ! isset($this->existing_forms[utf8::strtoupper($title)]))
				{
					// Form Active status
					$form_active = $form->getAttribute('active');

					// Make sure form status value is allowable
					$active = (isset($form_active) AND in_array($form_active, $this->allowable))? $form_active : 1;
					
					// Form Description
					$description = xml::get_node_text($form, 'description');
					
					// Save it
					$new_form = new Form_Model();
					$new_form->form_title = $title;
					$new_form->form_description = $description ? $description : NULL;
					$new_form->form_active = $active;
					$new_form->save();

					// Add new form to array of existing forms
					$this->existing_forms[utf8::strtoupper($title)] = $new_form->id;

					// Add new form to array of forms added during import
					$this->forms_added[] = $new_form->id;
					$this->notices[] = Kohana::lang('import.xml.new_form').'"'.$title.'"';
				}

				// Form Fields
				$this_form = $this->existing_forms[utf8::strtoupper($title)];
				$fields = $form->getElementsByTagName('field');
				if ($fields->length > 0)
				{
					foreach ($fields as $field)
					{
						// Increment form fields counter for this form
						$totalfields++;
						
						// Field Name
						$name = xml::get_node_text($field, 'name');
					
						// Field Type
						$field_type = $field->getAttribute('type');
						$allowable_types = array(1,2,3,4,5,6,7);
						
						// Make sure field_type value is allowable
						$type = (isset($field_type) AND in_array($field_type, $allowable_types) )? $field_type : NULL;
						
						// If field name is missing or field type is null 
						if (! $name OR ! isset($type))
						{
							$this->notices[] = Kohana::lang('import.xml.field_error').'"'.$title.'" : Field #'.$totalfields;
						}
						
						// Field name is provided, proceed
						else
						{
							// If the field does not already exist in this form
							if ( ! isset($this->existing_fields[utf8::strtoupper($name)][$this_form]))
							{
								// Field Required
								$field_required = $field->getAttribute('required');
								$required = (isset($field_required) AND in_array($field_required, $this->allowable)) ? $field_required : 0;
								
								// Field Publicly Visible
								$field_visible = $field->getAttribute('visible_by');
								$public_visible = (isset($field_visible) AND in_array($field_visible, $this->allowable)) ? $field_visible : 0;
								
								// Field Publicly submit?
								$field_submit = $field->getAttribute('submit_by');
								$public_submit = (isset($field_submit) AND in_array($field_submit, $this->allowable)) ? $field_submit : 0;

								// Field Default
								$default = xml::get_node_text($field, 'default');
								$default_values = $default ? $default : NULL;
								
								// Make sure we have default values for Radio buttons, Checkboxes and drop down fields
								// If not provided, don't import this custom field
								$default_required = array(5, 6, 7);
								if ( ! isset($default_values) AND in_array($type, $default_required))
								{
									$this->notices[] = Kohana::lang('import.xml.field_default').'"'.$title.'" : Field "'.$name.'"';
								}
								
								// Defaults have been provided / Not required
								else
								{
									// Save the form field
									$new_field = new Form_Field_Model();
									$new_field->form_id = $this_form;
									$new_field->field_name = $name;
									$new_field->field_type = $type;
									$new_field->field_required = $required;
									$new_field->field_default = isset($default_values) ? $default_values : NULL;
									$new_field->field_ispublic_visible  = $public_visible;
									$new_field->field_ispublic_submit  = $public_submit;
									$new_field->save();
									
									// Add this field to array of existing fields
									$this->existing_fields[utf8::strtoupper($name)][$this_form] = $new_field->id;
									
									// Also add it to array of fields added during import
									$this->fields_added[] = $new_field->id;
									$this->notices[] = Kohana::lang('import.xml.new_field').'"'.$name.'"';
									
									// Field Options exist?
									if ($field->hasAttribute('datatype') OR $field->hasAttribute('hidden'))
									{
										// Get current field_id
										$fieldid = $this->existing_fields[utf8::strtoupper($name)][$this_form];

										if ($field->hasAttribute('datatype'))
										{
											// Does datatype option already exist for this field?
											$existing_datatype = ORM::factory('form_field_option')
																->where('form_field_id', $fieldid)
																->where('option_name','field_datatype')
																->find_all();
											// No, none exists
											if (count($existing_datatype) == 0)
											{
												$datatype = xml::get_node_text($field,'datatype', FALSE);
												$allowed_types = array('email', 'phonenumber', 'numeric', 'text');
												$field_datatype = ($datatype AND in_array($datatype, $allowed_types))? $datatype : NULL;

												// If field datatype is not null, save
												if ($field_datatype != NULL)
												{
													$datatype_option = new Form_Field_Option_Model();
													$this->_save_field_option($datatype_option, $fieldid, 'field_datatype', $field_datatype);

													// Add to array of field options added during import
													$this->field_options_added[] = $datatype_option->id;
													$this->notices[] = Kohana::lang('import.xml.field_datatype').'"'.$name.'"';
												}
											}								
										}

										if ($field->hasAttribute('hidden'))
										{
											// Does hidden option already exist for this field?
											$existing_hidden = ORM::factory('form_field_option')
																->where('form_field_id', $fieldid)
																->where('option_name','field_hidden')
																->find_all();

											// No, none exists
											if (count($existing_hidden) == 0)
											{
												$hidden = $field->getAttribute('hidden');
												$field_hidden = ($hidden != '' AND in_array($hidden, $this->allowable)) ? $hidden : NULL;

												// If field datatype is not null, save
												if ($field_hidden != NULL)
												{
													$hidden_option = new Form_Field_Option_Model();
													$this->_save_field_option($hidden_option, $fieldid, 'field_hidden', $field_hidden);

													// Add to array of field options added during import
													$this->field_options_added[] = $hidden_option->id;
													$this->notices[] = Kohana::lang('import.xml.field_hidden').'"'.$name.'"';
												}
											} 
										}
										// End field hidden option exists
									}
									// End field options exist	
								}
								// End defaults provided
							}
							// End field does not exist
						}
						// End field name provided
					}
					// End individual form field import
				}
				// End if fields exist
			}
			// End form title exists			
		}
		// End individual form import
		
		// If we have errors, return FALSE, else TRUE
		return count($this->errors) === 0;
	}
	
	/**
	 * Import Reports via XML
	 * @param DOMNodeList Object $report
	 * @return bool
	 */
	public function import_reports($reports)
	{
		/* Import individual reports */
		foreach ($reports->getElementsByTagName('report') as $report)
		{
			$this->totalreports++;
			// Get Report id
			$report_id = $report->getAttribute('id');
			
			// Check if this incident already exists in the db
			if (isset($report_id) AND isset($this->incident_ids[$report_id]))
			{
				$this->notices[] = Kohana::lang('import.incident_exists').$report_id;
			}
			
			// Otherwise, begin import
			else
			{
				/* Step 1: Location information */
				$locations = $report->getElementsByTagName('location');
				
				// If location information has been provided
				if ($locations->length > 0)
				{
					$report_location = $locations->item(0);
					
					// Location Name
					$location_name = xml::get_node_text($report_location, 'name');
					
					// Longitude
					$longitude = xml::get_node_text($report_location, 'longitude');
					
					// Latitude
					$latitude = xml::get_node_text($report_location, 'latitude');
									
					if ($location_name)
					{
						// For geocoding purposes
						$location_geocoded = map::geocode($location_name);
						
						// Save the location
						$new_location = new Location_Model();
						$new_location->location_name = $location_name ? $location_name : NULL;
						$new_location->location_date = $this->time;
						
						// If longitude/latitude values are present
						if ($latitude AND $longitude)
						{
							$new_location->latitude = $latitude ? $latitude: 0;
							$new_location->longitude = $longitude ? $longitude: 0;
							
						}
						else
						{
							// Get geocoded lat/lon values
							$new_location->latitude = $location_geocoded ? $location_geocoded['latitude'] : $latitude;
							$new_location->longitude = $location_geocoded ? $location_geocoded['longitude'] : $longitude;
						} 
						$new_location->country_id = $location_geocoded ? $location_geocoded['country_id'] : 0;
						$new_location->save();
						
						// Add this location to array of imported locations
						$this->locations_added[] = $new_location->id;
					}
					
				}
				
				/* Step 2: Save Report */
				// Report Title
				$report_title = xml::get_node_text($report, 'title');
				
				// Report Date
				$report_date = xml::get_node_text($report, 'date');
				
				// Missing report title or report date?
				if ( ! $report_title OR ! $report_date)
				{
					$this->errors[] = Kohana::lang('import.xml.incident_title_date').$this->totalreports;
				}
				
				// If report date is not in the required format
				if ( ! strtotime($report_date))
				{
					$this->errors[] = Kohana::lang('import.incident_date').$this->totalreports.': '.html::escape($report_date);
				}
				
				// Report title and date(in correct format) both provided, proceed
				else
				{
					// Approval status?
					$approved = $report->getAttribute('approved');
					$report_approved = (isset($approved) AND in_array($approved, $this->allowable)) ? $approved : 0;
					
					// Verified Status?
					$verified = $report->getAttribute('verified');
					$report_verified = (isset($verified) AND in_array($verified, $this->allowable)) ? $verified : 0;
					
					// Report mode?
					$allowed_modes = array(1, 2, 3, 4);
					$mode = $report->getAttribute('mode');
					$report_mode = (isset($mode) AND in_array($mode, $allowed_modes)) ? $mode : 1;
					
					// Report Form
					$report_form = xml::get_node_text($report,'form_name', FALSE);
					if ($report_form)
					{
						if (! isset($this->existing_forms[utf8::strtoupper($report_form)]))
						{
							$this->notices[] = Kohana::lang('import.xml.no_form_exists').$this->totalreports
												.': "'.$report_form.'"';
						}
						
						$form_id = isset($this->existing_forms[utf8::strtoupper($report_form)])
						 			? $this->existing_forms[utf8::strtoupper($report_form)] 
									: 1;
					}
					
					// Report Date added
					$dateadd = xml::get_node_text($report, 'dateadd');
					
					// Report Description
					$report_description = xml::get_node_text($report, 'description');

					$new_report = new Incident_Model();
					$new_report->location_id = isset($new_location) ? $new_location->id : 0;
					$new_report->user_id = 0;
					$new_report->incident_title = $report_title;
					$new_report->incident_description = $report_description ? $report_description : '';
					$new_report->incident_date = date("Y-m-d H:i:s",strtotime($report_date));
					$new_report->incident_dateadd = ($dateadd AND strtotime($dateadd))? $dateadd : $this->time;
					$new_report->incident_active = $report_approved;
					$new_report->incident_verified = $report_verified;
					$new_report->incident_mode = $report_mode;
					$new_report->form_id = isset($form_id) ? $form_id : 1;
					$new_report->save();
					
					// Increment imported rows counter
					$this->importedreports++;
					
					// Add this report to array of reports added during import
					$this->incidents_added[] = $new_report->id;
					
					/* Step 3: Save Report Categories*/
					// Report Categories exist?
					$reportcategories = $report->getElementsByTagName('report_categories') ;
					if ($reportcategories->length > 0)
					{
						$report_categories = $reportcategories->item(0);
						
						foreach($report_categories->getElementsByTagName('category') as $r_category)
						{
							$category = trim($r_category->nodeValue);
							$report_category = (isset($category) AND $category != '') ? $category : '';
							if ($report_category != '' AND isset($this->existing_categories[utf8::strtoupper($report_category)]))
							{
								// Save the incident category
								$new_incident_category = new Incident_Category_Model();
								$new_incident_category->incident_id = $new_report->id;
								$new_incident_category->category_id = $this->existing_categories[utf8::strtoupper($report_category)];
								$new_incident_category->save();
								
								// Add this to array of incident categories added
								$this->incident_categories_added[] = $new_incident_category->id;
							}
							
							if ($report_category != '' AND ! isset($this->existing_categories[utf8::strtoupper($report_category)]))
							{
								$this->notices[] = Kohana::lang('import.xml.no_category_exists').$this->totalreports.': "'.$report_category.'"';
							}
						}	
					}
						
					/* Step 4: Save Custom form field responses for this report */
					// Report Custom Fields
					$this_form = $new_report->form_id;
					$reportfields = $report->getElementsByTagName('custom_fields');
					if ($reportfields->length > 0)
					{
						$report_fields = $reportfields->item(0);
						$custom_fields = $report_fields->getElementsByTagName('field');
						if ($custom_fields->length > 0)
						{
							foreach ($custom_fields as $field)
							{
								// Field Name
								$field_name = $field->hasAttribute('name') ? xml::get_node_text($field, 'name', FALSE) : FALSE;
								if ($field_name)
								{
									// If this field exists in the form listed for this report
									if(isset($this->existing_fields[utf8::strtoupper($field_name)][$this_form]))
									{
										// Get field type and default values
										$match_field_id = $this->existing_fields[utf8::strtoupper($field_name)][$this_form];
										
										// Grab form field object
										$match_fields = ORM::Factory('form_field', $match_field_id);
										$match_field_type = $match_fields->field_type;
										$match_field_defaults = $match_fields->field_default;
											
										// Grab form responses
										$field_response = trim($field->nodeValue);
										if ($field_response != '')
										{
											// Initialize form response model
											$new_form_response = new Form_Response_Model();
											$new_form_response->incident_id = $new_report->id;
											$new_form_response->form_field_id = $match_field_id;
												
											// For radio buttons, checkbox fields and drop downs, make sure form responses are
											// within bounds of allowable options for that field
											// Split field defaults into individual values
											$field_defaults = explode(',',$match_field_defaults);
												
											/* Radio buttons and Drop down fields which take single responses */
											if ($match_field_type == 5 OR $match_field_type == 7)
											{
												foreach ($field_defaults as $match_field_default)
												{
													// Carry out a case insensitive string comparison
													$new_form_response->form_response = strcasecmp($match_field_default, $field_response) == 0
																						? $match_field_default 
																						: NULL;
												}
											}
												
											// Checkboxes which 
											if ($match_field_type == 6)
											{
												// Split user responses into individual value
												$responses = explode(',', $field_response);
												$values = array();
												foreach ($match_field_defaults as $match_field_default)
												{
													foreach ($responses as $response)
													{
														$values[] = strcasecmp($match_field_default, $response) == 0
														 			? $match_field_default 
																	: NULL;
													}
												}
													
												// Concatenate checkbox values into a string, separated by a comma
												$new_form_response->form_response = implode(",", $values);
											}
											
											// For all other fields
											else
											{
												$new_form_response->form_response = $field_response;
											}
												
											// Only save if form response is not empty
											if ($new_form_response->form_response != NULL)
											{
												$new_form_response->save();
											}
												
											// Add this to array of form responses added
											$this->incident_responses_added[] = $new_form_response->id;	
										}								
									}
									else
									{
										$this->notices[] = Kohana::lang('import.xml.form_field_no_match')
															.$this->totalreports.': "'.$field_name.'" on form "'.$new_report->form->form_title.'"';
									}
								}
							}
						}	
					}
				
					
					/* Step 5: Save incident persons for this report */
					// Report Personal Information
					$personal_info = $report->getElementsByTagName('personal_info');
					
					// If personal info exists
					if ($personal_info->length > 0)
					{
						$report_info = $personal_info->item(0);

						// First Name
						$firstname = xml::get_node_text($report_info, 'first_name');

						// Last Name
						$lastname = xml::get_node_text($report_info, 'last_name');

						// Email
						$r_email = xml::get_node_text($report_info, 'email');	
						$email = ($r_email AND valid::email($r_email)) ? $r_email : NULL;

						$new_incident_person = new Incident_Person_Model();
						$new_incident_person->incident_id = $new_report->id;
						$new_incident_person->person_date = $new_report->incident_dateadd;

						// Make sure that at least one of the personal info field entries is provided
						if ($firstname OR $lastname OR $email != NULL)
						{
							$new_incident_person->person_first = $firstname ? $firstname: NULL;
							$new_incident_person->person_last = $lastname ? $firstname: NULL;
							$new_incident_person->person_email = $email;
							$new_incident_person->save();

							// Add this to array of incident persons added during import
							$this->incident_persons_added[] = $new_incident_person->id;
						}	
					}			


					/* Step 6: Save media links for this report */
					// Report Media
					$media = $report->getElementsByTagName('media') ;
					if ($media->length > 0)
					{
						$media = $media->item(0);

						foreach($media->getElementsByTagName('item') as $media_element)
						{
							$media_link = trim($media_element->nodeValue);
							$media_date = $media_element->getAttribute('date');
							if ( ! empty($media_link))
							{
								$media_item = new Media_Model();
								$media_item->location_id = isset($new_location) ? $new_location->id : 0;
								$media_item->incident_id = $new_report->id;
								$media_item->media_type = $media_element->getAttribute('type');
								$media_item->media_link = $media_link;
								$media_item->media_date = ! empty($media_date) ? $media_date : $new_report->incident_date;
								$media_item->save();
							}
						}
					}
				}
			}
		}
		// end individual report import
		
		// If we have errors, return FALSE, else TRUE
		return count($this->errors) === 0;
	}
	
	/**
	 * Function to undo import of data
	 */
	private function rollback()
	{
		if (count($this->categories_added)) ORM::factory('category')->delete_all($this->categories_added);
		if (count($this->category_translations_added)) ORM::factory('category_lang')->delete_all($this->category_translations_added);
		if (count($this->forms_added)) ORM::factory('form')->delete_all($this->forms_added);
		if (count($this->fields_added)) ORM::factory('form_field')->delete_all($this->fields_added);
		if (count($this->field_options_added)) ORM::factory('form_field_option')->delete_all($this->field_options_added);
		if (count($this->incidents_added)) ORM::factory('incident')->delete_all($this->incidents_added);
		if (count($this->locations_added)) ORM::factory('location')->delete_all($this->locations_added);
		if (count($this->incident_categories_added)) ORM::factory('incident_category')->delete_all($this->incident_categories_added);
		if (count($this->incident_persons_added)) ORM::factory('incident_person')->delete_all($this->incident_persons_added);
		if (count($this->incident_responses_added)) ORM::factory('form_response')->delete_all($this->incident_responses_added);
	}
	
	/**
	 * Function to save form field options
	 * @param ORM instance $model Form Field Option Model instance
	 * @param int $fieldid Form Field id
	 * @param string $option_name Either field_datatype or field_hidden
	 * @param mixed $option_value Dependent on whether its datatype/hidden option
 	 *
	 */
	private function _save_field_option($model, $fieldid, $option_name, $option_value)
	{
		$model->form_field_id = $fieldid;
		$model->option_name = $option_name;
		$model->option_value = $option_value;
		$model->save();
	}
}
?>
