<?php
class Reports_Helper_Test extends PHPUnit_Framework_TestCase {
	
	public function setUp()
	{
		// Report fields to be validated and saved
		$this->post = array
		(
			'incident_title' => 'Test incident title',
			'incident_description' => 'Testing reports helper for validation and saving of reports',
			'incident_date' => date("m/d/Y",time()),
			'incident_hour' => date('g'),
			'incident_minute' => date('i'),
			'incident_ampm' => date('a'),
			'latitude' => '-2.18250',
			'longitude' => '35.92056',
			'location_name' => 'Random location for the unit test',
			'country_id' => testutils::get_random_id('country'),
			'incident_category' => array(testutils::get_random_id('category', 'WHERE category_visible = 1')),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'person_first' => 'Test First Name',
			'person_last' => 'Test Last Name',
			'person_email' => 'testuser@example.com',
			'form_id' => '',
			'custom_field' => array()
		);
		
	}
	
	public function tearDown()
	{
		unset ($this->post);
	}
	
	/**
	 * Tests the report submit action
	 * @test
	 */
	public function testReportSubmit()
	{
		// Test if validation succeeds
		$this->assertEquals(TRUE, reports::validate($this->post), 'Report validation failed');
		
		// Location model
		$location = new Location_Model();
		
		// STEP 1: Save the location
		reports::save_location($this->post, $location);
		
		// Test the save
		$this->assertEquals(TRUE, intval($location->id) > 0, 'The location was not saved');
		
		// Incident model object
		$incident = new Incident_Model();
		
		// STEP 2: Save the incident
		reports::save_incident($this->post, $incident, $location->id);
		$this->assertEquals($location->id, $incident->location_id, 'Incident not associated with location');
		
		// Test if the incident has been saved
		$this->assertEquals(TRUE, intval($incident->id) > 0);
		
		// STEP 3: Save the category
		reports::save_category($this->post, $incident);
		
		// Test if the category has been saved
		$category_count = ORM::factory('incident_category')->where('incident_id',$incident->id)->find_all()->count();
		$this->assertEquals(TRUE, $category_count > 0, 'No entries in incident_categorgy for incident');
		
		// Save personal informatino
		 reports::save_personal_info($this->post, $incident);
		
		// Test
		$personal_info = ORM::factory('incident_person')->where('incident_id', $incident->id)->find_all()->count();
		$this->assertEquals(TRUE, $personal_info > 0, 'No entries in incident_person for incident');
		
		// @todo Test for saving of incident media
		
		// Cleanup
		ORM::factory('incident_category')->where('incident_id', $incident->id)->delete_all();
		ORM::factory('incident_person')->where('incident_id', $incident->id)->delete_all();
		$incident->delete();
		$location->delete();
	}
}
?>