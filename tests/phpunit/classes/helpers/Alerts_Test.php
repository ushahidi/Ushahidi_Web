<?php
class Alerts_Helper_Test extends PHPUnit_Framework_TestCase {

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
	 * Returns a dataset containing the alert data to be used for the tests
	 * in this test case
	 *
	 * @return PHPUnit_Database_Extensions_Database_DataSet_IDataSet
	 */
	protected function getDataSet()
	{
		return new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(TESTS_PATH.'data/alert.xml');
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
	 * Data provider for test_alert_code_exists()
	 *
	 * @return array
	 */
	public function providerValidate()
	{
		return array(
			array(
					testutils::get_random_id('alert', 'WHERE alert_confirmed = 1')
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->alert_code_exist() where the alert_code exists
	 *
	 * @test
	 * @dataProvider providerValidate
	 * @param array $data Input data to be validated
	 */
	public function test_alert_code_exists($data)
	{
		// Create instance for the Alert_Model class
		$model = new Alert_Model();
		// Check if the alert code exists
		$this->assertEquals(TRUE, $model->alert_code_exists($data), 'Alert Code exists');

	}

	/**
	 * Data provider for test_alert_code_not_exists()
	 *
	 * @return array
	 */
	public function providerValidateAlertCode()
	{
		return array(
			array(
					'3WUXAPRT'
			)
		);
		
	}
	
	/**
	 * Tests Alert_Model->alert_code_exist() where the alert_code is
	 * non-existent
	 *
	 * @test
	 * @dataProvider providerValidateAlertCode
	 * @param array $data Input data to be validated
	 */
	public function test_alert_code_non_exists($data)
	{
		// Create instance for the Alert_Model class
		$model = new Alert_Model();
		
		// Check if the alert code exists
		$this->assertEquals(FALSE, $model->alert_code_exists($data), 'Alert Code does not exist');

	}

}
?>
