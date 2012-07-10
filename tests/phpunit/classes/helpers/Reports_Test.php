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
			'country_name' => ORM::factory('country', Kohana::config('settings.default_country'))->country,
			'incident_category' =>array(testutils::get_random_id('category', 'WHERE category_visible = 1')),
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
		reports::save_report($this->post, $incident, $location->id);
		$this->assertEquals($location->id, $incident->location_id, 'Incident not associated with location');
		
		// Test if the incident has been saved
		$this->assertEquals(TRUE, intval($incident->id) > 0);
		
		// STEP 3: Save the category
		reports::save_category($this->post, $incident);
		
		// Test if the category has been saved
		$category_count = ORM::factory('incident_category')->where('incident_id',$incident->id)->find_all()->count();
		$this->assertEquals(TRUE, $category_count > 0, 'No entries in incident_categorgy for incident');
		
		// Save personal information
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
	
	/**
	 * Tests reports::fetch_incidents()
	 * @test
	 *
	 * This tests compares the output SQL of fetch_incidents against a pre-defined SQL
	 * statement based on dummy values. The objective of this test is to check whether
	 * reports::fetch_incidents processes the parameters property
	 */
	public function testFetchIncidents()
	{
		// Get random location and fetch the latitude and longitude
		$location = ORM::factory('location', testutils::get_random_id('location'));
		
		$longitude = $location->longitude;
		$latitude = $location->latitude;
		
		// Build the list of HTTP_GET parameters
		$filter_params = array(
			'c' => array(3, 4, 5),			// Category filters
			'start_loc' => $latitude.",".$longitude, 		// Start location
			'radius' => '20', 				// Location radius
			'mode' => array(1,2),			// Incident mode
			'm' => array(1),				// Media filter
			'from' => '07/07/2011',			// Start date
			'to' => '07/21/2011',			// End date
			'v' => 1						// Verification filter
		);
		
		// Add the report filter params to the list of HTTP_GET parameters
		$_GET = array_merge($_GET, $filter_params);
		
		// Get the incidents
		$incidents = reports::fetch_incidents();
		
		// Get the table prefix
		$table_prefix = Kohana::config('database.default.table_prefix');
		
		// Expected SQL statement; based on the $filter_params above
		$expected_sql = "SELECT DISTINCT i.id incident_id, i.incident_title, i.incident_description, i.incident_date, "
				. "i.incident_mode, i.incident_active, i.incident_verified, i.location_id, l.country_id, l.location_name, l.latitude, l.longitude "
				. ", ((ACOS(SIN(".$latitude." * PI() / 180) * SIN(l.`latitude` * PI() / 180) + COS(".$latitude." * PI() / 180) * "
				. "	COS(l.`latitude` * PI() / 180) * COS((".$longitude." - l.`longitude`) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance "
				. "FROM ".$table_prefix."incident i "
				. "LEFT JOIN ".$table_prefix."location l ON (i.location_id = l.id) "
				. "LEFT JOIN ".$table_prefix."incident_category ic ON (ic.incident_id = i.id) "
				. "LEFT JOIN ".$table_prefix."category c ON (ic.category_id = c.id) "
				. "WHERE i.incident_active = 1 "
				. "AND (c.id IN (".implode(",", $filter_params['c']).") OR c.parent_id IN (".implode(",", $filter_params['c']).")) "
				. "AND c.category_visible = 1 "
				. "AND i.incident_mode IN (".implode(",", $filter_params['mode']).") "
				. "AND i.incident_date >= \"2011-07-07\" "
				. "AND i.incident_date <= \"2011-07-21\" "
				. "AND i.id IN (SELECT DISTINCT incident_id FROM ".$table_prefix."media WHERE media_type IN (".implode(",", $filter_params['m']).")) "
				. "AND i.incident_verified IN (".$filter_params['v'].") "
				. "HAVING distance <= ".$filter_params['radius']." "
				. "ORDER BY i.incident_date DESC ";
		
		// Test the expected SQL against the returned
		$this->assertEquals($expected_sql, $incidents->sql());
		
		// Garbage collection
		unset ($location, $latitude, $longitude, $incidents, $filter_params);
	}
}
?>