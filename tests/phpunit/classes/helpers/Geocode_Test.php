<?php
class Geocode_Helper_Test extends PHPUnit_Framework_TestCase {

	
	public function setUp()
	{
		// Test location - Wanaka, NZ
		$this->lat = -44.696736;
		$this->lng = 169.131646;
	}


	/**
	 * Tests geocoder using google functions
	 *
	 * @test
	 */
	public function testGoogleGeocoder()
	{
		// reverse geocode
		$address = geocode::reverseGoogle($this->lat, $this->lng);
		$this->assertTrue($address !== FALSE);

		// geocode
		$result = geocode::google($address);
		$this->assertEquals($result["latitude"], $this->lat, null, 0.01);
		$this->assertEquals($result["longitude"], $this->lng, null, 0.01);
	}

	/**
	 * Tests geocoder using google functions
	 *
	 * @test
	 */
	public function testNominatinGeocoder()
	{
		// reverse geocode
		$address = geocode::reverseNominatim($this->lat, $this->lng);
		$this->assertTrue($address !== FALSE);

		// geocode
		$result = geocode::nominatim($address);
		$this->assertEquals($result["latitude"], $this->lat, null, 0.01);
		$this->assertEquals($result["longitude"], $this->lng, null, 0.01);
	}

}
