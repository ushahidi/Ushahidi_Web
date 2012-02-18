<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Alert_Model Unit test
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
class Alert_Model_Test extends PHPUnit_Framework_TestCase
{
	
	/**
	 * Data provider for test_validate_email_mobile()
	 *
	 * @return array
	 */
	public function providerValidate()
	{
		return array(
			array(
				array(
					'alert_lat' => -1.2821515239557,
					'alert_lon' => 36.819734568238,
					'alert_radius' => 10,
					'alert_email' => 'test@ushahidi.com',
					'alert_mobile' => '254721247824',
					'alert_country' => ORM::factory('country',Kohana::config('settings.default_country'))->id,
					'alert_confirmed' => 1

				),
				FALSE
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->validate() where both a subscriber email address and
	 * mobile phone no. have been specified
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $data Input data to be validated
	 */
	public function test_validate_email_mobile($data)
	{
		// Create instance for the Alert_Model class
		$model = new Alert_Model();
		
		// Check if the validation	succeeded
		$this->assertEquals(TRUE, $model->validate($data), 'Alert Validation Failed');

	}

	/**
	 * Data provider for test_validate_mobile()
	 *
	 * @return array
	 */
	public function providerValidateMobile()
	{
		return array(
			array(
				array(
					'alert_lat' => -1.2821515239557,
					'alert_lon' => 36.819734568238,
					'alert_radius' => 10,
					'alert_mobile' => '254721247824',
					'alert_country' => ORM::factory('country',Kohana::config('settings.default_country'))->id,
					'alert_confirmed' => 1

				),
				FALSE
			)
		);
		
	}

	/**
	 * When only the subscriber's mobile phone no. has been specified
	 *
	 * @dataProvider providerValidateMobile
	 */
	public function test_validate_mobile_only($data)
	{
		// Create an instance for the Alert_Model class
		$alert = new Alert_Model();

		//Check if validation succeeded
		$this->assertEquals(TRUE,$alert->validate($data), 'Alert Mobile phone number not specified');

	}

	/**
	 * Data provider for test_validate_email()
	 *
	 * @return array
	 */
	public function providerValidateEmail()
	{
		return array(
			array(
				array(
					'alert_lat' => -1.2821515239557,
					'alert_lon' => 36.819734568238,
					'alert_radius' => 10,
					'alert_email' => 'test@ushahidi.com',
					'alert_country' => ORM::factory('country',Kohana::config('settings.default_country'))->id,
					'alert_confirmed' => 1

				),
				FALSE
			)
		);
		
	}

	/**
	 * When only the subscriber's email address has been specified
	 *
	 * @dataProvider providerValidateEmail
	 */
	public function test_validate_email_only($data)
	{
		//Create an instance for the Alert_Model class
		$alert = new Alert_Model();

		//Check if validation succeeded
		$this->assertEquals(TRUE,$alert->validate($data), 'Alert Email adddress not specified');
	}

	/**
	 * Data provider for test_validate_no_subscriber()
	 *
	 * @return array
	 */
	public function providerValidateSubscriber()
	{
		return array(
			array(
				array(
					'alert_lat' => -1.2821515239557,
					'alert_lon' => 36.819734568238,
					'alert_radius' => 10,
					'alert_country' => ORM::factory('country',Kohana::config('settings.default_country'))->id,
					'alert_confirmed' => 1

				),
				FALSE
			)
		);
		
	}

	/**
	 * Tests where no subscriber email address or phone no. has been specified
	 * 
	 * @dataProvider providerValidateSubscriber
 	 */
	public function test_validate_no_subscriber($data)
	{
		// Create an instance for the Alert_Model class
		$alert = new Alert_Model();

		//Check if validation succeeded
		$this->assertFalse($alert->validate($data), 'Neither Alert Email address or Alert Mobile phone number has been specified');
	}

	
}
?>
