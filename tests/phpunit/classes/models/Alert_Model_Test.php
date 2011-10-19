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
	 * Data provider for testValidate()
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
					'alert_category' => array(40, 20),
					'alert_radius' => 10,
					'alert_email' => 'test@ushahidi.com',
					'alert_mobile' => '254721247824'
				),
				FALSE
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->validate()
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $data Input data to be validated
	 * @param boolean $save Toggles the saving of the alert data 
	 */
	public function testValidate($data, $save)
	{
		// Create instance for the Alert_Model class
		// $model = new Alert_Model();
		
		// Check if the validation	succeeded
		// $this->assertEquals(TRUE, $model->validate($data, $save));
	}
	
}
?>