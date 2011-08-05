<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Unit tests for the custom forms helper
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
class Customforms_Helper_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests the get_custom_forms method
	 *
	 * @test
	 */
	public function testGetCustomForms()
	{
		// Database instance for the test
		$db = new Database();
		
		// The record count should be the same since get_custom_forms() has no predicates
		$this->assertEquals($db->count_records('form'), customforms::get_custom_forms()->count());
	}


	/**
	 * Data provider for testValidateCustomFormFields
	 *
	 * @dataProvider
	 */
	public function providerTestValidateCustomFormFields()
	{
		return array(array(
			// Valid custom forms data
			array(
				'custom_field' => array(
					1 => 'Test compulsory text field data',
					2 => '07/20/2011',
					3 => 'Yes'
				)
			),

			// Invalid custom forms data
			array(
				'custom_field' => array(
					1 => 'Test compulsory text field data',
					2 => '2011/07/20',
					3 => ''
				)
			)
		));
	}

	/**
	 * Tests customforms::validate_custom_form_fields()
	 *
	 * @dataProvider providerTestValidateCustomFormFields
	 */
	public function testValidateCustomFormFields($valid_data, $invalid_data)
	{
		// Setup validation objects for the valid custom forms data
		$valid_validator = Validation::factory($valid_data)
							->pre_filter('trim', TRUE);

		// Get the return value for validation of valid date
		$errors = customforms::validate_custom_form_fields($valid_validator);

		// Assert that validation of the valid data returns no errors
		$this->assertEquals(0, count($errors), "Some errors have been found".Kohana::debug($errors));

		// Set up validation for the invalid custom forms data
		$invalid_validator = Validation::factory($invalid_data)
								->pre_filter('trim', TRUE);

		// Get the return value for validation of invalid data
		$errors = customforms::validate_custom_form_fields($invalid_validator);

		// Assert that the validation of the invalid data returns some errors
		$this->assertEquals(TRUE, count($errors) > 0, "Expected to encounter errors. None found: ".count($errors));

		// Garbage collection
		unset ($valid_validator, $invalid_validator, $errors);
	}
}
?>
