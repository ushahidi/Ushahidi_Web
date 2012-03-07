<?php defined('SYSPATH') or die('No direct script access allowed.');
/**
 * Unit tests for the form_field model
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
class Form_Field_Model_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * Data provider for test validate
	 * @dataProvider
	 */
	public function providerValidate()
	{
		return array(
			array(
				array(
					'form_id' => '1',
					'field_type' => '3',
					'field_name' => 'Test Date',
					'field_default' => '03/08/2011',
					'field_required' => '1',
					'field_height' => '',
					'field_width' => '',
					'field_isdate' => '1',
					'field_ispublic_visible' => '0',
					'field_ispublic_submit' => '0',
				), //Valid form field data

				array(
					'form_id' => '1',
					'field_type' => '3',
					'field_name' => 'Test Date',
					'field_default' => 'test',
					'field_required' => '1',
					'field_height' => '',
					'field_width' => '',
					'field_isdate' => '1',
					'field_ispublic_visible' => '',
					'field_ispublic_submit' => '',
				), //Invalid form field data
				
				TRUE // save record when validation succeeds
			)
		);
		
	}
	
	
	/**
	 * Tests the validate() method in Form Field Data
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $valid Valid data for the test
	 * @param array $invalid Invalid data for the test
	 * @param array $save Saves the record when validation succeeds
	 */
	public function testValidate($valid, $invalid, $save)
	{
		// Model instance for the test
		$form_field = new Form_Field_Model();
		
		// Valid data test
		$this->assertEquals(TRUE, $form_field->validate($valid, $save),Kohana::debug($valid->errors()));
		
		// Invalid data test
		$this->assertEquals(FALSE,
		$form_field->validate($invalid,$save), sprintf("Expected errors not found. Error count: %d", count($invalid->errors())));
	}


}


?>
