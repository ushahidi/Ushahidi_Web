<?php defined('SYSPATH') or die('No direct script access');

/**
 * Unit tests for the XML Reports download via the Download helper
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module	   Unit Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
 
 class Download_Helper_Test extends PHPUnit_Framework_TestCase{
	
	public function setUp()
	{
		// Set up post variable
		$this->post = array(
			'format' =>'xml',
			'data_active'   => array(0, 1),
			'data_verified'   => array(0, 1),
			'data_include' => array(1, 2, 3, 4, 5, 6, 7),
			'from_date'	   => '',
			'to_date'	   => '',
		);
		
		// Categories object : Limit it to one category only
		$this->category = ORM::factory('category')
							->join('category_lang', 'category.id', 'category_lang.category_id', 'inner')
							->where('parent_id !=', 0)
							->limit(1)
							->find_all();

		// Incidents object : Limit it to one incident only
		$this->incident = ORM::factory('incident')->limit(1)->find_all();
		
		// Forms object to be used for XML download : Limit it to one custom form only
		$this->forms = ORM::factory('form')->join('form_field','form_field.form_id', 'form.id', 'inner')->limit(1)->find_all();
		
		// Custom forms object to be used for CSV download
		$this->custom_forms = customforms::get_custom_form_fields('','',false);
	}
	
	public function tearDown()
	{
		unset($this->post, $this->category, $this->incident, $this->forms);
	}
	
	/**
	 * Data Provider for testGenerateArrayMap
	 * @dataProvider
	 */
	public function providerTestGenerateArrayMap()
	{
		/* Category Element/Attribute maps */
		// Select a random category
		$category = ORM::factory('category', testutils::get_random_id('category'));
		
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

		// Expected category array map
		$category_element_map = array(
			'attributes' => array(
				'color' => $category->category_color,
				'visible' => $category->category_visible,
				'trusted' => $category->category_trusted
			),
			'elements' => array(
				'title' => $category->category_title,
				'description' => $category->category_description
			)
		);
							
		/* Category translation Element/Attribute maps */
		// Translation ORM Object
		$translation = ORM::factory('category_lang', testutils::get_random_id('category_lang', 'WHERE category_id ='.$category->id.''));
		
		// Translation map
		$translation_map = array(
			'attributes' => array(
				'locale' => 'locale',
			),
			'elements' => array(
				'translation_title' => 'category_title',
				'translation_description' => 'category_description'
			)
		);

		// Expected translation array map
		$translation_element_map = array(
			'attributes' => array(
				'locale' => $translation->locale,
			),
			'elements' => array(
				'translation_title' => $translation->category_title,
				'translation_description' => $translation->category_description
			)
		);
		
		/* Form element/attribute maps */
		// Select a random form
		$form = ORM::factory('form', testutils::get_random_id('form'));
		
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
						
		// Expected form array map
		$form_element_map = array(
			'attributes' => array(
				'active' => $form->form_active
			),
			'elements' => array(
				'title' => $form->form_title,
				'description' => $form->form_description
			)
		);
		
		/* Reports element/attribute maps */
		// Select a random incident
		$incident = ORM::factory('incident', testutils::get_random_id('incident'));
		
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
				'description' => 'incident_description'
			)
		);
					
		// Expected report array map
		$report_element_map = array(
			'attributes' => array(
				'id' => $incident->id,
				'approved' => $incident->incident_active,
				'verified' => $incident->incident_verified,
				'mode' => $incident->incident_mode,
			),
			'elements' => array(
				'title' => $incident->incident_title,
				'date' => $incident->incident_date,
				'dateadd' => $incident->incident_dateadd,
				'description' => $incident->incident_description
			)
		);
		
		/* Report Location */
		// Report location ORM object
		$location = $incident->location;
		
		// Location Map
		$location_map = array(
			'attributes' => array(),
			'elements' => array(
				'name' => 'location_name',
				'longitude' => 'longitude',
				'latitude' => 'latitude'		
			)
		);

		// Expected location array map
		$location_element_map = array(
			'attributes' => array(),
			'elements' => array(
				'name' => $location->location_name,
				'longitude' => $location->longitude,
				'latitude' => $location->latitude		
			)
		);
								
		/* Report Media */
		// Report Media ORM Object
		$media = ORM::factory('media', testutils::get_random_id('media', 'WHERE incident_id ='.$incident->id.''));
		
		// Media Map
		$media_map = array(
			'attributes' => array(
				'type' => 'media_type',
				'active' => 'media_active',
				'date' => 'media_date'
			),
			'elements' => array()
		);

		// Expected media array map
		$media_element_map = array(
			'attributes' => array(
				'type' => $media->media_type,
				'active' => $media->media_active,
				'date' => $media->media_date
			),
			'elements' => array()
		);
		
		/* Report personal info */
		// Personal info ORM Object
		$person = $incident->incident_person;
		
		// Personal info map
		$person_map = array(
			'attributes' => array(),
			'elements' => array(
				'firstname' => 'person_first',
				'lastname' => 'person_last',
				'email' => 'person_email'	
			)
		);
		
		// Expected personal info array map
		$person_element_map = array(
			'attributes' => array(),
			'elements' => array(
				'firstname' => $person->person_first,
				'lastname' => $person->person_last,
				'email' => $person->person_email	
			)
		);		
		
		/* Incident Categories */
		// Incident Category ORM Object
		$incident_cat = ORM::Factory('category')
						->join('incident_category','incident_category.category_id','category.id','inner')
						->where('incident_category.incident_id', $incident->id)
						->limit(1)
						->find();
								
		// Incident Category map
		$incident_cat_map = array(
			'attributes' => array(),
			'elements' => array(
				'category' => 'category_title',
			)
		);
		
		// Expected incident category array Map
		$incident_cat_element_map = array(
			'attributes' => array(),
			'elements' => array(
				'category' => $incident_cat->category_title,
			)
		);							
							
		return array(
			array($category_map, $category_element_map, $category, 'Category'),
			array($translation_map, $translation_element_map, $translation, 'Category Translation'),
			array($form_map, $form_element_map, $form, 'Form'),
			array($report_map, $report_element_map, $incident, 'Report'),
			array($location_map, $location_element_map, $location, 'Report Location'),
			array($media_map, $media_element_map, $media, 'Report Media'),
			array($person_map, $person_element_map, $person, 'Reporter'),
			array($incident_cat_map, $incident_cat_element_map, $incident_cat, 'Incident category')
		);
	}
	
	/**
	 * Tests download helper function which generates object array maps
	 * to be used to generate XML element tags
	 * @test
	 * @dataProvider providerTestGenerateArrayMap
	 * @param array $object_map associative array map skeleton 
	 * @param array $expected_map expected output
	 * @param object $object_orm ORM object
	 * @param string $object_name
	 */
	public function testGenerateArrayMap($object_map, $expected_map, $object_orm, $object_name)
	{		
		// Get array map returned by download helper function
		$actual_map = xml::generate_element_attribute_map($object_orm, $object_map);
		
		// For the random category
		if ($object_name == 'Category')
		{
			// Check if this category has a parent
			if ($object_orm->parent_id > 0)
			{
				// Fetch the parent category
				$parent = ORM::Factory('category', $object_orm->parent_id);
				
				// Add category parent to actual_map and expected_map
				$expected_map['elements']['parent'] = $parent->category_title;
				$actual_map['elements']['parent'] = $parent->category_title;
			}	
		}
		
		if ($object_name == 'Report')
		{
			// Make sure the incident_form is loaded
			if ($object_orm->form->loaded)
			{
				// Add form_name attribute to actual map and expected map
				$expected_map['attributes']['form_name'] = $object_orm->form->form_title;
				$actual_map['attributes']['form_name'] = $object_orm->form->form_title;
			}
		}
		
		// Test to ensure expected array map and actual array map match
		$this->assertEquals($expected_map, $actual_map, 'Output does not match expected array for the '.$object_name.' object');	
	}
	/**
	 * Test XML Tag generation
	 * @test
	 * @return string $xml_content
	 */
	public function testDownloadXML()
	{	
		/* Test XML Tag generation */
		// Test to ensure validation passed
		$this->assertEquals(TRUE, download::validate($this->post), 'Report download validation failed');
		
		// Load XML Content into a string
		$xml_content = download::download_xml($this->post, $this->incident, $this->category, $this->forms);
		
		// Make sure string holding XML Content is not empty
		$this->assertNotEmpty($xml_content, 'XML Download Failed');
		
		return $xml_content;
	}
	
	/**
	 * Load XML Content generated and check for Categories, Custom Forms and Reports tags
	 * @test
	 * @depends testDownloadXML
	 * @param string $xml_content
	 */
	public function testReadDownloadXML($xml_content)
	{
		// XML Reader
		$reader = new DOMDocument('1.0');
		
		// Load XML string into reader
		$reader->loadXML($xml_content);
		
		// Ensure that the XML String is loaded
		$this->assertTrue(@$reader->loadXML($xml_content), 'XML Content loading failed');
		
		// Check for categories, customforms and reports elements
		$d_categories = $reader->getElementsByTagName('categories');
		$d_customforms = $reader->getElementsByTagName('custom_forms');
		$d_reports = $reader->getElementsByTagName('reports');
		
		// Ensure that at least one of the elements i.e categories, customforms OR reports exist
		$tag_exists = ($d_categories->length == 0 AND $d_customforms->length == 0 AND $d_reports->length == 0) 
						? FALSE
						: TRUE;
		$this->assertTrue($tag_exists, 'XML content must have at least one of the following: Categories, Custom forms or Reports');	
		
		return array($d_categories, $d_customforms, $d_reports);
		
	}
	
	/**
	 * Tests whether XML Category element matches ORM objects provided for download
	 * @test
	 * @depends testReadDownloadXML
	 * @param array $dom_nodes DOMNodeList Objects
	 */
	public function testCheckCategoryXML(array $dom_nodes)
	{
		/* Category check */
		// Categories DOMNodeList Object 
		$d_categories = $dom_nodes[0];
		
		// When category option is not selected, make sure the categories element does not exist
		if ( ! in_array(3, $this->post['data_include']))
		{
			$this->assertEquals(0, $d_categories->length, 'The "categories" element should not exist');
		}
		
		// Download of categories option was provided by the user
		else
		{
			// Make sure the categories element exists
			$this->assertGreaterThan(0, $d_categories->length, 'The "categories" element SHOULD exist');
			
			// Contents of <categories> element
			$categories_element = $d_categories->item(0);
			
			// If we have no categories on this deployment
			if (count($this->category) == 0)
			{
				// Ensure the categories element has the following message
				$this->assertEquals('There are no categories on this deployment.', $categories_element->nodeValue);
			}
			
			// We have categories on this deployment
			else
			{
				// Individual category
				$cat = $this->category[0];
				
				// Grab contents of <category> element
				$category_element = $categories_element->getElementsByTagName('category');
				
				// Test to see if category element exists
				$this->assertGreaterThan(0, $category_element->length, 'Category element does not exist for deployment with existing categories');
			
				// Test category Color
				$color = xml::get_node_text($category_element->item(0), 'color', FALSE);
				$this->assertEquals($cat->category_color, $color, 'Category Color does not match/ Color attribute does not exist');
				
				// Test category Visible
				$visible = $category_element->item(0)->getAttribute('visible');
				$this->assertEquals($cat->category_visible, $visible, 'Category visible status does not match/attribute does not exist');
				
				// Test category Trusted
				$trusted = $category_element->item(0)->getAttribute('trusted');
				$this->assertEquals($cat->category_trusted, $trusted, 'Category trusted status does not match/attribute does not exist');
				
				// Test category Title
				$title = xml::get_node_text($category_element->item(0), 'title');
				$this->assertEquals($cat->category_title, $title, 'Category title does not match/ title element does not exist');
				
				// Test category Description
				$description = xml::get_node_text($category_element->item(0), 'description');
				$this->assertEquals($cat->category_description, $description, 'Category description does not match/the element does not exist');
				
				// Test category Parent
				if ($cat->parent_id > 0)
				{
					// Fetch the parent category
					$parent = ORM::Factory('category', $cat->parent_id);
					$parent_title = xml::get_node_text($category_element->item(0), 'parent');
					$this->assertEquals($parent->category_title, $parent_title, 'Category parent title does not match/parent element does not exist');
				}
				
				/* Translation Check */
				// Grab contents of <translations> element
				$translations_element = $categories_element->getElementsByTagName('translations');
				
				// Grab the category translations
				$translations = ORM::Factory('category_lang')->where('category_id', $cat->id)->find_all();
				$translation_count = count($translations);
				
				// If we actually have translations for this category
				if ( $translation_count > 0)
				{
					// Pick out a random translation by generating a random index to select based on this count
					$index = rand(0, $translation_count-1);
					$translation = $translations[$index];
					
					// Test to see if the translations element exists
					$this->assertGreaterThan(0, $translations_element->length, 'Translations element does not exist for category with translations');
					
					// Grab contents of individual <translation> elements
					$translation_element = $translations_element->item(0)->getElementsByTagName('translation');
					
					// Test to see if the <translation> element exists
					$this->assertGreaterThan(0, $translation_element->length, 'Translation element does not exist for category with translations');
					
					// Test Translation locale
					$locale = xml::get_node_text($translation_element->item($index), 'locale', FALSE);
					$this->assertEquals($translation->locale, $locale, 'Translation locales do not match/ attribute does not exist');
					
					// Test Translation category title
					$transtitle = xml::get_node_text($translation_element->item($index), 'translation_title');
					$this->assertEquals($translation->category_title, $transtitle, 'Translation titles do not match/ element does not exist');
					
					// Test Translation category description
					$transdescription = xml::get_node_text($translation_element->item($index), 'translation_description');
					$this->assertEquals($translation->category_description, $transdescription, 'Translation descriptions do not match/ element does not exist');
				}
				
				// If we don't have translations for this category
				else
				{
					// Test to ensure that the translations element does NOT exist
					$this->assertEquals(0, $translations_element->length, 'Translations element should not exist for category with no translations');
				}
			}
		}
	}
	
	/**
	 * Tests whether XML Custom form element matches ORM objects provided for download
	 * @test
	 * @depends testReadDownloadXML
	 * @param array $domnodes DOMNodeList Objects
	 */
	
	public function testCheckCustomFormXML(array $dom_nodes)
	{
		/* Custom form check */
		$d_customforms = $dom_nodes[1];
		
		// When custom forms option is not selected, make sure the custom forms element does not exist
		if ( ! in_array(6, $this->post['data_include']))
		{
			$this->assertEquals(0, $d_customforms->length, 'The "custom_forms" element should not exist');
		}
		
		// Custom forms option is selected
		else
		{
			// Test to make sure <customforms> element exists
			$this->assertGreaterThan(0, $d_customforms->length, 'The "custom_forms" element SHOULD exist');
			
			// Contents of <customforms> element
			$forms_element = $d_customforms->item(0);
			
			// If we don't have custom forms on this deployment
			if (count($this->forms) == 0)
			{
				// Ensure the customforms element has the following message
				$this->assertEquals('There are no custom forms on this deployment.', $d_customforms->item(0)->nodeValue);
			}
			
			// We have custom forms on this deployment
			else
			{
				// Grab individual form
				$form = $this->forms[0];
				
				// Grab contents of <form> element
				$form_element = $forms_element->getElementsByTagName('form');
				
				// Test to see if the <form> element exists
				$this->assertGreaterThan(0,$form_element->length, 'The "form" element does not exist for a deployment with forms');
				
				// Test Form active status
				$active = $form_element->item(0)->getAttribute('active');
				$this->assertEquals($form->form_active, $active, 'Form active status does not match/attribute does not exist');
				
				// Test Form title
				$title = xml::get_node_text($form_element->item(0), 'title');
				$this->assertEquals($form->form_title, $title, 'Form title does not match/element does not exist');
				
				// Test Form description
				$description = xml::get_node_text($form_element->item(0), 'description');
				$this->assertEquals($form->form_description, $description, 'Form description does not match/element does not exist');
				
				/* Custom fields check */
				// Get custom fields associated with this form
				$form_fields = ORM::factory('form_field')
								->join('roles', 'roles.id', 'field_ispublic_visible', 'left')
								->where('form_id', $form->id)
								->orderby('field_position', 'ASC')
								->find_all();
				
				// Get custom field count, 
				$field_count = count($form_fields);
				$field_elements = $form_element->item(0)->getElementsByTagName('field');
				
				if ($field_count > 0)
				{
					$this->assertGreaterThan(0, $field_elements->length, 'This form has form fields. Field element should exist');
					
					// Grab a random custom field by generating a random index to select based on field count
					$field_index = rand(0, $field_count-1);
					$field = $form_fields[$field_index];
					
					// Grab the random field's corresponding element in XML download text
					$field_element = $field_elements->item($field_index);
					
					// Make sure this particular <field> element actually exists
					$this->assertNotNull($field_element, 'The field element SHOULD exist');
					
					// Test field type
					$type = $field_element->getAttribute('type');
					$this->assertEquals($field->field_type, $type, 'Field type does not match/attribute does not exist');
					
					// Test field required
					$required = $field_element->getAttribute('required');
					$this->assertEquals($field->field_required, $required, 'Field required does not match/attribute does not exist');
					
					// Test field visibility status
					$visible_by = $field_element->getAttribute('visible_by');
					$this->assertEquals($field->field_ispublic_visible, $visible_by, 'Field visible status does not match/attribute does not exist');
					
					// Test field submit status
					$submit_by = $field_element->getAttribute('submit_by');
					$this->assertEquals($field->field_ispublic_submit, $submit_by, 'Field submit status does not match/attribute does not exist');
					
					// Test field options
					// Check for field options
					$options = ORM::factory('form_field_option')->where('form_field_id',$field->id)->find_all();
					
					// If this field has field options
					if (count($options) > 0)
					{
						foreach ($options as $option)
						{
							// Test field datatype
							if ($option->option_name == 'field_datatype')
							{
								// Make sure the datatype attribute exists
								$this->assertEquals(TRUE, $field_element->hasAttribute('datatype'), 'Field datatype attribute should exist');
								
								$datatype = xml::get_node_text($field_element, 'datatype', FALSE);
								$this->assertEquals($option->option_value, $datatype, 'Datatype options do not match/attribute does not exist');
							}
							
							// Test field hidden
							if ($option->option_name == 'field_hidden')
							{
								// Make sure the datatype attribute exists
								$this->assertEquals(TRUE, $field_element->hasAttribute('hidden'), 'Field hidden attribute should exist');
								
								$hidden = $field_element->getAttribute('hidden');
								$this->assertEquals($option->option_value, $hidden, 'Hidden options do not match/attribute does not exist');
							}
						}
					}
					
					// Hidden/Datatype attributes should not exist
					else
					{
						$this->assertEquals(FALSE, $field_element->hasAttribute('hidden'), 'Field has no hidden option');
						$this->assertEquals(FALSE, $field_element->hasAttribute('datatype'), 'Field has no datatype option');
					}
					
					// Test field name
					$name = xml::get_node_text($field_element, 'name');
					$this->assertEquals($field->field_name, $name, 'Field names do not match/element does not exist');
					
					// Test Field default
					$default = xml::get_node_text($field_element, 'default');
					
					// If field does not have a default value
					if ($field->field_default != '')
					{
						$this->assertEquals($field->field_default, $default, 'Field defaults so not match/element does not exist');
					}
					
					// Field has a default value
					else
					{
						$this->assertEquals(FALSE, $default, 'Field default does not exist for this field');
					}	
				}
				
				else
				{
					$this->assertEquals(0, $field_elements->length, 'This form has no form fields. Field element should NOT exist');
				}
			}
		}
	}
	
	/**
	 * Tests whether XML Report element matches ORM objects provided for download
	 * @test
	 * @depends testReadDownloadXML
	 * @param array $domnodes DOMNodeList Objects
	 */
	public function testCheckReportsXML(array $domnodes)
	{
		$d_reports = $domnodes[2];
		
		// Ensure that the DOMNodeList Object is not empty
		$this->assertGreaterThan(0, $d_reports->length, 'Reports Element MUST exist.');
		
		// Contents of <Reports> element
		$reports_element = $d_reports->item(0);
		
		/* Report Check */
		// If we have no reports on this deployment
		if (count($this->incident) == 0)
		{
			// Ensure the customforms element has the following message
			$this->assertEquals('There are no reports on this deployment.', $d_reports->item(0)->nodeValue);
		}
		
		// We have reports on this deployment
		else
		{
			// Grab individual Report
			$incident = $this->incident[0];
			
			// Grab contents of <report> element
			$report_element = $reports_element->getElementsByTagName('report');
			
			// Test to see if the <report> element exists
			$this->assertGreaterThan(0, $report_element->length, 'Report element does not exist for deployment with reports');
			
			/* Report Check */
			// Test report id
			$id = $report_element->item(0)->getAttribute('id');
			$this->assertEquals($incident->id, $id, 'Report id does not match/attribute does not exist');
			
			// Test Report approval status
			$approved = $report_element->item(0)->getAttribute('approved');
			$this->assertEquals($incident->incident_active, $approved, 'Report active status does not match/attribute does not exist');
			
			// Test Report verified status
			$verified = $report_element->item(0)->getAttribute('verified');
			$this->assertEquals($incident->incident_verified, $verified, 'Report verified status does not match/attribute does not exist');
			
			// Test Report mode status
			$mode = $report_element->item(0)->getAttribute('mode');
			$this->assertEquals($incident->incident_mode, $mode, 'Report mode does not match/attribute does not exist');
			
			// Test Report form_name
			// Grab Default form object
			$default_form = ORM::factory('form', 1);
			$expected_form = $incident->form->loaded ? $incident->form->form_title : $default_form->form_title;
			$form_name = xml::get_node_text($report_element->item(0), 'form_name', FALSE);
			$this->assertEquals($expected_form, $form_name, 'Report form_name does not match/attribute does not exist');
			
			// Test Report Title
			$title = xml::get_node_text($report_element->item(0), 'title');
			$this->assertEquals($incident->incident_title, $title, 'Report title does not match/element does not exist');
			
			// Test Report Date
			$date = xml::get_node_text($report_element->item(0), 'date');
			$this->assertEquals($incident->incident_date, $date, 'Report date does not match/element does not exist');
			
			// Test Report Dateadd
			$date_add = xml::get_node_text($report_element->item(0), 'dateadd');
			$this->assertEquals($incident->incident_dateadd, $date_add, 'Report dateadd does not match/element does not exist');
			
			// Test report description
			$description = xml::get_node_text($report_element->item(0), 'description');
			
			// If download report description option is selected by user
			if (in_array(2, $this->post['data_include']))
			{
				$this->assertEquals($incident->incident_description, $description, 'Report description does not match/element does not exist');
			}
			
			else
			{
				$this->assertEquals(FALSE, $description, 'Report description element should not exist');
			}
			
			/* Location Check */
			$locations_element = $report_element->item(0)->getElementsByTagName('location');
			$location = $incident->location;
			
			// Include location option has been selected
			if (in_array(1, $this->post['data_include']))
			{
				// Make sure the <location> element exists
				$this->assertGreaterThan(0, $locations_element->length, 'Report location element SHOULD exist');
				
				// Test location name
				$location_name = xml::get_node_text($locations_element->item(0),'name');
				$this->assertEquals($location->location_name, $location_name, 'Location name does not match/element does not exist');
				
				// Test Latitude
				$latitude = xml::get_node_text($locations_element->item(0),'latitude');
				$this->assertEquals($location->latitude, $latitude, 'Latitude does not match/element does not exist');
				
				// Test longitude
				$longitude = xml::get_node_text($locations_element->item(0),'longitude');
				$this->assertEquals($location->longitude, $longitude, 'Longitude does not match/element does not exist');
			}
			
			else
			{
				$this->assertEquals(0, $locations_element->length, "Report location element should not exist");
			}
		
			/* Media Check */
			$incident_media = ORM::Factory('media')
							->where('media_type = 2 OR media_type = 4')
							->where('incident_id', $incident->id)
							->find_all();
			
			$media_element = $report_element->item(0)->getElementsByTagName('media');
			if (count($incident_media) > 0)
			{
				$media_count = count($incident_media);
				$media_index = rand(0, $media_count-1);
				
				// Make sure the media element exists
				$this->assertGreaterThan(0, $media_element->length, 'The media element SHOULD exist');
				
				// Grab contents of media <item> element
				$media_item = $media_element->item(0)->getElementsByTagName('item');
				
				// Grab random individual media item
				$this_media = $incident_media[$media_index];
				
				if ( $this_media->media_type == 2 OR $this_media->media_type == 4 )
				{
					// Make sure the <item> element exists
					$this->assertEquals('item', $media_item->item($media_index)->tagName, 'The media item element SHOULD exist');
			
					// Test Media Type
					$media_type = $media_item->item($media_index)->getAttribute('type');
					$this->assertEquals($this_media->media_type, $media_type, 'Media type does not match/ attribute does not exist');
			
					// Test media active
					$media_active = $media_item->item($media_index)->getAttribute('active');
					$this->assertEquals($this_media->media_active, $media_active, 'Media active does not match/ attribute does not exist');
			
					// Test Media date
					$media_date = xml::get_node_text($media_item->item($media_index), 'date', FALSE);
					$this->assertEquals($this_media->media_date, $media_date, 'Media date does not match/ attribute does not exist');
			
					// Test media link
					$media_link = $media_item->item($media_index)->nodeValue;
					$this->assertEquals($this_media->media_link, $media_link, 'Media link does not match/ element does not exist');
				}
				else
				{
					// Make sure the <item> element does NOT exists for this particular media item
					$this->assertNull($media_item->item($media_index), 'The media item element SHOULD NOT exist');
				}
			}
			
			// We have no media
			else
			{
				// Make sure the media element does NOT exist
				$this->assertEquals(0, $media_element->length, 'The media element should NOT exist');
			}
		
			/* Personal info check */
			$person_info_element = $report_element->item(0)->getElementsByTagName('personal_info');
			$incident_person = $incident->incident_person;
			
			// Include personal info option selected?
			if (in_array(7, $this->post['data_include']))
			{
				// If we actually have an incident_person for this report
				if ($incident_person->loaded)
				{
					// Make sure the <personalinfo> element exists
					$this->assertGreaterThan(0, $person_info_element->length, 'Report personal-info element SHOULD exist');
				
					// Test First Name
					$firstname = xml::get_node_text($person_info_element->item(0), 'first_name');
					$this->assertEquals($incident_person->person_first, $firstname, 'Person first name does not match/ element does not exist');
				
					// Test last name
					$lastname = xml::get_node_text($person_info_element->item(0), 'last_name');
					$this->assertEquals($incident_person->person_last, $lastname, 'Person last name does not match/ element does not exist');
				
					// Test Email
					$email = xml::get_node_text($person_info_element->item(0), 'email');
					$this->assertEquals($incident_person->person_email, $email, 'Person email does not match/ element does not exist');
				}
				else
				{
					$this->assertEquals(0, $person_info_element->length, "Report personal-info element should not exist");
				}	
			}
			else
			{
				$this->assertEquals(0, $person_info_element->length, "Report personal-info element should not exist");
			}
		
			/* Incident Category check */
			$report_categories_element = $report_element->item(0)->getElementsByTagName('report_categories');
			$incident_categories = $incident->incident_category;
			$incident_cat_count = count($incident_categories);
			$cat_index = rand(0, $incident_cat_count-1);
			
			// Include categories option selected?
			if (in_array(3, $this->post['data_include']))
			{
				// Make sure the <reportcategories> element exists
				$this->assertGreaterThan(0, $report_categories_element->length, "Report categories element should exist");
				
				// Pick a random incident category
				$this_cat = $incident_categories[$cat_index];
				
				$report_cat_element = $report_categories_element->item(0)->getElementsByTagName('category');
				
				// Test incident_category title
				$incident_cat = $report_cat_element->item($cat_index)->nodeValue;
				$this->assertEquals($this_cat->category->category_title, $incident_cat, 'Incident_category does not match/element does not exist');
			}
			else
			{
				$this->assertEquals(0, $report_cat_element->length, "Report categories element should not exist");
			}
		
			/* Custom response check */
			$custom_responses_element = $report_element->item(0)->getElementsByTagName('custom_fields');
			$sql = "SELECT form_field.*, form_response.form_response
					FROM form_field
					LEFT JOIN roles ON (roles.id = field_ispublic_visible)
					LEFT JOIN
						form_response ON (
							form_response.form_field_id = form_field.id AND
							form_response.incident_id = :incident_id
						)
					WHERE form_id = :form_id "
					. "ORDER BY field_position ASC";
							
			$customresponses = Database::instance()->query($sql, array(
								':form_id' => $incident->form_id,
								':incident_id' => $incident->id
								));
			
			// Grab response count
			$response_count = count($customresponses);
			
			// Include custom fields option selected?
			if (in_array(6, $this->post['data_include']))
			{	
				// If we have custom field responses for this incident
				if ($response_count > 0)
				{
					// Make sure the <customfields> element exists
					$this->assertGreaterThan(0, $custom_responses_element->length, "Report custom responses element should exist");
					
					// Grab a random custom response by generating a random index to select based on field count
					$response_index = rand(0, $response_count-1);
					$this_response = $customresponses[$response_index];
					
					// Grab contents of <field> element
					$field_element = $custom_responses_element->item(0)->getElementsByTagName('field');
					
					// Make sure a form_response has actually been provided
					if ($this_response->form_response != '')
					{
						// Make sure the <field> element exists
						$this->assertNotNull($field_element->item($response_index), 'Custom Field response element should exist');
						
						// Test Field Name
						$field_name = xml::get_node_text($field_element->item($response_index), 'name', FALSE);
						$this->assertEquals($this_response->field_name, $field_name, 'Response field name does not match/attribute does not exist');
				
						// Test Field Response
						$response = $field_element->item($response_index)->nodeValue;
						$this->assertEquals($this_response->form_response, $response, 'Custom response does not match/element does not exist');
					}
					
					// No field response exists
					else
					{
						$this->assertNull($field_element->item($response_index), 'Custom Field response element should NOT exist');
					}
				}
				else
				{
					$this->assertEquals(0, $custom_responses_element->length, 'Custom Field response element should NOT exist');
				}	
			}
			else
			{
				$this->assertEquals(0, $custom_responses_element->length, "Report custom responses element should not exist");
			}
		}	
	}
	
	/**
	 * Test CSV Download
	 * @test
	 */
	public function testDownloadCSV()
	{
		// Test to ensure validation passed
		$this->assertEquals(TRUE, download::validate($this->post), 'Report download validation failed');
		
		// If we have no reports
		if (count($this->incident) == 0)
		{
			$this->markTestSkipped('There are no reports, CSV Download test skipped');
		}
		
		$expected_csv_content = "\"#\",\"FORM #\",\"INCIDENT TITLE\",\"INCIDENT DATE\"";
		
		// Include location information?
		if (in_array(1,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"LOCATION\"";
		}
		
		// Include description information?
		if (in_array(2,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"DESCRIPTION\"";
		}
		
		// Include category information?
		if (in_array(3,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"CATEGORY\"";
		}
		
		// Include latitude information?
		if (in_array(4,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"LATITUDE\"";
		}
		
		// Include longitude information?
		if (in_array(5,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"LONGITUDE\"";
		}
		
		// Include custom forms information?
		if (in_array(6,$this->post['data_include']))
		{
			foreach($this->custom_forms as $field_name)
			{
				$expected_csv_content.= ",\"".$field_name['field_name']."-".$field_name['form_id']."\"";
			}
		}
		
		// Include personal information?
		if (in_array(7,$this->post['data_include']))
		{
			$expected_csv_content.= ",\"FIRST NAME\",\"LAST NAME\",\"EMAIL\"";	
		}
		
		$expected_csv_content.= ",\"APPROVED\",\"VERIFIED\"";
		$expected_csv_content.="\r\n";
		
		// Add Report information 
		$report = $this->incident[0];
		
		// Report id, form_id, title, and date
		$expected_csv_content.='"'.$report->id.'",'
								.'"'.$report->form_id.'",'
								.'"'.$report->incident_title.'",'
								.'"'.$report->incident_date.'"';
		
		
		
		// Include location information?
		if (in_array(1,$this->post['data_include']))
		{
			$expected_csv_content.= ',"'.$report->location->location_name.'"';
		}
		
		// Include description information?
		if (in_array(2,$this->post['data_include']))
		{
			$expected_csv_content.= ',"'.$report->incident_description.'"';
		}
		
		// Include category information?
		if (in_array(3,$this->post['data_include']))
		{
			$cat = array();
			foreach($report->incident_category as $category)
			{
				if ($category->category->category_title)
				{
					$cat[] = $category->category->category_title;
				}
			}
			$expected_csv_content.= ',"'.implode($cat,',').'"';
		}
		
		// Include latitude information?
		if (in_array(4,$this->post['data_include']))
		{
			$expected_csv_content.= ',"'.$report->location->latitude.'"';
		}
		
		// Include longitude information?
		if (in_array(5,$this->post['data_include']))
		{
			$expected_csv_content.= ',"'.$report->location->longitude.'"';
		}
		
		// Include custom forms information?
		if (in_array(6,$this->post['data_include']))
		{
			$custom_fields = customforms::get_custom_form_fields($report->id,'',false);
			if ( ! empty($custom_fields))
			{
				foreach($custom_fields as $custom_field)
				{
					$expected_csv_content.= ',"'.$custom_field['field_response'].'"';
				}
			}
			else
			{
				foreach ($this->custom_forms as $custom)
				{
					$expected_csv_content.= ',""';
				}
			}
		}
		
		// Include personal information?
		if (in_array(7,$this->post['data_include']))
		{
			$person = $report->incident_person;
			if($person->loaded)
			{
				$expected_csv_content.= ',"'.$person->person_first.'"'
										.',"'.$person->person_last.'"'
										.',"'.$person->person_email.'"';
			}
			else
			{
				$expected_csv_content.= ',""'.',""'.',""';
			}	
		}	
		
		// Approved status
		if ($report->incident_active)
		{
			$expected_csv_content.= ",\"YES\"";
		}
		else
		{
			$expected_csv_content.= ",\"NO\"";
		}

		// Verified Status
		if ($report->incident_verified)
		{
			$expected_csv_content.= ",\"YES\"";
		}
		else
		{
			$expected_csv_content.= ",\"NO\"";
		}
		
		// End Expected output
		$expected_csv_content.= "\r\n";
		
		// Grab actual output
		$actual_csv_content = download::download_csv($this->post, $this->incident, $this->custom_forms);
		
		// Test CSV Output
		$this->assertEquals($expected_csv_content, $actual_csv_content, 'CSV Download failed. Content mismatch');
	}
} 

?>