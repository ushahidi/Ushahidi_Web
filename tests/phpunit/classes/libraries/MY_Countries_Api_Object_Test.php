<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Unit tests for the countries API
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Countries_Api_Object_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * API Controller object to run the tests
	 * @var Api_Controller
	 */
	private $api_controller;
	
	protected function setUp()
	{
		// $_SERVER values
		$_SERVER = array_merge($_SERVER, array(
			'REQUEST_METHOD' => 'GET',
			'REQUEST_URI' => url::base().'api/?task=countries',
		));
		
		// Instantiate the API controller
		$this->api_controller = new Api_Controller();
	}
	
	protected function tearDown()
	{
		// Garbage collection
		unset ($this->api_controller);
	}
	
	/**
	 * Tests fetching all countries
	 * @test
	 */
	public function testGetCountriesByAll()
	{
		// HTTP GET values
		$_GET = array(
			'task' => 'countries'
		);
		
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals(0, (int)$contents->error->code);
		
	}
	
	/**
	 * Tests getting a country using the ISO code
	 * @test
	 */
	public function testGetCountryByISO()
	{
		// Test fetching by ISO code
		$country = ORM::factory('country', testutils::get_random_id('country'));
		
		// Set the HTTP GET data
		$_GET = array(
			'task' => 'countries',
			'by' => 'countryiso', 
			'iso' => $country->iso
		);
		
		// Fetcht the content
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		// No error should be returned and ISO code should match that of the loaded country
		$this->assertEquals($country->iso, $contents->payload->countries[0]->country->iso);
		$this->assertEquals(0, (int)$contents->error->code);
	}
	
	/**
	 * Tests fetching countries by country name
	 * @test
	 */
	public function testGetCountryByName()
	{
		// Test fetching by ISO code
		$country = ORM::factory('country', testutils::get_random_id('country'));
		
		// Test fetching by country name
		$_GET = array(
			'task' => 'countries',
			'by' => 'countryname', 
			'name' => $country->country
		);
		
		// Fetch the content
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
		
		$this->assertEquals($country->country, $contents->payload->countries[0]->country->name);
		$this->assertEquals(0, (int)$contents->error->code);
	}
	
	/**
	 * Tests fetching countries by country ID
	 * @test
	 */
	public function testGetCountryById()
	{
		// Test fetching by ISO code
		$country = ORM::factory('country', testutils::get_random_id('country'));
		
		// Test fetching countries by database id
		$_GET = array(
			'task' => 'countries',
			'by' => 'countryid', 
			'id' => $country->id
		);
		
		// Fetch the content
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
	
		$this->assertEquals($country->id, $contents->payload->countries[0]->country->id);
		$this->assertEquals(0, (int)$contents->error->code);
		
		// Test "No Data Found"
		// Test using invalid country id
		$_GET['id'] = 'PL';
		ob_start();
		$this->api_controller->index();
		$contents = json_decode(ob_get_clean());
	
		$this->assertEquals("007", $contents->error->code);
		
	}
}
?>