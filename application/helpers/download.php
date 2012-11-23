<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Reports Download helper class.
 *
 * @package	   Admin
 * @author	   Ushahidi Team
 * @copyright  (c) 2012 Ushahidi Team
 * @license	   http://www.ushahidi.com/license.html
 */
class download_Core {
	/**
	 * Validation of form fields
	 *
	 * @param array $post Values to be validated
	 */
	public static function validate(array & $post)
	{
		// Exception handling
		if ( ! isset($post) OR ! is_array($post))
			return FALSE;
		
		// Create validation object
		$post = Validation::factory($post)
				->pre_filter('trim', TRUE)
				->add_rules('format','required')
				->add_rules('data_active.*','required','numeric','between[0,1]')
				->add_rules('data_verified.*','required','numeric','between[0,1]')
				->add_rules('data_include.*','numeric','between[1,7]')
				->add_rules('from_date','date_mmddyyyy')
				->add_rules('to_date','date_mmddyyyy');
				
		// Validate the report dates, if included in report filter
		if (!empty($post->from_date) OR !empty($post->to_date))
		{
			// Valid FROM Date?
			if (empty($post->from_date) OR (strtotime($post->from_date) > strtotime("today")))
			{
				$post->add_error('from_date','range');
			}

			// Valid TO date?
			if (empty($post->to_date) OR (strtotime($post->to_date) > strtotime("today")))
			{
				$post->add_error('to_date','range');
			}

			// TO Date not greater than FROM Date?
			if (strtotime($post->from_date) > strtotime($post->to_date))
			{
				$post->add_error('to_date','range_greater');
			}
		}
			
		// Make sure valid format is passed
		if ($post->format !='csv' AND $post->format !='xml')
		{
			$post->add_error('format','valid');
		}	
				
		// Return
		return $post->validate();
	}
	
	/**
	 * Download Reports in CSV format
	 * @param Validation $post Validation object with the download criteria 
	 * @param array $incidents Reports to be downloaded
	 * @param array $custom_forms Custom form field structure and values
	 */
	public static function download_csv($post, $incidents, $custom_forms)
	{
		// Column Titles
		ob_start();
		echo "#,FORM #,INCIDENT TITLE,INCIDENT DATE";
		$item_map = array(
		    1 => 'LOCATION',
		    2 => 'DESCRIPTION',
		    3 => 'CATEGORY',
		    4 => 'LATITUDE',
		    5 => 'LONGITUDE',
		    7 => 'FIRST NAME, LAST NAME, EMAIL'
		);
		
		foreach($post->data_include as $item)
		{		
			if ( (int)$item == 6)
			{
				foreach($custom_forms as $field_name)
				{
					echo ",".$field_name['field_name']."-".$field_name['form_id'];
				}

			}
			else if ( array_key_exists($item, $item_map))
			{
			    echo sprintf(",%s", $item_map[$item]);
			}
		}

		echo ",APPROVED,VERIFIED";

		// Incase a plugin would like to add some custom fields
		$custom_headers = "";
		Event::run('ushahidi_filter.report_download_csv_header', $custom_headers);
		echo $custom_headers;

		echo "\n";

		foreach ($incidents as $incident)
		{
			echo '"'.$incident->id.'",';
			echo '"'.$incident->form_id.'",';
			echo '"'.self::_csv_text($incident->incident_title).'",';
			echo '"'.$incident->incident_date.'"';

			foreach($post->data_include as $item)
			{
				switch ($item)
				{
					case 1:
					echo ',"'.self::_csv_text($incident->location->location_name).'"';
					break;

					case 2:
					echo ',"'.self::_csv_text($incident->incident_description).'"';
					break;

					case 3:
					echo ',"';

					foreach($incident->incident_category as $category)
					{
						if ($category->category->category_title)
						{
							echo self::_csv_text($category->category->category_title) . ", ";
						}
					}
					echo '"';
					break;

					case 4:
					echo ',"'.self::_csv_text($incident->location->latitude).'"';
					break;

					case 5:
					echo ',"'.self::_csv_text($incident->location->longitude).'"';
					break;

					case 6:
					$incident_id = $incident->id;
					$custom_fields = customforms::get_custom_form_fields($incident_id,'',false);
					if ( ! empty($custom_fields))
					{
						foreach($custom_fields as $custom_field)
						{
							echo',"'.self::_csv_text($custom_field['field_response']).'"';
						}
					}
					else
					{
						$custom_field = customforms::get_custom_form_fields('','',false);
						foreach ($custom_field as $custom)
						{
							echo',"'.self::_csv_text("").'"';
						}
					}
					break;

					case 7:
					$incident_person = $incident->incident_person;
					if($incident_person->loaded)
					{
						echo',"'.self::_csv_text($incident_person->person_first).'"'.',"'.self::_csv_text($incident_person->person_last).'"'.
							',"'.self::_csv_text($incident_person->person_email).'"';
					}
					else
					{
						echo',"'.self::_csv_text("").'"'.',"'.self::_csv_text("").'"'.',"'.self::_csv_text("").'"';
					}
					break;
				}
			}

			if ($incident->incident_active)
			{
				echo ",YES";
			}
			else
			{
				echo ",NO";
			}

			if ($incident->incident_verified)
			{
				echo ",YES";
			}
			else
			{
				echo ",NO";
			}

			// Incase a plugin would like to add some custom data for an incident
			$event_data = array("report_csv" => "", "incident" => $incident);
			Event::run('ushahidi_filter.report_download_csv_incident', $event_data);
			echo $event_data['report_csv'];
			echo "\n";
		}
		$report_csv = ob_get_clean();

		return $report_csv;
	}
	
	/**
	 * Download Reports in XML format
	 * @param Validation $post Validation object with the download criteria 
	 * @param array $incidents reports to be downloaded
	 * @param array $categories deployment categories
	 * @param array $custom_forms Custom form field structure and values
	 */
	public static function download_xml($post, $incidents, $categories, $custom_forms)
	{
		// Adding XML Content
		$writer = new XMLWriter;
		$writer->openMemory();
		$writer->startDocument('1.0', 'UTF-8');
		$writer->setIndent(true);
		
		/* Category Element/Attribute maps */
		// Category map
		$category_map = array(
			'attributes' => array(
				'color' => 'category_color',
				'visible' => 'category_visible',
				'trusted' => 'category_trusted'
			),
			'elements' => array(
				'title' => 'category_title',
				'description' => 'category_description'
			)
		);

		// Array of category elements
		$category_elements = array('color', 'visible', 'trusted', 'title', 'description','parent');
		
		/* Category translation Element/Attribute maps */
		// Translation map
		$translation_map = array(
			'attributes' => array(
				'locale' => 'locale',
			),
			'elements' => array(
				'transtitle' => 'category_title',
				'transdescription' => 'category_description'
			)
		);
		
		// Array of translation elements
		$translation_elements = array('locale', 'transtitle', 'transdescription');
		
		/* Form element/attribute maps */
		// Forms map
		$form_map = array(
					'attributes' => array(
						'active' => 'form_active'
						),
					'elements' => array(
						'title' => 'form_title',
						'description' => 'form_description'
						)
					);
					
		// Forms element
		$form_elements = array('active', 'title', 'description');
		
		/* Custom fields element/attribute maps */	
		// Field elements
		$form_field_elements = array('type', 'required', 'visible-by', 'submit-by', 'datatype', 'hidden', 'name', 'default');
		
		/* Reports element/attribute maps */
		// Report map
		$report_map = array(
						'attributes' => array(
							'id' => 'id',
							'approved' => 'incident_active',
							'verified' => 'incident_verified',
							'mode' => 'incident_mode',
						),
						'elements' => array(
							'title' => 'incident_title',
							'date' => 'incident_date',
							'dateadd' => 'incident_dateadd',
						)
					);
					
		// Report elements
		$report_elements = array('id', 'approved', 'verified', 'form_id', 'mode', 'title', 'date', 'dateadd');
		
		// Location Map
		$location_map = array(
			'attributes' => array(),
			'elements' => array(
				'name' => 'location_name',
				'longitude' => 'longitude',
				'latitude' => 'latitude'		
			)
		);
		
		// Location elements
		$location_elements = array('name', 'longitude', 'latitude');
		
		// Media Map
		$media_map = array(
						'attributes' => array(
							'type' => 'media_type',
							'active' => 'media_active',
							'date' => 'media_date'
						),
						'elements' => array()
					);
		
		// Media elements
		$media_elements = array('type', 'active', 'date');
		
		// Personal info map
		$person_map = array(
						'attributes' => array(),
						'elements' => array(
							'firstname' => 'person_first',
							'lastname' => 'person_last',
							'email' => 'person_email'	
						)
					);
	
		// Personal info elements
		$person_elements = array('firstname', 'lastname', 'email');
		
		// Incident Category map
		$incident_category_map = array(
									'attributes' => array(),
									'elements' => array(
										'category' => 'category_title',
									)
			
								);
					
		// Incident Category elements 
		$incident_category_elements = array('category');
												
		/* Start Import Tag*/
		$writer->startElement('import');
		foreach ($post->data_include as $item)
		{
			switch($item)
			{
				case 3:
				/* Start Categories element */
				$writer->startElement('categories');
				if (count($categories) > 0)
				{
					foreach ($categories as $category)
					{
						// Begin individual category tag	
						$writer->startElement('category');	
						
						// Generate category element map
						$category_element_map = xml::generate_element_attribute_map($category, $category_map);
						
						if ($category->parent_id > 0)
						{
							// Category's parent
							$parent = ORM::factory('category', $category->parent_id);
						
							// If parent category exists
							if ($parent->loaded)
							{
								// Add to array of category_element_map for purposes of generating tags
								$category_element_map['elements']['parent'] = $parent->category_title;
							}
						}
						
						// Generate individual category tags						
						xml::generate_tags($writer, $category_element_map, $category_elements);

						// Category Translation
						$translations = ORM::factory('category_lang')->where('category_id', $category->id)->find_all();
				
						// If translations exist
						if (count($translations) > 0)
						{
							$writer->startElement('translations');
							foreach ($translations as $translation)
							{
								// Begin individual translation element
								$writer->startElement('translation');
								
								// Generate translation element map
								$translation_element_map = xml::generate_element_attribute_map($translation, $translation_map); 

								// Generate translation tags
								xml::generate_tags($writer, $translation_element_map, $translation_elements);
								
								// End individual category translation tag
								$writer->endElement();
							}
							$writer->endElement();
						}	
						$writer->endElement();
					}	
				}
		
				// If there are no categories
				else
				{
					$writer->text('There are no categories on this deployment.');
				}
		
				/* Close Categories Element */
				$writer->endElement();
				break;
				
				case 6:
				/* Start Customforms Element */
				$writer->startElement('customforms');
			
				// If we have custom forms
				if (count($custom_forms) > 0)
				{
					foreach ($custom_forms as $form)
					{	
						// Custom Form element
						$writer->startElement('form');
						
						// Generate form elements map
						$form_element_map = xml::generate_element_attribute_map($form, $form_map);
						
						// Generate form element tags
						xml::generate_tags($writer, $form_element_map, $form_elements);
					
						// Get custom fields associated with this form
						$form_fields = customforms::get_custom_form_fields('',$form->id,'');
						foreach ($form_fields as $field)
						{
							// Make sure this custom form field belongs to the current form
							if ($field['form_id'] == $form->id)
							{
								// Custom Form Fields
								$writer->startElement('field');
							
								$form_field_map = array(
													'attributes' => array(
														'type' => $field['field_type'],
														'required' => $field['field_required'],
														'visible-by' => $field['field_ispublic_visible'],
														'submit-by' => $field['field_ispublic_submit']
														),
													'elements' => array()
													);
								
								/* Get custom form field options */
								$options = ORM::factory('form_field_option')->where('form_field_id',$field['field_id'])->find_all();
								foreach ($options as $option)
								{
									if ($option->option_name == 'field_datatype')
									{
										// Data type i.e Free, Numeric, Email, Phone?
										$form_field_map['attributes']['datatype'] = $option->option_value;
									}
									if ($option->option_name == 'field_hidden')
									{
										// Hidden Field?
										$form_field_map['attributes']['hidden'] = $option->option_value;
									}
								}
							
								// Field name
								$form_field_map['elements']['name'] = $field['field_name'];

								// Default Value
								if ($field['field_default'] != '')
								{
									$form_field_map['elements']['default'] = $field['field_default'];
								}
	
								// Generate custom fields tags
								xml::generate_tags($writer, $form_field_map, $form_field_elements);
	
								// Close Custom form field element	
								$writer->endElement();
							}
						} 
				
						// Close Custom Form Element
						$writer->endElement();	
					}	
				}
		
				// We have no Custom forms
				else
				{
					$writer->text('There are no custom forms on this deployment.');
				}
		
				/* End Custom Forms Element */
				$writer->endElement();
				break;
			}
		}
			
		/* Start Reports Element*/
		$writer->startElement('reports');
		
			
		// If we have reports on this deployment
		if (count($incidents) > 0)
		{	
			foreach ($incidents as $incident)
			{
				// Start Individual report
				$writer->startElement('report');
								
				// Generate report map
				$report_element_map = xml::generate_element_attribute_map($incident, $report_map);
				
				// Form this incident belongs to?
				$form = ORM::factory('form')->find($incident->form_id);
				$form_name = $form->loaded ? $form->form_title : '';
				
				// Add it to report element map
				$report_element_map['attributes']['form_id'] = $form_name;
				
				// Generate report tags
				xml::generate_tags($writer, $report_element_map, $report_elements);
				
				// Report Media
				$reportmedia = $incident->media;
						
				if (count($reportmedia) > 0)
				{
					$writer->startElement('media');
					foreach ($reportmedia as $media)
					{
						// Videos and news links only
						if ($media->media_type == 2 OR $media->media_type == 4)
						{
							$writer->startElement('item');
									
							// Generate media elements map 
							$media_element_map = xml::generate_element_attribute_map($media, $media_map);
									
							// Generate media elements
							xml::generate_tags($writer, $media_element_map, $media_elements);
									
							$writer->endAttribute();
							$writer->text($media->media_link);
							
							// Close item tag
							$writer->endElement();
						}
					}
					$writer->endElement();
				}
								
				foreach($post->data_include as $item)
				{
					switch($item)
					{	
						// Report Description
						case 2:
						$writer->startElement('description');
							$writer->text($incident->incident_description);
						$writer->endElement();
						break;
						
						// Report Location
						case 1:
						$writer->startElement('location');
						
						// Generate location map
						$location_map_element = xml::generate_element_attribute_map($incident->location, $location_map);
						
						// Generate location tags
						xml::generate_tags($writer, $location_map_element, $location_elements);
						
						// Close location tag
						$writer->endElement();
						break;

						case 7:
						
						// Report Personal information
						$incident_person = $incident->incident_person;
						if ($incident_person->loaded)
						{
							$writer->startElement('personal-info');
							
							// Generate incident person element map
							$person_element_map = xml::generate_element_attribute_map($incident_person, $person_map);
							
							// Generate incident person element tags
							xml::generate_tags($writer, $person_element_map, $person_elements);
							
							// Close personal info tag	
							$writer->endElement();
						}
						break;

						case 3:
						
						// Report Category
						$writer->startElement('reportcategories');
						foreach($incident->incident_category as $category)
						{
							// Generate Incident Category Element Map
							$incident_category_element_map = xml::generate_element_attribute_map($category->category, $incident_category_map);
							
							// Generate Incident Category Tags
							xml::generate_tags($writer, $incident_category_element_map, $incident_category_elements);
						}
						$writer->endElement();
						break;

						case 6:
						
						// Report Fields
						$customresponses = customforms::get_custom_form_fields($incident->id,$incident->form_id,false);
						if ( ! empty($customresponses))
						{
							$writer->startElement('customfields');
							foreach($customresponses as $customresponse)
							{
								// If we don't have an empty form response
								if ($customresponse['field_response'] != '')
								{
									$writer->startElement('field');
										$writer->startAttribute('name');
											$writer->text($customresponse['field_name']);
										$writer->endAttribute();
										$writer->text($customresponse['field_response']);
									$writer->endElement();
								}
							}
							$writer->endElement();
						}
						break;
					}
				}
				
				// Close individual report	
				$writer->endElement();
			}
		}
		else
		{
			$writer->text('There are no reports on this deployment.');
		}
		
		/* Close reports Element */	
		$writer->endElement();

		/* Close import tag */
		$writer->endElement();

		// Close the document
		$writer->endDocument();

		// Print
		return $writer->outputMemory(TRUE);
		
	}
	
	private static function _csv_text($text)
	{
		$text = stripslashes(htmlspecialchars($text));
		return $text;
	}
}
?>