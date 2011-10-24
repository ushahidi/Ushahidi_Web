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
 * @subpackage Unit Tests
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
class Customforms_Helper_Test extends PHPUnit_Framework_TestCase {
	
	/**
	 * Executed when this test case is initialized
	 */
	public function setUp()
	{
		$this->databaseTester = NULL;
		
		// Build the PDO datasource name (DSN)
		$database_dsn = Kohana::config('database.default.connection.type').":"
					."dbname=".Kohana::config('database.default.connection.database').";"
					."host=".Kohana::config('database.default.connection.host');
		
		// Create PDO object
		$pdo = new PDO($database_dsn, Kohana::config('database.default.connection.user'), 
			Kohana::config('database.default.connection.pass'));
		
		// Create connection
		$connection = new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection($pdo, 
			Kohana::config('database.default.connection.database'));
		
		// Set up the database tester object, setup operation and dataset
		$this->databaseTester = new PHPUnit_Extensions_Database_DefaultTester($connection);
		$this->databaseTester->setSetUpOperation(PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT());
		
		// 	Set the dataset for the tester
		$this->databaseTester->setDataSet($this->getDataSet());
		
		// Run setup
		$this->databaseTester->onSetUp();
	}
	
	/**
	 * Returns a dataset containing the form field data to be used for the tests
	 * in this test case
	 *
	 * @return PHPUnit_Database_Extensions_Database_DataSet_IDataSet
	 */
	protected function getDataSet()
	{
		return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(TESTS_PATH.'data/form_field.xml');
	}
	
	/**
	 * Tear down operation - Executed when the test is complete
	 */
	public function tearDown()
	{
		// Tear down operation
		$this->databaseTester->setTearDownOperation(PHPUnit_Extensions_Database_Operation_Factory::TRUNCATE());
		$this->databaseTester->setDataSet($this->getDataSet());
		$this->databaseTester->onTearDown();
		
		// Garbage collection
		unset ($this->databaseTester);
	}
	
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
					7 => 'Test compulsory text field data',
					9 => '07/20/2011',
					10 => 'Radio 1'
				)
			),

			// Invalid custom forms data
			array(
				'custom_field' => array(
					1 => 'Test compulsory text field data',
					3 => '2011/07/20',
					11 => ''
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
