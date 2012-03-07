<?php
class Locations_Api_Object_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * API Controller object to be used for issuing API requests
	 * @var Api_Controller
	 */
	private $api_controller;
	
	protected function setUp()
	{
		if (ORM::factory('location')->count_all() == 0)
		{
			// Skip the test
			$this->markTestSkipped('The locations table is empty');
		}
		else
		{
			// $_SERVER values
			$_SERVER = array_merge($_SERVER, array(
				'REQUEST_METHOD' => 'GET',
				'REQUEST_URI' => url::base().'api/?task=locations',
			));
		
			$this->api_controller = new Api_Controller();
		}
	}
	
	protected function tearDown()
	{
		// Garbage collection
		unset ($this->api_controller);
	}
	
	/**
	 * Tests fetching all locations
	 */
	public function testGetAllLocations()
	{
		$_GET = array('task' => 'locations');
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals("0", $contents->error->code);
	}
	
	public function testGetLocationsByName()
	{
		
	}
	
	/**
	 * Tests fetching of locations by location id
	 */
	public function testGetLocationsByLocationId()
	{
		// Get a random location
		$location_id = testutils::get_random_id('location');
		
		// Parameters to submit
		$_GET = array('task' => 'locations', 'by' => 'locid', 'id' => $location_id);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals("0", $contents->error->code);
		$this->assertEquals($location_id, (int)$contents->payload->locations[0]->location->id);
	}
}
?>