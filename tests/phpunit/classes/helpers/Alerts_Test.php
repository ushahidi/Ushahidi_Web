<?php
class Alerts_Helper_Test extends PHPUnit_Framework_TestCase {

	public function setUp()
	{
		// Alert fields to be validated and saved
		$this->post = array
		(
			'alert_type' => '1',
			'alert_mobile' => '0723674180',
			'alert_code' => 'CDS0KJDS',
			'alert_lon' => '35.92056',
			'alert_lat' => '-2.18250',
			'alert_radius' => '20',
			'alert_confirmed' => '1',
			'alert_country' => ORM::factory('country', Kohana::config('settings.default_country'))->id
		);
		
	}
	
	public function tearDown()
	{
		unset ($this->post);
	}
	
	/**
	 * Tests the send mobile alerts action
	 * @test
	 */
	public function testSendMobileAlerts()
	{
		// Test if validation succeeds
		
		// Alert Model
		$alert = new Alert_Model();
		$this->assertTrue($alert->validate($this->post), 'Alert validation failed');

		//Check for duplicate alerts subscriptons
		$mobile_check = $alert->_mobile_check($this->post);
		$this->assertTrue(TRUE,count($mobile_check) > 0, 'Duplicate subscription');

	}

	
}
?>
