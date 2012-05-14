<?php defined('SYSPATH') or die('No direct script access');
/**
 * Unit tests for the incidents API
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
class Incidents_Api_Object_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * API controller object to run the tests
	 * @var Api_Controller
	 */
	private $api_controller;
	
	/**
	 * Database object for straight SQL queries
	 * @var Database
	 */
	private $db;
	
	/**
	 * Database table prefix
	 * @var string
	 */
	private $table_prefix;
	
	protected function setUp()
	{
		if (ORM::factory('incident')->count_all() == 0)
		{
			$this->markTestSkipped('The incident table is empty.');
		}
		else
		{
			// $_SERVER values
			$_SERVER = array_merge($_SERVER, array(
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI' => url::base().'api/?task=incidents',
			));

			// Instantiate the API controller
			$this->api_controller = new Api_Controller();
			
			// Instantiate the DB object
			$this->db = new Database();
			
			// Table prefix
			$this->table_prefix = Kohana::config('database.default.table_prefix');
		}
	}
	
	protected function tearDown()
	{
		// Garbage collection
		unset ($this->api_controller, $this->db, $this->table_prefix);
	}
	
	/**
	 * Tests fetching incidents when only the task is specified - without any
	 * extra parameters
	 * @test
	 */
	public function testGetIncidentsByAll()
	{
		// HTTP GET data
		$_GET = array('task' => 'incidents');
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Assert the results
		$this->assertEquals("0", $contents->error->code);
		$this->assertEquals(count(Incident_Model::get_incidents(array(), 20)), count($contents->payload->incidents));
	}
	
	/**
	 * Tests fetching of incidents when the limit parameter is specified
	 * @test
	 */
	public function testGetIncidentsByLimit()
	{
		// Randomly generate the record limit
		$limit = rand(1, Incident_Model::get_incidents()->count());
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'limit' => $limit);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals("0", $contents->error->code);
		$this->assertEquals($limit, count($contents->payload->incidents));
	}
	
	/**
	 * Tests fetching an incident by its database ID. 
	 * The operation should return a single record on succeed
	 * @test
	 */
	public function testGetIncidentsById()
	{
		// Get a random incident id
		$incident_id = testutils::get_random_id('incident', 'WHERE incident_active = 1');
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'incidentid', 'id'=> $incident_id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals("0", $contents->error->code);
		$this->assertEquals($incident_id, (int)$contents->payload->incidents[0]->incident->incidentid);
		
	}
	
	/**
	 * Tests fetching incidents by the ID of an incident that has not been
	 * approved
	 */
	public function testGetIncidentsByUnapprovedIncidentId()
	{
		// Get a random incident
		$incident = ORM::factory('incident', testutils::get_random_id('incident'));
		
		// Get the current incident status
		$incident_active = $incident->incident_active;
		$incident->incident_active = 0;
		$incident->save();
		
		// Verify that incident_active = 0
		$this->assertEquals(0, $incident->incident_active);
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'incidentid', 'id'=> $incident->id);
	
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Verify that no records have been returned
		$this->assertEquals("007", $contents->error->code, sprintf("No records should be returned for incident %d", $incident->id));
		
		// Change the incident_active back to its original value
		$incident->incident_active = $incident_active;
		$incident->save();
		
		// Garbage collection
		unset ($incident, $incident_active);
	}
	
	/**
	 * Gets incidents by lat/lon
	 * @test
	 */
	public function testGetIncidentsByLatLon()
	{
		// @todo Get random lat/lon
	}
	
	/**
	 * Gets incidents by location id
	 * @test
	 */
	public function testGetIncidentsByLocationId()
	{
		// Get location_id from a randomly selected incident
		$location_id = testutils::get_random_id('location', 
			'WHERE id IN (SELECT location_id FROM '.$this->table_prefix.'incident WHERE incident_active = 1)');
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'locid', 'id'=> $location_id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Vary test depending on the incident status
		$this->assertEquals("0", $contents->error->code, sprintf("No records found for location id: %d", $location_id));
		
		// Garbage collection
		unset($location_id);
	}
	
	/**
	 * Gets incidents by location name
	 * @test
	 */
	public function testGetIncidentsByLocationName()
	{
		// Get random location id
		$location_id = testutils::get_random_id('location', 
			'WHERE id IN (SELECT location_id FROM '.$this->table_prefix.'incident WHERE incident_active = 1)');
		
		// Get the location name
		$location_name = ORM::factory('location', $location_id)->location_name;
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'locname', 'name'=> $location_name);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Vary test depending on the incident status
		$this->assertEquals("0", $contents->error->code, sprintf("No data found for location :%s", $location_name));
		
		// Garbage collection
		unset ($location_id, $location_name);
	}
	
	/**
	 * Gets incidents by catgory id that is visible
	 * @test
	 */
	public function testGetIncidentsByCategoryId()
	{
		// Get a random category id
		$category = ORM::factory('incident_category', testutils::get_random_id('incident_category'))->category;
		
		$category_visible = $category->category_visible;
		$category->category_visible = 1;
		$category->save();
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'catid', 'id'=> $category->id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals(0, (int)$contents->error->code, sprintf("No incidents for category id %d", $category->id));
		
		// Get random index for the payload data items
		$index = rand(0, count($contents->payload->incidents) - 1);
		$this->assertGreaterThanOrEqual(1, count($contents->payload->incidents[$index]->categories));
		
		// Set the category back to its original visibile status
		$category->category_visible = $category_visible;
		$category->save();
		
		// Garbage collection
		unset ($category, $category_visible);
	}
	
	/**
	 * Tests fetching of incidents via a category id that is invisible
	 */
	public function testIncidentsByInvisibleCategoryId()
	{
		// Get random category
		$incident_category = ORM::factory('incident_category', testutils::get_random_id('incident_category'));
		
		if ( ! $incident_category->loaded)
		{
			// Skip test if not loaded
			$this->markTestSkipped('There are no entries in the incident_category table');
		}
		else
		{
			// Get the category
			$category = $incident_category->category;
			
			// Verify that the category is loaded
			$this->assertTrue($category->loaded, 'The category could not be loaded');
			
			// Verify the category id
			$category_visible = $category->category_visible;
				
			// Set the category visibility to 0
			$category->category_visible = 0;
			
			// Save
			$category->save();
			
			// Verify that the category visibility is 0
			$this->assertEquals(0, $category->category_visible, sprintf("Category %d is still visible", $category->id));
			
			// HTTP GET data
			$_GET = array('task' => 'incidents', 'by' => 'catid', 'id'=> $category->id);
		
			ob_start();
			$this->api_controller->index();
			$contents = json_decode(ob_get_clean());
		
			// Category is invisible, therefore no records should be returned
			$this->assertEquals("007", $contents->error->code, 
				sprintf("Catgeory %d is invisible therefore no records should be returned", $category->id));
			
			// Set the category visibility back to its original value
			$category->category_visible = $category_visible;
			$category->save();
				
			$this->assertEquals($category_visible, $category->category_visible, "Category visibility not changed back to original value");
			
			// Garbage collection
			unset ($category);
		}
		
		// Garbage collection
		unset ($incident_category);
	}
	
	/**
	 * Tests fetching incidents by category name
	 */
	public function testGetIncidentsByCategoryName()
	{
		// Get random incident category record
		$category_id = testutils::get_random_id('category', 
			'WHERE category_visible = 1 AND id IN (SELECT category_id FROM '.$this->table_prefix.'incident_category)');

		// Get the category name
		$category_name = ORM::factory('category', $category_id)->category_title;
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'catname', 'name'=> $category_name);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Test for successful execution
		$this->assertEquals("0", $contents->error->code, sprintf("No data found for the '%s' category", $category_name));
		
		// Garbage collection
		unset ($category_id, $category_name);
	}
	
	/**
	 * Tests fetching incidents using the sinceid parameter
	 */
	public function testGetIncidentsBySinceId()
	{
		// Get random incident id - it must be active
		$incident_id = testutils::get_random_id('incident', 'WHERE incident_active = 1');
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'sinceid', 'id'=> $incident_id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals("0", $contents->error->code);
		
		// Get random index for the payload data
		$index = rand(0, count($contents->payload->incidents) - 1);
		
		// Fetched incidents should have an id greather than the search parameter
		$this->assertGreaterThan($incident_id, (int)$contents->payload->incidents[$index]->incident->incidentid);
	}
	
	public function testGetIncidentsByMaxId()
	{
		// Get random incident id - it must be active
		$incident_id = testutils::get_random_id('incident', 'WHERE incident_active = 1');
		
		// HTTP GET data
		$_GET = array('task' => 'incidents', 'by' => 'maxid', 'id'=> $incident_id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// Test for successful execution
		$this->assertEquals("0", $contents->error->code);
		
		// Get random index for the payload data
		$index = rand(0, count($contents->payload->incidents) - 1);
		
		// Fetched incidents should have an id less than or equal to the search parameter
		$this->assertLessThanOrEqual($incident_id, (int)$contents->payload->incidents[$index]->incident->incidentid);
		
	}
	
	/**
	 * Tests fetching incidents by a bounded area
	 * @test
	 */
	public function testGetIncidentsByBounds()
	{
		// @todo Helper method to generate a bounding box
	}
	
}
?>
