<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User_Model Unit Test
 *
 * @author 		Ushahidi Team
 * @package 	Ushahidi
 * @category 	Unit Tests
 * @copyright 	(c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license 	For license information, see License.txt
 */
class User_Model_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * Provides dummy data for testing User_Model::custom_validate
	 * @dataProvider
	 */
	public function provider_custom_validate()
	{
		return array(array(
			array(
				'username' => 'ekala',
				'name' => 'Emmanuel Kala',
				'email'=>'emmanuel@example.com',
				'password' => 'abc#@_123tbhh', 
				'password_again'=>'abc#@_123tbhh',
				'notify' => '0',
				'role' => 'superadmin'
			),
			array(
				'username' => 'admin',
				'name' => 'Administrator',
				'email' => 'admin@example.com',
				'password' => 'admin123',
				'password_again' => 'admin_123',
				'notify' => '1',
				'role' => 'admin'
			)
		));
	}
	
	/**
	 * Tests User_Model::custom_validate
	 *
	 * @test
	 * @dataProvider provider_custom_validate
	 */
	public function test_custom_validate($valid, $invalid)
	{
		// set up mock, for prevent_superadmin_modification
		$auth = $this->getMock('Auth', array('logged_in'));
		$auth->expects($this->exactly(2))
			 ->method('logged_in')
			 ->with($this->equalTo('superadmin'))
			 ->will($this->returnValue(True));

		// Save initial data
		$initial_valid = $valid;
		$initial_invalid = $invalid;

		// Test with valid data
		$response = User_Model::custom_validate($valid, $auth);
		$this->assertEquals(TRUE, $valid instanceof Validation);
		$this->assertTrue($response, Kohana::debug($valid->errors()));
		
		// Test with invalid data
		$response = User_Model::custom_validate($invalid, $auth);
		$this->assertEquals(TRUE, $invalid instanceof Validation);
		$this->assertFalse($response);

		// restore valid, invalid
		$valid = $initial_valid;
		$invalid = $initial_invalid;

		// Test modification to superadmin as admin
		$auth = $this->getMock('Auth', array('logged_in'));
		$auth->expects($this->once())
			 ->method('logged_in')
			 ->with($this->equalTo('superadmin'))
			 ->will($this->returnValue(False));
		$response = User_Model::custom_validate($valid, $auth);
		$this->assertTrue($valid instanceof Validation);
		$this->assertFalse($response, Kohana::debug($valid->errors()));
	}
}
?>